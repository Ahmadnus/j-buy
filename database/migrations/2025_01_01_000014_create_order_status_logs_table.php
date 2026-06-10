<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Append-only — no updated_at. from_status NULL = initial placement.
    public function up(): void {
        Schema::create('order_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->enum('from_status', ['pending','confirmed','preparing','shipping','delivered','cancelled'])->nullable();
            $table->enum('to_status',   ['pending','confirmed','preparing','shipping','delivered','cancelled']);
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('note', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index('order_id');
            $table->index('to_status');
            $table->index('created_at');
        });
    }
    public function down(): void { Schema::dropIfExists('order_status_logs'); }
};
