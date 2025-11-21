<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Ad;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::withCount('posts')->get();
        $ads = Ad::where('is_active', true)->latest()->get();
        $query = Post::with(['user', 'category'])->where('status', 'published');

        if ($request->has('search') && $request->search != '') {
            $posts = $query->where('title', 'like', "%{$request->search}%")->latest()->paginate(12);
            return view('home', compact('posts', 'categories', 'ads'))->with('isSearch', true);
        }

        $heroPost = Post::with(['user', 'category'])->where('status', 'published')->latest()->first();
        $featuredPosts = collect();
        if ($heroPost) {
            $featuredPosts = Post::with(['user', 'category'])
                ->where('status', 'published')
                ->where('id', '!=', $heroPost->id)
                ->latest()->take(2)->get();
        }

        $excludeIds = collect([$heroPost->id ?? 0])->merge($featuredPosts->pluck('id'));
        $recentPosts = Post::with(['user', 'category'])
            ->where('status', 'published')
            ->whereNotIn('id', $excludeIds)
            ->latest()->paginate(9);

        return view('home', compact('categories', 'heroPost', 'featuredPosts', 'recentPosts', 'ads'));
    }

    // [CẬP NHẬT] Hàm Show giờ đây đã lấy đủ dữ liệu cho Header/Footer/Banner
    public function show($slug)
    {
        $post = Post::where('slug', $slug)->where('status', 'published')->firstOrFail();
        $post->increment('views');

        // 1. Lấy Danh mục cho Menu
        $categories = Category::withCount('posts')->get();
        
        // 2. Lấy Quảng cáo cho Banner
        $ads = Ad::where('is_active', true)->latest()->get();

        // 3. Lấy bài viết liên quan
        $relatedPosts = Post::where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->where('status', 'published')
            ->take(3)->get();

        return view('posts.show', compact('post', 'categories', 'ads', 'relatedPosts'));
    }
    
    public function category($slug)
    {
        $currentCategory = Category::where('slug', $slug)->firstOrFail();
        $categories = Category::withCount('posts')->get();
        $ads = Ad::where('is_active', true)->latest()->get();
        
        $posts = Post::with(['user', 'category'])
            ->where('category_id', $currentCategory->id)
            ->where('status', 'published')
            ->latest()->paginate(12);

        return view('home', compact('posts', 'categories', 'currentCategory', 'ads'))->with('isCategory', true);
    }
}