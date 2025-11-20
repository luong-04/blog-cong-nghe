<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Blog Công Nghệ</title>
    {{-- Sử dụng Tailwind CSS qua CDN cho nhanh --}}
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans antialiased">

    {{-- Header --}}
    <nav class="bg-white shadow mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    {{-- Logo --}}
                    <a href="/" class="text-2xl font-bold text-indigo-600">TechBlog 🚀</a>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-indigo-600 font-medium">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600 font-medium">Đăng nhập</a>
                        <a href="{{ route('register') }}" class="text-gray-700 hover:text-indigo-600 font-medium">Đăng ký</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- THANH TÌM KIẾM --}}
        <div class="mb-10 mt-6">
            <form action="{{ route('home') }}" method="GET" class="flex justify-center">
                <div class="relative w-full max-w-lg">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}"
                        placeholder="Tìm kiếm bài viết..." 
                        class="w-full border border-gray-300 rounded-full py-3 px-6 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                    <button type="submit" class="absolute right-2 top-2 bg-indigo-600 text-white p-2 rounded-full hover:bg-indigo-700 transition">
                        {{-- Icon Kính lúp --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        {{-- DANH SÁCH BÀI VIẾT --}}
        @if($posts->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($posts as $post)
                <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300 flex flex-col">
                    {{-- Ảnh đại diện --}}
                    @if($post->featured_image)
                        <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-400">
                            <span class="text-sm">No Image</span>
                        </div>
                    @endif

                    <div class="p-6 flex-1 flex flex-col">
                        <div class="text-sm text-indigo-500 mb-2 font-semibold uppercase tracking-wider">
                            {{ $post->category->name ?? 'Chưa phân loại' }}
                        </div>
                        
                        <h2 class="text-xl font-bold text-gray-900 mb-3 leading-tight">
                            <a href="{{ route('posts.show', $post->slug) }}" class="hover:text-indigo-600 transition">
                                {{ $post->title }}
                            </a>
                        </h2>
                        
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3 flex-1">
                            {{-- Lấy đoạn trích ngắn, loại bỏ thẻ HTML từ Markdown --}}
                            {{ Str::limit(strip_tags(Str::markdown($post->content)), 120) }}
                        </p>
                        
                        <div class="flex items-center justify-between text-xs text-gray-500 border-t pt-4 mt-auto">
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                </svg>
                                {{ $post->user->name }}
                            </span>
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                </svg>
                                {{ $post->created_at->format('d/m/Y') }}
                            </span>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>

            {{-- Phân trang --}}
            <div class="mt-10 mb-12">
                {{ $posts->links() }}
            </div>
        @else
            {{-- Thông báo nếu không tìm thấy bài nào --}}
            <div class="text-center py-16 bg-white rounded-lg shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-gray-500 text-lg">Không tìm thấy bài viết nào phù hợp với từ khóa "<strong>{{ request('search') }}</strong>".</p>
                <a href="{{ route('home') }}" class="text-indigo-600 hover:underline mt-4 inline-block font-medium">← Xem tất cả bài viết</a>
            </div>
        @endif
    </main>

    <footer class="bg-white border-t mt-auto py-8 text-center text-gray-500 text-sm">
        &copy; {{ date('Y') }} <strong>TechBlog</strong>. Xây dựng với Laravel 11 & Gemini AI.
    </footer>

</body>
</html>