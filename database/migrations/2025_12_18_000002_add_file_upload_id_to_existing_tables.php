<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Menambahkan relasi ke tabel file_uploads pada tabel existing
     * untuk backward compatibility dan tracking yang lebih baik
     */
    public function up(): void
    {
        // Add file_upload_id to rencana table
        Schema::table('rencana', function (Blueprint $table) {
            $table->foreignId('file_upload_id')->nullable()->after('path_file')
                ->constrained('file_uploads')->onDelete('set null');
        });

        // Add file_upload_id to bukti_self_assessment table
        Schema::table('bukti_self_assessment', function (Blueprint $table) {
            $table->foreignId('file_upload_id')->nullable()->after('path_file')
                ->constrained('file_uploads')->onDelete('set null');
        });

        // Add file_upload_id to pernyataan table
        Schema::table('pernyataan', function (Blueprint $table) {
            $table->foreignId('file_upload_id')->nullable()->after('bukti_persetujuan')
                ->constrained('file_uploads')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rencana', function (Blueprint $table) {
            $table->dropForeign(['file_upload_id']);
            $table->dropColumn('file_upload_id');
        });

        Schema::table('bukti_self_assessment', function (Blueprint $table) {
            $table->dropForeign(['file_upload_id']);
            $table->dropColumn('file_upload_id');
        });

        Schema::table('pernyataan', function (Blueprint $table) {
            $table->dropForeign(['file_upload_id']);
            $table->dropColumn('file_upload_id');
        });
    }
};
