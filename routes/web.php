<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryController;

// Dashboard Utama
Route::get('/', [InventoryController::class, 'index'])->name('inventory.index');

// Data Inventaris
Route::get('/inventaris', [InventoryController::class, 'data'])->name('inventory.data');

// Laporan / History Report
Route::get('/laporan', [InventoryController::class, 'laporan'])->name('inventory.laporan');

// Jadwal Shalat & Agenda QC
Route::get('/jadwal-shalat', [InventoryController::class, 'jadwal'])->name('inventory.jadwal');

// Data Sampah (Trash Bin)
Route::get('/trash', [InventoryController::class, 'trash'])->name('inventory.trash');
Route::post('/trash/{id}/restore', [InventoryController::class, 'restore'])->name('inventory.restore');

// Profil Akun
Route::get('/profile', [InventoryController::class, 'profile'])->name('inventory.profile');

// Rute CRUD Barang
Route::post('/inventory/store', [InventoryController::class, 'store'])->name('inventory.store');
Route::put('/inventory/update/{id}', [InventoryController::class, 'update'])->name('inventory.update');
Route::delete('/inventory/destroy/{id}', [InventoryController::class, 'destroy'])->name('inventory.destroy');

// Rute Agenda QC
Route::post('/qc-event/store', [InventoryController::class, 'storeQcEvent'])->name('qc.store');

// Rute Show Data
Route::get('/inventaris/{id}', [InventoryController::class, 'show'])->name('inventory.show');