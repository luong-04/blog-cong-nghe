<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HealthyBlog - Tạp chí Sức Khỏe</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; font-size: 15px; }
        
        /* CSS Cắt dòng & Scroll */
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
        .custom-scrollbar::-webkit-scrollbar { height: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 4px; }
        
        /* CSS Slider */
        .ad-slide { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; transition: opacity 0.8s ease-in-out; }
        .ad-slide.active { opacity: 1; z-index: 10; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased flex flex-col min-h-screen overflow-x-hidden w-full">

    {{-- HEADER --}}
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex justify-between items-center">
            
            <a href="/" class="group flex items-center gap-1.5 text-xl md:text-2xl font-extrabold tracking-tight hover:opacity-90 transition shrink-0">
                <span class="bg-indigo-600 text-white px-2 py-0.5 rounded-lg shadow-sm group-hover:bg-indigo-700 transition">Healthy</span>
                <span class="text-gray-900">Blog</span>
            </a>

            {{-- Search Desktop --}}
            <form action="{{ route('home') }}" method="GET" class="hidden md:flex flex-1 max-w-md mx-8 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm..." 
                       class="w-full bg-gray-100 border-0 rounded-full py-2 pl-4 pr-10 text-sm focus:ring-1 focus:ring-indigo-500 transition">
                <button class="absolute right-3 top-2 text-gray-400 hover:text-indigo-600">🔍</button>
            </form>

            <div class="flex items-center gap-2 sm:gap-3 text-sm shrink-0">
                {{-- Nút Search Mobile --}}
                <button class="md:hidden text-gray-500 p-2" onclick="document.getElementById('mobile-search').classList.toggle('hidden')">🔍</button>

                @auth
                    {{-- 2. NÚT DASHBOARD (HIỆN CẢ MOBILE VÀ DESKTOP) --}}
                    @if(Auth::user()->role === 'admin' || Auth::user()->role === 'author')
                        {{-- Desktop: Chữ Dashboard --}}
                        <a href="{{ route('dashboard') }}" class="hidden md:inline-flex items-center font-semibold text-gray-600 hover:text-indigo-600 border border-gray-200 px-3 py-1 rounded hover:bg-gray-50 transition">
                            Dashboard
                        </a>
                        {{-- Mobile: Icon Biểu đồ (MỚI) --}}
                        <a href="{{ route('dashboard') }}" class="md:hidden text-indigo-600 bg-indigo-50 p-2 rounded-full hover:bg-indigo-100 transition" title="Vào trang quản lý">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                            </svg>
                        </a>
                    @endif

                    {{-- Avatar User --}}
                    <div class="relative group">
                        <button class="flex items-center gap-2 py-2 focus:outline-none">
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="w-8 h-8 rounded-full object-cover border border-gray-200">
                            @else
                                <div class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs">{{ substr(Auth::user()->name, 0, 1) }}</div>
                            @endif
                        </button>
                        {{-- Dropdown --}}
                        <div class="absolute right-0 top-full pt-2 w-48 hidden group-hover:block z-50">
                            <div class="bg-white rounded-lg shadow-xl border border-gray-100 overflow-hidden">
                                <div class="px-4 py-2 bg-gray-50 border-b text-xs text-gray-500 font-bold uppercase">Tài khoản</div>
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Hồ sơ cá nhân</a>
                                <form method="POST" action="{{ route('logout') }}">@csrf<button class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Đăng xuất</button></form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-indigo-600">Đăng nhập</a>
                    <a href="{{ route('register') }}" class="ml-2 font-semibold bg-gray-900 text-white px-4 py-2 rounded hover:bg-gray-800 transition">Đăng ký</a>
                @endauth
            </div>
        </div>
        
        {{-- Search Box Mobile --}}
        <div id="mobile-search" class="hidden md:hidden border-t border-gray-100 p-3 bg-white">
            <form action="{{ route('home') }}" method="GET" class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm bài viết..." class="w-full bg-gray-100 border-0 rounded-lg py-2 pl-4 pr-10 text-sm">
                <button class="absolute right-3 top-2 text-gray-400">🔍</button>
            </form>
        </div>
    </header>

    {{-- MENU DANH MỤC (KÉO NGANG) --}}
    <nav class="border-b border-gray-200 bg-white/95 backdrop-blur-sm sticky top-16 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-6 overflow-x-auto custom-scrollbar py-2.5 text-sm font-medium">
                <a href="{{ route('home') }}" class="shrink-0 whitespace-nowrap {{ request()->routeIs('home') && !isset($isCategory) ? 'text-indigo-600' : 'text-gray-600 hover:text-indigo-600' }}">Mới nhất</a>
                @foreach($categories as $cat)
                    <a href="{{ route('categories.show', $cat->slug) }}" class="shrink-0 whitespace-nowrap transition {{ (isset($currentCategory) && $currentCategory->id === $cat->id) ? 'text-indigo-600' : 'text-gray-600 hover:text-indigo-600' }}">{{ $cat->name }}</a>
                @endforeach
            </div>
        </div>
    </nav>

    {{-- BANNER QUẢNG CÁO --}}
    @if(isset($ads) && $ads->count() > 0)
    <div class="bg-white border-b border-gray-200 py-5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div id="banner-slider" class="relative w-full h-36 md:h-52 rounded-xl overflow-hidden shadow-md group bg-gray-100">
                @foreach($ads as $index => $ad)
                    <a href="{{ $ad->link ?? '#' }}" target="_blank" class="ad-slide {{ $index == 0 ? 'active' : '' }} block w-full h-full">
                        <img src="{{ asset('storage/'.$ad->image) }}" class="w-full h-full object-cover" alt="{{ $ad->title }}">
                        <span class="absolute bottom-2 right-2 bg-black/50 text-white text-[10px] px-2 py-0.5 rounded backdrop-blur-sm">QC</span>
                    </a>
                @endforeach
                @if($ads->count() > 1)
                    <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5 z-20">
                        @foreach($ads as $index => $ad)
                            <span class="dot w-2 h-2 rounded-full bg-white/50 cursor-pointer hover:bg-white transition {{ $index == 0 ? 'bg-white' : '' }}" onclick="goToSlide({{ $index }})"></span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-grow w-full">
        
        @if(($isSearch ?? false) || ($isCategory ?? false))
            <div class="mb-6 border-b border-gray-200 pb-3">
                <h1 class="text-xl font-bold text-gray-900">
                    @if($isSearch ?? false) Kết quả: <span class="text-indigo-600">"{{ request('search') }}"</span>
                    @else Danh mục: <span class="text-indigo-600">{{ $currentCategory->name }}</span> @endif
                </h1>
            </div>
        @endif

        @php $displayPosts = isset($posts) ? $posts : ($recentPosts ?? collect()); @endphp

        {{-- HERO SECTION --}}
        @if(isset($heroPost) && !($isSearch ?? false) && !($isCategory ?? false))
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-10">
                {{-- Tin To --}}
                <div class="lg:col-span-8 group relative rounded-xl overflow-hidden shadow-sm h-[300px] sm:h-[400px]">
                     @auth @if(Auth::user()->role === 'admin' || (Auth::user()->role === 'author' && $heroPost->user_id === Auth::id()))
                        <a href="{{ route('admin.posts.edit', $heroPost->id) }}" class="absolute top-3 right-3 z-20 bg-white/90 text-indigo-600 px-2 py-1 rounded text-xs font-bold shadow">Sửa</a>
                     @endif @endauth
                    <a href="{{ route('posts.show', $heroPost->slug) }}" class="block h-full w-full">
                        @if($heroPost->featured_image) <img src="{{ asset('storage/' . $heroPost->featured_image) }}" class="w-full h-full object-cover transition duration-700 group-hover:scale-105">
                        @else <div class="w-full h-full bg-gray-800"></div> @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-transparent to-transparent"></div>
                        <div class="absolute bottom-0 left-0 p-6 w-full">
                            <span class="bg-indigo-600 text-white text-[10px] font-bold px-2 py-0.5 rounded mb-2 inline-block uppercase">{{ $heroPost->category->name ?? 'Hot' }}</span>
                            <h2 class="text-xl md:text-3xl font-bold text-white leading-tight mb-1 group-hover:text-indigo-200 transition line-clamp-2">{{ $heroPost->title }}</h2>
                            <div class="flex items-center gap-2 text-white/80 text-xs mt-1">
                                <span>{{ $heroPost->user->name }}</span> &bull; <span>{{ $heroPost->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </a>
                </div>
                {{-- Tin Phụ --}}
                <div class="lg:col-span-4 flex flex-col gap-6 h-auto lg:h-[400px]">
                    @foreach($featuredPosts as $subPost)
                    <div class="relative flex-1 rounded-xl overflow-hidden group h-[190px] shadow-sm bg-gray-900">
                        @auth @if(Auth::user()->role === 'admin' || (Auth::user()->role === 'author' && $subPost->user_id === Auth::id()))
                            <a href="{{ route('admin.posts.edit', $subPost->id) }}" class="absolute top-2 right-2 z-20 bg-white/90 text-indigo-600 px-2 py-1 rounded text-[10px] font-bold shadow">Sửa</a>
                         @endif @endauth
                        <a href="{{ route('posts.show', $subPost->slug) }}" class="block h-full w-full">
                            @if($subPost->featured_image) <img src="{{ asset('storage/' . $subPost->featured_image) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500 opacity-90"> @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 to-transparent"></div>
                            <div class="absolute bottom-0 p-4 w-full">
                                <span class="text-indigo-300 text-[10px] font-bold uppercase mb-1 block">{{ $subPost->category->name }}</span>
                                <h3 class="text-sm md:text-base font-bold text-white leading-snug hover:underline line-clamp-2">{{ $subPost->title }}</h3>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="pt-6 border-t border-gray-200 mb-4">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2"><span class="w-1 h-5 bg-indigo-600 rounded-full"></span> Tin mới nhất</h3>
            </div>
        @endif

        {{-- LIST BÀI VIẾT --}}
        @if($displayPosts->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($displayPosts as $post)
                    <article class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition duration-300 flex flex-col h-full relative group">
                        @auth @if(Auth::user()->role === 'admin' || (Auth::user()->role === 'author' && $post->user_id === Auth::id()))
                            <div class="absolute top-2 right-2 z-20 flex gap-1 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition duration-200">
                                <a href="{{ route('admin.posts.edit', $post->id) }}" class="bg-white text-indigo-600 p-1.5 rounded shadow hover:bg-indigo-600 hover:text-white transition" title="Sửa">✏️</a>
                                @if(Auth::user()->role === 'admin')
                                    <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Xóa?')">@csrf @method('DELETE')
                                    <button type="submit" class="bg-white text-red-500 p-1.5 rounded shadow hover:bg-red-500 hover:text-white transition" title="Xóa">🗑️</button></form>
                                @endif
                            </div>
                        @endif @endauth
                        <a href="{{ route('posts.show', $post->slug) }}" class="block h-48 overflow-hidden relative bg-gray-100 shrink-0">
                            @if($post->featured_image) <img src="{{ asset('storage/' . $post->featured_image) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @else <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs">No Image</div> @endif
                            <span class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm text-indigo-600 text-[10px] font-bold uppercase px-2 py-1 rounded shadow-sm">{{ $post->category->name ?? 'Tech' }}</span>
                        </a>
                        <div class="p-4 flex flex-col flex-1">
                            <h3 class="font-bold text-gray-900 text-[15px] leading-snug mb-2 h-[2.8rem] overflow-hidden">
                                <a href="{{ route('posts.show', $post->slug) }}" class="hover:text-indigo-600 transition">{{ $post->title }}</a>
                            </h3>
                            <p class="text-gray-500 text-xs leading-relaxed mb-3 flex-1 h-[3.6rem] overflow-hidden line-clamp-3">
                                {{ Str::limit(strip_tags($post->content), 110) }}
                            </p>
                            <div class="flex items-center justify-between pt-3 border-t border-gray-100 mt-auto text-xs text-gray-500">
                                <div class="flex items-center gap-1.5">
                                    @if($post->user->avatar) <img src="{{ asset('storage/' . $post->user->avatar) }}" class="w-5 h-5 rounded-full object-cover border border-gray-100">
                                    @else <div class="w-5 h-5 rounded-full bg-gray-100 flex items-center justify-center text-[9px] font-bold">{{ substr($post->user->name, 0, 1) }}</div> @endif
                                    <span class="font-medium truncate max-w-[80px] text-gray-600">{{ $post->user->name }}</span>
                                </div>
                                <span class="flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg> {{ $post->views }}</span>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="mt-8 text-sm">{{ $displayPosts->links() }}</div>
        @else
            <div class="text-center py-20 text-gray-500">Chưa có bài viết nào.</div>
        @endif
    </main>

    <footer class="bg-white border-t border-gray-200 py-8 text-center text-gray-500 text-xs">
        <p>© {{ date('Y') }} 
            <span class="bg-indigo-600 text-white px-2 py-0.5 rounded-lg shadow-sm group-hover:bg-indigo-700 transition">Healthy</span>
            <span class="text-gray-900">Blog</span>
        </p>
    </footer>

    <script>
        let slideIndex = 0;
        const slides = document.querySelectorAll('.ad-slide');
        const dots = document.querySelectorAll('.dot');
        function showSlides() {
            if (slides.length === 0) return;
            slides.forEach(slide => { slide.classList.remove('active'); slide.style.opacity = 0; });
            if(dots.length > 0) dots.forEach(dot => dot.classList.remove('bg-white'));
            if (slideIndex >= slides.length) slideIndex = 0;
            if (slideIndex < 0) slideIndex = slides.length - 1;
            slides[slideIndex].classList.add('active');
            slides[slideIndex].style.opacity = 1;
            if(dots.length > 0) {
                dots.forEach(dot => dot.classList.replace('bg-white', 'bg-white/50'));
                dots[slideIndex].classList.replace('bg-white/50', 'bg-white');
            }
        }
        function nextSlide() { slideIndex++; showSlides(); }
        function goToSlide(n) { slideIndex = n; showSlides(); }
        setInterval(nextSlide, 4000); showSlides();
    </script>
</body>
</html>