{{--
    AI Engineer — Chat Interface
    Layout: sidebar riwayat (kiri) + area chat (kanan)
    Styling: Tailwind (dipakai project ini) + Alpine.js (via CDN, ringan, tanpa build step tambahan)
    Streaming: fetch() + ReadableStream membaca SSE dari route ai-engineer.send

    CATATAN: layout/app.blade.php di project ini adalah Blade Component (pakai {{ $slot }}),
    jadi dipanggil lewat <x-app-layout>...</x-app-layout>, bukan @extends/@section.
    Kalau layout Anda TIDAK punya slot bernama "header", hapus saja blok
    <x-slot name="header"> di bawah — sisanya tetap berfungsi normal.
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
                <a
                    href="{{ route('ai-engineer.index', ['conversation' => $conv->id]) }}"
                    class="block px-4 py-3 text-sm border-l-2 transition
                        {{ $activeConversation?->id === $conv->id
                            ? 'border-teal-400 bg-slate-800/70 text-slate-50'
                            : 'border-transparent text-slate-400 hover:bg-slate-800/40 hover:text-slate-200' }}"
                >
                    <div class="truncate font-medium">{{ $conv->title }}</div>
                    <div class="text-xs text-slate-500 mt-0.5">
                        {{ $conv->last_message_at?->diffForHumans() ?? 'Belum ada pesan' }}
                    </div>
                </a>
            @empty
                <p class="px-4 py-6 text-sm text-slate-500 text-center">
                    Belum ada percakapan. Mulai satu di atas.
                </p>
            @endforelse
        </div>

        <div class="p-3 border-t border-slate-800 text-xs text-slate-500 flex items-center gap-2">
            <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
            Model: <span class="font-mono text-slate-400">{{ config('ai.default_model') }}</span> (offline)
        </div>
    </aside>

    {{-- CHAT AREA --}}
    <main class="flex-1 flex flex-col min-w-0">
        {{-- Header --}}
        <div class="h-14 shrink-0 border-b border-slate-800 flex items-center px-6">
            <h1 class="text-sm font-semibold text-slate-200">AI Engineer</h1>
            <span class="ml-2 text-xs text-slate-500">Tanya apa saja, dijawab lokal &mdash; tanpa data keluar server.</span>
        </div>

        {{-- Messages --}}
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
                        class="max-w-2xl rounded-2xl px-4 py-3 text-sm leading-relaxed whitespace-pre-wrap"
                        :class="msg.role === 'user'
                            ? 'bg-teal-600 text-white rounded-br-sm'
                            : 'bg-slate-800 text-slate-100 rounded-bl-sm border border-slate-700'"
                        x-text="msg.content"
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

        {{-- Input --}}
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

<script src="//unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
function aiEngineer({ conversationId, initialMessages, sendUrlTemplate, storeUrl }) {
    return {
        conversationId,
        messages: initialMessages,
        draft: '',
        isStreaming: false,

        async createConversation() {
            const res = await fetch(storeUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            });
            if (res.redirected) {
                window.location.href = res.url;
            }
        },

        async sendMessage() {
            const text = this.draft.trim();
            if (!text || this.isStreaming) return;

            if (!this.conversationId) {
                await this.createConversation();
                return; // halaman akan reload setelah redirect
            }

            this.messages.push({ role: 'user', content: text });
            this.draft = '';
            this.isStreaming = true;
            this.scrollToBottom();

            const url = sendUrlTemplate.replace('__ID__', this.conversationId);
            const assistantIndex = this.messages.push({ role: 'assistant', content: '' }) - 1;

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ message: text }),
                });

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
                        const line = part.replace(/^data: /, '').trim();
                        if (line === '[DONE]' || line === '') continue;

                        try {
                            const json = JSON.parse(line);
                            this.messages[assistantIndex].content += json.content ?? '';
                            this.scrollToBottom();
                        } catch (e) { /* abaikan baris tidak valid */ }
                    }
                }
            } catch (e) {
                this.messages[assistantIndex].content = 'Maaf, terjadi kesalahan menghubungi model AI.';
            } finally {
                this.isStreaming = false;
            }
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