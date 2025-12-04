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
            $user = Auth::user();

            // Cek apakah user ditangguhkan
            if ($user->is_suspended) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akun Anda telah ditangguhkan. Silakan hubungi administrator.',
                ])->onlyInput('email');
            }

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

        Auth::login($user);
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

                // Update photo jika ada perubahan
                if ($googleUser->getAvatar() && $user->photo !== $googleUser->getAvatar()) {
                    $user->update(['photo' => $googleUser->getAvatar()]);
                }
            } else {
                // Cek apakah email sudah terdaftar (login biasa)
                $existingUser = User::where('email', $googleUser->getEmail())->first();

                if ($existingUser) {
                    // Email sudah terdaftar, link Google account
                    $existingUser->update([
                        'google_id' => $googleUser->getId(),
                        'photo' => $googleUser->getAvatar() ?? $existingUser->photo,
                    ]);
                    $user = $existingUser;
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

            // Login user
            Auth::login($user, true);
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

