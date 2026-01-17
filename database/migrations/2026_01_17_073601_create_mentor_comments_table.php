<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mentor_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('file_id');
            $table->enum('file_type', ['a5', 'a6', 'a8']);
            $table->text('comment');
            $table->timestamps();

            $table->index(['file_id', 'file_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mentor_comments');
    }
};