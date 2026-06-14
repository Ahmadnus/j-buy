<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_rates', function (Blueprint $t) {
            $t->id();

            // Bilingual city name. city_ar is canonical — it matches what the
            // mobile app sends in customer_city when placing an order and is
            // what we look up against.
            $t->string('city_ar', 100)->index();
            $t->string('city_en', 100)->nullable();

            // Price in JOD with 3 decimal places to match the rest of the
            // codebase's money handling (decimal:3 -- never use float).
            $t->decimal('price', 8, 3)->unsigned();

            // Soft toggle so admins can disable a city without deleting it
            // (deleting would orphan historical orders' display).
            $t->boolean('is_active')->default(true);

            // Lets the admin reorder rows in the table without touching ids.
            $t->unsignedSmallInteger('sort_order')->default(0)->index();

            $t->timestamps();

            // city_ar is treated as a unique key — one rate per city.
            $t->unique('city_ar');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_rates');
    }
};