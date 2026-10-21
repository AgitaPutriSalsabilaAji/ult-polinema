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
        Schema::table('pesan_pengajuan', function (Blueprint $table) {
            $table->foreign(['id_pengajuan'], 'fk_pesan_pengajuan')->references(['id'])->on('pengajuan')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesan_pengajuan', function (Blueprint $table) {
            $table->dropForeign('fk_pesan_pengajuan');
        });
    }
};
