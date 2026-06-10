<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the users table.
     * Login is phone-based. Email is optional.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name_ar', 255);
            $table->string('username', 100)->unique();

            // Primary login identifier
            $table->string('phone', 30)->unique();

            // Optional now
            $table->string('email', 255)->nullable()->unique();

            $table->text('address')->nullable();
            $table->string('region', 100)->nullable();

            // Avatar fallback column; Spatie Media Library is the primary source
            $table->string('avatar_url', 500)->nullable();

            $table->enum('membership_tier', ['standard', 'silver', 'gold'])
                ->default('standard');

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