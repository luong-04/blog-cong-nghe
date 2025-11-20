<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Blog Công Nghệ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Font chữ đẹp hơn --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

    {{-- Header --}}
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <a href="/" class="text-2xl font-extrabold text-indigo-600 flex items-center gap-2">
                    <span class="text-3xl">🚀</span> TechBlog
                </a>
                
                <div class="flex items-center gap-4">
                    @auth
                        @if(Auth::user()->role === 'admin' || Auth::user()->role === 'author')
                            <a href="{{ route('dashboard') }}" class="hidden md:inline-block text-sm font-medium text-gray-700 hover:text-indigo-600 bg-gray-100 hover:bg-indigo-50 px-4 py-2 rounded-full transition">
                                📊 Dashboard
                            </a>
                        @endif

                        {{-- Menu User --}}
                        <div class="relative group">
                            <button class="flex items-center gap-2 focus:outline-none py-2">
                                {{-- Avatar User trên Menu --}}
                                @if(Auth::user()->avatar)
                                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="w-9 h-9 rounded-full object-cover border border-gray-200">
                                @else
                                    <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-sm">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                @endif
                                <span class="font-medium hidden sm:block">{{ Auth::user()->name }}</span>
                            </button>
                            
                            {{-- Dropdown --}}
                            <div class="absolute right-0 top-full mt-1 w-48 bg-white rounded-xl shadow-xl py-2 hidden group-hover:block border border-gray-100 z-50 transform origin-top-right transition-all">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">
                                    ⚙️ Hồ sơ cá nhân
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        🚪 Đăng xuất
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-indigo-600 font-medium">Đăng nhập</a>
                        <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-5 py-2 rounded-full hover:bg-indigo-700 font-medium shadow-md transition transform hover:-translate-y-0.5">Đăng ký</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        {{-- Thanh Tìm Kiếm --}}
        <div class="mb-12 text-center">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">Khám phá công nghệ mới nhất 💡</h2>
            <form action="{{ route('home') }}" method="GET" class="max-w-xl mx-auto relative">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Tìm kiếm bài viết (VD: iPhone 15, AI...)" 
                    class="w-full pl-5 pr-12 py-4 rounded-full border-0 shadow-md ring-1 ring-gray-200 focus:ring-2 focus:ring-indigo-500 text-gray-700 outline-none transition"
                >
                <button type="submit" class="absolute right-2 top-2 bg-indigo-600 text-white p-2.5 rounded-full hover:bg-indigo-700 transition shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </form>
        </div>

        {{-- Danh sách bài viết --}}
        @if($posts->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($posts as $post)
                <article class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden border border-gray-100 group relative">
                    
                    {{-- Nút Sửa Nhanh (Chỉ hiện khi có quyền) --}}
                    @auth
                        @if(Auth::user()->role === 'admin' || (Auth::user()->role === 'author' && $post->user_id === Auth::id()))
                            <a href="{{ route('admin.posts.edit', $post->id) }}" class="absolute top-3 right-3 bg-white/90 text-gray-700 p-2 rounded-full shadow-md opacity-0 group-hover:opacity-100 transition-all hover:bg-indigo-600 hover:text-white z-10 transform hover:scale-110" title="Sửa bài này">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                        @endif
                    @endauth

                    {{-- Ảnh bìa --}}
                    <a href="{{ route('posts.show', $post->slug) }}" class="block overflow-hidden h-56">
                        @if($post->featured_image)
                            <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover transform group-hover:scale-105 transition duration-500">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-gray-400">
                                <span class="text-sm font-medium">No Image</span>
                            </div>
                        @endif
                    </a>

                    <div class="p-6 flex-1 flex flex-col">
                        {{-- Danh mục --}}
                        <div class="mb-3">
                            <span class="bg-indigo-50 text-indigo-700 text-xs font-bold px-2.5 py-1 rounded-md uppercase tracking-wide">
                                {{ $post->category->name ?? 'General' }}
                            </span>
                        </div>
                        
                        {{-- Tiêu đề --}}
                        <h3 class="text-xl font-bold text-gray-900 mb-3 leading-snug line-clamp-2 group-hover:text-indigo-600 transition">
                            <a href="{{ route('posts.show', $post->slug) }}">
                                {{ $post->title }}
                            </a>
                        </h3>
                        
                        {{-- Tóm tắt ngắn --}}
                        <p class="text-gray-500 text-sm mb-4 line-clamp-3 flex-1">
                            {{ Str::limit(strip_tags($post->content), 120) }}
                        </p>
                        
                        {{-- Footer bài viết (Avatar + Tên + Ngày) --}}
                        <div class="flex items-center justify-between pt-4 border-t border-gray-50 mt-auto">
                            <div class="flex items-center gap-2">
                                {{-- Avatar Tác giả --}}
                                @if($post->user->avatar)
                                    <img src="{{ asset('storage/' . $post->user->avatar) }}" class="w-8 h-8 rounded-full object-cover border border-gray-100">
                                @else
                                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 font-bold text-xs">
                                        {{ substr($post->user->name, 0, 1) }}
                                    </div>
                                @endif
                                
                                <div class="text-xs">
                                    <div class="font-bold text-gray-800">{{ $post->user->name }}</div>
                                    <div class="text-gray-400">{{ $post->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>

            {{-- Phân trang --}}
            <div class="mt-12">
                {{ $posts->links() }}
            </div>
        @else
            {{-- Không tìm thấy bài --}}
            <div class="text-center py-20 bg-white rounded-2xl shadow-sm border border-gray-100">
                <div class="bg-gray-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Không tìm thấy kết quả</h3>
                <p class="text-gray-500 mt-1">Thử tìm kiếm với từ khóa khác xem sao?</p>
                <a href="{{ route('home') }}" class="inline-block mt-4 text-indigo-600 font-medium hover:underline">Quay lại trang chủ</a>
            </div>
        @endif
    </main>

    <footer class="bg-white border-t mt-auto py-10">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-gray-500 text-sm">
                &copy; {{ date('Y') }} <strong>TechBlog</strong>. Xây dựng bằng Laravel 11 & Gemini AI 🤖.
            </p>
        </div>
    </footer>

</body>
</html>