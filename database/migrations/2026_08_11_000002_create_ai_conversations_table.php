<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Satu baris = satu sesi chat (thread) milik seorang user.
     * Pakai ULID (bukan auto-increment) supaya:
     *  - aman untuk sharding / replikasi di masa depan
     *  - tidak bocorkan jumlah row lewat ID
     *  - tetap bisa diurutkan berdasarkan waktu (ULID sortable)
     */
    public function up(): void
    {
        Schema::create('ai_conversations', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ai_model_id')->nullable()->constrained('ai_models')->nullOnDelete();
            $table->string('title')->default('Percakapan Baru');
            $table->boolean('is_archived')->default(false);
            $table->boolean('is_pinned')->default(false);
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
            $table->softDeletes(); // biar histori tidak hilang permanen saat "dihapus"

            // Query paling sering: "ambil semua chat milik user, urut terbaru"
            $table->index(['user_id', 'last_message_at']);
            $table->index(['user_id', 'is_archived']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_conversations');
    }
};