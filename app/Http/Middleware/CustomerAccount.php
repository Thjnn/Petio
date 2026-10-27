<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CustomerAccount
{
    public function handle(Request $request, Closure $next)
    {
        abort_unless($request->user()->isActive(), 403, 'Tài khoản đã bị khóa.');
        if ($request->user()->role !== 'customer') {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}
