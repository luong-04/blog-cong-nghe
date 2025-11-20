<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Danh sách người dùng (chỉ hiện user và pending)
    public function index()
    {
        $users = User::whereIn('role', ['user', 'pending', 'author'])->latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    // Duyệt yêu cầu lên làm Tác giả
    public function approve(User $user)
    {
        $user->update(['role' => 'author']);
        return back()->with('success', 'Đã duyệt thành viên này thành Tác giả!');
    }
}