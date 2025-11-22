<div class="flex gap-4 border-b border-gray-100 pb-4 last:border-0 group animate-fade-in" id="comment-{{ $comment->id }}">
    <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center font-bold text-gray-500 shrink-0 border border-gray-200 overflow-hidden">
        @if($comment->user && $comment->user->avatar)
            <img src="{{ asset('storage/' . $comment->user->avatar) }}" class="w-full h-full object-cover">
        @else
            {{ substr($comment->author_name, 0, 1) }}
        @endif
    </div>
    
    <div class="flex-1">
        <div class="flex items-center justify-between mb-1">
            <div class="flex items-center gap-2">
                <h4 class="font-bold text-gray-900 text-sm">{{ $comment->author_name }}</h4>
                <span class="text-xs text-gray-400">&bull; {{ $comment->created_at->diffForHumans() }}</span>
                @if($comment->status === 'pending')
                    <span id="badge-pending-{{ $comment->id }}" class="bg-yellow-100 text-yellow-700 text-[10px] px-2 py-0.5 rounded-full font-bold">Chờ duyệt</span>
                @endif
            </div>

            @auth
                <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition duration-200">
                    @if(($comment->status === 'pending') && (Auth::user()->role === 'admin' || Auth::id() === $comment->post->user_id))
                         <button onclick="approveComment({{ $comment->id }})" id="btn-approve-{{ $comment->id }}" class="text-green-600 hover:underline text-xs font-bold">✓ Duyệt</button>
                    @endif
                    @if(Auth::id() === $comment->user_id)
                        <button onclick="toggleEdit({{ $comment->id }})" class="text-indigo-600 hover:underline text-xs font-bold">Sửa</button>
                    @endif
                    @if(Auth::user()->role === 'admin' || Auth::id() === $comment->post->user_id || Auth::id() === $comment->user_id)
                        <button onclick="deleteComment({{ $comment->id }})" class="text-red-500 hover:underline text-xs font-bold">Xóa</button>
                    @endif
                </div>
            @endauth
        </div>
        
        <div id="comment-body-{{ $comment->id }}">
            <p class="text-gray-700 text-sm leading-relaxed whitespace-pre-wrap">{{ $comment->content }}</p>
        </div>

        @if(Auth::id() === $comment->user_id)
            <form onsubmit="updateComment(event, {{ $comment->id }})" id="form-edit-{{ $comment->id }}" class="hidden mt-2">
                <textarea class="w-full border border-gray-300 rounded-lg p-2 text-sm mb-2 focus:ring-2 focus:ring-indigo-500 outline-none" rows="2" required>{{ $comment->content }}</textarea>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="toggleEdit({{ $comment->id }})" class="text-xs text-gray-500 hover:text-gray-700">Hủy</button>
                    <button type="submit" class="bg-indigo-600 text-white px-3 py-1 rounded text-xs font-bold hover:bg-indigo-700">Lưu</button>
                </div>
            </form>
        @endif
    </div>
</div>