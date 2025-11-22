<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    // 1. Gửi bình luận (AJAX)
    public function store(Request $request, $postId)
    {
        $post = Post::findOrFail($postId);
        $request->validate(['content' => 'required|string|max:1000']);

        // Tự động duyệt nếu là Admin hoặc Chủ bài viết
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

        // [QUAN TRỌNG] Nếu là AJAX thì trả về HTML để chèn vào trang
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

    // 2. Cập nhật (AJAX)
    public function update(Request $request, Comment $comment)
    {
        if (Auth::id() !== $comment->user_id) abort(403);
        
        $comment->update(['content' => $request->input('content')]);
        
        return response()->json([
            'status' => 'success', 
            'content' => $comment->content
        ]);
    }

    // 3. Xóa (AJAX)
    public function destroy(Comment $comment)
    {
        $post = $comment->post;
        $canDelete = Auth::check() && (
            Auth::user()->role === 'admin' || 
            Auth::id() === $post->user_id || 
            Auth::id() === $comment->user_id
        );

        if (!$canDelete) abort(403);

        $comment->delete();
        return response()->json(['status' => 'success']);
    }

    // 4. Duyệt (AJAX)
    public function approve(Comment $comment)
    {
        $post = $comment->post;
        $canApprove = Auth::check() && (Auth::user()->role === 'admin' || Auth::id() === $post->user_id);

        if (!$canApprove) abort(403);

        $comment->update(['status' => 'approved']);
        return response()->json(['status' => 'success']);
    }
}