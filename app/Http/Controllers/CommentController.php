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
        $request->validate(['content' => 'required|string|max:1000']);

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
            'author_name' => Auth::check() ? Auth::user()->name : ($request->author_name ?? 'Khách'),
            'author_email' => Auth::check() ? Auth::user()->email : ($request->author_email ?? 'guest@example.com'),
            'user_id' => Auth::id(),
        ];

        $comment = Comment::create($data);

        if ($request->ajax()) {
            $html = view('partials.comment_item', compact('comment'))->render();
            return response()->json([
                'status' => 'success',
                'html' => $html,
                'message' => ($status === 'approved') ? 'Đã đăng!' : 'Đang chờ duyệt.'
            ]);
        }

        return back();
    }

    // 2. Cập nhật (Sửa logic trả về content chuẩn)
    public function update(Request $request, Comment $comment)
    {
        if (Auth::id() !== $comment->user_id) {
            return response()->json(['status' => 'error', 'message' => 'Không có quyền'], 403);
        }
        
        $request->validate(['content' => 'required|string|max:1000']);
        
        // Cập nhật
        $comment->content = $request->input('content');
        $comment->save();
        
        return response()->json([
            'status' => 'success', 
            'content' => $comment->content // Trả về nội dung mới để JS cập nhật
        ]);
    }

    // 3. Xóa
    public function destroy(Comment $comment)
    {
        $post = $comment->post;
        $canDelete = Auth::check() && (
            Auth::user()->role === 'admin' || 
            Auth::id() === $post->user_id || 
            Auth::id() === $comment->user_id
        );

        if (!$canDelete) return response()->json(['status' => 'error'], 403);

        $comment->delete();
        return response()->json(['status' => 'success']);
    }

    // 4. Duyệt
    public function approve(Comment $comment)
    {
        $post = $comment->post;
        $canApprove = Auth::check() && (Auth::user()->role === 'admin' || Auth::id() === $post->user_id);

        if (!$canApprove) return response()->json(['status' => 'error'], 403);

        $comment->update(['status' => 'approved']);
        return response()->json(['status' => 'success']);
    }
}