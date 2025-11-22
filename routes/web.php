<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdController;
use App\Http\Controllers\Admin\CommentManagerController;
use App\Models\Post;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

// --- ROUTE DÀNH CHO KHÁCH (PUBLIC) ---
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/danh-muc/{slug}', [HomeController::class, 'category'])->name('categories.show');
Route::get('/bai-viet/{slug}', [HomeController::class, 'show'])->name('posts.show');

// Route gửi bình luận (Ai cũng gửi được, controller sẽ tự check login)
Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');


// --- ROUTE CẦN ĐĂNG NHẬP (AUTH) ---
Route::middleware('auth')->group(function () {
    // 1. Quản lý Hồ sơ cá nhân
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/request-author', [ProfileController::class, 'requestAuthor'])->name('profile.request-author');

    // 2. Xử lý Bình luận (Sửa, Xóa, Duyệt) - [ĐÃ THÊM VÀO ĐÂY]
    Route::patch('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::patch('/comments/{comment}/approve', [CommentController::class, 'approve'])->name('comments.approve');
});


// --- ROUTE DASHBOARD (Chỉ Admin & Author) ---
Route::get('/dashboard', function () {
    $query = Post::query();
    if(auth()->user()->role !== 'admin') {
        $query->where('user_id', auth()->id());
    }
    $totalPosts = $query->count();
    $latestPosts = $query->with('category')->latest()->take(5)->get();
    
    return view('dashboard', compact('totalPosts', 'latestPosts'));
})->middleware(['auth', 'verified', 'role:admin,author'])->name('dashboard');


// --- KHU VỰC QUẢN TRỊ (ADMIN PANEL) ---
Route::middleware(['auth', 'verified', 'role:admin,author'])->prefix('admin')->name('admin.')->group(function () {
    
    // 1. Quản lý Bài viết (Cả Admin & Author)
    Route::resource('posts', PostController::class);
    Route::post('posts/generate-content', [PostController::class, 'generateContent'])->name('posts.generate');

    // 2. NHÓM CHỈ DÀNH RIÊNG CHO ADMIN
    Route::middleware('role:admin')->group(function () {
        // Quản lý Danh mục
        Route::resource('categories', CategoryController::class);
        
        // Quản lý Thành viên
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::patch('users/{user}/approve', [UserController::class, 'approve'])->name('users.approve');
        Route::patch('users/{user}/revoke', [UserController::class, 'revoke'])->name('users.revoke');

        // Quản lý Bình luận (Admin xóa spam)
        Route::get('comments', [CommentManagerController::class, 'index'])->name('comments.index');
        Route::delete('comments/{comment}', [CommentManagerController::class, 'destroy'])->name('comments.destroy');

        // Quản lý Quảng cáo
        Route::resource('ads', AdController::class);
        Route::patch('ads/{ad}/toggle', [AdController::class, 'toggle'])->name('ads.toggle');
    });
});

require __DIR__.'/auth.php';