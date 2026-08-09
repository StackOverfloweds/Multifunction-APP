<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('file_storages', function (Blueprint $table) {
            $table->uuid('folder_id')->nullable()->after('user_id');

            $table->foreign('folder_id')
                ->references('id')->on('folders')
                ->nullOnDelete(); // kalau folder dihapus, file jadi pindah ke root (bukan ikut terhapus lewat FK)

            $table->index('folder_id');
        });
    }

    public function down(): void
    {
        Schema::table('file_storages', function (Blueprint $table) {
            $table->dropForeign(['folder_id']);
            $table->dropColumn('folder_id');
        });
    }
};
