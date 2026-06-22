<?php

use App\Http\Controllers\AdminPackageController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminCustomerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman awal
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    if (auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('user.home');
});

/*
|--------------------------------------------------------------------------
| Login dan registrasi
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [
        AuthController::class,
        'loginPage',
    ])->name('login');

    Route::post('/login', [
        AuthController::class,
        'login',
    ])->name('login.process');

    Route::get('/register', [
        AuthController::class,
        'registerPage',
    ])->name('register');

    Route::post('/register', [
        AuthController::class,
        'register',
    ])->name('register.process');
});

/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/

Route::post('/logout', [
    AuthController::class,
    'logout',
])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Route Admin
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get('/dashboard', [
            AdminController::class,
            'dashboard',
        ])->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | Kelola Pelanggan
        |--------------------------------------------------------------------------
        */

        Route::get('/customers', [
            AdminCustomerController::class,
            'index',
        ])->name('customers.index');

        Route::get('/customers/{user}', [
            AdminCustomerController::class,
            'show',
        ])->name('customers.show');

        Route::patch('/customers/{user}/password', [
            AdminCustomerController::class,
            'resetPassword',
        ])->name('customers.password');

        Route::patch('/customers/{user}/status', [
            AdminCustomerController::class,
            'toggleStatus',
        ])->name('customers.status');

        /*
        |--------------------------------------------------------------------------
        | Kelola Pesanan
        |--------------------------------------------------------------------------
        */

        Route::get('/orders', [
            AdminController::class,
            'orders',
        ])->name('orders');

        Route::get('/orders/history', [
            AdminController::class,
            'orderHistory',
        ])->name('orders.history');

        Route::get('/orders/{order}', [
            AdminController::class,
            'showOrder',
        ])->name('orders.show');

        Route::patch('/orders/{order}/status', [
            AdminController::class,
            'updateOrderStatus',
        ])->name('orders.status');

        /*
        |--------------------------------------------------------------------------
        | Verifikasi Pembayaran
        |--------------------------------------------------------------------------
        */

        Route::get('/payments', [
            AdminController::class,
            'payments',
        ])->name('payments');

        Route::patch('/payments/{payment}/verify', [
            AdminController::class,
            'verifyPayment',
        ])->name('payments.verify');

        Route::patch('/payments/{payment}/reject', [
            AdminController::class,
            'rejectPayment',
        ])->name('payments.reject');

        /*
        |--------------------------------------------------------------------------
        | Kelola Paket
        |--------------------------------------------------------------------------
        */

        Route::get('/packages', [
            AdminPackageController::class,
            'index',
        ])->name('packages');

        Route::post('/packages', [
            AdminPackageController::class,
            'store',
        ])->name('packages.store');

        Route::patch('/packages/{package}', [
            AdminPackageController::class,
            'update',
        ])->name('packages.update');

        Route::patch('/packages/{package}/status', [
            AdminPackageController::class,
            'toggleStatus',
        ])->name('packages.status');

        Route::delete('/packages/{package}', [
            AdminPackageController::class,
            'destroy',
        ])->name('packages.delete');

        /*
        |--------------------------------------------------------------------------
        | Kuota Produksi
        |--------------------------------------------------------------------------
        */

        Route::get('/quotas', [
            AdminController::class,
            'quotas',
        ])->name('quotas');

        Route::post('/quotas', [
            AdminController::class,
            'storeQuota',
        ])->name('quotas.store');

        /*
        |--------------------------------------------------------------------------
        | Voucher
        |--------------------------------------------------------------------------
        */

        Route::get('/vouchers', [
            AdminController::class,
            'vouchers',
        ])->name('vouchers');

        Route::post('/vouchers', [
            AdminController::class,
            'storeVoucher',
        ])->name('vouchers.store');

        /*
        |--------------------------------------------------------------------------
        | Tim Produksi
        |--------------------------------------------------------------------------
        */

        Route::get('/teams', [
            AdminController::class,
            'teams',
        ])->name('teams');

        Route::post('/teams', [
            AdminController::class,
            'storeTeam',
        ])->name('teams.store');
        Route::patch('/teams/{team}/status', [
            AdminController::class,
            'toggleTeamStatus',
        ])->name('teams.status');
    });

/*
|--------------------------------------------------------------------------
| Route User
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:user'])
    ->prefix('user')
    ->name('user.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Halaman utama user
        |--------------------------------------------------------------------------
        */

        Route::get('/home', [
            UserController::class,
            'home',
        ])->name('home');

        /*
        |--------------------------------------------------------------------------
        | Dashboard user
        |--------------------------------------------------------------------------
        */

        Route::get('/dashboard', [
            UserController::class,
            'dashboard',
        ])->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | Pesanan user
        |--------------------------------------------------------------------------
        */

        Route::get('/orders/create', [
            UserController::class,
            'createOrder',
        ])->name('orders.create');

        Route::post('/orders', [
            UserController::class,
            'storeOrder',
        ])->name('orders.store');

        Route::get('/orders/{order}', [
            UserController::class,
            'showOrder',
        ])->name('orders.show');

        /*
        |--------------------------------------------------------------------------
        | Upload bukti pembayaran
        |--------------------------------------------------------------------------
        */

        Route::post('/orders/{order}/payment', [
            UserController::class,
            'uploadPayment',
        ])->name('orders.payment');

        /*
        |--------------------------------------------------------------------------
        | Download hasil konten
        |--------------------------------------------------------------------------
        */

        Route::get('/orders/{order}/results/{result}/download', [
            UserController::class,
            'downloadResult',
        ])->name('orders.results.download');
    });