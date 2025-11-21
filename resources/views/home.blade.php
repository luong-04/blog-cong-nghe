<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TechBlog - Tạp chí Công nghệ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; font-size: 15px; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
        
        /* CSS cho Slider Quảng cáo */
        .ad-slide { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; transition: opacity 0.8s ease-in-out; }
        .ad-slide.active { opacity: 1; z-index: 10; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased flex flex-col min-h-screen">

    {{-- HEADER --}}
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex justify-between items-center">
        <a href="/" class="group flex items-center gap-1.5 text-2xl font-extrabold tracking-tight hover:opacity-90 transition">
            <span class="bg-indigo-600 text-white px-2 py-0.5 rounded-lg shadow-sm group-hover:bg-indigo-700 transition">Tech</span>
            <span class="text-gray-900">Blog</span>
        </a>

            <form action="{{ route('home') }}" method="GET" class="hidden md:flex flex-1 max-w-md mx-8 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm..." 
                       class="w-full bg-gray-100 border-0 rounded-full py-2 pl-4 pr-10 text-sm focus:ring-1 focus:ring-indigo-500 transition">
                <button class="absolute right-3 top-2 text-gray-400 hover:text-indigo-600">🔍</button>
            </form>

            <div class="flex items-center gap-3 text-sm">
                @auth
                    @if(Auth::user()->role === 'admin' || Auth::user()->role === 'author')
                        <a href="{{ route('dashboard') }}" class="hidden sm:inline-flex items-center font-semibold text-gray-600 hover:text-indigo-600 border px-3 py-1 rounded hover:bg-gray-100 transition">Dashboard</a>
                    @endif
                    <div class="relative group">
                        <button class="flex items-center gap-2 py-2 focus:outline-none">
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="w-8 h-8 rounded-full object-cover border border-gray-200">
                            @else
                                <div class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs">{{ substr(Auth::user()->name, 0, 1) }}</div>
                            @endif
                        </button>
                        <div class="absolute right-0 top-full pt-2 w-48 hidden group-hover:block z-50">
                            <div class="bg-white rounded-lg shadow-xl border border-gray-100 overflow-hidden">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Hồ sơ cá nhân</a>
                                <form method="POST" action="{{ route('logout') }}">@csrf<button class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Đăng xuất</button></form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-indigo-600">Đăng nhập</a>
                    <a href="{{ route('register') }}" class="font-semibold bg-gray-900 text-white px-4 py-2 rounded hover:bg-gray-800 transition">Đăng ký</a>
                @endauth
            </div>
        </div>
    </header>

    {{-- MENU DANH MỤC --}}
    <nav class="border-b border-gray-200 bg-white/95 backdrop-blur-sm sticky top-16 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Giao diện Máy tính (Hidden on Mobile) --}}
            <div class="hidden md:flex items-center gap-8 overflow-x-auto scrollbar-hide py-3">
                <a href="{{ route('home') }}" class="text-sm font-bold {{ request()->routeIs('home') && !isset($isCategory) ? 'text-indigo-600 border-b-2 border-indigo-600 pb-1' : 'text-gray-600 hover:text-indigo-600 pb-1' }} whitespace-nowrap">Mới nhất</a>
                @foreach($categories as $cat)
                    <a href="{{ route('categories.show', $cat->slug) }}" class="text-sm font-medium whitespace-nowrap transition pb-1 {{ (isset($currentCategory) && $currentCategory->id === $cat->id) ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-600 hover:text-indigo-600' }}">{{ $cat->name }}</a>
                @endforeach
            </div>

            {{-- Giao diện Điện thoại (Button Toggle) --}}
            <div class="md:hidden py-2" x-data="{ openCat: false }">
                <button @click="openCat = !openCat" class="flex items-center justify-between w-full text-sm font-bold text-gray-700 bg-gray-50 px-4 py-2 rounded-lg border border-gray-200">
                    <span>
                        @if(isset($currentCategory)) 📂 {{ $currentCategory->name }}
                        @else ⚡ Danh mục bài viết @endif
                    </span>
                    <svg class="w-4 h-4 transition-transform" :class="{'rotate-180': openCat}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                {{-- Danh sách xổ xuống --}}
                <div x-show="openCat" @click.away="openCat = false" class="absolute left-0 right-0 bg-white border-b border-gray-200 shadow-lg z-50 mt-2 p-4 grid grid-cols-2 gap-3" style="display: none;">
                    <a href="{{ route('home') }}" class="block p-2 rounded text-center text-sm {{ request()->routeIs('home') && !isset($isCategory) ? 'bg-indigo-50 text-indigo-600 font-bold' : 'bg-gray-50 text-gray-600' }}">🔥 Mới nhất</a>
                    @foreach($categories as $cat)
                        <a href="{{ route('categories.show', $cat->slug) }}" class="block p-2 rounded text-center text-sm truncate {{ (isset($currentCategory) && $currentCategory->id === $cat->id) ? 'bg-indigo-50 text-indigo-600 font-bold' : 'bg-gray-50 text-gray-600' }}">
                            {{ $cat->name }}
                        </a>
                    @endforeach
                </div>
            </div>

        </div>
    </nav>

    {{-- === [MỚI] KHU VỰC BANNER QUẢNG CÁO TRƯỢT (NGANG) === --}}
    @if(isset($ads) && $ads->count() > 0)
    <div class="bg-white border-b border-gray-200 py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div id="banner-slider" class="relative w-full h-32 md:h-48 rounded-xl overflow-hidden shadow-sm group bg-gray-100">
                @foreach($ads as $index => $ad)
                    <a href="{{ $ad->link ?? '#' }}" target="_blank" class="ad-slide {{ $index == 0 ? 'active' : '' }} block w-full h-full">
                        <img src="{{ asset('storage/'.$ad->image) }}" class="w-full h-full object-cover" alt="{{ $ad->title }}">
                        <span class="absolute bottom-2 right-2 bg-black/50 text-white text-[10px] px-2 py-0.5 rounded backdrop-blur-sm">QC</span>
                    </a>
                @endforeach

                {{-- Nút điều hướng slide (Chỉ hiện khi có > 1 ảnh) --}}
                @if($ads->count() > 1)
                    <button onclick="changeSlide(-1)" class="absolute left-2 top-1/2 -translate-y-1/2 bg-white/30 hover:bg-white/80 text-black p-1.5 rounded-full opacity-0 group-hover:opacity-100 transition z-20">❮</button>
                    <button onclick="changeSlide(1)" class="absolute right-2 top-1/2 -translate-y-1/2 bg-white/30 hover:bg-white/80 text-black p-1.5 rounded-full opacity-0 group-hover:opacity-100 transition z-20">❯</button>
                    
                    {{-- Dấu chấm --}}
                    <div class="absolute bottom-2 left-1/2 -translate-x-1/2 flex gap-1.5 z-20">
                        @foreach($ads as $index => $ad)
                            <span class="dot w-1.5 h-1.5 rounded-full bg-white/50 cursor-pointer {{ $index == 0 ? 'bg-white' : '' }}" onclick="goToSlide({{ $index }})"></span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-grow w-full">
        
        {{-- PHẦN 1: TÌM KIẾM HOẶC DANH MỤC --}}
        @if(($isSearch ?? false) || ($isCategory ?? false))
            <div class="mb-6 border-b border-gray-200 pb-3">
                <h1 class="text-xl font-bold text-gray-900">
                    @if($isSearch ?? false) Kết quả: <span class="text-indigo-600">"{{ request('search') }}"</span>
                    @else Danh mục: <span class="text-indigo-600">{{ $currentCategory->name }}</span> @endif
                </h1>
            </div>
        @endif

        @php $displayPosts = isset($posts) ? $posts : ($recentPosts ?? collect()); @endphp

        {{-- PHẦN 2: TRANG CHỦ CHÍNH --}}
        @if(isset($heroPost) && !($isSearch ?? false) && !($isCategory ?? false))
            {{-- A. HERO SECTION --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-10">
                {{-- Tin To (Chiếm 8 phần) --}}
                <div class="lg:col-span-8 group relative rounded-xl overflow-hidden shadow-sm h-[400px]">
                     @auth @if(Auth::user()->role === 'admin' || (Auth::user()->role === 'author' && $heroPost->user_id === Auth::id()))
                        <a href="{{ route('admin.posts.edit', $heroPost->id) }}" class="absolute top-3 right-3 z-20 bg-white/90 text-indigo-600 px-2 py-1 rounded text-xs font-bold shadow hover:text-indigo-800">Sửa</a>
                     @endif @endauth
                    <a href="{{ route('posts.show', $heroPost->slug) }}" class="block h-full w-full">
                        @if($heroPost->featured_image) 
                            <img src="{{ asset('storage/' . $heroPost->featured_image) }}" class="w-full h-full object-cover transition duration-700 group-hover:scale-105">
                        @else <div class="w-full h-full bg-gray-800"></div> @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-transparent to-transparent"></div>
                        <div class="absolute bottom-0 left-0 p-6 w-full">
                            <span class="bg-indigo-600 text-white text-[10px] font-bold px-2 py-0.5 rounded mb-2 inline-block uppercase">{{ $heroPost->category->name ?? 'Hot' }}</span>
                            <h2 class="text-2xl md:text-3xl font-bold text-white leading-tight mb-1 group-hover:text-indigo-200 transition">{{ $heroPost->title }}</h2>
                            <div class="flex items-center gap-2 text-white/80 text-xs mt-1">
                                <span>{{ $heroPost->user->name }}</span> &bull; <span>{{ $heroPost->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- Tin Phụ (Chiếm 4 phần) --}}
                <div class="lg:col-span-4 flex flex-col gap-6 h-[400px]">
                    @foreach($featuredPosts as $subPost)
                    <div class="relative flex-1 rounded-xl overflow-hidden group h-full shadow-sm bg-gray-900">
                         @auth @if(Auth::user()->role === 'admin' || (Auth::user()->role === 'author' && $subPost->user_id === Auth::id()))
                            <a href="{{ route('admin.posts.edit', $subPost->id) }}" class="absolute top-2 right-2 z-20 bg-white/90 text-indigo-600 px-2 py-1 rounded text-[10px] font-bold shadow hover:text-indigo-800">Sửa</a>
                         @endif @endauth
                        <a href="{{ route('posts.show', $subPost->slug) }}" class="block h-full w-full">
                            @if($subPost->featured_image) <img src="{{ asset('storage/' . $subPost->featured_image) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500 opacity-90"> @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 to-transparent"></div>
                            <div class="absolute bottom-0 p-4 w-full">
                                <span class="text-indigo-300 text-[10px] font-bold uppercase mb-1 block">{{ $subPost->category->name }}</span>
                                <h3 class="text-base font-bold text-white leading-snug hover:underline line-clamp-2">{{ $subPost->title }}</h3>
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

        {{-- DANH SÁCH BÀI VIẾT (FULL WIDTH - KHÔNG CÒN SIDEBAR) --}}
        @if($displayPosts->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6"> {{-- Tăng lên 4 cột cho rộng --}}
                @foreach($displayPosts as $post)
                    <article class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition duration-300 flex flex-col h-full relative group">
                        
                        {{-- Nút Sửa/Xóa (Fix Mobile) --}}
                        @auth @if(Auth::user()->role === 'admin' || (Auth::user()->role === 'author' && $post->user_id === Auth::id()))
                            <div class="absolute top-2 right-2 z-20 flex gap-1 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition duration-200">
                                <a href="{{ route('admin.posts.edit', $post->id) }}" class="bg-white text-indigo-600 p-1.5 rounded shadow hover:bg-indigo-600 hover:text-white transition" title="Sửa">✏️</a>
                                @if(Auth::user()->role === 'admin')
                                    <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Xóa?')">@csrf @method('DELETE')
                                    <button type="submit" class="bg-white text-red-500 p-1.5 rounded shadow hover:bg-red-500 hover:text-white transition" title="Xóa">🗑️</button></form>
                                @endif
                            </div>
                        @endif @endauth

                        <a href="{{ route('posts.show', $post->slug) }}" class="block h-40 overflow-hidden relative bg-gray-100 shrink-0">
                            @if($post->featured_image) 
                                <img src="{{ asset('storage/' . $post->featured_image) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @else <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs">No Image</div> @endif
                            <span class="absolute top-2 left-2 bg-white/90 backdrop-blur-sm text-indigo-600 text-[10px] font-bold uppercase px-2 py-0.5 rounded shadow-sm">{{ $post->category->name ?? 'Tech' }}</span>
                        </a>

                        <div class="p-3 flex flex-col flex-1">
                            <h3 class="font-bold text-gray-900 text-sm leading-snug mb-2 h-[2.5rem] overflow-hidden">
                                <a href="{{ route('posts.show', $post->slug) }}" class="hover:text-indigo-600 transition">{{ $post->title }}</a>
                            </h3>
                            <p class="text-gray-500 text-xs leading-relaxed mb-3 flex-1 h-[3.6rem] overflow-hidden line-clamp-3">
                                {{ Str::limit(strip_tags($post->content), 100) }}
                            </p>
                            <div class="flex items-center justify-between pt-2 border-t border-gray-100 mt-auto text-[11px] text-gray-400">
                                <div class="flex items-center gap-1.5">
                                    @if($post->user->avatar) <img src="{{ asset('storage/' . $post->user->avatar) }}" class="w-5 h-5 rounded-full object-cover border border-gray-100">
                                    @else <div class="w-5 h-5 rounded-full bg-gray-100 flex items-center justify-center text-[9px] font-bold">{{ substr($post->user->name, 0, 1) }}</div> @endif
                                    <span class="font-medium text-gray-600 truncate max-w-[80px]">{{ $post->user->name }}</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <span>👁 {{ $post->views }}</span>
                                </div>
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
            <span class="bg-indigo-600 text-white px-2 py-0.5 rounded-lg shadow-sm group-hover:bg-indigo-700 transition">Tech</span>
            <span class="text-gray-900">Blog</span>
        </p>
    </footer>

    {{-- JAVASCRIPT CHO SLIDER QUẢNG CÁO --}}
    <script>
        let slideIndex = 0;
        const slides = document.querySelectorAll('.ad-slide');
        const dots = document.querySelectorAll('.dot');

        function showSlides() {
            if (slides.length === 0) return;
            
            // Ẩn hết
            slides.forEach(slide => { slide.classList.remove('active'); slide.style.opacity = 0; });
            dots.forEach(dot => dot.classList.remove('bg-white'));
            
            // Reset index
            if (slideIndex >= slides.length) slideIndex = 0;
            if (slideIndex < 0) slideIndex = slides.length - 1;

            // Hiện slide hiện tại
            slides[slideIndex].classList.add('active');
            slides[slideIndex].style.opacity = 1;
            
            // Active dot
            if(dots.length > 0) {
                dots.forEach(dot => dot.classList.replace('bg-white', 'bg-white/50'));
                dots[slideIndex].classList.replace('bg-white/50', 'bg-white');
            }
        }

        function nextSlide() { slideIndex++; showSlides(); }
        function changeSlide(n) { slideIndex += n; showSlides(); }
        function goToSlide(n) { slideIndex = n; showSlides(); }

        // Tự chạy
        setInterval(nextSlide, 4000); 
        // Chạy lần đầu
        showSlides();
    </script>
</body>
</html>