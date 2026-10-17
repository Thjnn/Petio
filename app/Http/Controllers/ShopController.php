<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ShopController extends Controller
{
    /**
     * Trả về toàn bộ sản phẩm đang bán; tìm kiếm / lọc / sắp xếp do shop.js làm ngay trên trình duyệt.
     * ponytail: render hết một lần, đủ cho vài trăm sản phẩm; nhiều hơn thì chuyển lọc sang query + paginate().
     */
    public function index()
    {
        $products = Product::forCard()->orderBy('id')->get();
        $highest = $products->max(fn (Product $p) => $p->defaultVariant()->price) ?? 0;

        return view('shop.index', [
            'products' => $products,
            // Thanh trượt "Giá tối đa" làm tròn lên bội số 100.000đ
            'maxPrice' => (int) ceil($highest / 100000) * 100000,
        ]);
    }

    public function show(Product $product)
    {
        abort_unless($product->is_active && $product->variants()->exists(), 404);

        $product->load(['variants', 'tags', 'brand', 'category.parent']);
        $root = $product->category->root();

        $related = Product::forCard()
            ->whereKeyNot($product->id)
            ->whereHas('category', fn ($q) => $q->whereKey($root->id)->orWhere('parent_id', $root->id))
            ->take(4)
            ->get();

        return view('shop.show', compact('product', 'related'));
    }
}
