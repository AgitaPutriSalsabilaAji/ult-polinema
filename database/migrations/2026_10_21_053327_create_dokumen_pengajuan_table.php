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
        Schema::create('dokumen_pengajuan', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('id_pengajuan')->index('fk_dokumen_pengajuan');
            $table->string('nama_file');
            $table->string('path_file');
            $table->string('ukuran_file', 50);
            $table->timestamp('diunggah_pada')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_pengajuan');
    }
};
