<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('product_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            // label stored verbatim in order_items.selected_size
            $table->string('label', 20);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            $table->index(['product_id', 'sort_order'], 'idx_product_sizes_ordered');
            $table->index(['product_id', 'is_available'], 'idx_product_sizes_available');
        });
    }
    public function down(): void { Schema::dropIfExists('product_sizes'); }
};
