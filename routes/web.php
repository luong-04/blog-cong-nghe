<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Admin\UserController;
use App\Models\Post;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

// --- PUBLIC ROUTES ---
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/danh-muc/{slug}', [HomeController::class, 'category'])->name('categories.show');
Route::get('/bai-viet/{slug}', [HomeController::class, 'show'])->name('posts.show');
Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');

// --- AUTH ROUTES ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/request-author', [ProfileController::class, 'requestAuthor'])->name('profile.request-author');
});

Route::get('/dashboard', function () {
    $query = Post::query();
    // Author chỉ thấy thống kê bài của mình
    if(auth()->user()->role !== 'admin') {
        $query->where('user_id', auth()->id());
    }
    $totalPosts = $query->count();
    $latestPosts = $query->with('category')->latest()->take(5)->get();
    
    return view('dashboard', compact('totalPosts', 'latestPosts'));
})->middleware(['auth', 'verified', 'role:admin,author'])->name('dashboard');


// --- KHU VỰC QUẢN TRỊ ---
Route::middleware(['auth', 'verified', 'role:admin,author'])->prefix('admin')->name('admin.')->group(function () {
    
    // 1. Quản lý Bài viết (Ai cũng vào được, nhưng Controller sẽ lọc bài)
    Route::resource('posts', PostController::class);
    Route::post('posts/generate-content', [PostController::class, 'generateContent'])->name('posts.generate');

    // 2. NHÓM CHỈ DÀNH RIÊNG CHO ADMIN (Author không vào được)
    Route::middleware('role:admin')->group(function () {
        // Quản lý Danh mục
        Route::resource('categories', CategoryController::class);
        
        // Quản lý Thành viên
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::patch('users/{user}/approve', [UserController::class, 'approve'])->name('users.approve');
        
        // Route hủy quyền tác giả
        Route::patch('users/{user}/revoke', [UserController::class, 'revoke'])->name('users.revoke');
        //Quản lý bình luận
        Route::get('comments', [App\Http\Controllers\Admin\CommentManagerController::class, 'index'])->name('comments.index');
        Route::delete('comments/{comment}', [App\Http\Controllers\Admin\CommentManagerController::class, 'destroy'])->name('comments.destroy');
        //QUản lý quảng cáo
        Route::resource('ads', \App\Http\Controllers\Admin\AdController::class);
        Route::patch('ads/{ad}/toggle', [\App\Http\Controllers\Admin\AdController::class, 'toggle'])->name('ads.toggle');
    });
});

require __DIR__.'/auth.php';