<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Hồ sơ cá nhân') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- [PHẦN MỚI] TRẠNG THÁI TÀI KHOẢN & ĐĂNG KÝ TÁC GIẢ --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <section>
                    <header>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ __('Vai trò & Quyền hạn') }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ __('Xem trạng thái tài khoản và quyền hạn của bạn trong hệ thống.') }}
                        </p>
                    </header>

                    <div class="mt-6">
                        <div class="flex items-center gap-4">
                            <span class="text-gray-700 dark:text-gray-300 font-bold">Vai trò hiện tại:</span>
                            @if(Auth::user()->role === 'admin')
                                <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-bold">Quản trị viên (Admin)</span>
                            @elseif(Auth::user()->role === 'author')
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-bold">Tác giả (Được đăng bài)</span>
                            @elseif(Auth::user()->role === 'pending')
                                <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-bold">Đang chờ duyệt</span>
                            @else
                                <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm font-bold">Thành viên (Chỉ xem & bình luận)</span>
                            @endif
                        </div>

                        {{-- Nút đăng ký dành cho User thường --}}
                        @if(Auth::user()->role === 'user')
                            <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-4">
                                <p class="mb-4 text-gray-600 dark:text-gray-400">Bạn muốn đóng góp bài viết cho Blog? Hãy đăng ký trở thành tác giả.</p>
                                <form action="{{ route('profile.request-author') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition shadow-sm">
                                        📝 Gửi yêu cầu làm Tác giả
                                    </button>
                                </form>
                                @if(session('status') === 'request-sent')
                                    <p class="mt-3 text-green-600 font-bold">✅ Yêu cầu đã được gửi! Vui lòng chờ Admin phê duyệt.</p>
                                @endif
                            </div>
                        @elseif(Auth::user()->role === 'pending')
                            <div class="mt-4 text-yellow-600 bg-yellow-50 p-3 rounded-md border border-yellow-200">
                                ⏳ Yêu cầu của bạn đang được Admin xem xét.
                            </div>
                        @endif
                    </div>
                </section>
            </div>

            {{-- CÁC FORM CŨ GIỮ NGUYÊN --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>