<article class="group flex flex-col h-full bg-white border border-gray-200 rounded-2xl overflow-hidden hover:shadow-lg transition-all duration-300 relative">
    {{-- Nút Sửa/Xóa (Gọi component) --}}
    @include('partials.admin_buttons', ['post' => $post])

    <a href="{{ route('posts.show', $post->slug) }}" class="block aspect-video overflow-hidden bg-gray-100 relative">
        @if($post->featured_image)
            <img src="{{ asset('storage/' . $post->featured_image) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
        @else
            <div class="w-full h-full flex items-center justify-center text-gray-400">No Image</div>
        @endif
        <span class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm text-indigo-600 text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded-md shadow-sm">
            {{ $post->category->name ?? 'Tech' }}
        </span>
    </a>
    
    <div class="p-4 flex flex-col flex-1">
        <h3 class="text-lg font-bold text-gray-900 mb-2 leading-snug group-hover:text-indigo-600 transition line-clamp-2">
            <a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a>
        </h3>
        
        {{-- [QUAN TRỌNG] Đã sửa lỗi hiển thị tại đây: Dùng strip_tags thay vì Str::markdown --}}
        <p class="text-gray-500 text-sm line-clamp-2 mb-4 flex-1">
            {{ Str::limit(strip_tags($post->content), 100) }}
        </p>

        <div class="flex items-center justify-between pt-3 border-t border-gray-100 mt-auto">
            <div class="flex items-center gap-2">
                @if($post->user->avatar)
                    <img src="{{ asset('storage/' . $post->user->avatar) }}" class="w-6 h-6 rounded-full object-cover border border-gray-100">
                @else
                    <div class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 text-[10px] font-bold">{{ substr($post->user->name, 0, 1) }}</div>
                @endif
                <span class="text-xs font-medium text-gray-900">{{ $post->user->name }}</span>
            </div>
            <span class="text-xs text-gray-400">{{ $post->created_at->format('d/m') }}</span>
        </div>
    </div>
</article>