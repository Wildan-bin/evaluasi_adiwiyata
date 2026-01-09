<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tabel untuk menyimpan semua file uploads dengan metadata lengkap
     * - Menyimpan nama file asli dan nama file sistem
     * - Tracking status approval dari mentor
     * - Menyimpan komentar/feedback dari mentor
     */
    public function up(): void
    {
        Schema::create('file_uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // File Information
            $table->string('original_filename'); // Nama file yang di-upload user
            $table->string('system_filename'); // Nama file yang disimpan di sistem
            $table->string('file_path'); // Path lengkap file di storage
            $table->integer('file_size'); // Ukuran file dalam bytes
            $table->string('mime_type')->default('application/pdf');

            // Classification
            $table->enum('category', ['administrasi', 'self_assessment', 'rencana', 'pendampingan', 'pernyataan'])->default('administrasi');
            $table->string('indikator')->nullable(); // Indikator/subbab yang diisi
            $table->string('section')->nullable(); // A5, A6, A7, A8, etc.

            // Approval System
            $table->enum('status', ['pending', 'approved', 'rejected', 'revision_requested'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null'); // Mentor yang review
            $table->timestamp('reviewed_at')->nullable();
            $table->text('mentor_comment')->nullable();

            // Revision Tracking
            $table->foreignId('parent_file_id')->nullable()->constrained('file_uploads')->onDelete('cascade'); // Jika file ini adalah revisi
            $table->integer('revision_number')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('user_id');
            $table->index('category');
            $table->index('status');
            $table->index(['user_id', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_uploads');
    }
};
