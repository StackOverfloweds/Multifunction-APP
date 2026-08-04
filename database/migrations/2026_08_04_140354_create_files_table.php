<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Buat tabel dan kolom terlebih dahulu
        Schema::create('files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->uuid('parent_id')->nullable(); // Hanya deklarasi kolom UUID
            
            $table->string('name');
            $table->string('storage_path')->nullable();
            $table->bigInteger('size')->default(0);
            $table->string('mime_type')->nullable();
            $table->boolean('is_folder')->default(false);
            
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'parent_id']);
        });

        // 2. Tambahkan Foreign Key Self-Reference setelah Primary Key 'files' resmi terdaftar
        Schema::table('files', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('files')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};