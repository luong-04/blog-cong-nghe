<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class PostController extends Controller
{
    /**
     * Hiển thị danh sách bài viết
     */
    public function index()
    {
        // Lấy bài viết kèm tác giả và danh mục để tránh query N+1
        $posts = Post::with(['category', 'user'])->latest()->paginate(10);
        return view('admin.posts.index', compact('posts'));
    }

    /**
     * Hiển thị form tạo bài viết mới
     */
    public function create()
    {
        $categories = Category::all(); // Lấy danh mục để hiển thị select box
        $tags = Tag::all();            // Lấy thẻ để hiển thị
        return view('admin.posts.create', compact('categories', 'tags'));
    }

    /**
     * Lưu bài viết vào CSDL
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:posts',
            'category_id' => 'required|exists:categories,id',
            'content' => 'required', // Nội dung không được để trống
            'featured_image' => 'nullable|image|max:2048', // Ảnh tối đa 2MB
        ]);

        // 1. Xử lý upload ảnh (nếu có)
        $imagePath = null;
        if ($request->hasFile('featured_image')) {
            $imagePath = $request->file('featured_image')->store('posts', 'public');
        }

        // 2. Tạo bài viết
        $post = Post::create([
            'user_id' => auth()->id(),
            'category_id' => $request->category_id,
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            
            // [QUAN TRỌNG] Đã sửa lỗi tại đây: dùng input('content')
            'content' => $request->input('content'), 
            
            'featured_image' => $imagePath,
            'status' => $request->status ?? 'draft',
        ]);

        // 3. Lưu tags (quan hệ nhiều-nhiều)
        if ($request->tags) {
            $post->tags()->attach($request->tags);
        }

        return redirect()->route('admin.posts.index')
                         ->with('success', 'Đăng bài thành công!');
    }

    //AI tạo bài viết
    public function generateContent(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
        ]);

        $apiKey = env('GEMINI_API_KEY');
        $title = $request->title;
        
        // Prompt gửi cho AI
        $prompt = "Viết một bài blog công nghệ chi tiết, chuẩn SEO, có các thẻ heading (h2, h3) định dạng Markdown về chủ đề: " . $title;

        // Gọi API Gemini (Sử dụng model gemini-1.5-flash cho nhanh và rẻ)
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ]);

        if ($response->successful()) {
            $data = $response->json();
            // Lấy text từ phản hồi của Gemini
            $generatedText = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Không thể tạo nội dung.';
            return response()->json(['content' => $generatedText]);
        }

        return response()->json(['error' => 'Lỗi khi gọi Gemini API'], 500);
    }
    /**
     * Hiển thị form chỉnh sửa
     */
    public function edit(Post $post)
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.posts.edit', compact('post', 'categories', 'tags'));
    }

    /**
     * Cập nhật bài viết
     */
    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:posts,title,' . $post->id,
            'category_id' => 'required|exists:categories,id',
            'content' => 'required',
        ]);

        // Xử lý ảnh mới nếu có
        if ($request->hasFile('featured_image')) {
            // Xóa ảnh cũ nếu tồn tại
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $post->featured_image = $request->file('featured_image')->store('posts', 'public');
        }

        $post->update([
            'category_id' => $request->category_id,
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            
            // [QUAN TRỌNG] Dùng input() ở đây nữa
            'content' => $request->input('content'), 
            
            'status' => $request->status,
        ]);

        // Cập nhật tags (sync sẽ xóa tags cũ và thêm tags mới)
        if ($request->tags) {
            $post->tags()->sync($request->tags);
        } else {
            $post->tags()->detach();
        }

        return redirect()->route('admin.posts.index')
                         ->with('success', 'Cập nhật bài viết thành công!');
    }

    /**
     * Xóa bài viết
     */
    public function destroy(Post $post)
    {
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }
        $post->tags()->detach(); // Xóa liên kết tags
        $post->delete();

        return redirect()->route('admin.posts.index')
                         ->with('success', 'Đã xóa bài viết!');
    }
}