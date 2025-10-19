<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\CategoryState;

class TelegramBuySeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $categories = [
                ['slug' => 'buy.provider', 'title_key' => 'telegram.buy.choose_provider'],
                ['slug' => 'buy.plan',     'title_key' => 'telegram.buy.choose_plan'],
                ['slug' => 'buy.location', 'title_key' => 'telegram.buy.choose_location'],
                ['slug' => 'buy.os',       'title_key' => 'telegram.buy.choose_os'],
                ['slug' => 'buy.review',   'title_key' => 'telegram.buy.review'],
            ];

            $catId = [];
            foreach ($categories as $c) {
                $cat = Category::updateOrCreate(
                    ['slug' => $c['slug']],
                    ['title_key' => $c['title_key']]
                );
                $catId[$c['slug']] = $cat->id;
            }


            $buttons = [
                'buy.provider' => [
                    ['title_key' => 'telegram.providers.alpha', 'code' => 'provider-alpha', 'price' => 0, 'layout' => 'beside', 'sort' => 10],
                    ['title_key' => 'telegram.providers.beta',  'code' => 'provider-beta',  'price' => 0, 'layout' => 'beside', 'sort' => 20],
                    ['title_key' => 'telegram.providers.gamma', 'code' => 'provider-gamma', 'price' => 0, 'layout' => 'below',  'sort' => 30],
                ],
                'buy.plan' => [
                    ['title_key' => 'telegram.plans.basic', 'code' => 'plan-basic', 'price' => 100000, 'layout' => 'beside', 'sort' => 10],
                    ['title_key' => 'telegram.plans.pro',   'code' => 'plan-pro',   'price' => 200000, 'layout' => 'beside', 'sort' => 20],
                    ['title_key' => 'telegram.plans.ultra', 'code' => 'plan-ultra', 'price' => 350000, 'layout' => 'below',  'sort' => 30],
                ],
                'buy.location' => [
                    ['title_key' => 'telegram.locations.de', 'code' => 'loc-de', 'price' => 0, 'layout' => 'beside', 'sort' => 10],
                    ['title_key' => 'telegram.locations.fr', 'code' => 'loc-fr', 'price' => 0, 'layout' => 'beside', 'sort' => 20],
                    ['title_key' => 'telegram.locations.us', 'code' => 'loc-us', 'price' => 0, 'layout' => 'below',  'sort' => 30],
                ],
                'buy.os' => [
                    ['title_key' => 'telegram.os.android', 'code' => 'os-android', 'price' => 0, 'layout' => 'beside', 'sort' => 10],
                    ['title_key' => 'telegram.os.ios',     'code' => 'os-ios',     'price' => 0, 'layout' => 'beside', 'sort' => 20],
                ],
                'buy.review' => [
                    ['title_key' => 'telegram.buttons.review_confirm', 'code' => 'review-confirm', 'price' => 0, 'layout' => 'beside', 'sort' => 10],
                ],
            ];

            foreach ($buttons as $slug => $rows) {
                $categoryId = $catId[$slug] ?? null;
                if (!$categoryId) {
                    continue;
                }
                foreach ($rows as $i => $b) {
                    $match = ['category_id' => $categoryId];
                    if (!empty($b['code'])) {
                        $match['code'] = $b['code'];
                    } else {
                        $match['title_key'] = $b['title_key'];
                    }

                    CategoryState::updateOrCreate(
                        $match,
                        [
                            'title_key' => $b['title_key'],
                            'price'     => (int) ($b['price'] ?? 0),
                            'sort'      => (int) ($b['sort'] ?? (($i + 1) * 10)),
                            'active'    => true,
                        ]
                    );
                }
            }
        });
    }
}
