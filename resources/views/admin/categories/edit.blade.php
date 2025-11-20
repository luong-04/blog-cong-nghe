<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Chỉnh sửa Danh mục') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('admin.categories.update', $category) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tên danh mục</label>
                        <input type="text" name="name" value="{{ old('name', $category->name) }}" class="w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:text-white" required>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('admin.categories.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded">Hủy</a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded font-bold">Cập nhật</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>