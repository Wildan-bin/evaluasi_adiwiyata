<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('administrasi_sekolah', function (Blueprint $table) {
            // Verification fields
            $table->boolean('verified')->default(false)->after('tim_greenedu');
            $table->foreignId('verified_by_admin_id')->nullable()->after('verified')->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at_custom')->nullable()->after('verified_by_admin_id');

            // Submission tracking
            $table->timestamp('submitted_at')->nullable()->after('verified_by_admin_id');
            $table->timestamp('last_updated_by_user_at')->nullable()->after('submitted_at');

            // Admin note
            $table->text('admin_note')->nullable()->after('last_updated_by_user_at');

            // Edit request
            $table->boolean('edit_requested')->default(false)->after('admin_note');
            $table->text('edit_request_reason')->nullable()->after('edit_requested');
            $table->timestamp('edit_requested_at')->nullable()->after('edit_request_reason');
            $table->timestamp('edit_request_handled_at')->nullable()->after('edit_requested_at');
            $table->foreignId('edit_request_handled_by_admin_id')->nullable()->after('edit_request_handled_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('administrasi_sekolah', function (Blueprint $table) {
            $table->dropForeign(['verified_by_admin_id']);
            $table->dropForeign(['edit_request_handled_by_admin_id']);
            $table->dropColumn([
                'verified',
                'verified_at_custom',
                'verified_by_admin_id',
                'submitted_at',
                'last_updated_by_user_at',
                'admin_note',
                'edit_requested',
                'edit_request_reason',
                'edit_requested_at',
                'edit_request_handled_at',
                'edit_request_handled_by_admin_id',
            ]);
        });
    }
};
