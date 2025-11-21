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
        
        /* Hiệu ứng chuyển cảnh cho Slider */
        .fade-enter { opacity: 0; }
        .fade-enter-active { opacity: 1; transition: opacity 0.5s ease-in-out; }
        .fade-exit { opacity: 1; }
        .fade-exit-active { opacity: 0; transition: opacity 0.5s ease-in-out; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased flex flex-col min-h-screen">

    {{-- HEADER --}}
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex justify-between items-center">
            <a href="/" class="text-xl font-bold text-indigo-700 flex items-center gap-2">
                <span class="bg-indigo-700 text-white w-8 h-8 flex items-center justify-center rounded text-sm">T</span>
                TechBlog
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
    <nav class="border-b border-gray-200 bg-white/95 backdrop-blur-sm sticky top-16 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-6 overflow-x-auto scrollbar-hide py-2.5 text-sm font-medium">
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') && !isset($isCategory) ? 'text-indigo-600' : 'text-gray-600 hover:text-indigo-600' }} whitespace-nowrap">Mới nhất</a>
                @foreach($categories as $cat)
                    <a href="{{ route('categories.show', $cat->slug) }}" class="whitespace-nowrap transition {{ (isset($currentCategory) && $currentCategory->id === $cat->id) ? 'text-indigo-600' : 'text-gray-600 hover:text-indigo-600' }}">{{ $cat->name }}</a>
                @endforeach
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-grow w-full">
        
        {{-- 1. TÌM KIẾM / DANH MỤC --}}
        @if(($isSearch ?? false) || ($isCategory ?? false))
            <div class="mb-6 border-b border-gray-200 pb-3">
                <h1 class="text-xl font-bold text-gray-900">
                    @if($isSearch ?? false) Kết quả: <span class="text-indigo-600">"{{ request('search') }}"</span>
                    @else Danh mục: <span class="text-indigo-600">{{ $currentCategory->name }}</span> @endif
                </h1>
            </div>
        @endif

        @php $displayPosts = isset($posts) ? $posts : ($recentPosts ?? collect()); @endphp

        @if(isset($heroPost) && !($isSearch ?? false) && !($isCategory ?? false))
            {{-- HERO SECTION (Giữ nguyên phần Hero đẹp) --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-10">
                {{-- Tin To --}}
                <div class="lg:col-span-8 group relative rounded-xl overflow-hidden shadow-sm h-[400px]">
                     @auth @if(Auth::user()->role === 'admin' || (Auth::user()->role === 'author' && $heroPost->user_id === Auth::id()))
                        <a href="{{ route('admin.posts.edit', $heroPost->id) }}" class="absolute top-3 right-3 z-20 bg-white/90 text-indigo-600 px-2 py-1 rounded text-xs font-bold shadow">Sửa</a>
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

                {{-- 2 Tin Nhỏ Bên Phải --}}
                <div class="lg:col-span-4 flex flex-col gap-6 h-[400px]">
                    @foreach($featuredPosts as $subPost)
                    <div class="relative flex-1 rounded-xl overflow-hidden group h-full shadow-sm bg-gray-900">
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

        {{-- === PHẦN CHÍNH: CHIA CỘT BÀI VIẾT (TRÁI) VÀ QUẢNG CÁO (PHẢI) === --}}
        
        <div class="flex flex-col lg:flex-row gap-8">
            
            {{-- CỘT TRÁI: DANH SÁCH BÀI VIẾT (Chiếm 75%) --}}
            <div class="w-full lg:w-3/4">
                @if($displayPosts->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($displayPosts as $post)
                            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition group flex flex-col h-full relative">
                                {{-- Nút Sửa/Xóa --}}
                                @auth @if(Auth::user()->role === 'admin' || (Auth::user()->role === 'author' && $post->user_id === Auth::id()))
                                    <div class="absolute top-2 right-2 z-20 flex gap-1 opacity-0 group-hover:opacity-100 transition">
                                        <a href="{{ route('admin.posts.edit', $post->id) }}" class="bg-white text-indigo-600 p-1 rounded shadow hover:bg-indigo-600 hover:text-white transition text-xs">✏️</a>
                                        @if(Auth::user()->role === 'admin')
                                            <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Xóa?')">@csrf @method('DELETE')<button class="bg-white text-red-500 p-1 rounded shadow hover:bg-red-500 hover:text-white transition text-xs">🗑️</button></form>
                                        @endif
                                    </div>
                                @endif @endauth
                                
                                <a href="{{ route('posts.show', $post->slug) }}" class="block h-48 overflow-hidden relative bg-gray-100 shrink-0">
                                    @if($post->featured_image) 
                                        <img src="{{ asset('storage/' . $post->featured_image) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                    @else <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs">No Image</div> @endif
                                    <span class="absolute top-2 left-2 bg-white/90 backdrop-blur-sm text-indigo-600 text-[10px] font-bold uppercase px-2 py-1 rounded shadow-sm tracking-wide">
                                        {{ $post->category->name ?? 'Tech' }}
                                    </span>
                                </a>
                                
                                <div class="p-3 flex flex-col flex-1">
                                    <h3 class="font-bold text-gray-900 text-[16px] leading-snug mb-2 group-hover:text-indigo-600 transition line-clamp-2 h-[2.5rem] overflow-hidden">
                                        <a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a>
                                    </h3>
                                    <p class="text-gray-500 text-sm leading-relaxed mb-3 flex-1 h-[4.5rem] overflow-hidden line-clamp-3">
                                        {{ Str::limit(strip_tags($post->content), 100) }}
                                    </p>
                                    <div class="flex items-center justify-between pt-2 border-t border-gray-100 mt-auto text-[11px] text-gray-400">
                                        <div class="flex items-center gap-1.5">
                                            @if($post->user->avatar) <img src="{{ asset('storage/' . $post->user->avatar) }}" class="w-5 h-5 rounded-full object-cover border border-gray-100">
                                            @else <div class="w-5 h-5 rounded-full bg-gray-100 flex items-center justify-center text-[9px] font-bold">{{ substr($post->user->name, 0, 1) }}</div> @endif
                                            <span class="font-medium text-gray-600 truncate max-w-[80px]">{{ $post->user->name }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            {{-- [MỚI] Hiển thị View --}}
                                            <span class="flex items-center gap-1" title="Lượt xem">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                                {{ $post->views }}
                                            </span>
                                            <span>{{ $post->created_at->format('d/m') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-8 text-sm">{{ $displayPosts->links() }}</div>
                @else
                    <div class="text-center py-20 text-gray-500"><p class="text-lg">Chưa có bài viết nào.</p></div>
                @endif
            </div>

            {{-- CỘT PHẢI: QUẢNG CÁO (Chiếm 25%) --}}
            <div class="w-full lg:w-1/4 space-y-6">
                
                {{-- KHUNG QUẢNG CÁO TRƯỢT (Sticky) --}}
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 sticky top-24">
                    <h4 class="font-bold text-gray-400 text-[10px] uppercase mb-3 tracking-widest text-center">Quảng cáo</h4>
                    
                    {{-- SLIDER --}}
                    <div id="ad-slider" class="relative w-full h-[300px] overflow-hidden rounded-lg bg-gray-100 group">
                        {{-- Ảnh 1 --}}
                        <a href="#" class="ad-slide absolute inset-0 transition-opacity duration-700 opacity-100 block h-full">
                            <img src="https://image.pollinations.ai/prompt/tech%20gadget%20sale%20banner%20colorful?width=300&height=400&nologo=true" class="w-full h-full object-cover" alt="Quảng cáo 1">
                        </a>
                        {{-- Ảnh 2 --}}
                        <a href="#" class="ad-slide absolute inset-0 transition-opacity duration-700 opacity-0 block h-full">
                            <img src="https://image.pollinations.ai/prompt/new%20laptop%20promotion%20cyberpunk?width=300&height=400&nologo=true" class="w-full h-full object-cover" alt="Quảng cáo 2">
                        </a>
                        {{-- Ảnh 3 --}}
                        <a href="#" class="ad-slide absolute inset-0 transition-opacity duration-700 opacity-0 block h-full">
                            <img src="https://image.pollinations.ai/prompt/coding%20course%20advertisement%20minimalist?width=300&height=400&nologo=true" class="w-full h-full object-cover" alt="Quảng cáo 3">
                        </a>

                        {{-- Nút điều hướng nhỏ --}}
                        <div class="absolute bottom-2 left-0 w-full flex justify-center gap-1 z-10">
                            <button class="w-2 h-2 rounded-full bg-white/50 hover:bg-white transition ad-dot"></button>
                            <button class="w-2 h-2 rounded-full bg-white/50 hover:bg-white transition ad-dot"></button>
                            <button class="w-2 h-2 rounded-full bg-white/50 hover:bg-white transition ad-dot"></button>
                        </div>
                    </div>
                    
                    <p class="text-center text-[10px] text-gray-400 mt-2">Liên hệ quảng cáo: contact@techblog.com</p>
                </div>

            </div>
        </div>

    </main>

    <footer class="bg-white border-t border-gray-200 py-8 text-center text-gray-500 text-xs">
        <p class="font-bold text-gray-900 text-base mb-1">TechBlog.</p>
        <p>© {{ date('Y') }} Xây dựng với Laravel 11 & Gemini AI.</p>
    </footer>

    {{-- SCRIPT CHẠY SLIDER QUẢNG CÁO --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const slides = document.querySelectorAll('.ad-slide');
            const dots = document.querySelectorAll('.ad-dot');
            let currentSlide = 0;
            const totalSlides = slides.length;

            function showSlide(index) {
                // Ẩn tất cả
                slides.forEach(s => s.classList.remove('opacity-100'));
                slides.forEach(s => s.classList.add('opacity-0'));
                dots.forEach(d => d.classList.replace('bg-white', 'bg-white/50'));

                // Hiện slide hiện tại
                slides[index].classList.remove('opacity-0');
                slides[index].classList.add('opacity-100');
                dots[index].classList.replace('bg-white/50', 'bg-white');
            }

            function nextSlide() {
                currentSlide = (currentSlide + 1) % totalSlides;
                showSlide(currentSlide);
            }

            // Tự động chạy sau 3 giây
            setInterval(nextSlide, 3000);
            
            // Click vào chấm tròn
            dots.forEach((dot, idx) => {
                dot.addEventListener('click', () => {
                    currentSlide = idx;
                    showSlide(currentSlide);
                });
            });
        });
    </script>
</body>
</html>