<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use App\Support\Cart;
use Illuminate\Http\Request;

/** API JSON cho cart.js — khách chưa đăng nhập vẫn dùng được. */
class CartController extends Controller
{
    public function index()
    {
        return Cart::toJson();
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'variant_id' => 'required|integer',
            'qty' => 'required|integer|min:1|max:'.Cart::MAX_QTY,
        ]);

        $variant = ProductVariant::where('is_active', true)
            ->whereHas('product', fn ($q) => $q->where('is_active', true))
            ->findOrFail($data['variant_id']);

        $qty = (Cart::quantities()[$variant->id] ?? 0) + $data['qty'];

        if ($qty > $variant->stock_qty) {
            return response()->json(['message' => "Chỉ còn {$variant->stock_qty} sản phẩm trong kho."], 422);
        }

        Cart::set($variant->id, $qty);

        return Cart::toJson();
    }

    /** Đặt số lượng mới cho một dòng; 0 = xóa dòng. */
    public function update(Request $request)
    {
        $data = $request->validate([
            'variant_id' => 'required|integer',
            'qty' => 'required|integer|min:0|max:'.Cart::MAX_QTY,
        ]);

        $stock = ProductVariant::whereKey($data['variant_id'])->value('stock_qty') ?? 0;
        Cart::set($data['variant_id'], min($data['qty'], $stock));

        return Cart::toJson();
    }

    public function clear()
    {
        Cart::clear();

        return Cart::toJson();
    }
}
