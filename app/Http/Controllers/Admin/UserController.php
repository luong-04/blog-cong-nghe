<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        // Lấy danh sách user trừ admin ra
        $users = User::where('role', '!=', 'admin')->latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function approve(User $user)
    {
        // Nâng cấp user lên thành author
        $user->update(['role' => 'author']);
        return back()->with('success', 'Đã cấp quyền Tác giả cho ' . $user->name);
    }
    // Hủy quyền tác giả (Giáng cấp về User thường)
    public function revoke(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Không thể hủy quyền của Admin!');
        }

        $user->update(['role' => 'user']); // Reset về user thường
        return back()->with('success', 'Đã hủy quyền tác giả của ' . $user->name);
    }
}