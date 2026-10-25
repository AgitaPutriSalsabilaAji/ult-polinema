<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disposisi_pengajuan', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('id_pengajuan')->index('fk_disposisi_pengajuan');
            $table->integer('id_unit_tujuan')->index('fk_disposisi_unit_tujuan');
            $table->integer('id_admin_pengirim')->index('fk_disposisi_admin_pengirim');
            $table->integer('id_admin_tujuan')->index('fk_disposisi_admin_tujuan');
            $table->text('catatan_disposisi')->nullable();
            $table->string('status_disposisi', 50)->default('menunggu');
            $table->timestamp('tanggal_disposisi')->nullable()->useCurrent();
            $table->timestamp('diperbarui_pada')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disposisi_pengajuan');
    }
};
