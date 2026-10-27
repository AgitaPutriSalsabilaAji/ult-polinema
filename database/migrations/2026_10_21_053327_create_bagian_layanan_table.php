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
        Schema::create('bagian_layanan', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('id_layanan')->index('fk_bagian_layanan');
            $table->string('judul_bagian', 150);
            $table->text('konten');
            $table->string('gambar_pendukung')->nullable();
            $table->integer('urutan_bagian')->nullable()->default(1);
            $table->timestamp('dibuat_pada')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bagian_layanan');
    }
};
