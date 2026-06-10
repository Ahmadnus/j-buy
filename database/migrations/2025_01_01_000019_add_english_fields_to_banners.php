<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds the English columns to `banners` so the home hero carousel can show
 * localised content according to the active app locale.
 *
 * All columns are nullable so the migration is safe on a production DB with
 * existing rows. The Flutter UI falls back to the Arabic values whenever
 * an English value is missing.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->string('title_en',    255)->nullable()->after('title_ar');
            $table->string('subtitle_en', 255)->nullable()->after('subtitle_ar');
            $table->string('cta_text_en', 100)->nullable()->after('cta_text_ar');
        });
    }

    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn(['title_en', 'subtitle_en', 'cta_text_en']);
        });
    }
};