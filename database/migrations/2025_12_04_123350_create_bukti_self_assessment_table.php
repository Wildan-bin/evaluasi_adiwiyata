<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bukti_self_assessment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('indikator')->comment('Nama indikator self assessment');
            $table->string('path_file')->nullable()->comment('Path file bukti');
            $table->text('penjelasan')->nullable()->comment('Penjelasan/deskripsi indikator');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bukti_self_assessment');
    }
};