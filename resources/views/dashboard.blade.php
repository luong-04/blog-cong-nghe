<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- KHU VỰC DÀNH CHO USER THƯỜNG --}}
            @if(Auth::user()->role === 'user')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <h3 class="text-lg font-bold mb-2">Bạn đang là Thành viên thường</h3>
                    <p class="text-gray-600 mb-4">Bạn chỉ có thể xem và bình luận. Để đăng bài viết, hãy đăng ký làm Tác giả.</p>
                    
                    <form action="{{ route('profile.request-author') }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition shadow">
                            📝 Gửi yêu cầu làm Tác giả
                        </button>
                    </form>

                    @if(session('status') === 'request-sent')
                        <p class="mt-4 text-green-600 font-bold">✅ Đã gửi yêu cầu! Vui lòng chờ Admin duyệt.</p>
                    @endif
                </div>

            {{-- KHU VỰC ĐANG CHỜ DUYỆT --}}
            @elseif(Auth::user()->role === 'pending')
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                    <div class="flex">
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                ⏳ Yêu cầu làm tác giả của bạn đang chờ phê duyệt. Vui lòng quay lại sau.
                            </p>
                        </div>
                    </div>
                </div>

            {{-- KHU VỰC DÀNH CHO ADMIN & AUTHOR --}}
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-bold text-lg mb-4">Thống kê nhanh</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-indigo-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-indigo-700">{{ $totalPosts ?? 0 }}</div>
                            <div class="text-sm text-indigo-600">Tổng số bài viết</div>
                        </div>
                        {{-- Hiển thị thêm nếu là Admin --}}
                        @if(Auth::user()->role === 'admin')
                            <div class="bg-green-50 p-4 rounded-lg">
                                <a href="{{ route('admin.users.index') }}" class="block hover:underline">
                                    <div class="text-2xl font-bold text-green-700">Quản lý User</div>
                                    <div class="text-sm text-green-600">Bấm để duyệt thành viên</div>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>