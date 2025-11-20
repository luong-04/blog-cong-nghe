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
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($users as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap font-bold">{{ $user->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->role == 'pending')
                                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-bold">Đang chờ duyệt</span>
                                    @elseif($user->role == 'author')
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-bold">Tác giả</span>
                                    @elseif($user->role == 'user')
                                        <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs">Thành viên</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{-- Chỉ hiện nút duyệt nếu đang chờ hoặc là user thường --}}
                                    @if($user->role == 'pending' || $user->role == 'user')
                                        <form action="{{ route('admin.users.approve', $user->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-white bg-indigo-600 hover:bg-indigo-700 px-3 py-1 rounded text-sm shadow transition">
                                                ✅ Duyệt làm Tác giả
                                            </button>
                                        </form>
                                    @elseif($user->role == 'author')
                                        <span class="text-green-600 text-sm">✓ Đã cấp quyền</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>