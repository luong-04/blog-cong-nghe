<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Lưu bình luận mới
     */
    public function store(Request $request, Post $post)
    {
        $rules = [
            'content' => 'required|string|max:1000',
        ];

        // Nếu chưa đăng nhập thì validate thêm tên và email
        if (!Auth::check()) {
            $rules['author_name'] = 'required|string|max:255';
            $rules['author_email'] = 'required|email|max:255';
        }

        $request->validate($rules);

        // Chuẩn bị dữ liệu
        // Dùng input() an toàn hơn truy cập trực tiếp
        $data = [
            'content' => $request->input('content'), 
            'post_id' => $post->id,
        ];

        if (Auth::check()) {
            $user = Auth::user();
            $data['user_id'] = Auth::id(); // Dùng Auth::id() để tránh lỗi IDE
            $data['author_name'] = $user->name;
            $data['author_email'] = $user->email;
            $data['status'] = 'approved'; 
        } else {
            $data['author_name'] = $request->input('author_name');
            $data['author_email'] = $request->input('author_email');
            $data['status'] = 'pending'; 
        }

        $comment = Comment::create($data);

        $html = view('partials.comment_item', ['comment' => $comment])->render();

        return response()->json([
            'status' => 'success',
            'html' => $html,
            'message' => 'Gửi bình luận thành công!'
        ]);
    }

    /**
     * Cập nhật bình luận
     */
    public function update(Request $request, Comment $comment)
    {
        // Check quyền: Auth::id()
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['status' => 'error', 'message' => 'Bạn không có quyền sửa.'], 403);
        }

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        // SỬA LỖI Ở ĐÂY: Dùng input('content') thay vì $request->content
        $comment->update([
            'content' => $request->input('content')
        ]);

        return response()->json([
            'status' => 'success',
            'content' => $comment->content,
            'message' => 'Cập nhật thành công!'
        ]);
    }

    /**
     * Xóa bình luận
     */
    public function destroy(Comment $comment)
    {
        $currentUserId = Auth::id();
        $user = Auth::user();

        // 1. Admin
        if ($user && $user->role === 'admin') {
            $comment->delete();
            return response()->json(['status' => 'success', 'message' => 'Đã xóa bình luận.']);
        }

        // 2. Chính chủ
        if ($comment->user_id === $currentUserId) {
            $comment->delete();
            return response()->json(['status' => 'success', 'message' => 'Đã xóa bình luận.']);
        }

        // 3. Tác giả bài viết
        if ($comment->post && $comment->post->user_id === $currentUserId) {
            $comment->delete();
            return response()->json(['status' => 'success', 'message' => 'Đã xóa bình luận.']);
        }

        return response()->json(['status' => 'error', 'message' => 'Bạn không có quyền xóa.'], 403);
    }

    /**
     * Duyệt bình luận
     */
    public function approve(Comment $comment)
    {
        $currentUserId = Auth::id();
        $user = Auth::user();

        $isPostAuthor = $comment->post && $comment->post->user_id === $currentUserId;

        if (($user && $user->role !== 'admin') && !$isPostAuthor) {
            return response()->json(['status' => 'error', 'message' => 'Không có quyền duyệt.'], 403);
        }

        $comment->update(['status' => 'approved']);

        return response()->json([
            'status' => 'success',
            'message' => 'Đã duyệt bình luận.'
        ]);
    }
}