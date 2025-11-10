<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('administrasi_logs', function (Blueprint $table) {
            $table->id();

            // ✅ UBAH INI - Specify table name explicitly
            $table->foreignId('administrasi_sekolah_id')
                  ->constrained('administrasi_sekolah') // ← Tambahkan nama table explicit
                  ->onDelete('cascade');

            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Log action
            $table->enum('action', ['created', 'updated', 'submitted', 'approved', 'rejected']);
            $table->text('description')->nullable();

            // Snapshot data sebelum dan sesudah perubahan (JSON)
            $table->json('old_data')->nullable();
            $table->json('new_data')->nullable();

            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('administrasi_logs');
    }
};
