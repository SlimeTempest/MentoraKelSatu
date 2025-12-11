<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Controller untuk manajemen user oleh admin
 * 
 * Dibuat oleh: Reffael (Role: Admin)
 * Fitur:
 * - CRUD untuk manajemen user (suspend/unsuspend user)
 * - Membuat admin baru
 * 
 * BEST PRACTICE:
 * - Validasi authorization di setiap method
 * - Mencegah admin suspend diri sendiri atau admin lain
 * - Menggunakan query builder untuk filter dan search yang fleksibel
 * - Password di-hash menggunakan Hash facade
 */
class AdminUserController extends Controller
{
    /**
     * Menampilkan daftar semua user dengan fitur search dan filter
     * 
     * BEST PRACTICE:
     * - Menggunakan query builder untuk filter yang fleksibel
     * - Pagination untuk performa yang lebih baik pada data besar
     * - Mempertahankan filter state di view untuk UX yang lebih baik
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // BEST PRACTICE: Validasi authorization di awal
        if ($request->user()->role !== 'admin') {
            abort(403, 'This action is unauthorized.');
        }

        // BEST PRACTICE: Menggunakan query builder untuk filter yang fleksibel
        $query = User::query();

        // Fitur search: mencari user berdasarkan nama
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%");
        }

        // Filter berdasarkan role (mahasiswa, dosen, admin)
        if ($request->filled('role') && $request->get('role') !== 'all') {
            $query->where('role', $request->get('role'));
        }

        // Filter berdasarkan status suspension (suspended/active)
        if ($request->filled('suspended') && $request->get('suspended') !== 'all') {
            $query->where('is_suspended', $request->get('suspended') === '1');
        }

        // BEST PRACTICE: Pagination untuk performa yang lebih baik pada data besar
        $users = $query->orderBy('created_at', 'desc')->paginate(10);

        // BEST PRACTICE: Mempertahankan filter state di view untuk UX yang lebih baik
        return view('admin.users.index', [
            'users' => $users,
            'search' => $request->get('search', ''),
            'roleFilter' => $request->get('role', 'all'),
            'suspendedFilter' => $request->get('suspended', 'all'),
        ]);
    }

    /**
     * Menangguhkan (suspend) akun user
     * 
     * BEST PRACTICE:
     * - Mencegah admin suspend diri sendiri (security measure)
     * - Mencegah admin suspend admin lain (hierarchical protection)
     * - Memberikan feedback yang jelas kepada admin
     * 
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function suspend(Request $request, User $user)
    {
        // BEST PRACTICE: Validasi authorization di awal
        if ($request->user()->role !== 'admin') {
            abort(403, 'This action is unauthorized.');
        }

        // BEST PRACTICE: Mencegah admin suspend diri sendiri (security measure)
        // Ini penting untuk mencegah admin secara tidak sengaja mengunci diri sendiri
        if ($user->user_id === $request->user()->user_id) {
            return back()->with('error', 'Anda tidak bisa menangguhkan akun sendiri.');
        }

        // BEST PRACTICE: Mencegah admin suspend admin lain (hierarchical protection)
        // Hanya super admin atau sistem yang bisa suspend admin lain
        if ($user->role === 'admin') {
            return back()->with('error', 'Tidak bisa menangguhkan akun admin.');
        }

        // Update status suspension user
        $user->update(['is_suspended' => true]);

        return back()->with('status', "✅ Akun <strong>{$user->name}</strong> berhasil ditangguhkan. User tidak dapat login hingga akun diaktifkan kembali.");
    }

    /**
     * Mengaktifkan kembali (unsuspend) akun user yang ditangguhkan
     * 
     * BEST PRACTICE:
     * - Validasi authorization di awal
     * - Memberikan feedback yang jelas kepada admin
     * 
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unsuspend(Request $request, User $user)
    {
        // BEST PRACTICE: Validasi authorization di awal
        if ($request->user()->role !== 'admin') {
            abort(403, 'This action is unauthorized.');
        }

        // Update status suspension user menjadi aktif
        $user->update(['is_suspended' => false]);

        return back()->with('status', "✅ Akun <strong>{$user->name}</strong> berhasil diaktifkan kembali. User sekarang dapat login.");
    }

    /**
     * Menampilkan form untuk membuat admin baru
     * 
     * BEST PRACTICE:
     * - Validasi authorization di awal
     * - Separasi concern: create() untuk form, store() untuk proses
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        // BEST PRACTICE: Validasi authorization di awal
        if ($request->user()->role !== 'admin') {
            abort(403, 'This action is unauthorized.');
        }

        return view('admin.users.create');
    }

    /**
     * Menyimpan admin baru ke database
     * 
     * BEST PRACTICE:
     * - Validasi input menggunakan Laravel validation
     * - Password di-hash menggunakan Hash facade untuk keamanan
     * - Generate recovery code untuk keamanan tambahan
     * - Set default values untuk field yang diperlukan
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // BEST PRACTICE: Validasi authorization di awal
        if ($request->user()->role !== 'admin') {
            abort(403, 'This action is unauthorized.');
        }

        // BEST PRACTICE: Validasi input menggunakan Laravel validation
        // 'confirmed' rule memastikan password dan password_confirmation cocok
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        // BEST PRACTICE: Generate recovery code untuk keamanan tambahan
        // Recovery code digunakan jika admin lupa password
        $recoveryCode = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));

        // BEST PRACTICE: Password di-hash menggunakan Hash facade untuk keamanan
        // Hash::make() menggunakan bcrypt yang aman untuk menyimpan password
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
