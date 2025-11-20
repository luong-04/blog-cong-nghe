<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $post->title }} - Blog Công Nghệ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    {{-- CSS CHUYÊN NGHIỆP CHO BÀI VIẾT (Giống Viettel Store/TinhTe) --}}
    <style>
        /* Reset lại style cho phần nội dung bài viết */
        .article-content {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.8; /* Giãn dòng dễ đọc */
            font-size: 1.1rem;
        }
        
        /* Tiêu đề H2 (Mục lớn) */
        .article-content h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #BE1E2D; /* Màu đỏ giống Viettel Store hoặc dùng #2563eb (xanh) */
            margin-top: 2.5rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #f1f1f1; /* Gạch chân nhẹ */
        }

        /* Tiêu đề H3 (Mục nhỏ) */
        .article-content h3 {
            font-size: 1.4rem;
            font-weight: 600;
            color: #444;
            margin-top: 2rem;
            margin-bottom: 0.75rem;
        }

        /* Đoạn văn */
        .article-content p {
            margin-bottom: 1.5rem;
            text-align: justify; /* Căn đều 2 bên */
        }

        /* Danh sách dấu chấm */
        .article-content ul {
            list-style-type: disc;
            padding-left: 1.5rem;
            margin-bottom: 1.5rem;
            background-color: #f9fafb;
            padding: 1.5rem 1.5rem 1.5rem 3rem;
            border-radius: 0.5rem;
            border-left: 4px solid #BE1E2D;
        }
        .article-content li {
            margin-bottom: 0.5rem;
        }

        /* Chữ đậm */
        .article-content strong {
            font-weight: 700;
            color: #000;
        }

        /* Hình ảnh trong bài */
        .article-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 2rem auto; /* Căn giữa */
            display: block;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        /* Trích dẫn */
        .article-content blockquote {
            font-style: italic;
            color: #555;
            border-left: 4px solid #ccc;
            padding-left: 1rem;
            margin: 1.5rem 0;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased">

    {{-- Navigation --}}
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-gray-700 hover:text-red-600 transition font-bold">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Về trang chủ
            </a>
            
            @auth
                {{-- Admin được sửa tất cả, Author chỉ sửa bài mình --}}
                @if(Auth::user()->role === 'admin' || (Auth::user()->role === 'author' && $post->user_id === Auth::id()))
                    <a href="{{ route('admin.posts.edit', $post->id) }}" class="...">
                        ✏️ Sửa bài viết
                    </a>
                @endif
            @endauth
        </div>
    </nav>

    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            
            {{-- Ảnh bìa --}}
            @if($post->featured_image)
                <img src="{{ asset('storage/' . $post->featured_image) }}" class="w-full h-[400px] object-cover">
            @endif

            <div class="p-8 md:p-12">
                {{-- Header bài viết --}}
                <div class="mb-8">
                    <span class="text-red-600 font-bold uppercase tracking-wider text-sm">{{ $post->category->name ?? 'Tin tức' }}</span>
                    <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mt-2 mb-4 leading-tight">
                        {{ $post->title }}
                    </h1>
                    <div class="flex items-center text-gray-500 text-sm space-x-4">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            {{ $post->user->name }}
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            {{ $post->created_at->format('d/m/Y') }}
                        </span>
                    </div>
                </div>

                {{-- NỘI DUNG BÀI VIẾT (Đã áp dụng CSS .article-content) --}}
                <div class="article-content">
                    {!! $post->content !!}
                </div>

            </div>
        </div>

        {{-- Bình luận (Giữ nguyên code cũ của bạn ở đây) --}}
        <div class="mt-8 bg-white rounded-xl shadow-sm p-8">
             {{-- ... code bình luận cũ ... --}}
             <h3 class="text-xl font-bold mb-4">Bình luận</h3>
             {{-- Copy lại form bình luận vào đây --}}
             <form action="{{ route('comments.store', $post->id) }}" method="POST" class="mb-6">
                @csrf
                <textarea name="content" class="w-full border-gray-300 rounded-lg p-3 mb-2 focus:ring-red-500 focus:border-red-500" rows="3" placeholder="Bạn nghĩ gì về sản phẩm này?"></textarea>
                <div class="flex justify-end">
                    @auth
                        <button class="bg-red-600 text-white px-6 py-2 rounded hover:bg-red-700 transition">Gửi</button>
                    @else
                        <div class="flex gap-2 w-full">
                            <input type="text" name="author_name" placeholder="Tên" class="border-gray-300 rounded px-2 py-1 w-1/3" required>
                            <input type="email" name="author_email" placeholder="Email" class="border-gray-300 rounded px-2 py-1 w-1/3" required>
                            <button class="bg-red-600 text-white px-6 py-2 rounded hover:bg-red-700 transition ml-auto">Gửi</button>
                        </div>
                    @endauth
                </div>
             </form>
             
             {{-- List comment --}}
             @foreach($post->comments as $comment)
                <div class="border-b py-3">
                    <div class="font-bold text-gray-800">{{ $comment->author_name }}</div>
                    <div class="text-gray-600 mt-1">{{ $comment->content }}</div>
                </div>
             @endforeach
        </div>
    </main>

    <footer class="bg-white mt-12 py-8 border-t text-center text-gray-500">
        &copy; {{ date('Y') }} Blog Công Nghệ.
    </footer>
</body>
</html>