<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // add field username
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->unique()->after('name');
            }
            // add fild role
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['super_admin', 'admin', 'user'])->default('user')->after('email');
            }
            
            // revition email
            $table->string('email')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
            if (Schema::hasColumn('users', 'username')) {
                $table->dropColumn('username');
            }
            $table->string('email')->nullable(false)->change();
        });
    }
};
