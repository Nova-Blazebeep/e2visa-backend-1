<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            // pending: created at signup, awaiting payment confirmation from Mike's
            // merchant services. active: admin-confirmed, unlocks forum posting.
            // revoked: admin-disabled after being active. One-time fee — no expiry.
            $table->enum('status', ['pending', 'active', 'revoked'])->default('pending');
            $table->string('payment_reference')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->foreignId('activated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_badges');
    }
};
