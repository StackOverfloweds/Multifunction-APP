<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel ini yang paling cepat membesar (1 chat = banyak baris).
     * Desain untuk skala besar:
     *  - ULID primary key (sortable, tidak perlu round-trip ke DB utk urutan)
     *  - role sebagai string pendek (bukan enum Postgres, biar migrasi masa depan gampang)
     *  - content pakai TEXT (bukan varchar) karena jawaban AI bisa panjang
     *  - metadata JSON untuk simpan info tambahan (token usage, finish_reason, dll)
     *    tanpa perlu ALTER TABLE tiap ada field baru
     *  - index composite (conversation_id, created_at) untuk query "riwayat chat urut waktu"
     *
     * CATATAN SKALA BESAR (jutaan baris/bulan):
     * Setelah data tumbuh besar, tabel ini sangat cocok di-PARTITION per bulan
     * berdasarkan created_at (native PostgreSQL declarative partitioning).
     * Laravel migration tidak punya API partition bawaan, jadi kalau volumenya
     * sudah besar, buat migration terpisah dengan raw SQL:
     *
     *   DB::statement("
     *     CREATE TABLE ai_messages (...) PARTITION BY RANGE (created_at);
     *     CREATE TABLE ai_messages_2026_08 PARTITION OF ai_messages
     *       FOR VALUES FROM ('2026-08-01') TO ('2026-09-01');
     *   ");
     *
     * Untuk tahap awal (ribuan-puluhan ribu baris/bulan), index composite
     * di bawah ini sudah lebih dari cukup.
     */
    public function up(): void
    {
        Schema::create('ai_messages', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('conversation_id');
            $table->foreign('conversation_id')
                ->references('id')->on('ai_conversations')
                ->cascadeOnDelete();

            $table->string('role', 20); // user | assistant | system
            $table->text('content');
            $table->unsignedInteger('prompt_tokens')->nullable();
            $table->unsignedInteger('completion_tokens')->nullable();
            $table->unsignedInteger('latency_ms')->nullable(); // waktu respon model
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_messages');
    }
};
