<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $post->title }} - Blog Công Nghệ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    {{-- CSS cho bài viết đẹp hơn (Typography) --}}
    <style>
        .prose h2 { font-size: 1.5rem; font-weight: 700; margin-top: 2rem; margin-bottom: 1rem; color: #1f2937; }
        .prose h3 { font-size: 1.25rem; font-weight: 600; margin-top: 1.5rem; margin-bottom: 0.75rem; color: #374151; }
        .prose p { margin-bottom: 1.25rem; line-height: 1.75; color: #4b5563; }
        .prose ul { list-style-type: disc; padding-left: 1.5rem; margin-bottom: 1.25rem; }
        .prose li { margin-bottom: 0.5rem; }
        .prose strong { font-weight: 700; color: #111827; }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">

    <nav class="bg-white shadow mb-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="text-indigo-600 font-bold hover:underline">← Quay lại trang chủ</a>
            @auth
                <a href="{{ route('admin.posts.edit', $post->id) }}" class="text-sm bg-yellow-100 text-yellow-700 px-3 py-1 rounded">Sửa bài này</a>
            @endauth
        </div>
    </nav>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        <article class="bg-white shadow-lg rounded-lg overflow-hidden">
            @if($post->featured_image)
                <img src="{{ asset('storage/' . $post->featured_image) }}" class="w-full h-96 object-cover">
            @endif

            <div class="p-8 md:p-12">
                <div class="flex items-center gap-4 text-sm text-gray-500 mb-6">
                    <span class="bg-indigo-100 text-indigo-800 px-2 py-1 rounded">{{ $post->category->name }}</span>
                    <span>{{ $post->created_at->format('d/m/Y') }}</span>
                    <span>Bởi {{ $post->user->name }}</span>
                </div>

                <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-8 leading-tight">
                    {{ $post->title }}
                </h1>

                {{-- HIỂN THỊ NỘI DUNG MARKDOWN --}}
                <div class="prose max-w-none">
                    {!! Str::markdown($post->content) !!}
                </div>
            </div>
        </article>
        {{-- PHẦN BÌNH LUẬN --}}
        <div class="mt-12 bg-white shadow-lg rounded-lg p-8">
            <h3 class="text-2xl font-bold text-gray-900 mb-6">Bình luận ({{ $post->comments->count() }})</h3>

            {{-- Thông báo thành công --}}
            @if(session('success'))
                <div class="bg-green-100 text-green-700 p-4 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Form Bình luận --}}
            <form action="{{ route('comments.store', $post->id) }}" method="POST" class="mb-10">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nội dung bình luận</label>
                    <textarea name="content" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200" required></textarea>
                </div>

                @auth
                    <p class="text-sm text-gray-600 mb-4">Bạn đang bình luận với tên: <strong>{{ Auth::user()->name }}</strong></p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Tên của bạn</label>
                            <input type="text" name="author_name" class="w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                            <input type="email" name="author_email" class="w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                    </div>
                @endauth

                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 font-bold transition">
                    Gửi bình luận
                </button>
            </form>

            {{-- Danh sách bình luận cũ --}}
            <div class="space-y-6">
                @foreach($post->comments as $comment)
                    <div class="border-b pb-4 last:border-0">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-bold text-gray-800">{{ $comment->author_name }}</h4>
                            <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-gray-600">{{ $comment->content }}</p>
                    </div>
                @endforeach

                @if($post->comments->isEmpty())
                    <p class="text-gray-500 text-center italic">Chưa có bình luận nào. Hãy là người đầu tiên!</p>
                @endif
            </div>
        </div>
    </main>

</body>
</html>