<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Thống kê nhanh --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 dark:text-gray-400">Tổng số bài viết</div>
                    <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalPosts }}</div>
                </div>
                {{-- Bạn có thể thêm box thống kê Category, User tại đây --}}
            </div>
            {{-- -đăng ký tác giả --}}
            <div class="p-6 text-gray-900 dark:text-gray-100">
                @if(auth()->user()->role === 'user')
                    <p>Bạn đang là thành viên thường. Bạn muốn đăng bài?</p>
                    <form action="{{ route('profile.request-author') }}" method="POST" class="mt-4">
                        @csrf
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Đăng ký làm Tác giả</button>
                    </form>
                @elseif(auth()->user()->role === 'pending')
                    <div class="bg-yellow-100 p-4 text-yellow-800 rounded">
                        Yêu cầu của bạn đang chờ Admin duyệt.
                    </div>
                @endif
            </div>

            {{-- Danh sách bài mới --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold mb-4">Bài viết mới nhất</h3>
                    <ul>
                        @foreach($latestPosts as $post)
                            <li class="border-b border-gray-200 dark:border-gray-700 py-3 flex justify-between items-center">
                                <div>
                                    <a href="{{ route('admin.posts.edit', $post) }}" class="font-medium hover:text-blue-500">
                                        {{ $post->title }}
                                    </a>
                                    <div class="text-xs text-gray-500">
                                        {{ $post->created_at->format('d/m/Y') }} - {{ $post->category->name }}
                                    </div>
                                </div>
                                <span class="px-2 py-1 text-xs rounded {{ $post->status=='published' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $post->status }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>