<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Ketua
        Schema::create('ketua', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('administrasi_sekolah')->onDelete('cascade');
            $table->string('nama');
            $table->string('nip')->nullable();
            $table->string('email')->nullable();
            $table->string('nomor_telepon')->nullable(); // ✅ TAMBAH
            $table->timestamps();
        });

        // Tabel Anggota
        Schema::create('anggota', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ketua_id')->constrained('ketua')->onDelete('cascade');
            $table->foreignId('sekolah_id')->constrained('administrasi_sekolah')->onDelete('cascade');
            $table->string('nama');
            $table->string('nip')->nullable();
            $table->string('email')->nullable();
            $table->string('nomor_telepon')->nullable(); // ✅ TAMBAH
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota');
        Schema::dropIfExists('ketua');
    }
};
