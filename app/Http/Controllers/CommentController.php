<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    // --- 1. LƯU BÌNH LUẬN (SỬA LOGIC DUYỆT) ---
    public function store(Request $request, Post $post)
    {
        $rules = ['content' => 'required|string|max:1000'];
        if (!Auth::check()) {
            $rules['author_name'] = 'required|string|max:255';
            $rules['author_email'] = 'required|email|max:255';
        }
        $request->validate($rules);

        // Mặc định là chờ duyệt (cho cả khách và thành viên thường)
        $status = 'pending';
        $userId = null;
        $authorName = $request->input('author_name');
        $authorEmail = $request->input('author_email');

        if (Auth::check()) {
            $user = Auth::user();
            $userId = Auth::id();
            $authorName = $user->name;
            $authorEmail = $user->email;

            // Nếu là Admin hoặc Chính chủ bài viết comment vào bài mình -> Duyệt luôn
            if ($user->role === 'admin' || $post->user_id === $userId) {
                $status = 'approved';
            }
        }

        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => $userId,
            'author_name' => $authorName,
            'author_email' => $authorEmail,
            'content' => $request->input('content'),
            'status' => $status
        ]);

        $html = view('partials.comment_item', ['comment' => $comment])->render();

        return response()->json([
            'status' => 'success',
            'html' => $html,
            'comment_status' => $status,
            'message' => ($status === 'pending') ? 'Bình luận đang chờ duyệt.' : 'Đã gửi bình luận!'
        ]);
    }

    // --- 2. DASHBOARD QUẢN LÝ CHO AUTHOR ---
    public function authorIndex()
    {
        $userId = Auth::id();
        // Lấy comment thuộc các bài viết của Author này
        $comments = Comment::whereHas('post', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })
        ->with(['post', 'user'])
        ->latest()
        ->paginate(15);

        // Trả về view bạn vừa tạo ở Bước 1
        return view('author.comments.index', compact('comments'));
    }

    // --- 3. DUYỆT/XÓA HÀNG LOẠT (AUTHOR) ---
    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $commentIds = $request->input('comment_ids', []);

        if (empty($commentIds)) return back()->with('error', 'Chưa chọn bình luận nào.');

        // Chỉ tác động vào comment thuộc bài viết của Author này (Bảo mật)
        $validComments = Comment::whereIn('id', $commentIds)
            ->whereHas('post', function($q) {
                $q->where('user_id', Auth::id());
            });

        if ($action === 'approve') {
            $validComments->update(['status' => 'approved']);
            return back()->with('success', 'Đã duyệt các mục đã chọn.');
        } elseif ($action === 'delete') {
            $validComments->delete();
            return back()->with('success', 'Đã xóa các mục đã chọn.');
        }

        return back();
    }

    // --- CÁC HÀM AJAX CŨ (GIỮ NGUYÊN) ---
    public function update(Request $request, Comment $comment)
    {
        if ($comment->user_id !== Auth::id()) abort(403);
        $request->validate(['content' => 'required|string|max:1000']);
        $comment->update(['content' => $request->input('content')]);
        return response()->json(['status' => 'success', 'content' => $comment->content]);
    }

    public function destroy(Comment $comment)
    {
        $user = Auth::user();
        // Admin, chủ comment, hoặc tác giả bài viết được xóa
        if ($user->role === 'admin' || $comment->user_id === $user->id || $comment->post->user_id === $user->id) {
            $comment->delete();
            return response()->json(['status' => 'success']);
        }
        abort(403);
    }

    public function approve(Comment $comment)
    {
        $user = Auth::user();
        if ($user->role === 'admin' || $comment->post->user_id === $user->id) {
            $comment->update(['status' => 'approved']);
            return response()->json(['status' => 'success']);
        }
        abort(403);
    }
}