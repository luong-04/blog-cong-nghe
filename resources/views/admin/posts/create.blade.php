<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Viết bài mới') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-2 space-y-6">
                            <div>
                                <label class="block font-bold text-sm text-gray-700 mb-1">Tiêu đề bài viết</label>
                                <input type="text" id="post-title" name="title" class="w-full border-gray-300 rounded-md shadow-sm" required placeholder="Nhập tiêu đề để AI viết bài...">
                            </div>
                            
                            {{-- Nút bấm AI --}}
                            <div class="flex justify-end">
                                <button type="button" id="btn-generate" class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2 rounded-full shadow-lg flex items-center gap-2 transition transform hover:-translate-y-1">
                                    ✨ Viết bài & Vẽ ảnh bằng AI
                                </button>
                            </div>

                            <div>
                                <label class="block font-bold text-sm text-gray-700 mb-1">Nội dung bài viết</label>
                                {{-- CKEditor --}}
                                <textarea id="post-content" name="content" rows="10" class="w-full border-gray-300 rounded-md"></textarea>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <h3 class="font-bold text-gray-700 mb-4">Thông tin chung</h3>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Danh mục</label>
                                    <select name="category_id" class="w-full border-gray-300 rounded-md shadow-sm">
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Ảnh đại diện</label>
                                    <input type="file" name="featured_image" class="w-full text-sm text-gray-500">
                                    {{-- Khu vực hiển thị ảnh AI tạo ra --}}
                                    <div id="ai-image-preview-container" class="mt-3 hidden">
                                        <p class="text-xs text-green-600 font-bold mb-1">✅ AI đã vẽ ảnh bìa cho bạn:</p>
                                        <img id="ai-image-preview" src="" class="w-full rounded-lg shadow-md border-2 border-green-500">
                                        <p class="text-xs text-gray-500 mt-1 italic">(Hãy tải ảnh này về và chọn vào ô bên trên)</p>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                                    <select name="status" class="w-full border-gray-300 rounded-md shadow-sm">
                                        <option value="draft">Nháp</option>
                                        <option value="published">Công khai</option>
                                    </select>
                                </div>
                                <button type="submit" class="w-full bg-blue-600 text-white py-3 mt-6 rounded-lg font-bold shadow hover:bg-blue-700">ĐĂNG BÀI</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- SCRIPT & STYLE GIỐNG HỆT TRANG SHOW --}}
    <script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>
    <style>
        .ck-editor__editable_inline { min-height: 500px; padding: 2rem !important; }
        .ck-content { font-family: 'Segoe UI', sans-serif; line-height: 1.8; color: #333; }
        .ck-content h2 { font-size: 1.8rem; font-weight: 700; color: #BE1E2D; margin-top: 2rem; border-bottom: 2px solid #eee; }
        .ck-content ul { background: #f9fafb; padding: 1.5rem 2rem; border-left: 4px solid #BE1E2D; list-style: disc; }
        .ck-content img { margin: 2rem auto; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); display: block; }
    </style>

    <script>
        let myEditor;
        ClassicEditor.create(document.querySelector('#post-content'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'imageUpload', 'blockQuote', 'insertTable', 'undo', 'redo']
        }).then(editor => { myEditor = editor; }).catch(error => { console.error(error); });

        document.getElementById('btn-generate').addEventListener('click', function() {
            const title = document.getElementById('post-title').value;
            const btn = this;
            
            if (!title) { alert('⚠️ Nhập tiêu đề trước đã!'); return; }

            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = `⏳ Đang suy nghĩ & viết bài...`;
            btn.classList.add('opacity-50');

            fetch('{{ route('admin.posts.generate') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ title: title })
            })
            .then(async response => {
                if (!response.ok) {
                    const err = await response.json();
                    throw new Error(err.error || response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.content) {
                    // Đưa nội dung vào CKEditor
                    if(myEditor) myEditor.setData(data.content);

                    // Hiện ảnh bìa
                    if (data.image_url) {
                        document.getElementById('ai-image-preview').src = data.image_url;
                        document.getElementById('ai-image-preview-container').classList.remove('hidden');
                    }
                    alert('✅ Xong! Bài viết đã được tạo với định dạng chuẩn.');
                }
            })
            .catch(error => { alert('❌ Lỗi: ' + error.message); })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
                btn.classList.remove('opacity-50');
            });
        });
    </script>
</x-app-layout>