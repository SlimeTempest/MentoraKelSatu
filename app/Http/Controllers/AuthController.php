<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
}

