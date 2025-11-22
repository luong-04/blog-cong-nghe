<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Chỉnh sửa Thành viên</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Tên --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Tên hiển thị</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>

                    {{-- Email (Readonly - Không cho sửa email để tránh lỗi login) --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Email (Không thể sửa)</label>
                        <input type="email" value="{{ $user->email }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-500 cursor-not-allowed" readonly>
                    </div>

                    {{-- Phân quyền --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Vai trò & Quyền hạn</label>
                        <select name="role" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>Thành viên (Chỉ xem)</option>
                            <option value="pending" {{ $user->role == 'pending' ? 'selected' : '' }}>Đang chờ duyệt</option>
                            <option value="author" {{ $user->role == 'author' ? 'selected' : '' }}>Tác giả (Được đăng bài)</option>
                            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Quản trị viên (Full quyền)</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Lưu ý: Cấp quyền Admin sẽ cho phép người này quản lý toàn bộ hệ thống.</p>
                    </div>

                    {{-- Đổi mật khẩu --}}
                    <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                        <label class="block text-sm font-medium text-gray-700">Đặt lại Mật khẩu (Tùy chọn)</label>
                        <input type="password" name="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Chỉ nhập nếu muốn đổi mật khẩu mới...">
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('admin.users.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded text-sm">Hủy</a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-bold">Lưu thay đổi</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>