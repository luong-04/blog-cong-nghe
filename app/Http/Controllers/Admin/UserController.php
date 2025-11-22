<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // 1. Danh sách
    public function index(Request $request)
    {
        $query = User::query();

        // Tìm kiếm user
        if ($request->has('search')) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
        }

        // Không cho phép Admin tự thấy mình trong danh sách xóa (để an toàn)
        $users = $query->where('id', '!=', auth()->id())
                       ->latest()
                       ->paginate(10);
                       
        return view('admin.users.index', compact('users'));
    }

    // 2. Form Sửa (Cấp quyền, đổi pass)
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    // 3. Lưu cập nhật
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|in:user,author,admin,pending',
            'password' => 'nullable|string|min:8', // Mật khẩu không bắt buộc nhập
        ]);

        $data = [
            'name' => $request->name,
            'role' => $request->role,
        ];

        // Nếu Admin nhập mật khẩu mới thì mới đổi, không thì giữ nguyên
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Cập nhật tài khoản thành công!');
    }

    // 4. Xóa tài khoản
    public function destroy(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Không thể xóa tài khoản Admin!');
        }
        
        // Xóa ảnh avatar nếu có
        if ($user->avatar) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
        }
        
        $user->delete();
        return back()->with('success', 'Đã xóa người dùng!');
    }
}