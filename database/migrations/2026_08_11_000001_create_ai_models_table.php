<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel referensi model AI yang tersedia di sistem.
     * Memungkinkan multi-model (deepseek-r1, deepseek-coder, dll)
     * tanpa hardcode di kode aplikasi.
     */
    public function up(): void
    {
        Schema::create('ai_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');                 // "DeepSeek R1 7B"
            $table->string('slug')->unique();        // "deepseek-r1:7b" -> nama tag Ollama
            $table->string('provider')->default('ollama'); // ollama | llamacpp | api
            $table->string('endpoint')->nullable();  // override endpoint kalau multi-host
            $table->unsignedInteger('context_length')->default(4096);
            $table->decimal('temperature', 3, 2)->default(0.70);
            $table->json('config')->nullable();       // parameter tambahan (top_p, num_ctx, dll)
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index(['is_active', 'is_default']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_models');
    }
};
