<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ResepController;
use App\Http\Controllers\StafDapur\BahanBakuController;
use App\Http\Controllers\StafDapur\PenjualanController;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');  
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::resource('pengguna', App\Http\Controllers\Admin\UserController::class);
    Route::resource('menu', App\Http\Controllers\Admin\MenuController::class);

    Route::get('/menu/{menu}/resep', [ResepController::class, 'index'])->name('resep.index');
    Route::post('/menu/{menu}/resep', [ResepController::class, 'store'])->name('resep.store');
    Route::delete('/resep/{resep}', [ResepController::class, 'destroy'])->name('resep.destroy');
    Route::put('/resep/{resep}', [ResepController::class, 'update'])->name('resep.update');
});

Route::middleware(['auth', 'role:Staf Dapur'])->prefix('staf')->name('staf.')->group(function () {
    Route::resource('bahan-baku', BahanBakuController::class);

    Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
    Route::post('/penjualan', [PenjualanController::class, 'store'])->name('penjualan.store');
});

require __DIR__.'/auth.php';
