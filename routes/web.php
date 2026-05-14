<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\PermissionController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::group(['middleware' => ['auth'], 'prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');

    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

    Route::get('users/downloadImportTemplate', [UserController::class, 'downloadImportTemplate'])->name('users.downloadImportTemplate');
    Route::post('users/import', [UserController::class, 'import'])->name('users.import');
    Route::get('users/export', [UserController::class, 'export'])->name('users.export');
    Route::resource('users', UserController::class);

    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);

    Route::get('get-subcategories/{parentId}', [ProductController::class, 'getSubCategories'])->name('products.getSubCategories');
    Route::get('products/downloadImportTemplate', [ProductController::class, 'downloadImportTemplate'])->name('products.downloadImportTemplate');
    Route::post('products/import', [ProductController::class, 'import'])->name('products.import');
    Route::get('products/export', [ProductController::class, 'export'])->name('products.export');
    Route::resource('products', ProductController::class)->except(['show']);

    Route::resource('categories', CategoryController::class);
    
    Route::get('suppliers/downloadImportTemplate', [SupplierController::class, 'downloadImportTemplate'])->name('suppliers.downloadImportTemplate');
    Route::post('suppliers/import', [SupplierController::class, 'import'])->name('suppliers.import');
    Route::get('suppliers/export', [SupplierController::class, 'export'])->name('suppliers.export');
    Route::resource('suppliers', SupplierController::class)->except(['show']);

    Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
    Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
    Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');
    Route::get('/purchases/{id}', [PurchaseController::class, 'show'])->name('purchases.show');
    Route::get('/purchases/{id}/edit', [PurchaseController::class, 'edit'])->name('purchases.edit');
    Route::put('/purchases/{id}', [PurchaseController::class, 'update'])->name('purchases.update');
    Route::get('/purchases-confirmation', [PurchaseController::class, 'confirmation'])->name('purchases.confirmation');
    Route::post('/purchases/{id}/approve', [PurchaseController::class, 'approve'])->name('purchases.approve');
    Route::post('/purchases/{id}/cancel', [PurchaseController::class, 'cancel'])->name('purchases.cancel');

    Route::get('/orders/confirmation', [OrderController::class, 'confirmation'])->name('orders.confirmation');
    Route::get('/orders/pos', [OrderController::class, 'pos'])->name('orders.pos');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders/store', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{id}', [OrderController::class, 'show'])
        ->name('orders.show')
        ->where('id', '[0-9]+');
    Route::get('/orders/{id}/receipt', [OrderController::class, 'receipt'])->name('orders.receipt');
    Route::post('/orders/{order}/approve', [OrderController::class, 'approve'])->name('orders.approve');
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    Route::match(['GET', 'POST'], '/reports/daily', [ReportController::class, 'dailyReport'])->name('reports.daily');
    Route::match(['GET', 'POST'], '/reports/monthly', [ReportController::class, 'monthlyReport'])->name('reports.monthly');
    Route::match(['GET', 'POST'], '/reports/hourly', [ReportController::class, 'hourlyReport'])->name('reports.hourly');
    Route::get('/reports/daily/export', [ReportController::class, 'exportDailyPdf'])->name('reports.daily.export');
    Route::get('/reports/monthly/export', [ReportController::class, 'exportMonthlyPdf'])->name('reports.monthly.export');
    Route::get('/reports/hourly/export', [ReportController::class, 'exportHourlyPdf'])->name('reports.hourly.export');
});

Route::group(['middleware' => ['auth']], function () {
    Route::get('dashboard/profile', [UserController::class, 'profile'])->name('profile.index');
    Route::put('dashboard/profile/update', [UserController::class, 'updateProfile'])->name('profile.update');
});

Route::get('/preview-error-403', fn() => view('errors.403'));
Route::get('/preview-error-404', fn() => view('errors.404'));
Route::get('/preview-error-419', fn() => view('errors.419'));
Route::get('/preview-error-429', fn() => view('errors.429'));
Route::get('/preview-error-500', fn() => view('errors.500'));
Route::get('/preview-error-503', fn() => view('errors.503'));
