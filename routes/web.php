<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog');
Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');
Route::post('/midtrans/callback', [MidtransController::class, 'callback']);

// Chat API (Public for guests too)
Route::get('/chat/messages', [\App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');
Route::post('/chat/send', [\App\Http\Controllers\ChatController::class, 'store'])->name('chat.store');

// Socialite Routes
Route::get('/auth/{provider}/redirect', [\App\Http\Controllers\Auth\SocialiteController::class, 'redirect'])->name('socialite.redirect');
Route::get('/auth/{provider}/callback', [\App\Http\Controllers\Auth\SocialiteController::class, 'callback'])->name('socialite.callback');

// Static Pages
Route::view('/faq', 'pages.faq')->name('page.faq');
Route::view('/pengiriman', 'pages.shipping')->name('page.shipping');
Route::view('/pengembalian', 'pages.returns')->name('page.returns');
Route::view('/hubungi-kami', 'pages.contact')->name('page.contact');

// Protected Routes (Users)
Route::middleware(['auth'])->group(function () {
    // Reviews
    
    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{id}', [CartController::class, 'remove'])->name('cart.remove');

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Checkout & Orders
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::post('/checkout/coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.coupon');
    
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/reviews', [\App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');

    // Settings
    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings/profile', [\App\Http\Controllers\SettingsController::class, 'updateProfile'])->name('settings.profile');
    Route::put('/settings/security', [\App\Http\Controllers\SettingsController::class, 'updateSecurity'])->name('settings.security');
    Route::post('/settings/address', [\App\Http\Controllers\SettingsController::class, 'storeAddress'])->name('settings.address.store');
    Route::delete('/settings/address/{address}', [\App\Http\Controllers\SettingsController::class, 'destroyAddress'])->name('settings.address.destroy');
    Route::patch('/settings/address/{address}/primary', [\App\Http\Controllers\SettingsController::class, 'setPrimaryAddress'])->name('settings.address.primary');
    Route::put('/settings/bank', [\App\Http\Controllers\SettingsController::class, 'updateBank'])->name('settings.bank');
    Route::put('/settings/preferences', [\App\Http\Controllers\SettingsController::class, 'updatePreferences'])->name('settings.preferences');

    // RajaOngkir API
    Route::get('/api/rajaongkir/provinces', [\App\Http\Controllers\RajaOngkirController::class, 'getProvinces']);
    Route::get('/api/rajaongkir/cities/{provinceId}', [\App\Http\Controllers\RajaOngkirController::class, 'getCities']);
    Route::post('/api/rajaongkir/cost', [\App\Http\Controllers\RajaOngkirController::class, 'getCost']);
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Chat
    Route::get('/chat', [\App\Http\Controllers\Admin\ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{sessionId}', [\App\Http\Controllers\Admin\ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/{sessionId}/reply', [\App\Http\Controllers\Admin\ChatController::class, 'reply'])->name('chat.reply');

    Route::resource('products', AdminProductController::class)->except(['create', 'edit', 'show']);
    
    // Coupons
    Route::get('/coupons', [\App\Http\Controllers\Admin\CouponController::class, 'index'])->name('coupons.index');
    Route::post('/coupons', [\App\Http\Controllers\Admin\CouponController::class, 'store'])->name('coupons.store');
    Route::delete('/coupons/{coupon}', [\App\Http\Controllers\Admin\CouponController::class, 'destroy'])->name('coupons.destroy');
    
    // Orders
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::patch('/orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');
        
    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
});

require __DIR__.'/auth.php';
