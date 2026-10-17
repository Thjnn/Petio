<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::latest()
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->q, fn ($q, $term) => $q->where(fn ($q) => $q
                ->where('order_code', 'like', "%{$term}%")
                ->orWhere('receiver_phone', 'like', "%{$term}%")
                ->orWhere('receiver_name', 'like', "%{$term}%")))
            ->paginate(20)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        return view('admin.orders.show', ['order' => $order->load('items', 'user', 'statusHistory.changer')]);
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Order::STATUSES))],
            'payment_status' => ['required', Rule::in(array_keys(Order::PAYMENT_STATUSES))],
            'note' => 'nullable|string|max:255',
        ]);

        if (in_array($order->status, Order::FINAL_STATUSES, true) && $data['status'] !== $order->status) {
            return back()->withErrors(['status' => 'Đơn đã hủy/trả hàng, không đổi trạng thái được nữa.']);
        }

        DB::transaction(function () use ($data, $order, $request) {
            $from = $order->status;

            // Hủy / trả hàng: hoàn lại tồn kho và lượt bán đã trừ lúc đặt
            if ($from !== $data['status'] && in_array($data['status'], Order::FINAL_STATUSES, true)) {
                foreach ($order->items as $item) {
                    $variant = $item->variant_id ? ProductVariant::with('product')->find($item->variant_id) : null;
                    $variant?->increment('stock_qty', $item->quantity);
                    $variant?->product->decrement('sold_count', min($item->quantity, $variant->product->sold_count));
                }
                $order->cancel_reason = $data['note'] ?? $order->cancel_reason;
            }

            $order->status = $data['status'];
            $order->payment_status = $data['payment_status'];
            if ($data['payment_status'] === 'paid' && ! $order->paid_at) {
                $order->paid_at = now();
            }
            $order->save();

            if ($from !== $data['status'] || ! empty($data['note'])) {
                $order->statusHistory()->create([
                    'from_status' => $from,
                    'to_status' => $data['status'],
                    'changed_by' => $request->user()->id,
                    'note' => $data['note'] ?? null,
                ]);
            }
        });

        return back()->with('success', 'Đã cập nhật đơn hàng.');
    }
}
