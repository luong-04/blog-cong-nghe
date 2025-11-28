<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentManagerController extends Controller
{
    public function index()
    {
        $comments = Comment::with(['post', 'user'])->latest()->paginate(20);
        return view('admin.comments.index', compact('comments'));
    }

    // THÊM HÀM NÀY CHO ADMIN DUYỆT HÀNG LOẠT
    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $commentIds = $request->input('comment_ids', []);

        if (empty($commentIds)) return back()->with('error', 'Chưa chọn mục nào.');

        if ($action === 'approve') {
            Comment::whereIn('id', $commentIds)->update(['status' => 'approved']);
            return back()->with('success', 'Đã duyệt thành công.');
        } elseif ($action === 'delete') {
            Comment::whereIn('id', $commentIds)->delete();
            return back()->with('success', 'Đã xóa thành công.');
        }
        return back();
    }

    public function destroy($id)
    {
        Comment::findOrFail($id)->delete();
        return back()->with('success', 'Đã xóa bình luận!');
    }
}