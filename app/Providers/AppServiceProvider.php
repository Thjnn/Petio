<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Trang quản trị: admin và nhân viên. Khóa tài khoản / đổi vai trò: chỉ admin.
        Gate::define('admin', fn (User $user) => in_array($user->role, ['admin', 'staff'], true));
        Gate::define('manage-users', fn (User $user) => $user->role === 'admin');
    }
}
