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
    public function index(Request $request)
    {
        $query = Post::with(['category', 'user']);

        // 1. PHÂN QUYỀN: Nếu không phải Admin, chỉ được xem bài của chính mình
        if ($request->user()->role !== 'admin') {
            $query->where('user_id', $request->user()->id);
        }

        // 2. TÌM KIẾM: Nếu có từ khóa search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
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
     * [CHỨC NĂNG MỚI] Tạo nội dung bằng AI
     */
    public function generateContent(Request $request)
    {
        $request->validate(['title' => 'required|string']);
        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) return response()->json(['error' => 'Thiếu API Key'], 500);

        $model = 'gemini-2.0-flash';
        // PROMPT CHUYÊN NGHIỆP
        $prompt = "
        Đóng vai là một Reviewer công nghệ chuyên nghiệp, khách quan và am hiểu sâu sắc.
        Hãy viết một bài đánh giá chi tiết về sản phẩm: '{$request->title}'.
        
        YÊU CẦU VỀ NỘI DUNG & CẤU TRÚC (BẮT BUỘC):
        1.  **Mở đầu:** Dẫn dắt vấn đề hấp dẫn, nêu bật điểm đặc biệt nhất của sản phẩm.
        2.  **Thiết kế:** Đánh giá cảm giác cầm nắm, chất liệu, màu sắc.
        3.  **Màn hình:** Đánh giá độ sắc nét, tần số quét, trải nghiệm xem phim/chơi game.
        4.  **Hiệu năng:** Test game thực tế (Liên Quân, PUBG...), nhiệt độ, đa nhiệm.
        5.  **Camera:** Đánh giá ảnh chụp đủ sáng, thiếu sáng, quay video.
        6.  **Pin & Sạc:** Thời gian sử dụng thực tế (On-screen), tốc độ sạc.
        7.  **Tổng kết:** Nêu rõ Ưu điểm/Nhược điểm và ai nên mua sản phẩm này.

        YÊU CẦU VỀ ĐỊNH DẠNG (HTML ONLY):
        - Trả về mã HTML chuẩn (không dùng Markdown).
        - Sử dụng thẻ <h2> cho các mục lớn (Thiết kế, Hiệu năng...).
        - Sử dụng thẻ <h3> cho các ý nhỏ hơn.
        - Sử dụng thẻ <strong> hoặc <b> để bôi đậm các thông số kỹ thuật quan trọng (VD: Snapdragon 8 Gen 2, 5000mAh...).
        - Sử dụng thẻ <ul> và <li> cho danh sách thông số hoặc ưu/nhược điểm.
        - Chèn 3-4 thẻ <img> xen kẽ vào bài viết dùng link Pollinations:
          <img src='https://image.pollinations.ai/prompt/{từ_khóa_tiếng_anh_về_đoạn_này}?width=1280&height=720&nologo=true' alt='Mô tả ảnh'>
        
        Lưu ý: Giọng văn phải tự nhiên, lôi cuốn, như đang trò chuyện với người đọc. Không dùng các câu rập khuôn của AI.
        ";

        try {
            $response = Http::withoutVerifying()
                ->timeout(60) 
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'safetySettings' => [
                        ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_NONE'],
                        ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_NONE'],
                        ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_NONE'],
                        ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_NONE'],
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
                
                // Xóa các ký tự thừa nếu AI lỡ thêm vào
                $content = str_replace(['```html', '```'], '', $content);

                if ($content) {
                    // Tạo thêm 1 ảnh đại diện để lưu vào database
                    $imagePrompt = urlencode($request->title . " tech review, realistic, 8k, cinematic lighting");
                    $coverImage = "https://image.pollinations.ai/prompt/{$imagePrompt}?width=1280&height=720&nologo=true";

                    return response()->json([
                        'content' => $content,
                        'image_url' => $coverImage
                    ]);
                }
            }
            
            return response()->json(['error' => 'Lỗi Google', 'details' => $response->json()], 500);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Lỗi Server: ' . $e->getMessage()], 500);
        }
    }

    public function edit(Post $post)
    {
        // CHẶN: Author không được sửa bài của người khác
        if (auth()->user()->role !== 'admin' && $post->user_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền chỉnh sửa bài viết này.');
        }

        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.posts.edit', compact('post', 'categories', 'tags'));
    }

    public function update(Request $request, Post $post)
    {
        // CHẶN
        if (auth()->user()->role !== 'admin' && $post->user_id !== auth()->id()) {
            abort(403);
        }
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
        // CHẶN: Author không được xóa bài người khác (Admin được xóa tất)
        if (auth()->user()->role !== 'admin' && $post->user_id !== auth()->id()) {
            abort(403, 'Bạn không được phép xóa bài viết này.');
        }

        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }
        $post->tags()->detach();
        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'Đã xóa bài viết!');
    }
}