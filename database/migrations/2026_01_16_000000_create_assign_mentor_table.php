<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assign_mentor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id_mentor')->constrained('users')->onDelete('cascade');
            $table->foreignId('user_id_sekolah')->constrained('users')->onDelete('cascade');
            $table->timestamp('assign_time_begin')->nullable();
            $table->timestamp('assign_time_finished')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assign_mentor');
    }
};