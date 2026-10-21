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
        Schema::table('bagian_layanan', function (Blueprint $table) {
            $table->foreign(['id_layanan'], 'fk_bagian_layanan')->references(['id'])->on('layanan')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bagian_layanan', function (Blueprint $table) {
            $table->dropForeign('fk_bagian_layanan');
        });
    }
};
