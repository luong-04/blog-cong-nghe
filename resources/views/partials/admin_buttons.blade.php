@auth
    @if(Auth::user()->role === 'admin' || (Auth::user()->role === 'author' && $post->user_id === Auth::id()))
        <div class="absolute top-3 right-3 z-20 flex gap-2 opacity-0 group-hover:opacity-100 transition duration-200">
            {{-- Nút Sửa --}}
            <a href="{{ route('admin.posts.edit', $post->id) }}" class="bg-white/90 text-indigo-600 p-2 rounded-full shadow hover:bg-indigo-600 hover:text-white transition" title="Sửa">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
            </a>
            
            {{-- Nút Xóa (Chỉ Admin) --}}
            @if(Auth::user()->role === 'admin')
                <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Xóa bài này?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="bg-white/90 text-red-600 p-2 rounded-full shadow hover:bg-red-600 hover:text-white transition" title="Xóa">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </form>
            @endif
        </div>
    @endif
@endauth