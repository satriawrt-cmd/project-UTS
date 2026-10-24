<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ShopController;

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.process');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (General)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Root Redirect
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Customer Shopping & Storefront Routes (Accessible by User, Staff, Owner)
    |--------------------------------------------------------------------------
    */
    Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
    Route::get('/cart', [ShopController::class, 'cart'])->name('shop.cart');
    Route::post('/cart/add/{product}', [ShopController::class, 'addToCart'])->name('shop.cart.add');
    Route::post('/cart/update', [ShopController::class, 'updateCart'])->name('shop.cart.update');
    Route::post('/cart/remove/{productId}', [ShopController::class, 'removeFromCart'])->name('shop.cart.remove');
    Route::get('/checkout', [ShopController::class, 'checkout'])->name('shop.checkout');
    Route::post('/checkout', [ShopController::class, 'processCheckout'])->name('shop.checkout.process');
    Route::get('/my-orders', [ShopController::class, 'myOrders'])->name('my-orders.index');
    Route::get('/my-orders/{sale}', [ShopController::class, 'showOrder'])->name('my-orders.show');

    /*
    |--------------------------------------------------------------------------
    | Staff & Owner Backoffice Management Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:owner,staff')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index']);

        // Categories List
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');

        // Products List & Low Stock (Read-only for Staff, Manage for Owner)
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/low-stock', [ProductController::class, 'lowStock'])->name('products.low-stock');

        // Suppliers List
        Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');

        // Purchases (Stock-In Transactions)
        Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
        Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
        Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');
        Route::get('/purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');

        // Sales (Stock-Out Transactions from Cashier)
        Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
        Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
        Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
        Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');

        // Reports & Analytics
        Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    });


    // Customer cancellation request
    Route::post('/my-orders/{sale}/cancel', [ShopController::class, 'requestCancellation'])->name('my-orders.cancel');

    /*
    |--------------------------------------------------------------------------
    | Owner-Only Routes (Sensitive Master Data & System Administration)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:owner')->group(function () {
        // Category Management
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        // Product Management (Create / Edit / Delete)
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        // Supplier Management (Create / Edit / Delete)
        Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
        Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
        Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
        Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
        Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

        // Transaction Cancellations & Approval
        Route::post('/purchases/{purchase}/cancel', [PurchaseController::class, 'cancel'])->name('purchases.cancel');
        Route::post('/sales/{sale}/cancel', [SaleController::class, 'cancel'])->name('sales.cancel');
        Route::post('/sales/{sale}/approve-cancel', [SaleController::class, 'approveCancellation'])->name('sales.approve-cancel');
        Route::post('/sales/{sale}/reject-cancel', [SaleController::class, 'rejectCancellation'])->name('sales.reject-cancel');

        // User & Role Management
        Route::resource('users', UserController::class)->except(['show']);
    });


    // Parameterized Detail Views for Staff & Owner
    Route::middleware('role:owner,staff')->group(function () {
        Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
        Route::get('/suppliers/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
    });
});