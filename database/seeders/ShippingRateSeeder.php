<?php

namespace Database\Seeders;

use App\Models\ShippingRate;
use Illuminate\Database\Seeder;

/**
 * Delivery areas across Jordan, grouped by governorate, with a starting
 * delivery price per area. Admins can edit prices, disable areas or add new
 * ones from the dashboard ("Shipping").
 *
 * Re-running is safe: areas are matched on their Arabic name and updated.
 * Each governorate's first entry keeps the original governorate name (e.g.
 * "عمّان") so existing users/orders that saved it stay valid.
 */
class ShippingRateSeeder extends Seeder
{
    public function run(): void
    {
        // governorate => [ar, en, default price for the governorate, areas[]]
        // area: [ar, en, price override or null]
        $governorates = [
            ['عمّان', 'Amman', 2.000, [
                ['عمّان', 'Amman', null],
                ['صويلح', 'Sweileh', null],
                ['الجبيهة', 'Jubeiha', null],
                ['خلدا', 'Khalda', null],
                ['تلاع العلي', "Tla' Al-Ali", null],
                ['عبدون', 'Abdoun', null],
                ['طبربور', 'Tabarbour', null],
                ['ماركا', 'Marka', null],
                ['القويسمة', 'Quwaysimah', null],
                ['أبو علندا', 'Abu Alanda', null],
                ['مرج الحمام', 'Marj Al-Hamam', null],
                ['وادي السير', 'Wadi Al-Seer', null],
                ['شفا بدران', 'Shafa Badran', null],
                ['أبو نصير', 'Abu Nseir', null],
                ['البيادر', 'Al-Bayader', null],
                ['ناعور', "Na'ur", 2.500],
                ['سحاب', 'Sahab', 2.500],
                ['الجيزة', 'Al-Jiza', 3.000],
                ['الموقر', 'Al-Muwaqqar', 3.000],
            ]],
            ['الزرقاء', 'Zarqa', 3.000, [
                ['الزرقاء', 'Zarqa', null],
                ['الزرقاء الجديدة', 'New Zarqa', null],
                ['الرصيفة', 'Russeifa', 2.500],
                ['الهاشمية', 'Hashemiya', null],
                ['الضليل', 'Dhlail', 3.500],
                ['الأزرق', 'Azraq', 5.000],
            ]],
            ['البلقاء', 'Balqa', 3.000, [
                ['السلط', 'Salt', null],
                ['الفحيص', 'Fuheis', null],
                ['ماحص', 'Mahis', null],
                ['عين الباشا', 'Ain Al-Basha', null],
                ['دير علا', 'Deir Alla', 4.000],
                ['الشونة الجنوبية', 'South Shuna', 4.000],
                ['البحر الميت', 'Dead Sea', 5.000],
            ]],
            ['مادبا', 'Madaba', 3.000, [
                ['مادبا', 'Madaba', null],
                ['ماعين', "Ma'in", 3.500],
                ['ذيبان', 'Dhiban', 4.000],
            ]],
            ['إربد', 'Irbid', 4.000, [
                ['إربد', 'Irbid', null],
                ['الرمثا', 'Ramtha', null],
                ['الحصن', 'Al-Husn', null],
                ['الطيبة', 'Al-Taybeh', null],
                ['بني كنانة', 'Bani Kinana', null],
                ['الكورة', 'Al-Koura', null],
                ['الأغوار الشمالية', 'Northern Jordan Valley', 5.000],
            ]],
            ['جرش', 'Jerash', 4.000, [
                ['جرش', 'Jerash', null],
                ['برما', 'Burma', null],
                ['المصطبة', 'Al-Mastaba', null],
            ]],
            ['عجلون', 'Ajloun', 4.000, [
                ['عجلون', 'Ajloun', null],
                ['عنجرة', 'Anjara', null],
                ['كفرنجة', 'Kufranjah', null],
            ]],
            ['المفرق', 'Mafraq', 5.000, [
                ['المفرق', 'Mafraq', null],
                ['بلعما', "Bal'ama", null],
                ['الصفاوي', 'Safawi', 6.000],
                ['الرويشد', 'Ruwaished', 7.000],
            ]],
            ['الكرك', 'Karak', 5.000, [
                ['الكرك', 'Karak', null],
                ['مؤتة', "Mu'tah", null],
                ['المزار الجنوبي', 'Southern Mazar', null],
                ['القصر', 'Al-Qasr', null],
                ['غور الصافي', 'Ghor Al-Safi', 6.000],
            ]],
            ['الطفيلة', 'Tafila', 6.000, [
                ['الطفيلة', 'Tafila', null],
                ['بصيرا', 'Busayra', null],
            ]],
            ['معان', "Ma'an", 6.000, [
                ['معان', "Ma'an", null],
                ['وادي موسى (البتراء)', 'Wadi Musa (Petra)', null],
                ['الشوبك', 'Shobak', null],
            ]],
            ['العقبة', 'Aqaba', 7.000, [
                ['العقبة', 'Aqaba', null],
                ['القويرة', 'Al-Quwayrah', null],
                ['وادي رم', 'Wadi Rum', 8.000],
            ]],
        ];

        $order = 0;
        foreach ($governorates as [$govAr, $govEn, $govPrice, $areas]) {
            foreach ($areas as [$ar, $en, $price]) {
                $order++;
                $existing = ShippingRate::where('city_ar', $ar)->first();
                ShippingRate::updateOrCreate(
                    ['city_ar' => $ar],
                    [
                        'city_en'        => $en,
                        'governorate_ar' => $govAr,
                        'governorate_en' => $govEn,
                        // Keep a price an admin already changed; only set it
                        // for new areas.
                        'price'          => $existing?->price ?? ($price ?? $govPrice),
                        'is_active'      => $existing?->is_active ?? true,
                        'sort_order'     => $order,
                    ]
                );
            }
        }
    }
}
