<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ResepController;
use App\Http\Controllers\StafDapur\BahanBakuController;
use App\Http\Controllers\StafDapur\PenjualanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\StafDapur\StokController;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::resource('pengguna', App\Http\Controllers\Admin\UserController::class);
    Route::resource('menu', App\Http\Controllers\Admin\MenuController::class);

    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');

    Route::get('/menu/{menu}/resep', [ResepController::class, 'index'])->name('resep.index');
    Route::post('/menu/{menu}/resep', [ResepController::class, 'store'])->name('resep.store');
    Route::delete('/resep/{resep}', [ResepController::class, 'destroy'])->name('resep.destroy');
    Route::put('/resep/{resep}', [ResepController::class, 'update'])->name('resep.update');
    Route::get('/laporan/download', [LaporanController::class, 'download'])->name('laporan.download');
});

Route::middleware(['auth', 'role:Staf Dapur'])->prefix('staf')->name('staf.')->group(function () {
    Route::resource('bahan-baku', BahanBakuController::class);

    Route::get('/stok-masuk', [StokController::class, 'index'])->name('stok-masuk.index');
    Route::post('/stok-masuk', [StokController::class, 'store'])->name('stok-masuk.store');

    Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
    Route::post('/penjualan', [PenjualanController::class, 'store'])->name('penjualan.store');

    Route::get('/penjualan/hari-ini', [PenjualanController::class, 'showTodaySales'])->name('penjualan.today');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/prediksi', [App\Http\Controllers\PredictionController::class, 'index'])->name('prediksi.index');
});

require __DIR__.'/auth.php';
