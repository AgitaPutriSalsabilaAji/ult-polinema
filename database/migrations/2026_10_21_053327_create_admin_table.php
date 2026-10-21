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
        Schema::create('admin', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('nama_lengkap_gelar', 150);
            $table->string('email', 100)->unique('email');
            $table->string('kata_sandi');
            $table->string('nip', 50)->unique('nip');
            $table->string('peran', 50)->default('admin_unit');
            $table->integer('id_unit')->index('fk_admin_unit');
            $table->string('nomor_telepon', 30)->nullable();
            $table->string('avatar')->nullable();
            $table->timestamp('dibuat_pada')->nullable()->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin');
    }
};
