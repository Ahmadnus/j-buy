<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                  ->constrained('categories')
                  ->restrictOnDelete();
            $table->string('name_ar', 500);
            $table->string('name_en', 500);
            $table->string('product_code', 100)->unique();
            $table->decimal('price', 10, 2);
            $table->string('currency', 10)->default('JOD');
            $table->string('image_url', 500);
            $table->string('material_ar', 500)->nullable();
            $table->string('badge', 100)->nullable();
            $table->string('size_range', 200)->nullable();
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->unsignedInteger('review_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category_id', 'is_active'], 'idx_products_cat_active');
            $table->index('is_active');
            $table->index('badge');
            $table->index('rating');
            $table->index('deleted_at');
        });

        // FULLTEXT index only on MySQL — not supported on SQLite (used for testing)
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE products ADD FULLTEXT INDEX ft_products_search (name_ar, name_en, material_ar)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
