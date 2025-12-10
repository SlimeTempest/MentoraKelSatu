<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            // Refresh user dari database untuk memastikan data terbaru
            $user = Auth::user()->fresh();

            // Cek apakah user ditangguhkan
            if ($user->is_suspended) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akun Anda telah ditangguhkan. Silakan hubungi administrator.',
                ])->onlyInput('email');
            }

            // Refresh session dengan data user terbaru
            Auth::login($user, $remember);
            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password tidak sesuai.',
        ])->onlyInput('email');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'role' => ['required', 'in:mahasiswa,dosen'],
        ]);

        // Generate recovery code (8 karakter alphanumeric)
        $recoveryCode = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'recovery_code' => $recoveryCode,
        ]);

        // Refresh user dan login dengan data terbaru
        Auth::login($user->fresh(), true);
        $request->session()->regenerate();

        return redirect('/dashboard');
    }

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'recovery_code' => ['required', 'string', 'size:8'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = User::where('email', $data['email'])
            ->where('recovery_code', strtoupper($data['recovery_code']))
            ->first();

        if (!$user) {
            return back()->withErrors([
                'recovery_code' => 'Email atau recovery code tidak valid.',
            ])->onlyInput('email');
        }

        // Update password dan generate recovery code baru
        $newRecoveryCode = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));

        $user->update([
            'password' => Hash::make($data['password']),
            'recovery_code' => $newRecoveryCode,
        ]);

        return redirect()->route('login')->with('status', 'Password berhasil direset. Silakan login dengan password baru.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Anda telah berhasil logout.');
    }

    /**
     * Redirect user to Google OAuth provider
     */
    public function redirectToGoogle()
    {
        // Fix SSL certificate issue untuk development
        $socialite = Socialite::driver('google');
        if (app()->environment('local')) {
            $socialite->setHttpClient(new \GuzzleHttp\Client([
                'verify' => false, // Disable SSL verification untuk development
            ]));
        }
        // Tambahkan parameter untuk selalu menampilkan pilihan akun
        return $socialite->with(['prompt' => 'select_account'])->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            // Fix SSL certificate issue untuk development
            $socialite = Socialite::driver('google');
            if (app()->environment('local')) {
                $socialite->setHttpClient(new \GuzzleHttp\Client([
                    'verify' => false, // Disable SSL verification untuk development
                ]));
            }
            $googleUser = $socialite->user();

            // Validasi data dari Google
            if (!$googleUser->getEmail()) {
                \Log::error('Google OAuth: Email tidak ditemukan', ['google_user' => $googleUser]);
                return redirect()->route('login')->withErrors([
                    'email' => 'Email tidak ditemukan dari akun Google. Pastikan akun Google Anda memiliki email.',
                ]);
            }

            // Cek apakah user sudah ada berdasarkan google_id
            $user = User::where('google_id', $googleUser->getId())->first();

            if ($user) {
                // User sudah pernah login dengan Google
                // Cek apakah user ditangguhkan
                if ($user->is_suspended) {
                    return redirect()->route('login')->withErrors([
                        'email' => 'Akun Anda telah ditangguhkan. Silakan hubungi administrator.',
                    ]);
                }

                // Jangan update photo jika user sudah punya photo lokal yang diupload
                // Cek apakah photo adalah URL Google atau path lokal
                $isLocalPhoto = $user->photo && !filter_var($user->photo, FILTER_VALIDATE_URL);
                
                // Log untuk debugging
                \Log::info('Google OAuth: Existing user login', [
                    'user_id' => $user->user_id,
                    'has_photo' => !empty($user->photo),
                    'is_local_photo' => $isLocalPhoto,
                    'current_photo' => $user->photo ? substr($user->photo, 0, 50) : 'null',
                    'google_avatar' => $googleUser->getAvatar() ? 'provided' : 'null',
                ]);
                
                // Hanya update photo jika:
                // 1. User belum punya photo, ATAU
                // 2. Photo saat ini masih URL Google (belum diupload foto lokal)
                if ($googleUser->getAvatar() && !$isLocalPhoto) {
                    // Jika user belum punya photo atau photo masih dari Google, update dengan Google avatar
                    if (!$user->photo || filter_var($user->photo, FILTER_VALIDATE_URL)) {
                        $user->update(['photo' => $googleUser->getAvatar()]);
                        \Log::info('Google OAuth: Photo updated from Google', ['user_id' => $user->user_id]);
                    }
                } else {
                    \Log::info('Google OAuth: Photo NOT updated (local photo exists)', ['user_id' => $user->user_id]);
                }
                
                // JANGAN UPDATE name, email, phone, atau field lain - biarkan data user yang sudah diubah tetap ada
                
                // Refresh user dari database untuk memastikan data terbaru
                $user = $user->fresh();
                
                // Log untuk verifikasi data user setelah refresh
                \Log::info('Google OAuth: User data after refresh', [
                    'user_id' => $user->user_id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? 'null',
                    'photo' => $user->photo ? substr($user->photo, 0, 50) : 'null',
                ]);
            } else {
                // Cek apakah email sudah terdaftar (login biasa)
                $existingUser = User::where('email', $googleUser->getEmail())->first();

                if ($existingUser) {
                    // Email sudah terdaftar, link Google account
                    // Jangan overwrite data yang sudah ada, hanya link google_id
                    $updateData = ['google_id' => $googleUser->getId()];
                    
                    // Hanya update photo jika user belum punya photo lokal
                    $isLocalPhoto = $existingUser->photo && !filter_var($existingUser->photo, FILTER_VALIDATE_URL);
                    if ($googleUser->getAvatar() && !$isLocalPhoto) {
                        // Jika belum ada photo atau photo masih URL Google, gunakan Google avatar
                        if (!$existingUser->photo || filter_var($existingUser->photo, FILTER_VALIDATE_URL)) {
                            $updateData['photo'] = $googleUser->getAvatar();
                        }
                    }
                    
                    $existingUser->update($updateData);
                    $user = $existingUser->fresh();
                } else {
                    // User baru, buat akun baru (Auto-register)
                    // Generate recovery code
                    $recoveryCode = strtoupper(substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 8));

                    try {
                        $user = User::create([
                            'name' => $googleUser->getName() ?? 'User',
                            'email' => $googleUser->getEmail(),
                            'google_id' => $googleUser->getId(),
                            'password' => null, // Tidak ada password untuk Google login
                            'role' => 'mahasiswa', // Default role
                            'recovery_code' => $recoveryCode,
                            'photo' => $googleUser->getAvatar(),
                            'balance' => 0,
                            'avg_rating' => 0,
                            'is_suspended' => false,
                        ]);
                        
                        \Log::info('Google OAuth: User baru berhasil dibuat', [
                            'user_id' => $user->user_id,
                            'email' => $user->email,
                            'google_id' => $user->google_id
                        ]);
                    } catch (\Exception $createError) {
                        \Log::error('Google OAuth: Gagal membuat user baru', [
                            'error' => $createError->getMessage(),
                            'email' => $googleUser->getEmail(),
                            'google_id' => $googleUser->getId(),
                            'trace' => $createError->getTraceAsString()
                        ]);
                        throw $createError; // Re-throw untuk ditangkap oleh catch di atas
                    }
                }
            }

            // Login user dan refresh untuk memastikan data terbaru
            Auth::login($user->fresh(), true);
            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Google OAuth Database Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('login')->withErrors([
                'email' => 'Gagal menyimpan data. Silakan hubungi administrator.',
            ]);
        } catch (\Exception $e) {
            \Log::error('Google OAuth Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('login')->withErrors([
                'email' => 'Gagal login dengan Google: ' . $e->getMessage(),
            ]);
        }
    }
}

