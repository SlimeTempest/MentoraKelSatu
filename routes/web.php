<?php

use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\AdminSettingController;
use App\Http\Controllers\AdminTopupController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\JobAssignmentController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RedeemCodeController;
use App\Http\Controllers\ReportController;
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

    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

    // Google OAuth Routes
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('google.callback');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::resource('jobs', JobController::class)->except(['show']);
    Route::post('jobs/{job}/take', [JobAssignmentController::class, 'take'])->name('jobs.take');
    Route::post('jobs/{job}/complete', [JobAssignmentController::class, 'complete'])->name('jobs.complete');

    Route::get('jobs/{job}/feedback', [FeedbackController::class, 'create'])->name('jobs.feedback.create');
    Route::post('jobs/{job}/feedback', [FeedbackController::class, 'store'])->name('jobs.feedback.store');

    Route::get('reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('reports', [ReportController::class, 'store'])->name('reports.store');

    Route::resource('topups', TopupController::class)->only(['index', 'create', 'store']);
    Route::get('topups/{topup}/proof', [TopupController::class, 'showProof'])->name('topups.proof');

    // Redeem Codes - Dosen can create/list, Mahasiswa can claim
    Route::prefix('redeem-codes')->name('redeem-codes.')->group(function () {
        Route::get('/', [RedeemCodeController::class, 'index'])->name('index');
        Route::get('/create', [RedeemCodeController::class, 'create'])->name('create');
        Route::post('/', [RedeemCodeController::class, 'store'])->name('store');
        Route::get('/claim', [RedeemCodeController::class, 'claim'])->name('claim');
        Route::post('/claim', [RedeemCodeController::class, 'claimStore'])->name('claim.store');
    });

    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::post('profile/recovery-code', [ProfileController::class, 'generateRecoveryCode'])->name('profile.recovery-code.generate');
    Route::get('users/{user}/profile', [ProfileController::class, 'show'])->name('users.profile.show');

    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::get('topups', [AdminTopupController::class, 'index'])->name('topups.index');
        Route::post('topups/{topup}/approve', [AdminTopupController::class, 'approve'])->name('topups.approve');
        Route::post('topups/{topup}/reject', [AdminTopupController::class, 'reject'])->name('topups.reject');

        Route::get('settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [AdminSettingController::class, 'update'])->name('settings.update');

        Route::get('reports', [AdminReportController::class, 'index'])->name('reports.index');
        Route::get('reports/{report}', [AdminReportController::class, 'show'])->name('reports.show');
        Route::put('reports/{report}/status', [AdminReportController::class, 'updateStatus'])->name('reports.update-status');

        Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('users/create', [AdminUserController::class, 'create'])->name('users.create');
        Route::post('users', [AdminUserController::class, 'store'])->name('users.store');
        Route::post('users/{user}/suspend', [AdminUserController::class, 'suspend'])->name('users.suspend');
        Route::post('users/{user}/unsuspend', [AdminUserController::class, 'unsuspend'])->name('users.unsuspend');

        Route::resource('categories', AdminCategoryController::class)->except(['show']);
    });
});
