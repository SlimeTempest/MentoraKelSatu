<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(User $user = null)
    {
        // Jika tidak ada user_id, tampilkan profile sendiri
        // Gunakan fresh() untuk memastikan data terbaru dari database
        $profileUser = $user ?? auth()->user()->fresh();
        
        $stats = [];
        
        // Jika admin, tampilkan statistik laporan dan topup yang sudah ditangani
        if ($profileUser->role === 'admin') {
            $stats = [
                'handled_reports' => DB::table('reports')
                    ->whereIn('status', ['on_review', 'done'])
                    ->count(),
                'handled_topups' => DB::table('topup_histories')
                    ->where('approved_by', $profileUser->user_id)
                    ->count(),
            ];
        } else {
            // Statistik job untuk non-admin
            $stats = [
                'jobs_created' => $profileUser->jobsCreated()->count(),
                'jobs_completed' => $profileUser->jobsAssigned()->where('status', Job::STATUS_DONE)->count(),
                'jobs_in_progress' => $profileUser->jobsAssigned()->where('status', Job::STATUS_PROGRESS)->count(),
                'total_earned' => $profileUser->jobsAssigned()
                    ->where('status', Job::STATUS_DONE)
                    ->sum('price'),
            ];
        }

        return view('profile.show', [
            'user' => $profileUser,
            'stats' => $stats,
            'isOwnProfile' => $user === null || $user->user_id === auth()->id(),
        ]);
    }

    public function edit()
    {
        return view('profile.edit', [
            'user' => auth()->user()->fresh(), // Refresh dari database
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->user_id . ',user_id'],
            'phone' => ['nullable', 'string', 'max:20'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists (hanya jika foto lokal, bukan URL Google)
            if ($user->photo) {
                $isLocalPhoto = !filter_var($user->photo, FILTER_VALIDATE_URL);
                if ($isLocalPhoto && Storage::disk('public')->exists($user->photo)) {
                    Storage::disk('public')->delete($user->photo);
                }
            }

            // Store new photo
            $data['photo'] = $request->file('photo')->store('profiles', 'public');
            
            \Log::info('Profile photo uploaded', [
                'user_id' => $user->user_id,
                'photo_path' => $data['photo'],
                'storage_exists' => Storage::disk('public')->exists($data['photo']),
            ]);
        } else {
            // Don't update photo if not provided
            unset($data['photo']);
        }

        // Update data profile
        $user->update($data);
        
        // Log untuk debugging
        \Log::info('Profile updated', [
            'user_id' => $user->user_id,
            'updated_fields' => array_keys($data),
            'name' => $data['name'] ?? 'not updated',
            'email' => $data['email'] ?? 'not updated',
            'phone' => $data['phone'] ?? 'not updated',
            'has_photo' => isset($data['photo']),
        ]);
        
        // Refresh user dari database untuk memastikan data terbaru
        $user = $user->fresh();
        
        // Log untuk verifikasi data setelah refresh
        \Log::info('Profile after refresh', [
            'user_id' => $user->user_id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'photo' => $user->photo ? 'exists' : 'null',
            'photo_path' => $user->photo,
            'photo_url' => $user->photo ? $user->photo_url : 'null',
            'storage_exists' => $user->photo && !filter_var($user->photo, FILTER_VALIDATE_URL) ? Storage::disk('public')->exists($user->photo) : 'N/A',
        ]);
        
        // Refresh session dengan data user terbaru
        Auth::login($user, true);

        return redirect()->route('profile.show')->with('status', 'Profile berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        if (!Hash::check($data['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => 'Password saat ini tidak sesuai.',
            ]);
        }

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        return redirect()->route('profile.show')->with('status', 'Password berhasil diubah.');
    }

    public function generateRecoveryCode(Request $request)
    {
        $user = $request->user();
        
        // Generate new recovery code (8 characters alphanumeric)
        $recoveryCode = strtoupper(substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 8));
        
        $user->update([
            'recovery_code' => $recoveryCode,
        ]);

        return redirect()->route('profile.show')->with('status', 'Recovery code berhasil digenerate.');
    }
}
