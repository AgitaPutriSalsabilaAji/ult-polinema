<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_the_application_returns_a_successful_response(): void
    {
        Schema::create('unit', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('nama_unit');
        });

        Schema::create('layanan', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('nama_layanan');
            $table->integer('id_unit');
            $table->text('deskripsi_singkat')->nullable();
            $table->string('status')->default('aktif');
        });

        Schema::create('bagian_layanan', function (Blueprint $table): void {
            $table->increments('id');
            $table->integer('id_layanan');
            $table->string('gambar_pendukung')->nullable();
            $table->integer('urutan_bagian')->nullable();
        });

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
