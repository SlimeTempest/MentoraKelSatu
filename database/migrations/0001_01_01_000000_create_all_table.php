<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // =========================
        // USERS TABLE
        // =========================
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['mahasiswa', 'dosen', 'admin']);
            $table->float('avg_rating')->default(0);
            $table->boolean('is_suspended')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });

        // =========================
        // CATEGORIES TABLE
        // =========================
        Schema::create('categories', function (Blueprint $table) {
            $table->id('category_id');
            $table->string('name');
            $table->timestamps();
        });

        // =========================
        // JOBS TABLE
        // =========================
        Schema::create('jobs', function (Blueprint $table) {
            $table->id('job_id');
            $table->string('title');
            $table->text('description');
            $table->foreignId('created_by')->constrained('users', 'user_id')->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users', 'user_id')->onDelete('set null');
            $table->date('deadline')->nullable();
            $table->enum('status', ['belum_diambil', 'on_progress', 'selesai', 'kadaluarsa'])->default('belum_diambil');
            $table->decimal('price', 12, 2);
            $table->timestamps();
        });

        // =========================
        // JOB_CATEGORIES TABLE
        // =========================
        Schema::create('job_categories', function (Blueprint $table) {
            $table->id('job_category_id');
            $table->foreignId('job_id')->constrained('jobs', 'job_id')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories', 'category_id')->onDelete('cascade');
            $table->timestamps();
        });

        // =========================
        // FEEDBACKS TABLE
        // =========================
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id('feedback_id');
            $table->foreignId('job_id')->constrained('jobs', 'job_id')->onDelete('cascade');
            $table->foreignId('given_by')->constrained('users', 'user_id')->onDelete('cascade');
            $table->foreignId('given_to')->constrained('users', 'user_id')->onDelete('cascade');
            $table->tinyInteger('rating')->checkBetween(1, 5);
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        // =========================
        // PAYMENTS TABLE
        // =========================
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->foreignId('job_id')->constrained('jobs', 'job_id')->onDelete('cascade');
            $table->foreignId('payer_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->foreignId('receiver_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->timestamps();
        });

        // =========================
        // TOPUPS TABLE
        // =========================
        Schema::create('topups', function (Blueprint $table) {
            $table->id('topup_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->string('bukti_pembayaran')->nullable();
            $table->string('rekening_tujuan')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });

        // =========================
        // TOPUP_HISTORIES TABLE
        // =========================
        Schema::create('topup_histories', function (Blueprint $table) {
            $table->id('history_id');
            $table->foreignId('topup_id')->constrained('topups', 'topup_id')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users', 'user_id')->onDelete('set null');
            $table->enum('status_before', ['pending', 'approved', 'rejected'])->nullable();
            $table->enum('status_after', ['pending', 'approved', 'rejected'])->nullable();
            $table->timestamps();
        });

        // =========================
        // REPORTS TABLE
        // =========================
        Schema::create('reports', function (Blueprint $table) {
            $table->id('report_id');
            $table->foreignId('reported_by')->constrained('users', 'user_id')->onDelete('cascade');
            $table->foreignId('reported_user')->nullable()->constrained('users', 'user_id')->onDelete('set null');
            $table->text('description');
            $table->enum('status', ['pending', 'on_review', 'done'])->default('pending');
            $table->timestamps();
        });

        // =========================
        // JOB_HISTORIES TABLE
        // =========================
        Schema::create('job_histories', function (Blueprint $table) {
            $table->id('history_id');
            $table->foreignId('job_id')->constrained('jobs', 'job_id')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->foreignId('payment_id')->constrained('payments', 'payment_id')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_histories');
        Schema::dropIfExists('reports');
        Schema::dropIfExists('topup_histories');
        Schema::dropIfExists('topups');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('feedbacks');
        Schema::dropIfExists('job_categories');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('users');
    }
};
