<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Trang chủ: Hiển thị danh sách bài viết mới nhất
     */
    public function index(Request $request)
    {
        // Khởi tạo query lấy bài viết đã Public
        $query = Post::with(['user', 'category'])->where('status', 'published');

        // [TÍNH NĂNG TÌM KIẾM]
        // Nếu trên thanh địa chỉ có ?search=... thì lọc theo tiêu đề
        if ($request->has('search') && $request->search != '') {
            $keyword = $request->search;
            $query->where('title', 'like', "%{$keyword}%");
        }

        // Lấy dữ liệu và phân trang
        $posts = $query->latest()->paginate(9);
        
        // Giữ lại từ khóa tìm kiếm khi bấm chuyển trang (VD: trang 2, trang 3...)
        $posts->appends(['search' => $request->search]);

        return view('home', compact('posts'));
    }

    /**
     * Trang chi tiết bài viết
     */
    public function show($slug)
    {
        $post = Post::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        // Tăng lượt xem (nếu muốn)
        // $post->increment('views');

        return view('posts.show', compact('post'));
    }
}