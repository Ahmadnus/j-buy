<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Adds the `name_en` column to `product_colors` for bilingual color labels.
 *
 * Strategy:
 *   1. Add the column as nullable so the migration is safe on a production DB
 *      with existing rows.
 *   2. Backfill existing rows with a translation from the small known
 *      Arabic → English vocabulary used by the seed data. Anything unknown
 *      is left NULL — the API/Flutter will fall back to name_ar.
 *
 * No FK changes. No data deletion. No structural changes to other tables.
 */
return new class extends Migration {

    public function up(): void
    {
        Schema::table('product_colors', function (Blueprint $table) {
            // Nullable so the column is safe to add to a populated table.
            // We never make it NOT NULL in a follow-up migration because some
            // products may legitimately have only an Arabic name.
            $table->string('name_en', 100)->nullable()->after('name_ar');
        });

        // Backfill known Arabic color names so the English UI renders correctly
        // for existing products. Anything not in the map stays NULL and the
        // Flutter UI will fall back to the Arabic value.
        $arabicToEnglish = [
            'الأصفر' => 'Yellow',
            'أصفر'   => 'Yellow',
            'الأحمر' => 'Red',
            'أحمر'   => 'Red',
            'الأزرق' => 'Blue',
            'أزرق'   => 'Blue',
            'الأخضر' => 'Green',
            'أخضر'   => 'Green',
            'الأسود' => 'Black',
            'أسود'   => 'Black',
            'الأبيض' => 'White',
            'أبيض'   => 'White',
            'الزهري' => 'Pink',
            'زهري'   => 'Pink',
            'الوردي' => 'Pink',
            'وردي'   => 'Pink',
            'بني'    => 'Brown',
            'البني'  => 'Brown',
            'رمادي'  => 'Gray',
            'الرمادي'=> 'Gray',
            'بنفسجي' => 'Purple',
            'البنفسجي'=> 'Purple',
            'برتقالي'=> 'Orange',
            'البرتقالي'=> 'Orange',
            'ذهبي'   => 'Gold',
            'الذهبي' => 'Gold',
            'فضي'    => 'Silver',
            'الفضي'  => 'Silver',
            'بيج'    => 'Beige',
            'البيج'  => 'Beige',
        ];

        foreach ($arabicToEnglish as $ar => $en) {
            DB::table('product_colors')
                ->where('name_ar', $ar)
                ->whereNull('name_en')
                ->update(['name_en' => $en]);
        }
    }

    public function down(): void
    {
        Schema::table('product_colors', function (Blueprint $table) {
            $table->dropColumn('name_en');
        });
    }
};