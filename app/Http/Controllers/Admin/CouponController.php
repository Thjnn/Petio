<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    public function index()
    {
        return view('admin.coupons.index', ['coupons' => Coupon::latest('id')->get()]);
    }

    public function create()
    {
        return view('admin.coupons.form', ['coupon' => new Coupon(['is_active' => true, 'discount_type' => 'percent'])]);
    }

    public function store(Request $request)
    {
        $this->save($request, new Coupon);

        return redirect()->route('admin.coupons.index')->with('success', 'Đã thêm mã giảm giá.');
    }

    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.form', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $this->save($request, $coupon);

        return redirect()->route('admin.coupons.index')->with('success', 'Đã lưu mã giảm giá.');
    }

    /** Mã đã dùng trong đơn thì đơn vẫn giữ (coupon_id về NULL), số tiền giảm nằm sẵn trong đơn. */
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return back()->with('success', 'Đã xóa mã giảm giá.');
    }

    private function save(Request $request, Coupon $coupon): void
    {
        $request->merge(['code' => strtoupper(trim((string) $request->code))]);

        $data = $request->validate([
            'code' => ['required', 'alpha_dash', 'max:30', Rule::unique('coupons')->ignore($coupon->id)],
            'description' => 'nullable|string|max:255',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => ['required', 'integer', 'min:1', $request->discount_type === 'percent' ? 'max:100' : 'max:100000000'],
            'max_discount' => 'nullable|integer|min:0',
            'min_order_value' => 'nullable|integer|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
        ], ['code.unique' => 'Mã này đã tồn tại.', 'discount_value.max' => 'Phần trăm giảm tối đa 100.']);

        $coupon->fill([
            'min_order_value' => $data['min_order_value'] ?? 0,
            'first_order_only' => $request->boolean('first_order_only'),
            'is_active' => $request->boolean('is_active'),
        ] + $data)->save();
    }
}
