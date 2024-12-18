<?php

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/register');
});

Route::get('/dashboard', [InvoiceController::class, 'index_stats'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::resource('invoices', InvoiceController::class)->only(['index', 'create', 'store', 'edit', 'update'])->middleware(['auth', 'verified']);

Route::middleware(['auth', 'verified'])->name('invoices.')->prefix('/invoices')->group(function () {
    Route::get('/download/{invoice}', [InvoiceController::class, 'download'])->name('download');
});

require __DIR__ . '/auth.php';
