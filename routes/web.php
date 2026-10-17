<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Models\Faq;
use App\Models\SpaService;
use Illuminate\Support\Facades\Route;

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/', [HomeController::class, 'index'])->name('home');

// Cửa hàng
Route::get('/shop', [ShopController::class, 'index'])->name('shop');
Route::get('/products/{product}', [ShopController::class, 'show'])->name('products.show');

// Tin tức
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{post}', [BlogController::class, 'show'])->name('blog.show');

// Giỏ hàng (JSON cho cart.js) — không yêu cầu đăng nhập
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// Thanh toán — khách vãng lai vẫn đặt được (orders.user_id = NULL)
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->middleware('throttle:10,1')->name('checkout.store');
Route::get('/orders/{order}/success', [CheckoutController::class, 'success'])->name('checkout.success');

// Liên hệ & bản tin
Route::view('/contact', 'pages.contact')->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');
Route::post('/newsletter', [ContactController::class, 'subscribe'])->middleware('throttle:5,1')->name('newsletter.store');

// Các trang thông tin
Route::view('/about', 'pages.about')->name('about');
Route::get('/spa', function () {
    return view('pages.spa', ['services' => SpaService::where('is_active', true)->with('prices')->orderBy('sort_order')->get()]);
})->name('spa');
Route::get('/faq', function () {
    $sections = Faq::where('is_active', true)->orderBy('section_order')->orderBy('sort_order')->get()->groupBy('section_title');

    return view('pages.faq', compact('sections'));
})->name('faq');

// Quản trị — admin và nhân viên
Route::prefix('admin')->name('admin.')->middleware(['auth', 'can:admin'])->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/orders', [Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [Admin\OrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{order}', [Admin\OrderController::class, 'update'])->name('orders.update');

    // Bind theo id (model public dùng slug trên URL)
    Route::resource('products', Admin\ProductController::class)->except('show')->parameters(['products' => 'product:id']);
    Route::resource('categories', Admin\CategoryController::class)->except('show');
    Route::resource('posts', Admin\BlogPostController::class)->except('show')->parameters(['posts' => 'post:id']);
    Route::resource('banners', Admin\BannerController::class)->except('show');
    Route::resource('faqs', Admin\FaqController::class)->except('show');
    Route::resource('spa', Admin\SpaServiceController::class)->except('show')->parameters(['spa' => 'service']);
    Route::resource('coupons', Admin\CouponController::class)->except('show');

    Route::get('/users', [Admin\UserController::class, 'index'])->name('users.index');
    Route::put('/users/{user}', [Admin\UserController::class, 'update'])->middleware('can:manage-users')->name('users.update');

    Route::get('/contacts', [Admin\ContactMessageController::class, 'index'])->name('contacts.index');
    Route::get('/contacts/{contact}', [Admin\ContactMessageController::class, 'show'])->name('contacts.show');
    Route::put('/contacts/{contact}', [Admin\ContactMessageController::class, 'update'])->name('contacts.update');

    Route::get('/newsletter', [Admin\NewsletterController::class, 'index'])->name('newsletter.index');
});
