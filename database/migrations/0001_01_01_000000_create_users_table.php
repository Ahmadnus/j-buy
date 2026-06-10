<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // email_verified_at removed — no email verification flow in this app.
    // Email is used only for forgot-password / reset-password OTP delivery.
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar', 255);
            $table->string('username', 100)->unique();
            $table->string('email', 255)->unique();
            $table->string('phone', 30);
            $table->text('address')->nullable();
            // avatar_url kept as fallback; primary avatar via Spatie Media Library
            $table->string('avatar_url', 500)->nullable();
            // Flutter: 'ذهبية'=gold  'فضية'=silver  'عادية'=standard
            $table->enum('membership_tier', ['standard', 'silver', 'gold'])->default('standard');
            $table->boolean('notifications_enabled')->default(true);
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->index('phone');
            $table->index('membership_tier');
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
