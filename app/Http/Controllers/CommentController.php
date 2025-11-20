<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, $postId)
    {
        $post = Post::findOrFail($postId);

        // 1. Validate dữ liệu
        $request->validate([
            'content' => 'required|string|max:1000',
            'author_name' => auth()->check() ? 'nullable' : 'required|string|max:255',
            'author_email' => auth()->check() ? 'nullable' : 'required|email|max:255',
        ], [
            'content.required' => 'Nội dung bình luận không được để trống',
            'author_name.required' => 'Vui lòng nhập tên của bạn',
            'author_email.required' => 'Vui lòng nhập email'
        ]);

        // 2. Chuẩn bị dữ liệu
        $data = [
            'post_id' => $post->id,
            // [SỬA LỖI TẠI ĐÂY] Dùng input() để tránh xung đột
            'content' => $request->input('content'), 
            'status' => 'approved',
        ];

        if (auth()->check()) {
            $data['user_id'] = auth()->id();
            $data['author_name'] = auth()->user()->name;
            $data['author_email'] = auth()->user()->email;
        } else {
            $data['author_name'] = $request->author_name;
            $data['author_email'] = $request->author_email;
        }

        // 3. Lưu vào CSDL
        Comment::create($data);

        return back()->with('success', 'Bình luận của bạn đã được đăng!');
    }
}