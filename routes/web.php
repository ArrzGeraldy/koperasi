<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PinjamanController;
use App\Http\Controllers\PembayaranCicilanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SetorSimpananController;
use App\Http\Controllers\SimpananController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


// ========================================
// Auth Routes
// ========================================
require __DIR__.'/auth.php';

// ========================================
// Anggota Routes
// ========================================
Route::middleware('auth')->name('anggota.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'anggota'])->name('dashboard');
    
    // simpanan
    Route::get('/anggota/simpanan', [SimpananController::class, 'indexAnggota'])->name('simpanan.index');

    // setor
    Route::get('/anggota/setor', [SetorSimpananController::class, 'create'])->name('setor.create');
    Route::post('/anggota/setor', [SetorSimpananController::class, 'store'])->name('setor.store');

    
    // Pengajuan Pinjaman
    Route::get('/anggota/pengajuan-pinjaman', [PinjamanController::class, 'create'])->name('pengajuan.create');
    Route::post('/anggota/pengajuan-pinjaman', [PinjamanController::class, 'store'])->name('pengajuan.store');
    
    // Cicilan & Pinjaman
    Route::get('/anggota/pinjaman/{id}/cicilans', [DashboardController::class, 'cicilans'])->name('cicilans');
    
    Route::get('/anggota/riwayat', [DashboardController::class, 'riwayat'])->name('riwayat');
    
    // Pembayaran Cicilan
    Route::get('/anggota/cicilan/{id}/bayar', [PembayaranCicilanController::class, 'create'])->name('pembayaran-cicilan.create');
    Route::post('/anggota/cicilan/{id}/bayar', [PembayaranCicilanController::class, 'store'])->name('pembayaran-cicilan.store');
});

// ========================================
// Admin Routes
// ========================================
Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
    // Print members summary (A4 landscape)
    Route::get('/print/members', [DashboardController::class, 'printMembers'])->name('print.members');
    
    // Pinjaman Management
    Route::prefix('pinjaman')->name('pinjaman.')->group(function () {
        Route::get('/pending', [PinjamanController::class, 'pending'])->name('pending');
        Route::get('/', [PinjamanController::class, 'index'])->name('index');
        Route::get('/{id}', [PinjamanController::class, 'show'])->name('show');
        Route::get('/{id}/approve-form', [PinjamanController::class, 'approveForm'])->name('approve-form');
        Route::post('/{id}/approve', [PinjamanController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [PinjamanController::class, 'reject'])->name('reject');
    });

    // setor
    Route::prefix('setor')->name('setor.')->group(function () {
        Route::get('/', [SetorSimpananController::class, 'indexAdmin'])->name('index');
        Route::put('/{id}/reject', [SetorSimpananController::class, 'reject'])->name('reject');
        Route::put('/{id}/verify', [SetorSimpananController::class, 'verify'])->name('verify');
    });
    
    // Pembayaran Cicilan Management
    Route::prefix('pembayaran-cicilan')->name('pembayaran-cicilan.')->group(function () {
        Route::get('/notif', [PembayaranCicilanController::class, 'index'])->name('notif.index');
        Route::put('/{id}/verify', [PembayaranCicilanController::class, 'verify'])->name('verify');
        Route::delete('/{id}/reject', [PembayaranCicilanController::class, 'reject'])->name('reject');
    });
    
    // User Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{id}', [UserController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{id}', [UserController::class, 'update'])->name('update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
    });
});
