<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pernyataan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('pernyataan_data', ['benar', 'tidak benar'])->default('benar')->comment('Pernyataan kebenaran data');
            $table->enum('persetujuan_publikasi', ['setuju', 'tidak setuju'])->default('setuju')->comment('Persetujuan publikasi hasil evaluasi');
            $table->string('bukti_persetujuan')->nullable()->comment('Path file bukti persetujuan');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pernyataan');
    }
};