<?php

namespace App\Http\Controllers;

use App\Ai\Agents\AiEngineerAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Ai\Models\Conversation;

class AIEngineerController extends Controller
{
    /**
     * Halaman utama chat (Blade).
     */
    public function index(Request $request)
    {
        $conversations = Auth::user()->conversations()
            ->latest('updated_at')
            ->get();

        $activeConversation = null;

        if ($request->filled('conversation')) {
            // Pakai relasi $user->conversations() untuk query, bukan
            // Conversation::find() langsung — supaya otomatis ter-scope
            // ke milik user ini tanpa perlu tahu/pegang nama kolom
            // internal (participant_id/participant_type dsb) yang bisa
            // berbeda antar versi paket.
            $activeConversation = Auth::user()->conversations()
                ->whereKey($request->query('conversation'))
                ->with(['messages' => function ($query) {
                    // PENTING: paket laravel/ai menyimpan row internal
                    // (mis. hasil pengecekan moderasi/guardrail) di tabel
                    // yang sama dengan role selain 'user'/'assistant'.
                    // Kalau tidak difilter, row semacam itu ikut muncul
                    // sebagai bubble chat aneh ("User Safety: safe", dst)
                    // di frontend. Cukup ambil role percakapan asli saja.
                    $query->whereIn('role', ['user', 'assistant'])
                        ->orderBy('created_at');
                }])
                ->firstOrFail();
        }

        return view('ai-engineer.index', compact('conversations', 'activeConversation'));
    }

    /**
     * Buat percakapan baru (row kosong) lalu redirect ke halaman chat.
     * Dibuat eksplisit (bukan lazy saat prompt pertama) supaya ID-nya
     * langsung tersedia untuk sidebar & URL, tanpa perlu menunggu
     * respons AI pertama selesai.
     *
     * Judul masih default 'Percakapan Baru' di sini — baru diganti
     * di send() begitu pesan pertama masuk, lihat generateTitle().
     */
    public function store(Request $request)
    {
        // Kolom id di tabel agent_conversations adalah string(36) primary key,
        // TAPI tidak auto-generate lewat Eloquent create() biasa (SDK biasanya
        // generate ID sendiri lewat alur forUser()->prompt(), bukan create()
        // langsung seperti ini) — jadi isi manual di sini.
        $conversation = Auth::user()->conversations()->create([
            'id' => (string) Str::orderedUuid(),
            'title' => 'Percakapan Baru',
        ]);

        return redirect()->route('ai-engineer.index', ['conversation' => $conversation->id]);
    }

    /**
     * Endpoint streaming. Cukup return langsung objek stream dari SDK —
     * laravel/ai yang urus header SSE, buffering, dan formatnya.
     * usingVercelDataProtocol() dipakai supaya format event-nya baku
     * dan gampang di-parse di sisi frontend (event: text-delta, finish, dll).
     */
    public function send(Request $request, Conversation $conversation)
    {
        // Sama seperti index(): otorisasi lewat relasi, bukan kolom mentah.
        abort_unless(
            Auth::user()->conversations()->whereKey($conversation->id)->exists(),
            403
        );

        $request->validate([
            'message' => ['required', 'string', 'max:8000'],
        ]);

        // Auto-generate judul dari pesan pertama, supaya sidebar tidak
        // menampilkan "Percakapan Baru" terus untuk semua percakapan.
        // Dicek dulu title-nya masih default supaya ini cuma jalan
        // sekali per percakapan (pesan pertama), bukan tiap kirim pesan.
        if ($conversation->title === 'Percakapan Baru') {
            $conversation->update([
                'title' => $this->generateTitle($request->input('message')),
            ]);
        }

        // set_time_limit(0) penting untuk request panjang (reasoning model,
        // tool call berantai, dll) supaya tidak keputus oleh max_execution_time.
        set_time_limit(0);

        return (new AiEngineerAgent)
            ->continue($conversation->id, as: Auth::user())
            ->stream($request->input('message'))
            ->usingVercelDataProtocol();
    }

    /**
     * Hapus percakapan beserta seluruh pesannya.
     *
     * Kolom conversation_id di tabel pesan cuma diberi index biasa di
     * migration (bukan foreign key constraint dengan ON DELETE CASCADE),
     * jadi pesan-pesannya harus dihapus manual di sini — kalau tidak,
     * row-nya akan jadi orphan di database.
     */
    public function destroy(Conversation $conversation)
    {
        abort_unless(
            Auth::user()->conversations()->whereKey($conversation->id)->exists(),
            403
        );

        $conversation->messages()->delete();
        $conversation->delete();

        return response()->json(['deleted' => true]);
    }

    /**
     * Judul sederhana dari pesan pertama: rapikan whitespace/newline
     * jadi satu baris, lalu potong ke panjang yang wajar buat
     * ditampilkan di sidebar (konsisten dengan pemotongan di sisi
     * JS pada index.blade.php — updateSidebarTitle()).
     */
    private function generateTitle(string $message): string
    {
        $clean = trim(preg_replace('/\s+/', ' ', $message));

        return Str::limit($clean, 50);
    }
}