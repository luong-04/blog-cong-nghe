<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http; // [QUAN TRỌNG]: Để gọi API

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['category', 'user'])->latest()->paginate(10);
        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.posts.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:posts',
            'category_id' => 'required|exists:categories,id',
            'content' => 'required',
        ]);

        $imagePath = null;
        if ($request->hasFile('featured_image')) {
            $imagePath = $request->file('featured_image')->store('posts', 'public');
        }

        $post = Post::create([
            'user_id' => auth()->id(),
            'category_id' => $request->category_id,
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $request->input('content'),
            'featured_image' => $imagePath,
            'status' => $request->status ?? 'draft',
        ]);

        if ($request->tags) {
            $post->tags()->attach($request->tags);
        }

        return redirect()->route('admin.posts.index')->with('success', 'Đăng bài thành công!');
    }

    /**
     * [CHỨC NĂNG MỚI] Tạo nội dung bằng AI
     */
    public function generateContent(Request $request)
    {
        $request->validate(['title' => 'required|string']);
        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) return response()->json(['error' => 'Thiếu API Key'], 500);

        // [QUAN TRỌNG] Dùng model 'gemini-2.0-flash' vì nó CÓ trong danh sách của bạn
        $model = 'gemini-2.0-flash';
        
        $prompt = "Viết bài blog công nghệ chi tiết, chuẩn SEO, định dạng Markdown về chủ đề: " . $request->title;

        try {
            $response = Http::withoutVerifying()
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
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
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
                return response()->json(['content' => $text]);
            }

            return response()->json([
                'error' => 'Lỗi Google (' . $response->status() . ')',
                'model_used' => $model,
                'details' => $response->json()
            ], 500);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Lỗi Server: ' . $e->getMessage()], 500);
        }
    }

    public function edit(Post $post)
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.posts.edit', compact('post', 'categories', 'tags'));
    }

    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:posts,title,' . $post->id,
            'category_id' => 'required|exists:categories,id',
            'content' => 'required',
        ]);

        if ($request->hasFile('featured_image')) {
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $post->featured_image = $request->file('featured_image')->store('posts', 'public');
        }

        $post->update([
            'category_id' => $request->category_id,
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $request->input('content'),
            'status' => $request->status,
        ]);

        if ($request->tags) {
            $post->tags()->sync($request->tags);
        } else {
            $post->tags()->detach();
        }

        return redirect()->route('admin.posts.index')->with('success', 'Cập nhật bài viết thành công!');
    }

    public function destroy(Post $post)
    {
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }
        $post->tags()->detach();
        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'Đã xóa bài viết!');
    }
}