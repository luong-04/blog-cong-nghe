<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Thêm Banner Quảng Cáo</h2></x-slot>
    <div class="py-12"><div class="max-w-xl mx-auto sm:px-6 lg:px-8"><div class="bg-white p-6 shadow-sm rounded-lg">
        <form action="{{ route('admin.ads.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div><label>Tiêu đề (Ghi nhớ)</label><input type="text" name="title" class="w-full border rounded p-2"></div>
            <div><label>Link đích (Khi bấm vào)</label><input type="url" name="link" class="w-full border rounded p-2"></div>
            <div><label>Hình ảnh (*)</label><input type="file" name="image" required class="w-full border p-2"></div>
            <div class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" checked> <label>Kích hoạt ngay</label></div>
            <button class="bg-blue-600 text-white px-4 py-2 rounded">Lưu lại</button>
        </form>
    </div></div></div>
</x-app-layout>