<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // All product fields snapshotted — order_items is self-sufficient for display
    // even if the product is later soft-deleted. product_id SET NULL on delete.
    public function up(): void {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            // Snapshots
            $table->string('name_ar', 500);
            $table->string('name_en', 500);
            $table->decimal('price', 10, 2);
            $table->string('currency', 10)->default('JOD');
            $table->string('image_url', 500);
            $table->string('selected_size', 20);
            $table->string('selected_color', 100);
            $table->unsignedTinyInteger('quantity')->default(1);
            $table->timestamps();
            $table->index('order_id');
            $table->index('product_id');
        });
    }
    public function down(): void { Schema::dropIfExists('order_items'); }
};
