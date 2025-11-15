<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(User $user = null)
    {
        // Jika tidak ada user_id, tampilkan profile sendiri
        $profileUser = $user ?? auth()->user();
        
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
            'user' => auth()->user(),
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
            // Delete old photo if exists
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            // Store new photo
            $data['photo'] = $request->file('photo')->store('profiles', 'public');
        } else {
            // Don't update photo if not provided
            unset($data['photo']);
        }

        $user->update($data);

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
}
