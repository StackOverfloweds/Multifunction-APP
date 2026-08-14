{{--
    AI Engineer — Chat Interface (versi OpenRouter via laravel/ai)

    Perbedaan dari versi Ollama sebelumnya:
    - Backend sekarang me-return stream langsung dari SDK (usingVercelDataProtocol()),
      jadi format event SSE-nya BUKAN {"content": "..."} custom kita lagi,
      melainkan format resmi Vercel AI protocol: {"type":"text-delta","delta":"..."}
    - Riwayat percakapan (messages) sekarang datang dari relasi resmi
      $activeConversation->messages, bukan model AiMessage custom kita.
      NAMA KOLOM di bawah ini (role/content) adalah ASUMSI mengikuti konvensi
      umum — kalau setelah migrate ternyata beda, sesuaikan bagian
      $m->role / $m->content di PHP dan JS di bawah.
      Role selain 'user'/'assistant' (mis. row moderasi/guardrail internal
      paket) sudah difilter di AIEngineerController@index sebelum sampai
      ke view ini.
--}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            AI Engineer
        </h2>
    </x-slot>

<div
    x-data="aiEngineer({
        conversationId: {{ $activeConversation?->id ? "'{$activeConversation->id}'" : 'null' }},
        initialMessages: {{ $activeConversation ? $activeConversation->messages->map(fn($m) => ['role' => $m->role, 'content' => $m->content])->toJson() : '[]' }},
        sendUrlTemplate: '{{ route('ai-engineer.send', ['conversation' => '__ID__']) }}',
        storeUrl: '{{ route('ai-engineer.store') }}',
        deleteUrlTemplate: '{{ route('ai-engineer.destroy', ['conversation' => '__ID__']) }}',
        indexUrl: '{{ route('ai-engineer.index') }}',
    })"
    x-init="
        const setHeight = () => { $el.style.height = (window.innerHeight - $el.getBoundingClientRect().top) + 'px' };
        setHeight();
        window.addEventListener('resize', setHeight);
    "
    class="flex bg-slate-950 text-slate-100"
>
    {{-- SIDEBAR --}}
    <aside class="w-72 shrink-0 border-r border-slate-800 bg-slate-900/60 flex flex-col">
        <div class="p-4 border-b border-slate-800">
            <button
                @click="createConversation()"
                class="w-full flex items-center justify-center gap-2 rounded-lg bg-teal-500/10 text-teal-400 border border-teal-500/30 px-3 py-2 text-sm font-medium hover:bg-teal-500/20 transition"
            >
                <span class="text-lg leading-none">+</span> Percakapan Baru
            </button>
        </div>

        <div class="flex-1 overflow-y-auto py-2">
            @forelse ($conversations as $conv)
                <div
                    class="group relative flex items-center border-l-2 transition
                        {{ $activeConversation?->id === $conv->id
                            ? 'border-teal-400 bg-slate-800/70'
                            : 'border-transparent hover:bg-slate-800/40' }}"
                >
                    <a
                        href="{{ route('ai-engineer.index', ['conversation' => $conv->id]) }}"
                        data-conversation-id="{{ $conv->id }}"
                        class="flex-1 min-w-0 px-4 py-3 text-sm
                            {{ $activeConversation?->id === $conv->id
                                ? 'text-slate-50'
                                : 'text-slate-400 hover:text-slate-200' }}"
                    >
                        <div class="truncate font-medium conv-title">{{ $conv->title ?? 'Percakapan' }}</div>
                        <div class="text-xs text-slate-500 mt-0.5">
                            {{ $conv->updated_at?->diffForHumans() ?? '' }}
                        </div>
                    </a>

                    <button
                        type="button"
                        @click.prevent.stop="deleteConversation('{{ $conv->id }}')"
                        title="Hapus percakapan"
                        class="shrink-0 mr-2 p-1.5 rounded text-slate-600 opacity-0 group-hover:opacity-100
                               hover:text-red-400 hover:bg-red-500/10 transition"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m2 0v13a2 2 0 01-2 2H8a2 2 0 01-2-2V7h12z" />
                        </svg>
                    </button>
                </div>
            @empty
                <p class="px-4 py-6 text-sm text-slate-500 text-center">
                    Belum ada percakapan. Mulai satu di atas.
                </p>
            @endforelse
        </div>

        <div class="p-3 border-t border-slate-800 text-xs text-slate-500 flex items-center gap-2">
            <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
            Model: <span class="font-mono text-slate-400">{{ config('ai.providers.openrouter.models.text.default') }}</span> (OpenRouter)
        </div>
    </aside>

    {{-- CHAT AREA --}}
    <main class="flex-1 flex flex-col min-w-0">
        <div class="h-14 shrink-0 border-b border-slate-800 flex items-center px-6">
            <h1 class="text-sm font-semibold text-slate-200">AI Engineer</h1>
            <span class="ml-2 text-xs text-slate-500">Ditenagai OpenRouter.</span>
        </div>

        <div class="flex-1 overflow-y-auto px-6 py-6 space-y-6" x-ref="scrollArea">
            <template x-if="messages.length === 0">
                <div class="h-full flex flex-col items-center justify-center text-center text-slate-500">
                    <div class="text-3xl mb-2">🤖</div>
                    <p class="text-sm">Mulai percakapan dengan mengetik pertanyaan di bawah.</p>
                </div>
            </template>

            <template x-for="(msg, idx) in messages" :key="idx">
                <div class="flex gap-3" :class="msg.role === 'user' ? 'justify-end' : 'justify-start'">
                    <div
                        class="max-w-2xl rounded-2xl px-4 py-3 text-sm leading-relaxed"
                        :class="msg.role === 'user'
                            ? 'bg-teal-600 text-white rounded-br-sm'
                            : 'bg-slate-800 text-slate-100 rounded-bl-sm border border-slate-700 markdown-body'"
                        x-html="renderMessage(msg)"
                    ></div>
                </div>
            </template>

            <template x-if="isStreaming">
                <div class="flex justify-start">
                    <div class="rounded-2xl rounded-bl-sm bg-slate-800 border border-slate-700 px-4 py-3 text-sm text-slate-400">
                        <span class="inline-flex gap-1">
                            <span class="h-1.5 w-1.5 rounded-full bg-teal-400 animate-bounce [animation-delay:-0.3s]"></span>
                            <span class="h-1.5 w-1.5 rounded-full bg-teal-400 animate-bounce [animation-delay:-0.15s]"></span>
                            <span class="h-1.5 w-1.5 rounded-full bg-teal-400 animate-bounce"></span>
                        </span>
                    </div>
                </div>
            </template>
        </div>

        <form @submit.prevent="sendMessage()" class="border-t border-slate-800 p-4">
            <div class="flex items-end gap-3 rounded-xl border border-slate-700 bg-slate-900 focus-within:border-teal-500 transition px-3 py-2">
                <textarea
                    x-model="draft"
                    @keydown.enter.prevent="if (!$event.shiftKey) sendMessage()"
                    rows="1"
                    placeholder="Tulis pertanyaan... (Shift+Enter untuk baris baru)"
                    class="flex-1 resize-none bg-transparent text-sm text-slate-100 placeholder-slate-500 focus:outline-none py-2"
                    :disabled="isStreaming"
                ></textarea>
                <button
                    type="submit"
                    :disabled="isStreaming || !draft.trim()"
                    class="shrink-0 rounded-lg bg-teal-500 text-slate-950 font-medium text-sm px-4 py-2 disabled:opacity-40 disabled:cursor-not-allowed hover:bg-teal-400 transition"
                >
                    Kirim
                </button>
            </div>
        </form>
    </main>
</div>

<script src="//cdnjs.cloudflare.com/ajax/libs/marked/12.0.2/marked.min.js"></script>

<style>
    /* Styling manual untuk hasil render Markdown di bubble chat AI —
       Tailwind tidak otomatis mempercantik tag HTML mentah seperti
       <strong>, <ul>, <code>, dst, jadi perlu di-style eksplisit di sini. */
    .markdown-body { line-height: 1.65; }
    .markdown-body > *:first-child { margin-top: 0; }
    .markdown-body > *:last-child { margin-bottom: 0; }
    .markdown-body p { margin: 0.5em 0; }
    .markdown-body strong { font-weight: 700; color: #f1f5f9; }
    .markdown-body em { font-style: italic; }
    .markdown-body ul, .markdown-body ol { margin: 0.5em 0; padding-left: 1.4em; }
    .markdown-body ul { list-style-type: disc; }
    .markdown-body ol { list-style-type: decimal; }
    .markdown-body li { margin: 0.2em 0; }
    .markdown-body h1, .markdown-body h2, .markdown-body h3 {
        font-weight: 700; color: #f1f5f9; margin: 0.8em 0 0.4em;
    }
    .markdown-body h1 { font-size: 1.25em; }
    .markdown-body h2 { font-size: 1.15em; }
    .markdown-body h3 { font-size: 1.05em; }
    .markdown-body code {
        background: rgba(148, 163, 184, 0.15);
        padding: 0.15em 0.4em;
        border-radius: 0.3em;
        font-size: 0.85em;
        font-family: ui-monospace, monospace;
        color: #5eead4;
    }
    .markdown-body pre {
        background: #0f172a;
        border: 1px solid rgba(148, 163, 184, 0.2);
        border-radius: 0.5em;
        padding: 0.8em 1em;
        margin: 0.6em 0;
        overflow-x: auto;
    }
    .markdown-body pre code {
        background: none;
        padding: 0;
        color: #e2e8f0;
    }
    .markdown-body blockquote {
        border-left: 3px solid #14b8a6;
        padding-left: 0.8em;
        margin: 0.6em 0;
        color: #94a3b8;
    }
    .markdown-body a { color: #2dd4bf; text-decoration: underline; }
    .markdown-body table { border-collapse: collapse; margin: 0.6em 0; width: 100%; }
    .markdown-body th, .markdown-body td {
        border: 1px solid rgba(148, 163, 184, 0.2);
        padding: 0.4em 0.6em;
        text-align: left;
    }
</style>
<script>
function aiEngineer({ conversationId, initialMessages, sendUrlTemplate, storeUrl, deleteUrlTemplate, indexUrl }) {
    return {
        conversationId,
        messages: initialMessages,
        draft: '',
        isStreaming: false,
        deleteUrlTemplate,
        indexUrl,

        getCsrfToken() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            if (!meta) {
                alert('CSRF token meta tag tidak ditemukan di halaman. Cek layout <head> Anda.');
                throw new Error('Missing csrf-token meta tag');
            }
            return meta.content;
        },

        async createConversation() {
            try {
                const res = await fetch(storeUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.getCsrfToken(),
                        'Accept': 'application/json',
                    },
                });

                if (res.status === 419) {
                    alert('Sesi kadaluarsa (CSRF token invalid). Silakan refresh halaman lalu coba lagi.');
                    return;
                }

                if (!res.ok && !res.redirected) {
                    alert('Gagal membuat percakapan baru. Status: ' + res.status);
                    return;
                }

                if (res.redirected) {
                    window.location.href = res.url;
                } else {
                    // fallback kalau server tidak redirect (mis. return JSON)
                    window.location.reload();
                }
            } catch (e) {
                console.error(e);
                alert('Terjadi kesalahan saat membuat percakapan baru: ' + e.message);
            }
        },

        async deleteConversation(id) {
            if (!confirm('Hapus percakapan ini? Tindakan ini tidak bisa dibatalkan.')) return;

            try {
                const url = this.deleteUrlTemplate.replace('__ID__', id);
                const res = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': this.getCsrfToken(),
                        'Accept': 'application/json',
                    },
                });

                if (res.status === 419) {
                    alert('Sesi kadaluarsa (CSRF token invalid). Silakan refresh halaman lalu coba lagi.');
                    return;
                }

                if (!res.ok) {
                    alert('Gagal menghapus percakapan. Status: ' + res.status);
                    return;
                }

                // Kalau yang dihapus adalah percakapan yang sedang dibuka,
                // balik ke halaman AI Engineer kosong (tanpa ?conversation=).
                if (id === this.conversationId) {
                    window.location.href = this.indexUrl;
                    return;
                }

                // Kalau bukan yang sedang aktif, cukup hilangkan barisnya
                // dari sidebar tanpa reload halaman.
                const link = document.querySelector(`a[data-conversation-id="${id}"]`);
                link?.closest('.group')?.remove();
            } catch (e) {
                console.error(e);
                alert('Terjadi kesalahan saat menghapus percakapan: ' + e.message);
            }
        },

        async sendMessage() {
            const text = this.draft.trim();
            if (!text || this.isStreaming) return;

            if (!this.conversationId) {
                // Tidak lagi lazy-create — arahkan dulu ke createConversation()
                // supaya ID sudah pasti ada sebelum kirim pesan pertama.
                await this.createConversation();
                return;
            }

            this.messages.push({ role: 'user', content: text });
            this.draft = '';
            this.isStreaming = true;
            this.scrollToBottom();

            // Optimistic UI: kalau ini pesan pertama di percakapan ini,
            // langsung update teks di sidebar tanpa nunggu reload —
            // logikanya disamakan dengan Str::limit(..., 50) di backend.
            if (this.messages.length === 1) {
                this.updateSidebarTitle(text);
            }

            const url = sendUrlTemplate.replace('__ID__', this.conversationId);
            const assistantIndex = this.messages.push({ role: 'assistant', content: '' }) - 1;

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.getCsrfToken(),
                    },
                    body: JSON.stringify({ message: text }),
                });

                if (response.status === 419) {
                    this.messages[assistantIndex].content = 'Sesi kadaluarsa (CSRF token invalid). Refresh halaman dan coba lagi.';
                    return;
                }

                if (!response.ok || !response.body) {
                    throw new Error('Request gagal: ' + response.status);
                }

                const reader = response.body.getReader();
                const decoder = new TextDecoder();
                let buffer = '';

                while (true) {
                    const { done, value } = await reader.read();
                    if (done) break;

                    buffer += decoder.decode(value, { stream: true });
                    const parts = buffer.split('\n\n');
                    buffer = parts.pop();

                    for (const part of parts) {
                        const line = part.replace(/^data:\s*/, '').trim();
                        if (line === '' || line === '[DONE]') continue;

                        let event;
                        try {
                            event = JSON.parse(line);
                        } catch (e) {
                            continue; // baris tidak valid, abaikan
                        }

                        // Format resmi Vercel AI protocol yang dipakai laravel/ai:
                        // {"type":"text-delta","id":"...","delta":"..."}
                        // {"type":"text-start"|"text-end", ...}  -> diabaikan, cuma penanda
                        // {"type":"finish", ...}                 -> stream selesai
                        // {"type":"error","errorText":"..."}     -> tampilkan sebagai error
                        switch (event.type) {
                            case 'text-delta':
                                this.messages[assistantIndex].content += event.delta ?? '';
                                this.scrollToBottom();
                                break;
                            case 'error':
                                this.messages[assistantIndex].content =
                                    'Maaf, terjadi kesalahan: ' + (event.errorText ?? 'unknown error');
                                break;
                            // tipe lain (text-start, text-end, finish, tool-*, dll) sengaja diabaikan di UI ini
                        }
                    }
                }
            } catch (e) {
                this.messages[assistantIndex].content = 'Maaf, terjadi kesalahan menghubungi model AI.';
                console.error(e);
            } finally {
                this.isStreaming = false;
            }
        },

        renderMessage(msg) {
            if (msg.role === 'user') {
                // Pesan user: bukan markdown, cukup escape HTML manual
                // (cegah XSS kalau user ketik teks mengandung tag html)
                // lalu ganti newline jadi <br> supaya line break tetap kelihatan.
                const div = document.createElement('div');
                div.textContent = msg.content;
                return div.innerHTML.replace(/\n/g, '<br>');
            }

            // Pesan AI: parse markdown -> HTML, lalu sanitasi.
            // DOMPurify WAJIB dipakai di sini — teks dari model AI tidak
            // boleh dipercaya mentah-mentah, bisa saja (sengaja/tidak
            // sengaja) mengandung tag <script> atau sejenisnya.
            if (typeof marked === 'undefined' || typeof DOMPurify === 'undefined') {
                // fallback kalau CDN/bundle gagal load, minimal tidak error
                return msg.content;
            }

            const html = marked.parse(msg.content ?? '');
            return DOMPurify.sanitize(html);
        },

        updateSidebarTitle(text) {
            const el = document.querySelector(
                `a[data-conversation-id="${this.conversationId}"] .conv-title`
            );
            if (!el) return;

            const clean = text.replace(/\s+/g, ' ').trim();
            el.textContent = clean.length > 50 ? clean.slice(0, 50) + '...' : clean;
        },

        scrollToBottom() {
            this.$nextTick(() => {
                this.$refs.scrollArea.scrollTop = this.$refs.scrollArea.scrollHeight;
            });
        },
    };
}
</script>
</x-app-layout>