<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Quản lý Quảng cáo</h2></x-slot>
    <div class="py-12"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8"><div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        <div class="mb-4"><a href="{{ route('admin.ads.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">Thêm Banner mới</a></div>
        <table class="min-w-full border">
            <thead><tr class="bg-gray-100 text-left"><th class="p-3">Hình ảnh</th><th class="p-3">Link</th><th class="p-3">Trạng thái</th><th class="p-3">Hành động</th></tr></thead>
            <tbody>
                @foreach($ads as $ad)
                <tr class="border-t">
                    <td class="p-3"><img src="{{ asset('storage/'.$ad->image) }}" class="h-16 object-cover rounded"></td>
                    <td class="p-3 text-blue-600 truncate max-w-xs">{{ $ad->link }}</td>
                    <td class="p-3">
                        <form action="{{ route('admin.ads.toggle', $ad) }}" method="POST">@csrf @method('PATCH')
                            <button class="px-2 py-1 rounded text-xs {{ $ad->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $ad->is_active ? 'Đang hiện' : 'Đang ẩn' }}
                            </button>
                        </form>
                    </td>
                    <td class="p-3">
                        <form action="{{ route('admin.ads.destroy', $ad) }}" method="POST" onsubmit="return confirm('Xóa?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline">Xóa</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div></div></div>
</x-app-layout>