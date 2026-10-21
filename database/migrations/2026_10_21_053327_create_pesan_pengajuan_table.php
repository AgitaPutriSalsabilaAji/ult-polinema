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
        Schema::create('pesan_pengajuan', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('id_pengajuan')->index('fk_pesan_pengajuan');
            $table->string('pengirim_tipe', 20);
            $table->integer('id_pengirim');
            $table->text('pesan');
            $table->string('lampiran')->nullable();
            $table->timestamp('dibuat_pada')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesan_pengajuan');
    }
};
