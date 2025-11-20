<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\CategoryController; // Import CategoryController
use App\Http\Controllers\Admin\PostController;     // [MỚI] Import PostController
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController; //router trang chủ
use App\Http\Controllers\CommentController; //comment
use App\Models\Post;

Route::get('/', function () {
    return view('welcome');
});
// --- ROUTE DÀNH CHO KHÁCH (PUBLIC) ---

// Trang chủ
Route::get('/', [HomeController::class, 'index'])->name('home');

// Trang xem chi tiết bài viết (slug là đường dẫn thân thiện)
Route::get('/bai-viet/{slug}', [HomeController::class, 'show'])->name('posts.show');

// Route gửi bình luận
Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');

// --- ROUTE ADMIN ----
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
    //xử lý AI
    Route::post('posts/generate-content', [PostController::class, 'generateContent'])->name('posts.generate');
});
Route::get('/dashboard', function () {
    $totalPosts = Post::count();
    $latestPosts = Post::with('category')->latest()->take(5)->get();
    
    return view('dashboard', compact('totalPosts', 'latestPosts'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/check-key', function () {
    $apiKey = env('GEMINI_API_KEY');

    if (!$apiKey) {
        return "❌ LỖI: Không tìm thấy Key trong file .env";
    }

    // 1. Kiểm tra xem Key có lấy được danh sách Model không
    $response = Http::withoutVerifying()->get("https://generativelanguage.googleapis.com/v1beta/models?key={$apiKey}");

    if ($response->successful()) {
        return [
            "TRẠNG THÁI" => "✅ KEY HOẠT ĐỘNG TỐT!",
            "Key_đang_dùng" => substr($apiKey, 0, 8) . '...', // Kiểm tra xem có đúng key mới không
            "Danh_sách_Model" => collect($response->json()['models'])->pluck('name')
        ];
    }

    // 2. Nếu lỗi, in ra nguyên nhân
    return [
        "TRẠNG THÁI" => "❌ KEY BỊ TỪ CHỐI",
        "Mã_Lỗi" => $response->status(),
        "Chi_tiết_từ_Google" => $response->json()
    ];
});

require __DIR__.'/auth.php';