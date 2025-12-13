<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendampingan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('id_permintaan')->constrained('permintaan')->onDelete('cascade');
            $table->string('kebutuhan')->comment('Kebutuhan pendampingan');
            $table->date('waktu_pendampingan')->nullable()->comment('Tanggal rencana pendampingan');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendampingan');
    }
};