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
        Schema::create('riwayat_pengajuan', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('id_pengajuan')->index('fk_riwayat_pengajuan');
            $table->string('status_sebelumnya', 30)->nullable();
            $table->string('status_baru', 30);
            $table->string('judul_status', 150);
            $table->text('deskripsi');
            $table->string('nama_aktor', 150);
            $table->integer('id_admin')->nullable()->index('fk_riwayat_admin');
            $table->timestamp('dibuat_pada')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_pengajuan');
    }
};
