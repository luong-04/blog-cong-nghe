<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TechBlog - Tạp chí Công nghệ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 antialiased flex flex-col min-h-screen">

    {{-- HEADER --}}
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex justify-between items-center">
            <a href="/" class="text-2xl font-extrabold text-indigo-600 tracking-tighter flex items-center gap-2">
                <span class="bg-indigo-600 text-white w-8 h-8 flex items-center justify-center rounded-lg">T</span>
                TechBlog
            </a>

            <form action="{{ route('home') }}" method="GET" class="hidden md:flex flex-1 max-w-md mx-8 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm..." 
                       class="w-full bg-gray-100 border-0 rounded-full py-2.5 pl-5 pr-12 text-sm focus:ring-2 focus:ring-indigo-500 transition">
                <button class="absolute right-3 top-2 text-gray-400 hover:text-indigo-600">🔍</button>
            </form>

            {{-- USER MENU --}}
            <div class="flex items-center gap-4">
                @auth
                    @if(Auth::user()->role === 'admin' || Auth::user()->role === 'author')
                        <a href="{{ route('dashboard') }}" class="hidden sm:inline-flex items-center gap-1 text-xs font-bold uppercase text-gray-600 hover:text-indigo-600 border border-gray-300 px-3 py-1.5 rounded-lg transition hover:bg-gray-50">Dashboard</a>
                    @endif
                    <div class="relative group">
                        <button class="flex items-center gap-2 py-2 focus:outline-none">
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="w-9 h-9 rounded-full object-cover ring-2 ring-white shadow-sm">
                            @else
                                <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-sm">{{ substr(Auth::user()->name, 0, 1) }}</div>
                            @endif
                        </button>
                        <div class="absolute right-0 top-full pt-2 w-56 hidden group-hover:block z-50">
                            <div class="bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden">
                                <div class="px-4 py-3 border-b border-gray-50 bg-gray-50">
                                    <p class="text-sm font-bold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                                </div>
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-indigo-50">⚙️ Hồ sơ cá nhân</a>
                                <form method="POST" action="{{ route('logout') }}">@csrf<button class="block w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50">🚪 Đăng xuất</button></form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-600 hover:text-indigo-600">Đăng nhập</a>
                    <a href="{{ route('register') }}" class="text-sm font-semibold bg-gray-900 text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition shadow-md">Đăng ký</a>
                @endauth
            </div>
        </div>
    </header>

    {{-- NAVBAR DANH MỤC --}}
    <nav class="border-b border-gray-200 bg-white/95 backdrop-blur-sm sticky top-16 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-8 overflow-x-auto scrollbar-hide py-3">
                <a href="{{ route('home') }}" class="text-sm font-bold {{ request()->routeIs('home') && !isset($isCategory) ? 'text-indigo-600 border-b-2 border-indigo-600 pb-1' : 'text-gray-600 hover:text-indigo-600 pb-1' }} whitespace-nowrap">Mới nhất</a>
                @foreach($categories as $cat)
                    <a href="{{ route('categories.show', $cat->slug) }}" class="text-sm font-medium whitespace-nowrap transition pb-1 {{ (isset($currentCategory) && $currentCategory->id === $cat->id) ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-600 hover:text-indigo-600' }}">{{ $cat->name }}</a>
                @endforeach
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-grow">
        
        {{-- 1. TÌM KIẾM / DANH MỤC --}}
        @if(($isSearch ?? false) || ($isCategory ?? false))
            <div class="mb-8 border-b border-gray-200 pb-4">
                <h1 class="text-2xl font-bold text-gray-900">
                    @if($isSearch ?? false) Kết quả: <span class="text-indigo-600">"{{ request('search') }}"</span>
                    @else Danh mục: <span class="text-indigo-600">{{ $currentCategory->name }}</span> @endif
                </h1>
            </div>
            @if($posts->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($posts as $post)
                        @include('partials.post_card_grid', ['post' => $post])
                    @endforeach
                </div>
                <div class="mt-10">{{ $posts->links() }}</div>
            @else
                <div class="text-center py-20 text-gray-500">Chưa có bài viết nào.</div>
            @endif

        {{-- 2. TRANG CHỦ CHÍNH --}}
        @else
            @if($heroPost)
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-12">
                {{-- Hero Post --}}
                <div class="lg:col-span-8 group relative rounded-2xl overflow-hidden shadow-md h-[400px] md:h-[500px]">
                    @include('partials.admin_buttons', ['post' => $heroPost]) {{-- Nút sửa Hero --}}
                    <a href="{{ route('posts.show', $heroPost->slug) }}" class="block h-full w-full">
                        @if($heroPost->featured_image) <img src="{{ asset('storage/' . $heroPost->featured_image) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-700">
                        @else <div class="w-full h-full bg-gray-800"></div> @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 p-6 md:p-8 w-full">
                            <span class="bg-indigo-600 text-white text-xs font-bold px-3 py-1 rounded mb-3 inline-block">{{ $heroPost->category->name ?? 'Hot' }}</span>
                            <h2 class="text-2xl md:text-4xl font-bold text-white leading-tight mb-2 group-hover:text-indigo-200 transition">{{ $heroPost->title }}</h2>
                            <div class="flex items-center gap-3 text-white/80 text-xs mt-2">
                                <span class="font-bold">{{ $heroPost->user->name }}</span> &bull; <span>{{ $heroPost->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </a>
                </div>
                {{-- Featured Posts --}}
                <div class="lg:col-span-4 flex flex-col gap-6">
                    @foreach($featuredPosts as $subPost)
                    <div class="relative flex-1 rounded-2xl overflow-hidden group h-[240px] shadow-md bg-gray-900">
                        @include('partials.admin_buttons', ['post' => $subPost]) {{-- Nút sửa Featured --}}
                        <a href="{{ route('posts.show', $subPost->slug) }}" class="block h-full">
                            @if($subPost->featured_image) <img src="{{ asset('storage/' . $subPost->featured_image) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500 opacity-80 group-hover:opacity-100"> @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 to-transparent"></div>
                            <div class="absolute bottom-0 p-5 w-full">
                                <span class="text-indigo-300 text-xs font-bold uppercase mb-1 block">{{ $subPost->category->name }}</span>
                                <h3 class="text-lg font-bold text-white leading-snug hover:underline line-clamp-2">{{ $subPost->title }}</h3>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Tin Mới Nhất --}}
            <div class="pt-8 border-t border-gray-200">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2"><span class="w-2 h-6 bg-indigo-600 rounded-full"></span> Tin mới nhất</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach($recentPosts as $post)
                        @include('partials.post_card_grid', ['post' => $post])
                    @endforeach
                </div>
                <div class="mt-10">{{ $recentPosts->links() }}</div>
            </div>
        @endif
    </main>

    <footer class="bg-white border-t border-gray-200 mt-auto py-8 text-center text-gray-500 text-sm">
        <p class="font-bold text-gray-900 text-lg mb-2">TechBlog.</p>
        <p>© {{ date('Y') }} Xây dựng với Laravel 11 & Gemini AI.</p>
    </footer>
</body>
</html>