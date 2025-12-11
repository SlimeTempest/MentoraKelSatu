<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            abort(403, 'This action is unauthorized.');
        }

        $query = User::query();

        // Search by name
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%");
        }

        // Filter by role
        if ($request->filled('role') && $request->get('role') !== 'all') {
            $query->where('role', $request->get('role'));
        }

        // Filter by suspension status
        if ($request->filled('suspended') && $request->get('suspended') !== 'all') {
            $query->where('is_suspended', $request->get('suspended') === '1');
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.users.index', [
            'users' => $users,
            'search' => $request->get('search', ''),
            'roleFilter' => $request->get('role', 'all'),
            'suspendedFilter' => $request->get('suspended', 'all'),
        ]);
    }

    public function suspend(Request $request, User $user)
    {
        if ($request->user()->role !== 'admin') {
            abort(403, 'This action is unauthorized.');
        }

        // Admin tidak bisa suspend diri sendiri
        if ($user->user_id === $request->user()->user_id) {
            return back()->with('error', 'Anda tidak bisa menangguhkan akun sendiri.');
        }

        // Admin tidak bisa suspend admin lain
        if ($user->role === 'admin') {
            return back()->with('error', 'Tidak bisa menangguhkan akun admin.');
        }

        $user->update(['is_suspended' => true]);

        return back()->with('status', "✅ Akun <strong>{$user->name}</strong> berhasil ditangguhkan. User tidak dapat login hingga akun diaktifkan kembali.");
    }

    public function unsuspend(Request $request, User $user)
    {
        if ($request->user()->role !== 'admin') {
            abort(403, 'This action is unauthorized.');
        }

        $user->update(['is_suspended' => false]);

        return back()->with('status', "✅ Akun <strong>{$user->name}</strong> berhasil diaktifkan kembali. User sekarang dapat login.");
    }

    /**
     * Menampilkan form untuk membuat admin baru
     */
    public function create(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            abort(403, 'This action is unauthorized.');
        }

        return view('admin.users.create');
    }

    /**
     * Menyimpan admin baru
     */
    public function store(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            abort(403, 'This action is unauthorized.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        // Generate recovery code (8 karakter alphanumeric)
        $recoveryCode = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'admin',
            'recovery_code' => $recoveryCode,
            'balance' => 0,
            'avg_rating' => 0,
            'is_suspended' => false,
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', "✅ Admin baru <strong>{$data['name']}</strong> berhasil dibuat.");
    }
}
