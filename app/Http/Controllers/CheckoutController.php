<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Support\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $lines = Cart::lines();

        if ($lines->isEmpty()) {
            return redirect()->route('shop')->with('success', 'Giỏ hàng đang trống, hãy chọn vài món cho bé nhé!');
        }

        $subtotal = $lines->sum('line_total');

        return view('checkout.index', [
            'lines' => $lines,
            'subtotal' => $subtotal,
            'shippingFee' => Cart::shippingFee($subtotal),
            'user' => $request->user(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'receiver_name' => 'required|string|max:100',
            'receiver_phone' => ['required', 'regex:/^0[0-9]{9}$/'],
            'receiver_email' => 'nullable|email|max:150',
            'ship_province' => 'required|string|max:100',
            'ship_district' => 'required|string|max:100',
            'ship_ward' => 'required|string|max:100',
            'ship_address' => 'required|string|max:255',
            'note' => 'nullable|string|max:500',
            'payment_method' => ['required', Rule::in(array_keys(Order::PAYMENT_METHODS))],
            'coupon_code' => 'nullable|string|max:30',
        ], [
            'required' => 'Vui lòng nhập đầy đủ thông tin nhận hàng.',
            'receiver_phone.regex' => 'Số điện thoại phải gồm 10 chữ số và bắt đầu bằng 0.',
            'receiver_email.email' => 'Email không hợp lệ.',
        ]);

        $user = $request->user();

        $order = DB::transaction(function () use ($data, $user) {
            // Khóa các dòng tồn kho trong lúc đặt để hai người không cùng mua món cuối cùng.
            $qty = Cart::quantities();
            $variants = ProductVariant::with('product')->whereIn('id', array_keys($qty))
                ->where('is_active', true)->lockForUpdate()->get();

            if ($variants->isEmpty()) {
                throw ValidationException::withMessages(['cart' => 'Giỏ hàng đang trống.']);
            }

            foreach ($variants as $v) {
                if (! $v->product->is_active || $v->stock_qty < $qty[$v->id]) {
                    throw ValidationException::withMessages([
                        'cart' => "\"{$v->product->name}\" chỉ còn {$v->stock_qty} sản phẩm, vui lòng giảm số lượng.",
                    ]);
                }
            }

            $subtotal = $variants->sum(fn ($v) => $v->price * $qty[$v->id]);

            $coupon = null;
            $discount = 0;
            if (! empty($data['coupon_code'])) {
                $coupon = Coupon::where('code', strtoupper(trim($data['coupon_code'])))->lockForUpdate()->first();
                $reason = $coupon ? $coupon->rejectReason($subtotal, $user) : 'Mã giảm giá không tồn tại.';
                if ($reason) {
                    throw ValidationException::withMessages(['coupon_code' => $reason]);
                }
                $discount = $coupon->discountFor($subtotal);
                $coupon->increment('used_count');
            }

            $shippingFee = Cart::shippingFee($subtotal);

            $order = Order::create([
                'order_code' => 'PT'.now()->format('ymd').'-'.Str::upper(Str::random(5)),
                'user_id' => $user?->id,
                'coupon_id' => $coupon?->id,
                'subtotal' => $subtotal,
                'discount_amount' => $discount,
                'shipping_fee' => $shippingFee,
                'total_amount' => $subtotal - $discount + $shippingFee,
            ] + collect($data)->except('coupon_code')->all());

            foreach ($variants as $v) {
                $order->items()->create([
                    'variant_id' => $v->id,
                    'product_name' => $v->product->name,
                    'variant_name' => $v->name,
                    'sku' => $v->sku,
                    'unit_price' => $v->price,
                    'quantity' => $qty[$v->id],
                ]);
                $v->decrement('stock_qty', $qty[$v->id]);
                $v->product->increment('sold_count', $qty[$v->id]);
            }

            $order->statusHistory()->create(['to_status' => 'pending', 'changed_by' => $user?->id, 'note' => 'Khách đặt hàng']);

            return $order;
        });

        Cart::clear();

        // Khách vãng lai chỉ xem được đơn vừa đặt của chính mình (ghi nhớ trong session).
        session()->push('placed_orders', $order->order_code);

        return redirect()->route('checkout.success', $order);
    }

    public function success(Request $request, Order $order)
    {
        $mine = in_array($order->order_code, session('placed_orders', []), true)
            || ($order->user_id && $order->user_id === $request->user()?->id);
        abort_unless($mine, 404);

        return view('checkout.success', ['order' => $order->load('items')]);
    }
}
