<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Quản lý Thành viên</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <table class="min-w-full">
                    <thead>
                        <tr class="text-left font-bold">
                            <th class="pb-4">Tên</th>
                            <th class="pb-4">Email</th>
                            <th class="pb-4">Vai trò</th>
                            <th class="pb-4">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr class="border-t">
                            <td class="py-3">{{ $user->name }}</td>
                            <td class="py-3">{{ $user->email }}</td>
                            <td class="py-3">
                                @if($user->role == 'pending')
                                    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Chờ duyệt</span>
                                @elseif($user->role == 'author')
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Tác giả</span>
                                @else
                                    User
                                @endif
                            </td>
                            <td class="py-3">
                                @if($user->role == 'pending' || $user->role == 'user')
                                    <form action="{{ route('admin.users.approve', $user) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button class="text-blue-600 hover:underline">Duyệt làm Tác giả</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">{{ $users->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>