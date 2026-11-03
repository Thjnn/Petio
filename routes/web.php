<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Customer\AccountController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Middleware\CustomerAccount;
use App\Models\Faq;
use App\Models\SpaService;
use Illuminate\Support\Facades\Route;

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])
        ->whereIn('provider', ['google', 'facebook'])->name('social.redirect');
    Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
        ->whereIn('provider', ['google', 'facebook'])->name('social.callback');
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
Route::post('/checkout/coupon', [CheckoutController::class, 'coupon'])->middleware('throttle:30,1')->name('checkout.coupon');
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

// Khu vực riêng của khách hàng: mọi truy vấn đều giới hạn theo tài khoản đăng nhập.
Route::prefix('tai-khoan')->name('customer.')->middleware(['auth', CustomerAccount::class])->group(function () {
    $c = AccountController::class;
    Route::get('/', [$c, 'index'])->name('dashboard');
    Route::get('/ho-so', [$c, 'profile'])->name('profile');
    Route::post('/anh-dai-dien', [$c, 'uploadAvatar'])->name('avatar.update');
    Route::get('/anh-dai-dien', [$c, 'avatar'])->name('avatar');
    Route::post('/don-hang/{order}/huy', [$c, 'cancelOrder'])->middleware('throttle:6,1')->name('orders.cancel');
    Route::put('/ho-so', [$c, 'updateProfile'])->name('profile.update');
    Route::put('/mat-khau', [$c, 'password'])->middleware('throttle:6,1')->name('password');
    Route::get('/don-hang', [$c, 'orders'])->name('orders');
    Route::get('/don-hang/{order}', [$c, 'order'])->name('orders.show');
    Route::get('/yeu-thich', [$c, 'favorites'])->name('favorites');
    Route::post('/yeu-thich/{product}', [$c, 'favorite'])->name('favorites.store');
    Route::delete('/yeu-thich/{product}', [$c, 'unfavorite'])->name('favorites.destroy');
    Route::get('/thong-bao', [$c, 'notifications'])->name('notifications');
    Route::patch('/thong-bao/{notice}', [$c, 'notice'])->name('notifications.read');
    Route::delete('/thong-bao/{notice}', [$c, 'notice'])->name('notifications.destroy');
    Route::get('/ma-giam-gia', [$c, 'coupons'])->name('coupons');
    Route::get('/dia-chi', [$c, 'addressIndex'])->name('addresses');
    Route::get('/dia-chi/them', [$c, 'addressForm'])->name('addresses.create');
    Route::post('/dia-chi', [$c, 'saveAddress'])->name('addresses.store');
    Route::get('/dia-chi/{address}/sua', [$c, 'addressForm'])->name('addresses.edit');
    Route::put('/dia-chi/{address}', [$c, 'saveAddress'])->name('addresses.update');
    Route::delete('/dia-chi/{address}', [$c, 'deleteAddress'])->name('addresses.destroy');
    Route::get('/ho-tro', [$c, 'support'])->name('support');
    Route::view('/bao-mat', 'customer.privacy')->name('privacy');
    Route::view('/ve-petio','customer.about')->name('about');
});
