<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds an optional image column to categories so the dashboard can upload
 * a representative image for each category alongside the icon string.
 */
return new class extends Migration {

    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('image_url', 500)->nullable()->after('icon');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('image_url');
        });
    }
};