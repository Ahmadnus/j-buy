<?php

namespace Database\Seeders;

use App\Models\ShippingRate;
use Illuminate\Database\Seeder;

class ShippingRateSeeder extends Seeder
{
    public function run(): void
    {
        // Initial rates — admins can edit any of these from the dashboard.
        // Amman is the base rate; everywhere else is more expensive.
        $rates = [
            ['city_ar' => 'عمّان',     'city_en' => 'Amman',   'price' => 2.000, 'sort_order' => 1],
            ['city_ar' => 'إربد',      'city_en' => 'Irbid',   'price' => 4.000, 'sort_order' => 2],
            ['city_ar' => 'الزرقاء',   'city_en' => 'Zarqa',   'price' => 3.000, 'sort_order' => 3],
            ['city_ar' => 'المفرق',    'city_en' => 'Mafraq',  'price' => 5.000, 'sort_order' => 4],
            ['city_ar' => 'السلط',     'city_en' => 'Salt',    'price' => 3.000, 'sort_order' => 5],
            ['city_ar' => 'مادبا',     'city_en' => 'Madaba',  'price' => 3.000, 'sort_order' => 6],
            ['city_ar' => 'الكرك',     'city_en' => 'Karak',   'price' => 5.000, 'sort_order' => 7],
            ['city_ar' => 'الطفيلة',   'city_en' => 'Tafila',  'price' => 6.000, 'sort_order' => 8],
            ['city_ar' => 'معان',      'city_en' => 'Ma\'an',  'price' => 6.000, 'sort_order' => 9],
            ['city_ar' => 'العقبة',    'city_en' => 'Aqaba',   'price' => 7.000, 'sort_order' => 10],
            ['city_ar' => 'جرش',       'city_en' => 'Jerash',  'price' => 4.000, 'sort_order' => 11],
            ['city_ar' => 'عجلون',     'city_en' => 'Ajloun',  'price' => 4.000, 'sort_order' => 12],
        ];

        foreach ($rates as $r) {
            ShippingRate::updateOrCreate(
                ['city_ar' => $r['city_ar']],
                ['city_en' => $r['city_en'], 'price' => $r['price'],
                 'is_active' => true, 'sort_order' => $r['sort_order']]
            );
        }
    }
}