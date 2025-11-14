<?php

use App\Http\Controllers\AdminSettingController;
use App\Http\Controllers\AdminTopupController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobAssignmentController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\TopupController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::resource('jobs', JobController::class)->except(['show']);
    Route::post('jobs/{job}/take', [JobAssignmentController::class, 'take'])->name('jobs.take');
    Route::post('jobs/{job}/complete', [JobAssignmentController::class, 'complete'])->name('jobs.complete');

    Route::resource('topups', TopupController::class)->only(['index', 'create', 'store']);

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('topups', [AdminTopupController::class, 'index'])->name('topups.index');
        Route::post('topups/{topup}/approve', [AdminTopupController::class, 'approve'])->name('topups.approve');
        Route::post('topups/{topup}/reject', [AdminTopupController::class, 'reject'])->name('topups.reject');
        
        Route::get('settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [AdminSettingController::class, 'update'])->name('settings.update');
    });
});
