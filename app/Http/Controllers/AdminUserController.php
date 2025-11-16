<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

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
}
