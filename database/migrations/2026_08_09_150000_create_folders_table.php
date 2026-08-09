<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Buat tabel dan kolom terlebih dahulu
        Schema::create('folders', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->uuid('parent_id')->nullable(); // hanya deklarasi kolom dulu

            $table->string('name');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'parent_id']);
            // 1 user tidak boleh punya 2 folder dengan nama sama di level yang sama
            $table->unique(['user_id', 'parent_id', 'name']);
        });

        // 2. Tambahkan Foreign Key self-reference setelah tabel resmi terdaftar
        Schema::table('folders', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('folders')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('folders');
    }
};
