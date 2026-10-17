<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public const ROLES = ['customer' => 'Khách hàng', 'staff' => 'Nhân viên', 'admin' => 'Quản trị viên'];

    public function index(Request $request)
    {
        $users = User::withCount('orders')->latest('id')
            ->when($request->role, fn ($q, $role) => $q->where('role', $role))
            ->when($request->q, fn ($q, $term) => $q->where(fn ($q) => $q
                ->where('username', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%")
                ->orWhere('full_name', 'like', "%{$term}%")))
            ->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => 'required|in:'.implode(',', array_keys(self::ROLES)),
            'status' => 'required|in:active,locked',
        ]);

        // Tự khóa / tự hạ quyền mình thì không ai vào được trang quản trị nữa
        if ($user->is($request->user()) && ($data['status'] === 'locked' || $data['role'] !== 'admin')) {
            return back()->withErrors(['user' => 'Không thể tự khóa hoặc tự hạ quyền tài khoản đang đăng nhập.']);
        }

        $user->update($data);

        return back()->with('success', 'Đã cập nhật tài khoản '.$user->username.'.');
    }
}
