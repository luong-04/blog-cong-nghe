<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Quản lý Bình luận cá nhân') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Hiển thị thông báo --}}
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                {{-- Form Bulk Action --}}
                <form action="{{ route('author.comments.bulk') }}" method="POST" id="bulk-form">
                    @csrf
                    <div class="flex flex-wrap items-center justify-between mb-4 gap-4">
                        <div class="flex items-center gap-2">
                            <select name="action" class="border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">-- Chọn hành động --</option>
                                <option value="approve">Duyệt đã chọn</option>
                                <option value="delete">Xóa đã chọn</option>
                            </select>
                            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-bold hover:bg-indigo-700 transition duration-150 ease-in-out">
                                Thực hiện
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b">
                                    <th class="p-3 w-10 text-center">
                                        <input type="checkbox" id="select-all" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    </th>
                                    <th class="p-3 font-semibold text-gray-700">Người gửi</th>
                                    <th class="p-3 font-semibold text-gray-700">Nội dung</th>
                                    <th class="p-3 font-semibold text-gray-700">Bài viết</th>
                                    <th class="p-3 font-semibold text-gray-700">Trạng thái</th>
                                    <th class="p-3 font-semibold text-gray-700">Ngày gửi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($comments as $comment)
                                <tr class="border-b hover:bg-gray-50 transition duration-150">
                                    <td class="p-3 text-center">
                                        <input type="checkbox" name="comment_ids[]" value="{{ $comment->id }}" class="comment-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    </td>
                                    <td class="p-3 text-sm">
                                        <div class="font-bold text-gray-900">{{ $comment->author_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $comment->author_email }}</div>
                                    </td>
                                    <td class="p-3 text-sm text-gray-600 max-w-xs truncate" title="{{ $comment->content }}">
                                        {{ Str::limit($comment->content, 50) }}
                                    </td>
                                    <td class="p-3 text-sm">
                                        <a href="{{ route('posts.show', $comment->post->slug) }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 hover:underline font-medium">
                                            {{ Str::limit($comment->post->title, 30) }}
                                        </a>
                                    </td>
                                    <td class="p-3">
                                        @if($comment->status == 'pending')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Chờ duyệt
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Đã duyệt
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-xs text-gray-500 whitespace-nowrap">
                                        {{ $comment->created_at->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-gray-500 italic">
                                        Chưa có bình luận nào cần xử lý.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>
                
                <div class="mt-4">
                    {{ $comments->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        // Script chọn tất cả checkbox
        const selectAll = document.getElementById('select-all');
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.comment-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });
        }
    </script>
</x-app-layout>