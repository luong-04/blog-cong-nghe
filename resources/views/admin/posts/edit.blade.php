<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Chỉnh sửa bài viết') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('admin.posts.update', $post) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        {{-- Cột trái: Nội dung --}}
                        <div class="lg:col-span-2 space-y-6">
                            <div>
                                <label class="block font-bold text-sm text-gray-700 dark:text-gray-300 mb-1">Tiêu đề</label>
                                <input type="text" name="title" value="{{ old('title', $post->title) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            </div>
                            
                            <div>
                                <label class="block font-bold text-sm text-gray-700 dark:text-gray-300 mb-1">Nội dung chi tiết</label>
                                {{-- TextArea này sẽ được thay thế bởi CKEditor --}}
                                <textarea id="post-content" name="content" rows="20">{{ old('content', $post->content) }}</textarea>
                            </div>
                        </div>

                        {{-- Cột phải: Cài đặt --}}
                        <div class="space-y-6">
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <h3 class="font-bold text-gray-700 mb-4">Cài đặt bài viết</h3>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Danh mục</label>
                                    <select name="category_id" class="w-full border-gray-300 rounded-md shadow-sm">
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ $post->category_id == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                                    <select name="status" class="w-full border-gray-300 rounded-md shadow-sm">
                                        <option value="draft" {{ $post->status == 'draft' ? 'selected' : '' }}>Bản nháp</option>
                                        <option value="published" {{ $post->status == 'published' ? 'selected' : '' }}>Công khai</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Ảnh bìa hiện tại</label>
                                    @if($post->featured_image)
                                        <div class="relative group">
                                            <img src="{{ asset('storage/' . $post->featured_image) }}" class="w-full rounded-lg shadow-sm">
                                            <div class="absolute inset-0 bg-black/50 hidden group-hover:flex items-center justify-center rounded-lg text-white text-xs">
                                                Ảnh hiện tại
                                            </div>
                                        </div>
                                    @else
                                        <div class="bg-gray-200 h-32 rounded-lg flex items-center justify-center text-gray-400 text-sm">Chưa có ảnh</div>
                                    @endif
                                    
                                    <label class="block mt-3 text-sm font-medium text-gray-700">Thay ảnh mới</label>
                                    <input type="file" name="featured_image" class="w-full text-sm text-gray-500 mt-1">
                                </div>

                                <div class="pt-4 mt-4 border-t border-gray-200">
                                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded shadow-md transition transform hover:scale-105">
                                        💾 Lưu thay đổi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- TÍCH HỢP CKEDITOR VỚI CSS GIỐNG HỆT TRANG CHỦ --}}
    <script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>
    
    {{-- Copy y nguyên bộ CSS "đẹp" từ trang show.blade.php vào đây --}}
    <style>
        /* Tùy chỉnh độ cao editor */
        .ck-editor__editable_inline {
            min-height: 500px;
            padding: 2rem !important; /* Giống padding trang đọc */
        }

        /* CSS để nội dung trong khung soạn thảo hiển thị đẹp như trang thật */
        .ck-content {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.8;
            font-size: 16px;
        }
        .ck-content h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #BE1E2D;
            margin-top: 2.5rem;
            margin-bottom: 1rem;
            border-bottom: 2px solid #f1f1f1;
        }
        .ck-content h3 {
            font-size: 1.4rem; font-weight: 600; color: #444; margin-top: 2rem;
        }
        .ck-content ul {
            list-style-type: disc; padding-left: 1.5rem; margin-bottom: 1.5rem;
            background-color: #f9fafb; padding: 1.5rem; border-radius: 0.5rem; border-left: 4px solid #BE1E2D;
        }
        .ck-content img {
            max-width: 100%; border-radius: 8px; margin: 2rem auto; display: block; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .ck-content p { margin-bottom: 1.5rem; text-align: justify; }
    </style>

    <script>
        ClassicEditor
            .create(document.querySelector('#post-content'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', 'mediaEmbed', 'undo', 'redo'],
                heading: {
                    options: [
                        { model: 'paragraph', title: 'Đoạn văn', class: 'ck-heading_paragraph' },
                        { model: 'heading2', view: 'h2', title: 'Tiêu đề lớn (H2)', class: 'ck-heading_heading2' },
                        { model: 'heading3', view: 'h3', title: 'Tiêu đề nhỏ (H3)', class: 'ck-heading_heading3' }
                    ]
                }
            })
            .catch(error => {
                console.error(error);
            });
    </script>
</x-app-layout>