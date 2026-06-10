<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('product_colors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            // name_ar is stored verbatim in order_items.selected_color (snapshot)
            $table->string('name_ar', 100);
            // hex_code maps to Flutter ProductColorModel.swatch Color value
            $table->string('hex_code', 7)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['product_id', 'sort_order'], 'idx_product_colors_ordered');
        });
    }
    public function down(): void { Schema::dropIfExists('product_colors'); }
};
