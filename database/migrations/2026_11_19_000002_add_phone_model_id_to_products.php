<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A spare part (product) can be linked to the phone model it fits.
 * Nullable so generic items (tools, equipment) don't need a model.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('phone_model_id')
                  ->nullable()
                  ->after('category_id')
                  ->constrained('phone_models')
                  ->nullOnDelete();
            $table->unsignedInteger('stock')->nullable()->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('phone_model_id');
            $table->dropColumn('stock');
        });
    }
};
