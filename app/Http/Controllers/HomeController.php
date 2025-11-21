<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // 1. Lấy danh mục cho menu
        $categories = Category::withCount('posts')->get();

        // 2. Khởi tạo query cơ bản (chỉ lấy bài đã Public)
        $query = Post::with(['user', 'category'])->where('status', 'published');

        // --- TRƯỜNG HỢP 1: CÓ TÌM KIẾM ---
        if ($request->has('search') && $request->search != '') {
            $posts = $query->where('title', 'like', "%{$request->search}%")
                           ->latest()
                           ->paginate(12);
            return view('home', compact('posts', 'categories'))->with('isSearch', true);
        }

        // --- TRƯỜNG HỢP 2: TRANG CHỦ (GIAO DIỆN TẠP CHÍ) ---
        
        // a. Lấy 1 bài mới nhất làm Hero (Bài to đùng trên cùng)
        $heroPost = Post::with(['user', 'category'])
            ->where('status', 'published')
            ->latest()
            ->first();

        // b. Lấy 2 bài tiếp theo làm Featured (Nằm bên cạnh bài Hero)
        $featuredPosts = collect();
        if ($heroPost) {
            $featuredPosts = Post::with(['user', 'category'])
                ->where('status', 'published')
                ->where('id', '!=', $heroPost->id)
                ->latest()
                ->take(2)
                ->get();
        }

        // c. Lấy các bài còn lại (Recent News)
        $excludeIds = collect([$heroPost->id ?? 0])->merge($featuredPosts->pluck('id'));
        
        $recentPosts = Post::with(['user', 'category'])
            ->where('status', 'published')
            ->whereNotIn('id', $excludeIds)
            ->latest()
            ->paginate(9);

        return view('home', compact('categories', 'heroPost', 'featuredPosts', 'recentPosts'));
    }

    public function show($slug)
    {
        $post = Post::where('slug', $slug)->where('status', 'published')->firstOrFail();
        $post->increment('views');
        return view('posts.show', compact('post'));
    }
    
    public function category($slug)
    {
        $currentCategory = Category::where('slug', $slug)->firstOrFail();
        $categories = Category::withCount('posts')->get();
        
        $posts = Post::with(['user', 'category'])
            ->where('category_id', $currentCategory->id)
            ->where('status', 'published')
            ->latest()
            ->paginate(12);

        return view('home', compact('posts', 'categories', 'currentCategory'))->with('isCategory', true);
    }
}