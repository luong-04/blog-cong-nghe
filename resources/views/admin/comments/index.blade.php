<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Quản lý Toàn bộ Bình luận (Admin)') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Thông báo --}}
            @if(session('success')) <div class="mb-4 bg-green-100 text-green-700 p-3 rounded">{{ session('success') }}</div> @endif
            @if(session('error')) <div class="mb-4 bg-red-100 text-red-700 p-3 rounded">{{ session('error') }}</div> @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                {{-- Form Bulk cho Admin --}}
                <form action="{{ route('admin.comments.bulk') }}" method="POST">
                    @csrf
                    <div class="flex gap-2 mb-4">
                        <select name="action" class="border-gray-300 rounded text-sm" required>
                            <option value="">-- Hành động --</option>
                            <option value="approve">Duyệt</option>
                            <option value="delete">Xóa</option>
                        </select>
                        <button class="bg-blue-600 text-white px-4 py-2 rounded text-sm font-bold hover:bg-blue-700">Áp dụng</button>
                    </div>

                    <table class="min-w-full">
                        <thead>
                            <tr class="text-left border-b bg-gray-50">
                                <th class="p-3 w-10"><input type="checkbox" onclick="toggleAll(this)"></th>
                                <th class="p-3">Người gửi</th>
                                <th class="p-3">Nội dung</th>
                                <th class="p-3">Bài viết</th>
                                <th class="p-3">Trạng thái</th>
                                <th class="p-3">Ngày gửi</th>
                                <th class="p-3">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($comments as $comment)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-3"><input type="checkbox" name="comment_ids[]" value="{{ $comment->id }}" class="cmt-chk"></td>
                                <td class="p-3 text-sm font-bold">{{ $comment->author_name }} <br> <span class="text-xs font-normal text-gray-500">{{ $comment->author_email }}</span></td>
                                <td class="p-3 text-sm text-gray-600 max-w-xs truncate">{{ $comment->content }}</td>
                                <td class="p-3 text-sm text-blue-600"><a href="{{ route('posts.show', $comment->post->slug) }}" target="_blank">Xem bài</a></td>
                                <td class="p-3">
                                    <span class="px-2 py-1 rounded text-xs font-bold {{ $comment->status=='pending'?'bg-yellow-100 text-yellow-800':'bg-green-100 text-green-800' }}">
                                        {{ $comment->status == 'pending' ? 'Chờ duyệt' : 'Đã duyệt' }}
                                    </span>
                                </td>
                                <td class="p-3 text-xs text-gray-400">{{ $comment->created_at->format('d/m H:i') }}</td>
                                <td class="p-3">
                                    <button type="submit" form="del-form-{{$comment->id}}" class="text-red-500 hover:underline text-xs font-bold">Xóa</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </form>
                {{-- Form xóa lẻ (ẩn) để tránh xung đột form lồng nhau --}}
                @foreach($comments as $comment)
                    <form id="del-form-{{$comment->id}}" action="{{ route('admin.comments.destroy', $comment->id) }}" method="POST" onsubmit="return confirm('Xóa?');" class="hidden">
                        @csrf @method('DELETE')
                    </form>
                @endforeach

                <div class="mt-4">{{ $comments->links() }}</div>
            </div>
        </div>
    </div>
    <script>
        function toggleAll(source) {
            document.querySelectorAll('.cmt-chk').forEach(c => c.checked = source.checked);
        }
    </script>
</x-app-layout>