<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\Admin\AlbumController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PhotoController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    $featuredPhotos = \App\Models\Photo::where('is_featured', true)
        ->with('album')
        ->orderBy('created_at', 'desc')
        ->limit(12)
        ->get();

    $albums = \App\Models\Album::where('is_published', true)
        ->withCount('photos')
        ->orderBy('display_order')
        ->limit(6)
        ->get();

    return Inertia::render('Welcome', [
        'featuredPhotos' => $featuredPhotos,
        'albums' => $albums,
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
})->name('homepage');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('albums', AlbumController::class);
    Route::resource('photos', PhotoController::class);
    Route::resource('products', ProductController::class);
    Route::resource('orders', OrderController::class)->except(['create', 'store']);
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::resource('settings', SettingController::class);
    Route::resource('contact-messages', ContactMessageController::class)->only(['index', 'show', 'destroy']);
    Route::patch('contact-messages/{contactMessage}/toggle-read', [ContactMessageController::class, 'toggleRead'])->name('contact-messages.toggle-read');
});

// Public gallery routes
Route::prefix('gallery')->name('gallery.')->group(function () {
    Route::get('/', [GalleryController::class, 'index'])->name('index');
    Route::get('/{slug}', [GalleryController::class, 'show'])->name('show');
    Route::get('/{slug}/photo/{photo}', [GalleryController::class, 'photo'])->name('photo');
});

// About page
Route::get('/about', [AboutController::class, 'index'])->name('about');

// Contact page
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Shop routes
Route::prefix('shop')->name('shop.')->group(function () {
    Route::get('/', [ShopController::class, 'index'])->name('index');
    Route::get('/{slug}', [ShopController::class, 'show'])->name('show');
});

// Cart routes
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/', [CartController::class, 'store'])->name('store');
    Route::patch('/{cartItem}', [CartController::class, 'update'])->name('update');
    Route::delete('/{cartItem}', [CartController::class, 'destroy'])->name('destroy');
});

// Checkout routes
Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/', [CheckoutController::class, 'store'])->name('store');
    Route::get('/success/{order}', [CheckoutController::class, 'success'])->name('success');
});

require __DIR__.'/auth.php';
