<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PetioExtraProductsSeeder extends Seeder
{
    public function run(): void
    {
        $items = json_decode(file_get_contents(__DIR__.'/data/petio-extra-products.json'), true, 512, JSON_THROW_ON_ERROR);
        foreach ($items as $item) {
            if (! is_file(public_path($item['thumbnail_url']))) {
                throw new RuntimeException('Thiếu ảnh: '.$item['thumbnail_url'].'. Hãy chép thư mục public trước khi chạy.');
            }
        }

        $added = DB::transaction(function () use ($items) {
            $count = 0;
            $parents = ['do-choi' => 'Đồ chơi', 'phu-kien' => 'Phụ kiện', 've-sinh' => 'Vệ sinh'];
            foreach ($items as $item) {
                // Reruns preserve existing products, including edited price and stock.
                if (DB::table('products')->where('slug', $item['slug'])->exists()) {
                    continue;
                }
                if (DB::table('product_variants')->where('sku', $item['code'])->exists()) {
                    throw new RuntimeException('SKU đã được sử dụng: '.$item['code']);
                }
                $now = now();
                $parentId = DB::table('categories')->where('slug', $item['parent_slug'])->value('id');
                if (! $parentId) {
                    $parentId = DB::table('categories')->insertGetId([
                        'name' => $parents[$item['parent_slug']], 'slug' => $item['parent_slug'],
                        'is_active' => true, 'created_at' => $now, 'updated_at' => $now,
                    ]);
                }
                $categoryId = DB::table('categories')->where('slug', $item['category_slug'])->value('id');
                if (! $categoryId) {
                    $categoryId = DB::table('categories')->insertGetId([
                        'parent_id' => $parentId, 'name' => $item['category_name'], 'slug' => $item['category_slug'],
                        'is_active' => true, 'created_at' => $now, 'updated_at' => $now,
                    ]);
                }
                $productId = DB::table('products')->insertGetId([
                    'category_id' => $categoryId, 'name' => $item['name'], 'slug' => $item['slug'],
                    'pet_type' => $item['pet_type'], 'short_description' => $item['short_description'],
                    'description' => '<h2>'.e($item['name']).'</h2><p>'.e($item['short_description']).'</p>',
                    'thumbnail_url' => $item['thumbnail_url'], 'is_active' => true,
                    'is_featured' => false, 'rating_avg' => 0, 'rating_count' => 0, 'sold_count' => 0,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
                DB::table('product_variants')->insert([
                    'product_id' => $productId, 'sku' => $item['code'], 'name' => 'Mặc định',
                    'price' => $item['price'], 'old_price' => null, 'stock_qty' => $item['stock_qty'],
                    'is_default' => true, 'is_active' => true, 'sort_order' => 0,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
                $count++;
            }
            return $count;
        });
        $this->command?->info("Đã thêm {$added} sản phẩm. Sản phẩm đã tồn tại được giữ nguyên.");
    }
}
