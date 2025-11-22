<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Quản lý Thành viên') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto w-full">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr class="border-t hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            @if($user->avatar)
                                                <img class="h-10 w-10 rounded-full object-cover mr-3" src="{{ asset('storage/' . $user->avatar) }}">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold mr-3">{{ substr($user->name, 0, 1) }}</div>
                                            @endif
                                            <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">{{ $user->email }}</td>
                                    <td class="px-6 py-4">
                                        @if($user->role == 'admin')
                                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-bold">Admin</span>
                                        @elseif($user->role == 'author')
                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-bold">Tác giả</span>
                                        @elseif($user->role == 'pending')
                                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-bold">Chờ duyệt</span>
                                        @else
                                            <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs">Thành viên</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        {{-- Nút Sửa --}}
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3 font-bold">Sửa / Cấp quyền</a>
                                        
                                        {{-- Nút Xóa --}}
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn chắc chắn muốn xóa tài khoản này? Hành động không thể hoàn tác!');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-bold">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>