<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ResepController;

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
});

require __DIR__.'/auth.php';
