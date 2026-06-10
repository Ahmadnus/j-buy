<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds English equivalents for the descriptive product fields that the
 * Explore card displays: `material_en` (the "Material: …" line) and
 * `badge_en` (the "الأعلى تقييماً", "جديد" pill).
 *
 * Both columns are nullable; the Flutter UI falls back to the Arabic value
 * when English is missing.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('material_en', 500)->nullable()->after('material_ar');
            $table->string('badge_en',    100)->nullable()->after('badge');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['material_en', 'badge_en']);
        });
    }
};