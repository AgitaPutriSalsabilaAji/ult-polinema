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
        Schema::create('pengajuan', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('nomor_tiket', 50)->unique('nomor_tiket');
            $table->integer('id_pengguna')->index('fk_pengajuan_pengguna');
            $table->integer('id_layanan')->index('fk_pengajuan_layanan');
            $table->string('subjek');
            $table->text('deskripsi');
            $table->string('status', 30)->default('diajukan');
            $table->timestamp('dibuat_pada')->nullable()->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan');
    }
};
