<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $post->title }} - TechBlog</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Merriweather:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; font-size: 15px; }
        .custom-scrollbar::-webkit-scrollbar { height: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 4px; }
        
        /* CSS Slider Quảng cáo */
        .ad-slide { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; transition: opacity 0.8s ease-in-out; }
        .ad-slide.active { opacity: 1; z-index: 10; }

        /* CSS Nội dung bài viết */
        .article-content { font-family: 'Merriweather', serif; line-height: 1.9; color: #1f2937; font-size: 1.125rem; }
        .article-content h2 { font-family: 'Inter', sans-serif; font-size: 1.75rem; font-weight: 800; color: #111827; margin-top: 2.5rem; margin-bottom: 1rem; }
        .article-content h3 { font-family: 'Inter', sans-serif; font-size: 1.4rem; font-weight: 700; color: #374151; margin-top: 2rem; margin-bottom: 0.75rem; }
        .article-content p { margin-bottom: 1.5rem; text-align: justify; }
        .article-content img { border-radius: 0.75rem; margin: 2rem auto; box-shadow: 0 4px 15px rgba(0,0,0,0.05); max-width: 100%; display: block; }
        .article-content ul { list-style-type: disc; padding-left: 1.5rem; margin-bottom: 1.5rem; background: #f9fafb; padding: 1.5rem 2rem; border-radius: 0.75rem; }
        .article-content li { margin-bottom: 0.5rem; }
    </style>
</head>
<body class="bg-white text-gray-900 antialiased flex flex-col min-h-screen overflow-x-hidden w-full">

    {{-- HEADER (ĐỒNG BỘ) --}}
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex justify-between items-center">
            <a href="/" class="group flex items-center gap-1.5 text-xl md:text-2xl font-extrabold tracking-tight hover:opacity-90 transition shrink-0">
                <span class="bg-indigo-600 text-white px-2 py-0.5 rounded-lg shadow-sm group-hover:bg-indigo-700 transition">Tech</span>
                <span class="text-gray-900">Blog</span>
            </a>

            <form action="{{ route('home') }}" method="GET" class="hidden md:flex flex-1 max-w-md mx-8 relative">
                <input type="text" name="search" placeholder="Tìm kiếm..." class="w-full bg-gray-100 border-0 rounded-full py-2 pl-4 pr-10 text-sm focus:ring-1 focus:ring-indigo-500 transition">
                <button class="absolute right-3 top-2 text-gray-400 hover:text-indigo-600">🔍</button>
            </form>

            <div class="flex items-center gap-3 text-sm shrink-0">
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
                    <a href="{{ route('register') }}" class="ml-2 font-semibold bg-gray-900 text-white px-4 py-2 rounded hover:bg-gray-800 transition">Đăng ký</a>
                @endauth
            </div>
        </div>
    </header>

    {{-- MENU DANH MỤC (ĐỒNG BỘ) --}}
    <nav class="border-b border-gray-200 bg-white/95 backdrop-blur-sm sticky top-16 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-6 overflow-x-auto custom-scrollbar py-2.5 text-sm font-medium">
                <a href="{{ route('home') }}" class="shrink-0 whitespace-nowrap text-gray-600 hover:text-indigo-600">Mới nhất</a>
                @foreach($categories as $cat)
                    <a href="{{ route('categories.show', $cat->slug) }}" class="shrink-0 whitespace-nowrap transition {{ ($post->category_id === $cat->id) ? 'text-indigo-600 font-bold' : 'text-gray-600 hover:text-indigo-600' }}">{{ $cat->name }}</a>
                @endforeach
            </div>
        </div>
    </nav>

    {{-- BANNER QUẢNG CÁO (ĐỒNG BỘ) --}}
    @if(isset($ads) && $ads->count() > 0)
    <div class="bg-white border-b border-gray-200 py-5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div id="banner-slider" class="relative w-full h-32 md:h-48 rounded-xl overflow-hidden shadow-sm group bg-gray-100">
                @foreach($ads as $index => $ad)
                    <a href="{{ $ad->link ?? '#' }}" target="_blank" class="ad-slide {{ $index == 0 ? 'active' : '' }} block w-full h-full">
                        <img src="{{ asset('storage/'.$ad->image) }}" class="w-full h-full object-cover" alt="{{ $ad->title }}">
                        <span class="absolute bottom-2 right-2 bg-black/40 text-white text-[10px] px-2 py-0.5 rounded backdrop-blur-sm">QC</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- MAIN CONTENT: BÀI VIẾT CHI TIẾT --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-grow w-full">
        
        {{-- Thanh điều hướng Breadcrumb --}}
        <div class="mb-6 text-sm text-gray-500 flex items-center gap-2">
            <a href="{{ route('home') }}" class="hover:text-indigo-600">Trang chủ</a>
            <span>/</span>
            <a href="{{ route('categories.show', $post->category->slug) }}" class="hover:text-indigo-600 font-semibold">{{ $post->category->name }}</a>
        </div>

        <article class="max-w-3xl mx-auto">
            {{-- Header Bài Viết --}}
            <div class="text-center mb-8">
                <a href="{{ route('categories.show', $post->category->slug) }}" class="inline-block bg-indigo-50 text-indigo-700 text-xs font-bold px-3 py-1 rounded-full mb-4 uppercase tracking-wide hover:bg-indigo-100 transition">
                    {{ $post->category->name }}
                </a>
                <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 leading-tight mb-6">{{ $post->title }}</h1>
                
                <div class="flex items-center justify-center gap-4 text-gray-500 text-sm">
                    <div class="flex items-center gap-2">
                        @if($post->user->avatar) <img src="{{ asset('storage/' . $post->user->avatar) }}" class="w-8 h-8 rounded-full object-cover">
                        @else <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs">{{ substr($post->user->name, 0, 1) }}</div> @endif
                        <span class="font-bold text-gray-900">{{ $post->user->name }}</span>
                    </div>
                    <span>&bull;</span> <time>{{ $post->created_at->format('d/m/Y') }}</time>
                    <span>&bull;</span> <span class="flex items-center gap-1">👁 {{ $post->views }}</span>
                    
                    @auth
                        @if(Auth::user()->role === 'admin' || (Auth::user()->role === 'author' && $post->user_id === Auth::id()))
                            <a href="{{ route('admin.posts.edit', $post->id) }}" class="ml-2 text-indigo-600 font-bold hover:underline">✏️ Sửa</a>
                        @endif
                    @endauth
                </div>
            </div>

            {{-- Ảnh Bìa --}}
            @if($post->featured_image)
                <div class="mb-10 rounded-2xl overflow-hidden shadow-sm">
                    <img src="{{ asset('storage/' . $post->featured_image) }}" class="w-full object-cover" alt="{{ $post->title }}">
                </div>
            @endif

            {{-- Nội Dung --}}
            <div class="article-content">
                {!! $post->content !!}
            </div>

            {{-- Bình Luận --}}
            <div class="mt-16 pt-10 border-t border-gray-100">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">Bình luận (<span id="comment-count">{{ $post->comments->count() }}</span>)</h3>
                
                <form id="comment-form" action="{{ route('comments.store', $post->id) }}" class="mb-10 relative">
                    @csrf
                    @if(!Auth::check())
                        <div class="grid grid-cols-2 gap-4 mb-3">
                            <input type="text" name="author_name" placeholder="Tên của bạn" class="w-full border border-gray-300 rounded-lg p-3 text-sm outline-none focus:ring-2 focus:ring-indigo-500" required>
                            <input type="email" name="author_email" placeholder="Email" class="w-full border border-gray-300 rounded-lg p-3 text-sm outline-none focus:ring-2 focus:ring-indigo-500" required>
                        </div>
                    @endif
                    <textarea name="content" rows="3" class="w-full border border-gray-300 rounded-xl p-4 text-sm focus:ring-2 focus:ring-indigo-500 outline-none shadow-sm" placeholder="Viết bình luận..." required></textarea>
                    <div class="mt-3 flex justify-end">
                        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg text-sm font-bold hover:bg-indigo-700 transition shadow-md">Gửi</button>
                    </div>
                </form>

                <div class="space-y-6" id="comments-list">
                    @foreach($post->comments as $comment)
                        @include('partials.comment_item', ['comment' => $comment])
                    @endforeach
                    @if($post->comments->isEmpty())
                        <p class="text-center text-gray-400 italic" id="no-comment-msg">Chưa có bình luận nào.</p>
                    @endif
                </div>
            </div>
        </article>

        {{-- BÀI VIẾT LIÊN QUAN --}}
        @if(isset($relatedPosts) && $relatedPosts->count() > 0)
            <div class="mt-16 border-t border-gray-200 pt-10">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Bài viết cùng chủ đề</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($relatedPosts as $relPost)
                        <a href="{{ route('posts.show', $relPost->slug) }}" class="group block bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-md transition">
                            <div class="h-40 overflow-hidden bg-gray-100">
                                @if($relPost->featured_image) <img src="{{ asset('storage/' . $relPost->featured_image) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500"> @endif
                            </div>
                            <div class="p-4">
                                <h4 class="font-bold text-gray-900 text-sm group-hover:text-indigo-600 line-clamp-2">{{ $relPost->title }}</h4>
                                <p class="text-xs text-gray-500 mt-2">{{ $relPost->created_at->format('d/m/Y') }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </main>

    <footer class="bg-white border-t border-gray-200 py-8 text-center text-gray-500 text-xs">
        <p>© {{ date('Y') }} 
            <span class="bg-indigo-600 text-white px-2 py-0.5 rounded-lg shadow-sm group-hover:bg-indigo-700 transition">Tech</span>
            <span class="text-gray-900">Blog</span>
        </p>
    </footer>
    {{-- JAVASCRIPT AJAX --}}
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // 1. Gửi bình luận
        document.getElementById('comment-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const btn = form.querySelector('button[type="submit"]');
            const oldText = btn.innerText;
            btn.disabled = true; btn.innerText = "Đang gửi...";

            fetch(form.action, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(form)
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    document.getElementById('comments-list').insertAdjacentHTML('afterbegin', data.html);
                    form.reset();
                    const noMsg = document.getElementById('no-comment-msg');
                    if(noMsg) noMsg.remove();
                    
                    // Tăng số lượng comment
                    let countSpan = document.getElementById('comment-count');
                    countSpan.innerText = parseInt(countSpan.innerText) + 1;
                } else {
                    alert('Lỗi: ' + JSON.stringify(data));
                }
            })
            .catch(err => console.error(err))
            .finally(() => { btn.disabled = false; btn.innerText = oldText; });
        });

        // 2. Xóa bình luận
        function deleteComment(id) {
            if(!confirm('Xóa bình luận này?')) return;
            fetch(`/comments/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' }
            }).then(res => res.json()).then(data => {
                if(data.status === 'success') {
                    document.getElementById(`comment-${id}`).remove();
                     // Giảm số lượng comment
                    let countSpan = document.getElementById('comment-count');
                    countSpan.innerText = parseInt(countSpan.innerText) - 1;
                }
            });
        }

        // 3. Duyệt bình luận
        function approveComment(id) {
            fetch(`/comments/${id}/approve`, {
                method: 'PATCH',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' }
            }).then(res => res.json()).then(data => {
                if(data.status === 'success') {
                    const badge = document.getElementById(`badge-pending-${id}`);
                    if(badge) badge.remove();
                    const btnApprove = document.getElementById(`btn-approve-${id}`);
                    if(btnApprove) btnApprove.remove();
                }
            });
        }

        // 4. Sửa bình luận
        function toggleEdit(id) {
            document.getElementById(`form-edit-${id}`).classList.toggle('hidden');
            document.getElementById(`comment-body-${id}`).classList.toggle('hidden');
        }

        function updateComment(e, id) {
            e.preventDefault();
            const form = e.target;
            const content = form.querySelector('textarea').value;

            fetch(`/comments/${id}`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ content: content })
            }).then(res => res.json()).then(data => {
                if(data.status === 'success') {
                    document.getElementById(`comment-body-${id}`).innerHTML = `<p class="text-gray-700 text-sm leading-relaxed whitespace-pre-wrap">${data.content}</p>`;
                    toggleEdit(id);
                }
            });
        }
    </script>
</body>
</html>