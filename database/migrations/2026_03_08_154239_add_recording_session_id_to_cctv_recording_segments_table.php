<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cctv_recording_segments', function (Blueprint $table) {
            $table->uuid('recording_session_id')->nullable()->after('room_id');
            $table->index('recording_session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cctv_recording_segments', function (Blueprint $table) {
            $table->dropIndex(['recording_session_id']);
            $table->dropColumn('recording_session_id');
        });
    }
};
