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
            // ✅ Hapus default 'user', ubah menjadi nullable
            $table->string('role', 50)->nullable()->change();
        });
    }

    /**
     * Down the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // ✅ Kembalikan ke default 'user' jika rollback
            $table->string('role', 50)->default('user')->change();
        });
    }
};
