<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // Doanh thu tính trên đơn chưa hủy/trả
        $valid = fn () => Order::whereNotIn('status', Order::FINAL_STATUSES);

        // Doanh thu 7 ngày gần nhất, ngày không có đơn = 0
        $daily = $valid()->where('created_at', '>=', today()->subDays(6))->get(['created_at', 'total_amount'])
            ->groupBy(fn ($o) => $o->created_at->toDateString())->map->sum('total_amount');
        $week = collect(range(6, 0))->mapWithKeys(function ($ago) use ($daily) {
            $day = today()->subDays($ago);

            return [$day->format('d/m') => $daily[$day->toDateString()] ?? 0];
        });

        return view('admin.dashboard', [
            'stats' => [
                'revenueMonth' => $valid()->where('created_at', '>=', now()->startOfMonth())->sum('total_amount'),
                'ordersToday' => Order::whereDate('created_at', today())->count(),
                'pendingOrders' => Order::where('status', 'pending')->count(),
                'customers' => User::where('role', 'customer')->count(),
                'newContacts' => ContactMessage::where('status', 'new')->count(),
            ],
            'week' => $week,
            'recentOrders' => Order::latest()->limit(8)->get(),
            'lowStock' => ProductVariant::with('product')->where('is_active', true)
                ->where('stock_qty', '<', 5)->orderBy('stock_qty')->limit(8)->get(),
        ]);
    }
}
