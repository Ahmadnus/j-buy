<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Category;
use App\Models\PhoneModel;
use App\Models\Product;
use Illuminate\Database\Seeder;

/**
 * Demo catalogue for the phone spare-parts shop:
 *   brand (category) → phone model → parts with prices
 * plus two pickup branches.
 *
 * Safe to re-run: everything is keyed with updateOrCreate.
 *   php artisan db:seed --class=PhonePartsSeeder
 */
class PhonePartsSeeder extends Seeder
{
    /** Part templates: [code suffix, name_ar, name_en, base price JOD] */
    private const PARTS = [
        ['SCR', 'شاشة كاملة أصلية',      'Original Full Screen',     1.00],
        ['BAT', 'بطارية',                'Battery',                  0.35],
        ['CHG', 'منفذ شحن (فلات شحن)',   'Charging Port Flex',       0.18],
        ['CAM', 'كاميرا خلفية',          'Rear Camera',              0.45],
        ['BCK', 'غطاء خلفي (زجاج)',      'Back Glass Cover',         0.22],
        ['SPK', 'سماعة خارجية',          'Loudspeaker',              0.12],
    ];

    private const CATALOGUE = [
        'apple' => [
            'name_ar' => 'آيفون', 'name_en' => 'Apple', 'icon' => 'phone_iphone',
            'models' => [
                ['آيفون 15 برو ماكس', 'iPhone 15 Pro Max', 2023, 180],
                ['آيفون 15 برو',      'iPhone 15 Pro',     2023, 160],
                ['آيفون 14 برو',      'iPhone 14 Pro',     2022, 130],
                ['آيفون 13',          'iPhone 13',         2021,  85],
                ['آيفون 12',          'iPhone 12',         2020,  65],
                ['آيفون 11',          'iPhone 11',         2019,  45],
            ],
        ],
        'samsung' => [
            'name_ar' => 'سامسونج', 'name_en' => 'Samsung', 'icon' => 'smartphone',
            'models' => [
                ['جالكسي S24 ألترا', 'Galaxy S24 Ultra', 2024, 170],
                ['جالكسي S23',       'Galaxy S23',       2023, 110],
                ['جالكسي A54',       'Galaxy A54',       2023,  55],
                ['جالكسي A34',       'Galaxy A34',       2023,  45],
            ],
        ],
        'xiaomi' => [
            'name_ar' => 'شاومي', 'name_en' => 'Xiaomi', 'icon' => 'smartphone',
            'models' => [
                ['ريدمي نوت 13 برو', 'Redmi Note 13 Pro', 2024, 40],
                ['ريدمي نوت 12',     'Redmi Note 12',     2023, 30],
                ['شاومي 13T',        'Xiaomi 13T',        2023, 60],
            ],
        ],
        'oppo' => [
            'name_ar' => 'أوبو', 'name_en' => 'Oppo', 'icon' => 'smartphone',
            'models' => [
                ['أوبو رينو 10', 'Oppo Reno 10', 2023, 45],
                ['أوبو A78',     'Oppo A78',     2023, 30],
            ],
        ],
        'huawei' => [
            'name_ar' => 'هواوي', 'name_en' => 'Huawei', 'icon' => 'smartphone',
            'models' => [
                ['هواوي نوفا 11', 'Huawei Nova 11', 2023, 50],
                ['هواوي Y9a',     'Huawei Y9a',     2020, 28],
            ],
        ],
    ];

    public function run(): void
    {
        $sort = 0;
        foreach (self::CATALOGUE as $slug => $brand) {
            $category = Category::updateOrCreate(
                ['slug' => $slug],
                [
                    'name_ar'    => $brand['name_ar'],
                    'name_en'    => $brand['name_en'],
                    'icon'       => $brand['icon'],
                    'sort_order' => $sort++,
                    'is_active'  => true,
                ]
            );

            foreach ($brand['models'] as $i => [$nameAr, $nameEn, $year, $screenPrice]) {
                $model = PhoneModel::updateOrCreate(
                    ['category_id' => $category->id, 'name_en' => $nameEn],
                    ['name_ar' => $nameAr, 'release_year' => $year, 'sort_order' => $i, 'is_active' => true]
                );

                $codeBase = strtoupper(preg_replace('/[^A-Za-z0-9]+/', '', $nameEn));
                foreach (self::PARTS as [$suffix, $partAr, $partEn, $factor]) {
                    Product::updateOrCreate(
                        ['product_code' => "{$codeBase}-{$suffix}"],
                        [
                            'category_id'    => $category->id,
                            'phone_model_id' => $model->id,
                            'name_ar'        => "{$partAr} {$nameAr}",
                            'name_en'        => "{$nameEn} {$partEn}",
                            'price'          => max(3, round($screenPrice * $factor, 0)),
                            'currency'       => 'JOD',
                            'image_url'      => '',
                            'material_ar'    => 'جودة أصلية - كفالة 3 أشهر',
                            'material_en'    => 'Original quality - 3 months warranty',
                            'stock'          => 10,
                            'is_active'      => true,
                        ]
                    );
                }
            }
        }

        // Generic categories for tools & equipment (no phone model)
        Category::updateOrCreate(['slug' => 'tools'], [
            'name_ar' => 'معدات صيانة', 'name_en' => 'Repair Tools', 'icon' => 'build',
            'sort_order' => $sort++, 'is_active' => true,
        ]);

        Branch::updateOrCreate(['name_ar' => 'الفرع الرئيسي - عمّان'], [
            'name_en'       => 'Main Branch - Amman',
            'city_ar'       => 'عمان',
            'address_ar'    => 'عمّان - شارع الجاردنز، مقابل مكة مول',
            'phone'         => '0790000000',
            'working_hours' => 'يومياً 10:00 ص - 10:00 م',
            'latitude'      => 31.9810000,
            'longitude'     => 35.8770000,
            'sort_order'    => 0,
            'is_active'     => true,
        ]);
        Branch::updateOrCreate(['name_ar' => 'فرع إربد'], [
            'name_en'       => 'Irbid Branch',
            'city_ar'       => 'إربد',
            'address_ar'    => 'إربد - شارع الجامعة',
            'phone'         => '0780000000',
            'working_hours' => 'السبت - الخميس 10:00 ص - 9:00 م',
            'latitude'      => 32.5370000,
            'longitude'     => 35.8550000,
            'sort_order'    => 1,
            'is_active'     => true,
        ]);
    }
}
