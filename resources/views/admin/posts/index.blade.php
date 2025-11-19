<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Quản lý Bài viết') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-end mb-4">
                    <a href="{{ route('admin.posts.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Viết bài mới</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="bg-gray-50 dark:bg-gray-700 uppercase">
                            <tr>
                                <th class="px-4 py-3">Hình ảnh</th>
                                <th class="px-4 py-3">Tiêu đề</th>
                                <th class="px-4 py-3">Danh mục</th>
                                <th class="px-4 py-3">Tác giả</th>
                                <th class="px-4 py-3">Trạng thái</th>
                                <th class="px-4 py-3 text-right">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($posts as $post)
                                <tr class="border-b dark:border-gray-700">
                                    <td class="px-4 py-3">
                                        @if($post->featured_image)
                                            <img src="{{ asset('storage/' . $post->featured_image) }}" class="w-16 h-16 object-cover rounded">
                                        @else
                                            <span class="text-gray-400">Không có ảnh</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $post->title }}</td>
                                    <td class="px-4 py-3">{{ $post->category->name ?? 'Chưa phân loại' }}</td>
                                    <td class="px-4 py-3">{{ $post->user->name }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded text-xs {{ $post->status == 'published' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $post->status == 'published' ? 'Công khai' : 'Nháp' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Xóa bài viết này?');">
                                            @csrf @method('DELETE')
                                            <button class="text-red-600 hover:underline">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $posts->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>