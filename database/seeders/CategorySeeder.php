<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    // Slugs match Flutter CategoryModel.dummyCategories() exactly
    public function run(): void
    {
        $categories = [
            ['name_ar' => 'ملابس نسائية', 'name_en' => "Women's Fashion",  'slug' => 'womens',      'icon' => 'checkroom_outlined',      'sort_order' => 1],
            ['name_ar' => 'ملابس رجالية', 'name_en' => "Men's Fashion",    'slug' => 'mens',        'icon' => 'dry_cleaning_outlined',   'sort_order' => 2],
            ['name_ar' => 'أحذية',         'name_en' => 'Shoes',            'slug' => 'shoes',       'icon' => 'directions_run_outlined', 'sort_order' => 3],
            ['name_ar' => 'إكسسوارات',     'name_en' => 'Accessories',      'slug' => 'accessories', 'icon' => 'watch_outlined',          'sort_order' => 4],
            ['name_ar' => 'حقائب',          'name_en' => 'Bags',             'slug' => 'bags',        'icon' => 'shopping_bag_outlined',   'sort_order' => 5],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}
