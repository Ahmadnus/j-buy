<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // link_type / link_value added proactively — CTA button needs a navigation target
    // link_type values: 'product' (id), 'category' (slug), 'url' (href), 'none'
    public function up(): void
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title_ar', 255);
            $table->string('subtitle_ar', 255);
            $table->string('cta_text_ar', 100);
            $table->string('image_url', 500);
            $table->string('background_color', 7)->default('#FFFFFF');
            $table->enum('link_type', ['product', 'category', 'url', 'none'])->default('none');
            $table->string('link_value', 255)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();

            // Active banner query: WHERE is_active=1 AND starts_at<=now AND ends_at>=now
            $table->index(['is_active', 'starts_at', 'ends_at'], 'idx_banners_schedule');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
