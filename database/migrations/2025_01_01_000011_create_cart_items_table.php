<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // UNIQUE(user_id, product_id, selected_size, selected_color) mirrors
    // Flutter CartController.addToCart() duplicate-detection logic exactly.
    //
    // Snapshot fields (name_ar, price, image_url) ensure cart display survives
    // product soft-delete — confirmed by CartItemModel pattern.
    //
    // product_id is NULLABLE so cart items survive product deletion.
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->foreignId('product_id')
                  ->nullable()
                  ->constrained('products')
                  ->nullOnDelete();

            // Snapshot fields — captured at add-to-cart time
            $table->string('name_ar', 500);
            $table->decimal('price', 10, 2);
            $table->string('currency', 10)->default('JOD');
            $table->string('image_url', 500);

            $table->string('selected_size', 20);
            $table->string('selected_color', 100);
            $table->unsignedTinyInteger('quantity')->default(1);
            $table->timestamps();

            $table->unique(
                ['user_id', 'product_id', 'selected_size', 'selected_color'],
                'uq_cart_variant'
            );
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
