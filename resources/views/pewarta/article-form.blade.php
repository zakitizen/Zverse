<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $article ? 'Edit Artikel' : 'Tulis Artikel' }} — Zverse</title>
    
    {{-- Fonts & Icons --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    {{-- Zverse Design System (Sky Blue Theme) --}}
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        primary: { 500: '#0ea5e9', 600: '#0284c7' }, // Sky Blue
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        /* Editor Title Focus Removal */
        .editor-title:focus { outline: none; box-shadow: none; }
    </style>
</head>
<body class="bg-slate-50 text-slate-700 min-h-screen flex flex-col selection:bg-sky-500 selection:text-white">

@php
    $formAction = $article
        ? ($user->role === 'redaksi' ? route('redaksi.articles.update', $article->id) : route('pewarta.articles.update', $article->id))
        : route('pewarta.articles.store');
    $imageUploadRoute = $user->role === 'redaksi'
        ? route('redaksi.articles.upload-image')
        : route('pewarta.articles.upload-image');
    $dashboardRoute = $user->role === 'redaksi' ? route('redaksi.dashboard') : route('pewarta.dashboard');
@endphp

<form action="{{ $formAction }}"
      method="POST" enctype="multipart/form-data" class="flex flex-col flex-1 min-h-screen">
    @csrf
    @if($article) @method('PUT') @endif

    {{-- Topbar (Sticky Header) --}}
    <header class="sticky top-0 z-50 bg-white border-b border-slate-200 px-4 sm:px-6 h-16 flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-4">
            <a href="{{ $dashboardRoute }}" class="w-10 h-10 rounded-full flex items-center justify-center text-slate-500 hover:bg-slate-100 hover:text-slate-900 transition-colors" title="Kembali ke Dashboard">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div class="hidden sm:block border-l border-slate-200 h-6"></div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold uppercase tracking-widest text-sky-500">Zverse Editor</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-500">{{ $article ? 'EDIT MODE' : 'DRAFT' }}</span>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ $dashboardRoute }}" class="hidden sm:block px-4 py-2 text-sm font-semibold text-slate-600 hover:text-slate-900 transition-colors">
                Batal
            </a>
            <button type="submit" name="action" value="draft" class="flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold px-5 py-2.5 rounded-xl transition-all shadow-sm hover:-translate-y-0.5">
                <i data-lucide="file-text" class="w-4 h-4"></i> {{ $article ? 'Simpan Perubahan' : 'Simpan ke Draft' }}
            </button>
            <button type="submit" name="action" value="submit" class="flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-all shadow-sm shadow-sky-500/20 hover:-translate-y-0.5">
                <i data-lucide="send" class="w-4 h-4"></i> {{ $user->role === 'redaksi' ? 'Setujui' : 'Kirim ke Redaksi' }}
            </button>
        </div>
    </header>

    {{-- Error Validation Alerts --}}
    @if($errors->any())
    <div class="mx-auto max-w-7xl w-full px-4 sm:px-6 pt-6">
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700 flex items-start gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
            <ul class="list-disc space-y-1 pl-4">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    </div>
    @endif

    {{-- Main Editor Layout (Blogspot Style) --}}
    <main class="flex-1 mx-auto max-w-7xl w-full px-4 sm:px-6 py-6 grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        {{-- Left Area: Main Editor Canvas --}}
        <div class="lg:col-span-8 xl:col-span-9 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col h-[calc(100vh-140px)]">
            
            {{-- Toolbar --}}
            <div class="bg-slate-50 border-b border-slate-200 p-2 flex flex-wrap items-center gap-1">
                <button type="button" onclick="event.preventDefault(); wrapSelection('**', '**', 'Teks Bold'); return false;" class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-600 hover:bg-white hover:text-sky-600 hover:shadow-sm transition-all" title="Bold">
                    <i data-lucide="bold" class="w-4 h-4"></i>
                </button>
                <button type="button" onclick="event.preventDefault(); wrapSelection('*', '*', 'Teks Italic'); return false;" class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-600 hover:bg-white hover:text-sky-600 hover:shadow-sm transition-all" title="Italic">
                    <i data-lucide="italic" class="w-4 h-4"></i>
                </button>
                <div class="w-px h-5 bg-slate-300 mx-1"></div>
                <button type="button" onclick="event.preventDefault(); insertFmt('## Heading 2'); return false;" class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-600 hover:bg-white hover:text-sky-600 hover:shadow-sm transition-all" title="Heading 2">
                    <i data-lucide="heading-2" class="w-4 h-4"></i>
                </button>
                <button type="button" onclick="event.preventDefault(); insertFmt('### Heading 3'); return false;" class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-600 hover:bg-white hover:text-sky-600 hover:shadow-sm transition-all" title="Heading 3">
                    <i data-lucide="heading-3" class="w-4 h-4"></i>
                </button>
                <div class="w-px h-5 bg-slate-300 mx-1"></div>
                <button type="button" onclick="event.preventDefault(); insertFmt('- List Item'); return false;" class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-600 hover:bg-white hover:text-sky-600 hover:shadow-sm transition-all" title="Bullet List">
                    <i data-lucide="list" class="w-4 h-4"></i>
                </button>
                <button type="button" onclick="event.preventDefault(); insertFmt('1. Numbered Item'); return false;" class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-600 hover:bg-white hover:text-sky-600 hover:shadow-sm transition-all" title="Numbered List">
                    <i data-lucide="list-ordered" class="w-4 h-4"></i>
                </button>
                <div class="w-px h-5 bg-slate-300 mx-1"></div>
                <button type="button" onclick="event.preventDefault(); insertFmt('[Teks Link](https://)'); return false;" class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-600 hover:bg-white hover:text-sky-600 hover:shadow-sm transition-all" title="Insert Link">
                    <i data-lucide="link" class="w-4 h-4"></i>
                </button>
                <button type="button" onclick="event.preventDefault(); document.getElementById('editor-image-upload').click(); return false;" class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-600 hover:bg-white hover:text-sky-600 hover:shadow-sm transition-all" title="Upload Gambar">
                    <i data-lucide="image" class="w-4 h-4"></i>
                </button>
            </div>

                {{-- Editor Canvas --}}
            <div class="flex-1 overflow-hidden flex flex-col">
                {{-- Tab Toggle: Edit / Preview --}}
                <div class="flex border-b border-slate-200 bg-slate-50/80 px-2">
                    <button type="button" id="tab-edit" class="tab-btn active px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-sky-600 border-b-2 border-sky-500 transition-colors" onclick="switchTab('edit')">
                        <i data-lucide="edit-3" class="w-3.5 h-3.5 inline-block mr-1.5"></i>Edit
                    </button>
                    <button type="button" id="tab-preview" class="tab-btn px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-slate-400 border-b-2 border-transparent hover:text-slate-600 transition-colors" onclick="switchTab('preview')">
                        <i data-lucide="eye" class="w-3.5 h-3.5 inline-block mr-1.5"></i>Preview
                    </button>
                </div>

                {{-- Edit Panel --}}
                <div id="panel-edit" class="flex-1 overflow-y-auto p-6 sm:p-10 flex flex-col">
                    <input type="text" name="title" value="{{ old('title', $article?->title) }}" required
                           class="editor-title w-full text-3xl sm:text-4xl font-black text-slate-900 bg-transparent border-0 border-b border-transparent hover:border-slate-100 focus:border-sky-500 pb-3 mb-6 transition-colors placeholder:text-slate-300"
                           placeholder="Judul Artikel...">
                    
                    <input type="file" id="editor-image-upload" accept="image/*" class="hidden">
                    <textarea id="content" name="content" required
                              class="flex-1 w-full resize-none text-base text-slate-700 bg-transparent border-0 focus:ring-0 placeholder:text-slate-400 leading-relaxed font-serif"
                              placeholder="Mulai menulis konten artikelmu di sini...">{{ old('content', $article?->content) }}</textarea>
                </div>

                {{-- Preview Panel --}}
                <div id="panel-preview" class="flex-1 overflow-y-auto p-6 sm:p-10 hidden">
                    <div id="preview-content" class="prose prose-lg max-w-none">
                        <div class="text-slate-400 text-sm italic">Preview akan muncul di sini...</div>
                    </div>
                </div>
            </div>
            
            {{-- Excerpt Box --}}
            <div class="border-t border-slate-100 bg-slate-50/50 p-4">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Ringkasan (Excerpt)</label>
                <textarea name="excerpt" rows="2" required
                          class="w-full resize-none rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition-all focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 placeholder:text-slate-400"
                          placeholder="Tulis ringkasan singkat untuk preview artikel di beranda...">{{ old('excerpt', $article?->excerpt) }}</textarea>
            </div>
        </div>

        {{-- Right Area: Sidebar Settings (Post Settings) --}}
        <div class="lg:col-span-4 xl:col-span-3 space-y-5 h-full overflow-y-auto pb-10 custom-scrollbar">
            
            {{-- Publish Settings Card --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2 mb-4 border-b border-slate-100 pb-3">
                    <i data-lucide="settings-2" class="w-4 h-4 text-sky-500"></i> Pengaturan Pos
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1.5">Kategori</label>
                        <select name="category" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none transition-all focus:border-sky-500 focus:bg-white focus:ring-2 focus:ring-sky-500/20">
                            @foreach(['games'=>'Games','musik'=>'Musik','film'=>'Film','entertainment'=>'Entertainment'] as $v=>$l)
                            <option value="{{ $v }}" {{ old('category', $article?->category) === $v ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1.5">Estimasi Baca</label>
                        <input type="text" name="read_time" value="{{ old('read_time', $article?->read_time ?? '5 menit') }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none transition-all focus:border-sky-500 focus:bg-white focus:ring-2 focus:ring-sky-500/20" 
                               placeholder="Contoh: 5 menit">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1.5">Tags / Label</label>
                        <input type="text" name="tags" value="{{ old('tags', is_array($article?->tags) ? implode(', ', $article->tags) : '') }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none transition-all focus:border-sky-500 focus:bg-white focus:ring-2 focus:ring-sky-500/20"
                               placeholder="Pisahkan dengan koma">
                    </div>
                </div>
            </div>

            {{-- Media Settings Card --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2 mb-4 border-b border-slate-100 pb-3">
                    <i data-lucide="image" class="w-4 h-4 text-sky-500"></i> Media Sampul
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1.5">Upload Gambar Baru</label>
                        <input type="file" name="image_upload" accept="image/*"
                               class="w-full text-sm text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-sky-50 file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-sky-700 hover:file:bg-sky-100 cursor-pointer border border-dashed border-slate-200 rounded-xl p-2 bg-slate-50">
                    </div>
                    
                    <div class="relative flex items-center py-2">
                        <div class="flex-grow border-t border-slate-200"></div>
                        <span class="flex-shrink-0 mx-3 text-slate-400 text-xs font-semibold">ATAU</span>
                        <div class="flex-grow border-t border-slate-200"></div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1.5">Gunakan URL Gambar</label>
                        <input type="url" name="image" value="{{ old('image', $article?->image) }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none transition-all focus:border-sky-500 focus:bg-white focus:ring-2 focus:ring-sky-500/20"
                               placeholder="https://...">
                    </div>
                </div>
            </div>

        </div>
    </main>
</form>

<script>
    lucide.createIcons();

    const ta = document.getElementById('content');
    const imageInput = document.getElementById('editor-image-upload');
    const uploadUrl = "{{ $imageUploadRoute }}";
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]')?.value;
    const previewEl = document.getElementById('preview-content');
    const panelEdit = document.getElementById('panel-edit');
    const panelPreview = document.getElementById('panel-preview');

    // ── Markdown to HTML Renderer ──
    function renderMarkdown(text) {
        const lines = text.split('\n');
        let html = '';

        for (let line of lines) {
            const trimmed = line.trim();

            if (trimmed === '') {
                html += '<div class="h-4"></div>';
                continue;
            }

            if (trimmed.startsWith('## ')) {
                html += `<h2 class="flex items-center gap-3 text-2xl mt-10 mb-4 text-slate-900"><span class="w-2 h-8 bg-orange-500 rounded-full inline-block"></span>${escapeHtml(trimmed.slice(3))}</h2>`;
                continue;
            }

            if (trimmed.startsWith('### ')) {
                html += `<h3 class="text-xl mt-8 mb-3 text-slate-800">${escapeHtml(trimmed.slice(4))}</h3>`;
                continue;
            }

            if (trimmed.startsWith('- ')) {
                html += `<li class="ml-4 mb-2">${escapeHtml(trimmed.slice(2))}</li>`;
                continue;
            }

            if (/^\d+\./.test(trimmed)) {
                html += `<li class="ml-4 mb-2 list-decimal">${escapeHtml(trimmed.replace(/^\d+\.\s*/, ''))}</li>`;
                continue;
            }

            let processed = escapeHtml(trimmed);

            // Image: ![alt](url)
            processed = processed.replace(/!\[([^\]]*)\]\(([^)]+)\)/g, '<img src="$2" alt="$1" class="rounded-2xl shadow-md my-6 w-full h-auto object-cover" loading="lazy" style="max-height:400px;">');

            // Bold: **text**
            processed = processed.replace(/\*\*(.*?)\*\*/g, '<strong class="text-slate-900">$1</strong>');

            // Italic: *text*
            processed = processed.replace(/\*(.*?)\*/g, '<em>$1</em>');

            // Standalone URL as link
            if (/^https?:\/\//i.test(trimmed)) {
                const escapedUrl = escapeHtml(trimmed);
                html += `<a href="${escapedUrl}" target="_blank" rel="noopener" class="text-sky-600 hover:text-sky-700 underline break-all">${escapedUrl}</a>`;
                continue;
            }

            html += `<p class="text-slate-600 leading-loose mb-4">${processed}</p>`;
        }

        return html;
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // ── Update Preview ──
    function updatePreview() {
        previewEl.innerHTML = renderMarkdown(ta.value);
    }

    // ── Tab Switching ──
    function switchTab(tab) {
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('text-sky-600', 'border-sky-500');
            btn.classList.add('text-slate-400', 'border-transparent');
        });

        if (tab === 'edit') {
            document.getElementById('tab-edit').classList.add('text-sky-600', 'border-sky-500');
            panelEdit.classList.remove('hidden');
            panelPreview.classList.add('hidden');
        } else {
            document.getElementById('tab-preview').classList.add('text-sky-600', 'border-sky-500');
            panelEdit.classList.add('hidden');
            panelPreview.classList.remove('hidden');
            updatePreview();
        }
    }

    // ── Auto-preview on input ──
    ta.addEventListener('input', function () {
        if (!panelPreview.classList.contains('hidden')) {
            updatePreview();
        }
    });

    // Pastikan textarea selalu bisa di-focus
    function ensureTextareaFocus() {
        if (!ta) {
            console.error('❌ Textarea content tidak ditemukan!');
            return false;
        }
        ta.focus();
        return true;
    }

    function wrapSelection(prefix, suffix, placeholder) {
        if (!ensureTextareaFocus()) return;

        const s = ta.selectionStart;
        const e = ta.selectionEnd;
        const selected = ta.value.substring(s, e);
        const text = selected || placeholder;

        ta.value = ta.value.substring(0, s) + prefix + text + suffix + ta.value.substring(e);
        ta.focus();

        const start = s + prefix.length;
        const end = start + text.length;
        ta.setSelectionRange(start, end);

        if (!panelPreview.classList.contains('hidden')) {
            updatePreview();
        }
    }

    function insertFmt(markdown) {
        if (!ensureTextareaFocus()) return;

        const s = ta.selectionStart;
        const e = ta.selectionEnd;
        const newline = s > 0 && ta.value[s - 1] !== '\n' ? '\n' : '';
        ta.value = ta.value.substring(0, s) + newline + markdown + '\n' + ta.value.substring(e);
        ta.focus();
        const newPos = s + newline.length + markdown.length + 1;
        ta.setSelectionRange(newPos, newPos);

        if (!panelPreview.classList.contains('hidden')) {
            updatePreview();
        }
    }

    function insertAtCursor(markdown) {
        if (!ensureTextareaFocus()) return;

        const s = ta.selectionStart;
        const e = ta.selectionEnd;
        ta.value = ta.value.substring(0, s) + markdown + ta.value.substring(e);
        ta.focus();
        ta.setSelectionRange(s + markdown.length, s + markdown.length);

        if (!panelPreview.classList.contains('hidden')) {
            updatePreview();
        }
    }

    imageInput.addEventListener('change', async function () {
        const file = this.files?.[0];
        if (!file) {
            console.log('⚠️ No file selected');
            return;
        }

        console.log('📤 Uploading file:', file.name, file.size, 'bytes');

        const formData = new FormData();
        formData.append('image', file);

        try {
            const response = await fetch(uploadUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                body: formData,
            });

            const data = await response.json();
            
            if (!response.ok) {
                console.error('❌ Upload failed:', data);
                throw new Error(data.message || 'Gagal mengunggah gambar.');
            }

            console.log('✅ Upload success:', data.url);
            
            const altText = file.name.replace(/\.[^.]+$/, '') || 'Foto';
            const markdown = `![${altText}](${data.url})`;
            console.log('📝 Inserting markdown:', markdown);
            
            insertAtCursor(markdown);
            ensureTextareaFocus();
        } catch (error) {
            console.error('❌ Error:', error);
            alert(error.message || 'Gagal mengunggah gambar.');
        } finally {
            this.value = '';
        }
    });
    
    console.log('✅ Editor script loaded successfully');
</script>
</body>
</html>