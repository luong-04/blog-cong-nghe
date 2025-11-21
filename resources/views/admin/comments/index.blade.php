<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Quản lý Bình luận') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <table class="min-w-full">
                    <thead>
                        <tr class="text-left border-b">
                            <th class="pb-3">Người gửi</th>
                            <th class="pb-3">Nội dung</th>
                            <th class="pb-3">Bài viết</th>
                            <th class="pb-3">Ngày gửi</th>
                            <th class="pb-3">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($comments as $comment)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 font-bold text-sm">{{ $comment->author_name }}</td>
                            <td class="py-3 text-sm text-gray-600 max-w-xs truncate">{{ $comment->content }}</td>
                            <td class="py-3 text-sm text-blue-600"><a href="{{ route('posts.show', $comment->post->slug) }}" target="_blank">Xem bài</a></td>
                            <td class="py-3 text-xs text-gray-400">{{ $comment->created_at->format('d/m H:i') }}</td>
                            <td class="py-3">
                                <form action="{{ route('admin.comments.destroy', $comment->id) }}" method="POST" onsubmit="return confirm('Xóa bình luận này?');">
                                    @csrf @method('DELETE')
                                    <button class="text-red-500 hover:underline text-xs font-bold">Xóa</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">{{ $comments->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>