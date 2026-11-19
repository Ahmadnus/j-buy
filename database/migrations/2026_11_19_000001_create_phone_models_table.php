<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phone models (e.g. "iPhone 13 Pro", "Galaxy S23") grouped under a brand.
 * Brands are stored as categories, so a model belongs to a category and
 * spare parts (products) belong to a model.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phone_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                  ->constrained('categories')
                  ->cascadeOnDelete();
            $table->string('name_ar', 255);
            $table->string('name_en', 255);
            $table->string('image_url', 500)->nullable();
            $table->unsignedSmallInteger('release_year')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['category_id', 'is_active', 'sort_order'], 'idx_phone_models_cat');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phone_models');
    }
};
