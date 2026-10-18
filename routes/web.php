<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InventoryController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.store');

    Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1')->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventaris', [InventoryController::class, 'data'])->name('inventory.data');
    Route::get('/laporan', [InventoryController::class, 'laporan'])->name('inventory.laporan');
    Route::get('/jadwal-shalat', [InventoryController::class, 'jadwal'])->name('inventory.jadwal');
    Route::get('/jadwal-shalat/prayer-times', [InventoryController::class, 'prayerSchedule'])->name('inventory.prayer-schedule');

    Route::get('/trash', [InventoryController::class, 'trash'])->name('inventory.trash');
    Route::post('/trash/{id}/restore', [InventoryController::class, 'restore'])->name('inventory.restore');

    Route::get('/profile', [InventoryController::class, 'profile'])->name('inventory.profile');
    Route::put('/profile', [InventoryController::class, 'updateProfile'])->name('inventory.profile.update');

    Route::post('/inventory/store', [InventoryController::class, 'store'])->name('inventory.store');
    Route::put('/inventory/update/{id}', [InventoryController::class, 'update'])->name('inventory.update');
    Route::delete('/inventory/destroy/{id}', [InventoryController::class, 'destroy'])->name('inventory.destroy');

    Route::post('/qc-event/store', [InventoryController::class, 'storeQcEvent'])->name('qc.store');
    Route::put('/qc-event/{event}', [InventoryController::class, 'updateQcEvent'])->name('qc.update');
    Route::delete('/qc-event/{event}', [InventoryController::class, 'destroyQcEvent'])->name('qc.destroy');
    Route::get('/inventaris/{id}', [InventoryController::class, 'show'])->name('inventory.show');

    Route::get('/admin', [AdminController::class, 'index'])
        ->middleware('admin')
        ->name('admin.dashboard');
});
