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
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->foreign(['id_layanan'], 'fk_pengajuan_layanan')->references(['id'])->on('layanan')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['id_pengguna'], 'fk_pengajuan_pengguna')->references(['id'])->on('pengguna')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->dropForeign('fk_pengajuan_layanan');
            $table->dropForeign('fk_pengajuan_pengguna');
        });
    }
};
