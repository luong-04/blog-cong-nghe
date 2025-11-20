<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Admin\UserController; // [QUAN TRỌNG] Import UserController để sửa lỗi Unknown class
use App\Models\Post;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

// --- ROUTE DÀNH CHO KHÁCH (PUBLIC) ---

// Trang chủ
Route::get('/', [HomeController::class, 'index'])->name('home');

// Trang xem chi tiết bài viết
Route::get('/bai-viet/{slug}', [HomeController::class, 'show'])->name('posts.show');

// Route gửi bình luận
Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');


// --- ROUTE AUTH & PROFILE ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Route đăng ký làm tác giả
    Route::post('/request-author', [ProfileController::class, 'requestAuthor'])->name('profile.request-author');
});


// --- ROUTE DASHBOARD (Chỉ Admin & Author mới được vào) ---
// User thường sẽ bị chặn bởi middleware 'role:admin,author'
Route::get('/dashboard', function () {
    $totalPosts = Post::count();
    $latestPosts = Post::with('category')->latest()->take(5)->get();
    return view('dashboard', compact('totalPosts', 'latestPosts'));
})->middleware(['auth', 'verified', 'role:admin,author'])->name('dashboard');


// --- ROUTE ADMIN (Khu vực quản trị) ---
Route::middleware(['auth', 'verified', 'role:admin,author'])->prefix('admin')->name('admin.')->group(function () {
    
    // Quản lý Danh mục
    Route::resource('categories', CategoryController::class);

    // Quản lý Bài viết
    Route::resource('posts', PostController::class);
    
    // Route xử lý AI
    Route::post('posts/generate-content', [PostController::class, 'generateContent'])->name('posts.generate');

    // [MỚI] Quản lý User (Chỉ Admin được vào duyệt)
    // Dùng middleware 'role:admin' để Author cũng không vào được
    Route::middleware('role:admin')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::patch('users/{user}/approve', [UserController::class, 'approve'])->name('users.approve');
    });
});

// Route Test Key (Giữ lại để kiểm tra nếu cần)
Route::get('/check-key', function () {
    $apiKey = env('GEMINI_API_KEY');
    if (!$apiKey) return "❌ LỖI: Không tìm thấy Key trong file .env";

    $response = Http::withoutVerifying()->get("https://generativelanguage.googleapis.com/v1beta/models?key={$apiKey}");

    if ($response->successful()) {
        return [
            "TRẠNG THÁI" => "✅ KEY HOẠT ĐỘNG TỐT!",
            "Danh_sách_Model" => collect($response->json()['models'])->pluck('name')
        ];
    }
    return ["TRẠNG THÁI" => "❌ KEY BỊ TỪ CHỐI", "Chi_tiết" => $response->json()];
});

require __DIR__.'/auth.php';