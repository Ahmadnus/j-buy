<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductSize;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all()->keyBy('slug');

        $products = [
            [
                'category_slug' => 'shoes',
                'name_ar'       => 'حذاء رجالي جلد مع قماش مميز',
                'name_en'       => 'Premium Leather Sneaker',
                'product_code'  => 'SH-001',
                'price'         => 4.99,
                'image_url'     => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400',
                'badge'         => 'الأعلى تقييماً',
                'size_range'    => '38 - 39 - 40 - 41 - 42 - 43',
                'rating'        => 4.80,
                'review_count'  => 210,
                'colors'        => [['name_ar'=>'أسود','name_en'=>'Black','hex_code'=>'#000000'],['name_ar'=>'أبيض','name_en'=>'White','hex_code'=>'#FFFFFF']],
                'sizes'         => ['38','39','40','41','42','43'],
            ],
            [
                'category_slug' => 'womens',
                'name_ar'       => 'طقم رياضي نسائي أنيق',
                'name_en'       => 'Elegant Women Sports Set',
                'product_code'  => 'WF-001',
                'price'         => 3.99,
                'image_url'     => 'https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=400',
                'material_ar'   => 'قطن رياضي مرن',
                'size_range'    => 'S - M - L - XL - XXL',
                'rating'        => 4.40,
                'review_count'  => 89,
                'colors'        => [['name_ar'=>'الأصفر','name_en'=>'Yellow','hex_code'=>'#D4A017'],['name_ar'=>'الزهري','name_en'=>'Pink','hex_code'=>'#E8A0BF'],['name_ar'=>'الأزرق','name_en'=>'Blue','hex_code'=>'#5B8DB8']],
                'sizes'         => ['S','M','L','XL','XXL'],
            ],
            [
                'category_slug' => 'bags',
                'name_ar'       => 'حقيبة يد نسائية جلدية فاخرة',
                'name_en'       => 'Luxury Women Leather Handbag',
                'product_code'  => 'BG-001',
                'price'         => 8.99,
                'image_url'     => 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=400',
                'badge'         => 'جديد',
                'material_ar'   => 'جلد طبيعي عالي الجودة',
                'rating'        => 4.60,
                'review_count'  => 54,
                'colors'        => [['name_ar'=>'بني','name_en'=>'Brown','hex_code'=>'#8B4513'],['name_ar'=>'أسود','name_en'=>'Black','hex_code'=>'#000000']],
                'sizes'         => [],
            ],
        ];

        foreach ($products as $data) {
            $category = $categories[$data['category_slug']];

            $product = Product::updateOrCreate(
                ['product_code' => $data['product_code']],
                [
                    'category_id'  => $category->id,
                    'name_ar'      => $data['name_ar'],
                    'name_en'      => $data['name_en'],
                    'price'        => $data['price'],
                    'image_url'    => $data['image_url'],
                    'material_ar'  => $data['material_ar'] ?? null,
                    'badge'        => $data['badge'] ?? null,
                    'size_range'   => $data['size_range'] ?? null,
                    'rating'       => $data['rating'],
                    'review_count' => $data['review_count'],
                    'is_active'    => true,
                ]
            );

            // Colors
            ProductColor::where('product_id', $product->id)->delete();
            foreach (($data['colors'] ?? []) as $i => $color) {
                ProductColor::create([
                    'product_id' => $product->id,
                    'name_ar'    => $color['name_ar'],
                    'name_en'    => $color['name_en'] ?? null,
                    'hex_code'   => $color['hex_code'],
                    'sort_order' => $i,
                ]);
            }

            // Sizes
            ProductSize::where('product_id', $product->id)->delete();
            foreach (($data['sizes'] ?? []) as $i => $label) {
                ProductSize::create([
                    'product_id' => $product->id,
                    'label'      => $label,
                    'sort_order' => $i,
                    'is_available' => true,
                ]);
            }
        }
    }
}