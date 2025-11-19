<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Chỉnh sửa bài viết') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <form action="{{ route('admin.posts.update', $post) }}" method="POST" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <div class="lg:col-span-2 space-y-4">
                                <div>
                                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Tiêu đề</label>
                                    <input type="text" name="title" value="{{ old('title', $post->title) }}" class="border-gray-300 dark:bg-gray-900 dark:text-white rounded-md w-full mt-1" required>
                                </div>
                                <div>
                                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Nội dung</label>
                                    <textarea name="content" rows="12" class="border-gray-300 dark:bg-gray-900 dark:text-white rounded-md w-full mt-1">{{ old('content', $post->content) }}</textarea>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Danh mục</label>
                                    <select name="category_id" class="border-gray-300 dark:bg-gray-900 dark:text-white rounded-md w-full mt-1">
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ $post->category_id == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Trạng thái</label>
                                    <select name="status" class="border-gray-300 dark:bg-gray-900 dark:text-white rounded-md w-full mt-1">
                                        <option value="draft" {{ $post->status == 'draft' ? 'selected' : '' }}>Bản nháp</option>
                                        <option value="published" {{ $post->status == 'published' ? 'selected' : '' }}>Công khai</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Ảnh hiện tại</label>
                                    @if($post->featured_image)
                                        <img src="{{ asset('storage/' . $post->featured_image) }}" class="w-full rounded mt-2">
                                    @endif
                                    <input type="file" name="featured_image" class="w-full text-sm text-gray-500 mt-2">
                                </div>
                                <div class="pt-4">
                                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Cập nhật</button>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>