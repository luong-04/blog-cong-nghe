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
    public function index(Request $request)
    {
        $query = Post::with(['category', 'user']);

        // Phân quyền: Author chỉ thấy bài mình
        if ($request->user()->role !== 'admin') {
            $query->where('user_id', $request->user()->id);
        }

        // Tìm kiếm
        if ($request->has('search') && $request->search != '') {
            $query->where('title', 'like', "%{$request->search}%");
        }

        $posts = $query->latest()->paginate(10)->appends(['search' => $request->search]);
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
     * [AI THÔNG MINH] Tự động nhận diện chủ đề để viết bài
     */
    public function generateContent(Request $request)
    {
        $request->validate(['title' => 'required|string']);
        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) return response()->json(['error' => 'Thiếu API Key'], 500);

        $model = 'gemini-2.0-flash';

        // PROMPT ĐA NĂNG: Xử lý mọi loại chủ đề
        $prompt = "
        Bạn là một biên tập viên công nghệ chuyên nghiệp (giọng văn hiện đại, tinh tế như VnExpress Số Hóa, TinhTe).
        Nhiệm vụ: Viết bài chi tiết về chủ đề: '{$request->title}'.

        BƯỚC 1: PHÂN TÍCH & CHỌN CẤU TRÚC
        Hãy tự động nhận diện xem tiêu đề này thuộc thể loại nào để chọn cách viết phù hợp nhất:
        - Loại 1: Review Sản phẩm (Điện thoại, Laptop, Loa...) -> Cấu trúc: Mở đầu, Thiết kế, Màn hình/Âm thanh, Hiệu năng, Pin, Kết luận.
        - Loại 2: Thủ thuật / Hướng dẫn (Cách cài Win, Fix lỗi...) -> Cấu trúc: Vấn đề là gì?, Các bước thực hiện (Bước 1, 2, 3...), Lưu ý quan trọng.
        - Loại 3: Tin tức / Xu hướng (AI, Blockchain, Sự kiện ra mắt...) -> Cấu trúc: Bối cảnh sự kiện, Chi tiết nổi bật, Tác động đến thị trường, Nhận định tương lai.
        - Loại 4: Top List (Top 5 điện thoại giá rẻ...) -> Cấu trúc: Mở đầu, Danh sách từng món (kèm ưu/nhược điểm), Tư vấn chọn mua.

        BƯỚC 2: ĐỊNH DẠNG JSON (BẮT BUỘC)
        Chỉ trả về duy nhất 1 JSON object (không thêm lời dẫn):
        {
            \"html_content\": \"(Mã HTML nội dung bài viết)\",
            \"cover_image_prompt\": \"(Câu lệnh tiếng Anh để vẽ ảnh bìa)\"
        }

        YÊU CẦU VỀ NỘI DUNG HTML:
        1. Sử dụng thẻ <h2>, <h3>, <p>, <ul>, <li>. KHÔNG dùng Markdown.
        2. Sử dụng Class Tailwind CSS để trình bày đẹp mắt:
           - Tiêu đề H2: <h2 class='text-2xl font-bold text-indigo-700 mt-8 mb-4 border-l-4 border-indigo-500 pl-4'>Nội dung thẻ H2</h2>
           - Tiêu đề H3: <h3 class='text-xl font-semibold text-gray-800 mt-6 mb-2'>Nội dung thẻ H3</h3>
           - Đoạn văn: <p class='mb-4 text-gray-600 leading-relaxed text-justify'>...</p>
           - Danh sách: <ul class='list-disc list-inside mb-4 space-y-2 bg-gray-50 p-4 rounded-lg border border-gray-100'>...</ul>
        3. CHÈN ẢNH MINH HỌA THÔNG MINH:
           - Tự động chèn 2-3 thẻ <img> xen kẽ vào giữa bài viết (sau mỗi phần chính).
           - Link ảnh: https://image.pollinations.ai/prompt/{KEYWORD_TIẾNG_ANH}?width=1024&height=600&nologo=true
           - Quan trọng: {KEYWORD_TIẾNG_ANH} phải mô tả đúng nội dung đoạn đó. Ví dụ đoạn nói về 'CPU nhiệt độ cao' thì keyword là 'cpu processor overheating closeup realistic'.

        YÊU CẦU VỀ ẢNH BÌA (cover_image_prompt):
        - Viết một câu prompt tiếng Anh thật nghệ thuật, sát với tiêu đề bài viết (VD: 'MacBook Pro M3 on wooden desk, cinematic lighting, 8k').
        ";

        try {
            $response = Http::withoutVerifying()
                ->timeout(60) 
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => ['responseMimeType' => 'application/json']
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $rawText = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
                $jsonResult = json_decode($rawText, true);

                if ($jsonResult) {
                    $content = $jsonResult['html_content'] ?? '';
                    $englishPrompt = $jsonResult['cover_image_prompt'] ?? $request->title;
                    
                    // Xử lý prompt ảnh bìa để URL hợp lệ
                    $encodedPrompt = urlencode($englishPrompt . ", 4k, realistic, tech style");
                    $coverImage = "https://image.pollinations.ai/prompt/{$encodedPrompt}?width=1280&height=720&nologo=true";

                    return response()->json([
                        'content' => $content,
                        'image_url' => $coverImage
                    ]);
                }
            }

            return response()->json(['error' => 'Lỗi xử lý từ AI', 'raw' => $response->json()], 500);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Lỗi Server: ' . $e->getMessage()], 500);
        }
    }

    // ... (Các hàm edit, update, destroy GIỮ NGUYÊN như cũ) ...
    public function edit(Post $post)
    {
        if (auth()->user()->role !== 'admin' && $post->user_id !== auth()->id()) abort(403);
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.posts.edit', compact('post', 'categories', 'tags'));
    }

    public function update(Request $request, Post $post)
    {
        if (auth()->user()->role !== 'admin' && $post->user_id !== auth()->id()) abort(403);
        
        $request->validate([
            'title' => 'required|string|max:255|unique:posts,title,' . $post->id,
            'category_id' => 'required|exists:categories,id',
            'content' => 'required',
        ]);

        if ($request->hasFile('featured_image')) {
            if ($post->featured_image) Storage::disk('public')->delete($post->featured_image);
            $post->featured_image = $request->file('featured_image')->store('posts', 'public');
        }

        $post->update([
            'category_id' => $request->category_id,
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $request->input('content'),
            'status' => $request->status,
        ]);
        
        if ($request->tags) $post->tags()->sync($request->tags);

        return redirect()->route('admin.posts.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy(Post $post)
    {
        if (auth()->user()->role !== 'admin' && $post->user_id !== auth()->id()) abort(403);
        if ($post->featured_image) Storage::disk('public')->delete($post->featured_image);
        $post->tags()->detach();
        $post->delete();
        return redirect()->route('admin.posts.index')->with('success', 'Đã xóa!');
    }
}