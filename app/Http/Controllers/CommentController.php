<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    // 1. Gửi bình luận
    public function store(Request $request, $postId)
    {
        $post = Post::findOrFail($postId);

        // Validate...
        $request->validate([
            'content' => 'required|string|max:1000',
            'author_name' => auth()->check() ? 'nullable' : 'required|string|max:255',
            'author_email' => auth()->check() ? 'nullable' : 'required|email|max:255',
        ]);

        // Logic duyệt
        $status = 'pending';
        if (Auth::check()) {
            if (Auth::user()->role === 'admin' || Auth::id() === $post->user_id) {
                $status = 'approved';
            }
        }

        $data = [
            'post_id' => $post->id,
            'content' => $request->input('content'),
            'status' => $status,
        ];

        if (Auth::check()) {
            $data['user_id'] = Auth::id();
            $data['author_name'] = Auth::user()->name;
            $data['author_email'] = Auth::user()->email;
        } else {
            $data['author_name'] = $request->author_name;
            $data['author_email'] = $request->author_email;
        }

        $comment = Comment::create($data);

        // NẾU LÀ AJAX -> TRẢ VỀ HTML ĐỂ CHÈN VÀO TRANG
        if ($request->ajax()) {
            // Render file partial vừa tạo ở Bước 1
            $html = view('partials.comment_item', compact('comment'))->render();
            
            return response()->json([
                'status' => 'success',
                'html' => $html,
                'message' => ($status === 'approved') ? 'Đã đăng bình luận!' : 'Đang chờ duyệt.'
            ]);
        }

        return back()->with('success', 'Đã gửi bình luận!');
    }

    // 2. Cập nhật bình luận (Chỉ người viết mới được sửa)
    public function update(Request $request, Comment $comment)
    {
        if (Auth::id() !== $comment->user_id) {
            abort(403, 'Bạn không có quyền sửa bình luận này');
        }
        
        $request->validate(['content' => 'required|string|max:1000']);
        
        $comment->update(['content' => $request->input('content')]);
        
        return back()->with('success', 'Đã cập nhật nội dung bình luận!');
    }

    // 3. Xóa bình luận (Chủ comment, Tác giả bài viết, hoặc Admin được xóa)
    public function destroy(Comment $comment)
    {
        $post = $comment->post;
        
        // Kiểm tra quyền: Admin OR Tác giả bài viết OR Chủ comment
        $canDelete = Auth::check() && (
            Auth::user()->role === 'admin' || 
            Auth::id() === $post->user_id || 
            Auth::id() === $comment->user_id
        );

        if (!$canDelete) {
            abort(403, 'Bạn không được phép xóa bình luận này');
        }

        $comment->delete();
        return back()->with('success', 'Đã xóa bình luận!');
    }

    // 4. Duyệt bình luận (Chỉ Tác giả bài viết hoặc Admin)
    public function approve(Comment $comment)
    {
        $post = $comment->post;

        $canApprove = Auth::check() && (
            Auth::user()->role === 'admin' || 
            Auth::id() === $post->user_id
        );

        if (!$canApprove) {
            abort(403, 'Chỉ tác giả mới được duyệt bình luận');
        }

        $comment->update(['status' => 'approved']);
        return back()->with('success', 'Đã duyệt bình luận này!');
    }
}