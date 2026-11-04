<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesan_pengajuan', function (Blueprint $table): void {
            $table->string('nama_lampiran')->nullable()->after('lampiran');
        });
    }

    public function down(): void
    {
        Schema::table('pesan_pengajuan', function (Blueprint $table): void {
            $table->dropColumn('nama_lampiran');
        });
    }
};
