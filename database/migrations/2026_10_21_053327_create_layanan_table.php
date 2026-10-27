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
        Schema::create('layanan', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('nama_layanan', 150);
            $table->integer('id_unit')->index('fk_layanan_unit');
            $table->string('estimasi_waktu', 50)->default('1 - 3 Hari Kerja');
            $table->text('deskripsi_singkat')->nullable();
            $table->string('tarif', 100)->default('Rp 0 (Gratis)');
            $table->string('jam_operasional', 100)->default('Senin - Jumat, 08.00 - 16.00 WIB');
            $table->string('status', 20)->default('aktif');
            $table->string('tautan_eksternal')->nullable();
            $table->timestamp('dibuat_pada')->nullable()->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('layanan');
    }
};
