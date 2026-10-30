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
        Schema::table('riwayat_pengajuan', function (Blueprint $table) {
            if (!Schema::hasColumn('riwayat_pengajuan', 'status_sebelumnya')) {
                $table->string('status_sebelumnya', 30)->nullable()->after('id_pengajuan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('riwayat_pengajuan', function (Blueprint $table) {
            if (Schema::hasColumn('riwayat_pengajuan', 'status_sebelumnya')) {
                $table->dropColumn('status_sebelumnya');
            }
        });
    }
};
