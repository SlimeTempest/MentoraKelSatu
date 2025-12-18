<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up(): void
  {
    Schema::create('redeem_codes', function (Blueprint $table) {
      // Primary key with custom name to match the model
      $table->id('redeem_code_id');

      // Redeem code string (unique)
      $table->string('code', 64)->unique();

      // Creator (dosen) references users.user_id
      $table->unsignedBigInteger('created_by');
      $table->foreign('created_by')->references('user_id')->on('users')->onDelete('cascade');

      // Claimer (mahasiswa) may be null until claimed
      $table->unsignedBigInteger('claimed_by')->nullable();
      $table->foreign('claimed_by')->references('user_id')->on('users')->nullOnDelete();

      // Amount, boolean and timestamps related to claim/expiry
      $table->decimal('amount', 12, 2);
      $table->boolean('is_claimed')->default(false);
      $table->timestamp('claimed_at')->nullable();
      $table->timestamp('expires_at')->nullable();

      // Useful indexes
      $table->index(['is_claimed']);
      $table->index(['expires_at']);

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down(): void
  {
    Schema::table('redeem_codes', function (Blueprint $table) {
      // Drop foreign keys first
      $table->dropForeign(['created_by']);
      $table->dropForeign(['claimed_by']);
    });

    Schema::dropIfExists('redeem_codes');
  }
};
