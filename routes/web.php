<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\CategoryController; // Import CategoryController
use App\Http\Controllers\Admin\PostController;     // [MỚI] Import PostController
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// --- NHÓM ROUTE ADMIN ---
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    
    // Route quản lý Danh mục
    Route::resource('categories', CategoryController::class);

    // [MỚI] Route quản lý Bài viết
    Route::resource('posts', PostController::class);

});

require __DIR__.'/auth.php';