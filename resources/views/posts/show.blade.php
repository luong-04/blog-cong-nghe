<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $post->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Merriweather:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .article-body { font-family: 'Merriweather', serif; font-size: 1.125rem; line-height: 1.9; color: #1f2937; }
        .article-body h2 { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.8rem; font-weight: 800; color: #111827; margin-top: 2.5rem; margin-bottom: 1rem; }
        .article-body h3 { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.4rem; font-weight: 700; color: #374151; margin-top: 2rem; margin-bottom: 0.75rem; }
        .article-body p { margin-bottom: 1.5rem; }
        .article-body img { border-radius: 0.75rem; margin: 2.5rem auto; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); max-width: 100%; }
        .article-body ul { list-style-type: disc; padding-left: 1.5rem; margin-bottom: 1.5rem; }
        .article-body blockquote { border-left: 4px solid #4f46e5; padding-left: 1rem; font-style: italic; color: #4b5563; margin: 2rem 0; }
    </style>
</head>
<body class="bg-white">

    {{-- Navigation Minimal --}}
    <nav class="border-b border-gray-100 sticky top-0 bg-white/90 backdrop-blur-md z-50">
        <div class="max-w-4xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-gray-600 hover:text-indigo-600 font-bold transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Trang chủ
            </a>
            @auth
                @if(Auth::user()->role === 'admin' || (Auth::user()->role === 'author' && $post->user_id === Auth::id()))
                    <a href="{{ route('admin.posts.edit', $post->id) }}" class="text-sm font-bold text-indigo-600 hover:underline">Sửa bài viết</a>
                @endif
            @endauth
        </div>
    </nav>

    <article class="max-w-3xl mx-auto px-4 py-10">
        {{-- Header Bài Viết --}}
        <div class="text-center mb-10">
            <a href="#" class="inline-block bg-indigo-50 text-indigo-700 text-sm font-bold px-3 py-1 rounded-full mb-4 uppercase tracking-wide hover:bg-indigo-100 transition">
                {{ $post->category->name ?? 'Review' }}
            </a>
            <h1 class="text-3xl md:text-5xl font-extrabold text-gray-900 leading-tight mb-6">{{ $post->title }}</h1>
            
            <div class="flex items-center justify-center gap-4 text-gray-500 text-sm">
                <div class="flex items-center gap-2">
                    @if($post->user->avatar)
                        <img src="{{ asset('storage/' . $post->user->avatar) }}" class="w-8 h-8 rounded-full object-cover">
                    @endif
                    <span class="font-bold text-gray-900">{{ $post->user->name }}</span>
                </div>
                <span>&bull;</span>
                <time>{{ $post->created_at->format('d/m/Y') }}</time>
                <div class="flex items-center gap-1" title="Lượt xem">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    <span>{{ $post->views }}</span>
                </div>
            </div>
        </div>

        {{-- Ảnh Bìa --}}
        @if($post->featured_image)
            <div class="mb-12 rounded-2xl overflow-hidden shadow-lg">
                <img src="{{ asset('storage/' . $post->featured_image) }}" class="w-full object-cover">
            </div>
        @endif

        {{-- Nội Dung --}}
        <div class="article-body">
            {!! $post->content !!}
        </div>

        {{-- Tác giả Bio (Cuối bài) --}}
        <div class="mt-16 p-6 bg-gray-50 rounded-xl flex items-center gap-4 border border-gray-100">
            <div class="shrink-0">
                @if($post->user->avatar)
                    <img src="{{ asset('storage/' . $post->user->avatar) }}" class="w-16 h-16 rounded-full object-cover ring-2 ring-white">
                @else
                    <div class="w-16 h-16 rounded-full bg-indigo-200 flex items-center justify-center text-indigo-700 font-bold text-xl">{{ substr($post->user->name, 0, 1) }}</div>
                @endif
            </div>
            <div>
                <p class="text-xs text-gray-500 font-bold uppercase mb-1">Đăng bởi</p>
                <h4 class="text-lg font-bold text-gray-900">{{ $post->user->name }}</h4>
                <p class="text-sm text-gray-600">Tác giả tại TechBlog. Đam mê công nghệ và chia sẻ kiến thức.</p>
            </div>
        </div>

        {{-- Bình Luận --}}
        <div class="mt-12 pt-10 border-t border-gray-100">
            <h3 class="text-2xl font-bold text-gray-900 mb-6">Bình luận ({{ $post->comments->count() }})</h3>
            <form action="{{ route('comments.store', $post->id) }}" method="POST" class="mb-10 relative">
                @csrf
                <textarea name="content" rows="3" class="w-full border border-gray-300 rounded-xl p-4 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none shadow-sm" placeholder="Viết bình luận của bạn..." required></textarea>
                <div class="absolute bottom-3 right-3">
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-1.5 rounded-lg text-sm font-bold hover:bg-indigo-700 transition">Gửi</button>
                </div>
                @auth <p class="mt-2 text-xs text-gray-500 ml-1">Đang đăng nhập là: <strong>{{ Auth::user()->name }}</strong></p> @endauth
            </form>

            <div class="space-y-6">
                @foreach($post->comments as $comment)
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center font-bold text-gray-500 shrink-0">
                            {{ substr($comment->author_name, 0, 1) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <h4 class="font-bold text-gray-900 text-sm">{{ $comment->author_name }}</h4>
                                <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-gray-700 text-sm leading-relaxed">{{ $comment->content }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </article>

</body>
</html>