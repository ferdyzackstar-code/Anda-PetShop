<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

// Redirect setelah login biasanya ke /home, kita arahkan ke dashboard controller jika perlu
Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::group(['middleware' => ['auth'], 'prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
    // Ini halaman utama dashboard: route('dashboard.index')
    Route::get('/', function () {
        return view('dashboard.index');
    })->name('index');

    // Semua resource di bawah ini otomatis punya awalan 'dashboard.'
    // Contoh: route('dashboard.users.index'), route('dashboard.products.index')
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
});
