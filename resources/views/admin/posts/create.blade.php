<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Viết bài mới') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                {{-- Form cần có enctype="multipart/form-data" để upload ảnh --}}
                <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Cột bên trái: Nội dung chính --}}
                        <div class="md:col-span-2 space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tiêu đề bài viết</label>
                                <input type="text" id="post-title" name="title" class="w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:text-white" required>
                            </div>
                            {{-- Nút tạo AI --}}
                            <div class="flex justify-end">
                                <button type="button" id="btn-generate" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded shadow text-sm flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    Viết nội dung bằng Gemini AI
                                </button>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nội dung</label>
                                <textarea id="post-content" name="content" rows="10" class="w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:text-white"></textarea>
                            </div>
                            {{--Script xử lý Ajax --}}
                            <script>
                                document.getElementById('btn-generate').addEventListener('click', function() {
                                    const title = document.getElementById('post-title').value;
                                    const btn = this;
                                    const contentArea = document.getElementById('post-content');

                                    if (!title) {
                                        alert('Vui lòng nhập tiêu đề trước để AI biết cần viết gì!');
                                        return;
                                    }

                                    // Hiệu ứng đang tải
                                    btn.disabled = true;
                                    btn.innerHTML = 'Đang suy nghĩ...';
                                    btn.classList.add('opacity-50');

                                    fetch('{{ route('admin.posts.generate') }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        body: JSON.stringify({ title: title })
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.content) {
                                            contentArea.value = data.content;
                                        } else {
                                            alert('Lỗi: ' + (data.error || 'Không có phản hồi'));
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        alert('Có lỗi xảy ra khi kết nối tới server.');
                                    })
                                    .finally(() => {
                                        // Khôi phục nút
                                        btn.disabled = false;
                                        btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg> Viết nội dung bằng Gemini AI';
                                        btn.classList.remove('opacity-50');
                                    });
                                });
                            </script>
                        </div>

                        {{-- Cột bên phải: Cài đặt --}}
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Danh mục</label>
                                <select name="category_id" class="w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:text-white">
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ảnh đại diện</label>
                                <input type="file" name="featured_image" class="w-full text-sm text-gray-500 dark:text-gray-300">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Trạng thái</label>
                                <select name="status" class="w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:text-white">
                                    <option value="draft">Nháp</option>
                                    <option value="published">Công khai</option>
                                </select>
                            </div>

                            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 font-bold">Đăng bài</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>