<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Delivery areas are now finer than governorates (e.g. "الرصيفة" inside
 * "الزرقاء"). The governorate columns let the app group and search areas.
 * Nullable so areas added from the dashboard without one still work.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipping_rates', function (Blueprint $t) {
            $t->string('governorate_ar', 100)->nullable()->after('city_en');
            $t->string('governorate_en', 100)->nullable()->after('governorate_ar');
        });
    }

    public function down(): void
    {
        Schema::table('shipping_rates', function (Blueprint $t) {
            $t->dropColumn(['governorate_ar', 'governorate_en']);
        });
    }
};
