<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'title_ar'         => 'أحدث صيحات',
                'title_en'         => 'Latest Trends',

                'subtitle_ar'      => 'الموضة',
                'subtitle_en'      => 'Fashion',

                'cta_text_ar'      => 'تسوق الآن',
                'cta_text_en'      => 'Shop Now',

                'image_url'        => 'https://images.unsplash.com/photo-1558769132-cb1aea458c5e?w=800',
                'background_color' => '#F5F0E8',
                'link_type'        => 'category',
                'link_value'       => 'womens',
                'sort_order'       => 1,
            ],

            [
                'title_ar'         => 'تخفيضات',
                'title_en'         => 'Exclusive Sale',

                'subtitle_ar'      => 'حصرية',
                'subtitle_en'      => 'Exclusive',

                'cta_text_ar'      => 'اكتشف الآن',
                'cta_text_en'      => 'Discover Now',

                'image_url'        => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?w=800',
                'background_color' => '#F5EDE0',
                'link_type'        => 'category',
                'link_value'       => 'accessories',
                'sort_order'       => 2,
            ],

            [
                'title_ar'         => 'كولكشن',
                'title_en'         => 'Winter Collection',

                'subtitle_ar'      => 'الشتاء',
                'subtitle_en'      => 'Winter',

                'cta_text_ar'      => 'تسوق الآن',
                'cta_text_en'      => 'Shop Now',

                'image_url'        => 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=800',
                'background_color' => '#EEF0F5',
                'link_type'        => 'category',
                'link_value'       => 'mens',
                'sort_order'       => 3,
            ],
        ];

        foreach ($banners as $banner) {
            Banner::updateOrCreate(
                ['sort_order' => $banner['sort_order']],
                $banner
            );
        }
    }
}