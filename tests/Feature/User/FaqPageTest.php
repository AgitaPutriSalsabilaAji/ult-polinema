<?php

namespace Tests\Feature\User;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FaqPageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('unit', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('nama_unit');
        });

        Schema::create('faq', function (Blueprint $table): void {
            $table->increments('id');
            $table->text('pertanyaan');
            $table->text('jawaban');
            $table->unsignedInteger('id_unit')->nullable();
            $table->string('status', 20)->default('aktif');
            $table->timestamp('dibuat_pada')->nullable();
            $table->timestamp('diperbarui_pada')->nullable();
        });
    }

    public function test_search_and_category_filter_match_database_answers_and_related_unit(): void
    {
        DB::table('unit')->insert([
            ['id' => 1, 'nama_unit' => 'Bagian Keuangan'],
            ['id' => 2, 'nama_unit' => 'Bagian Akademik'],
        ]);
        DB::table('faq')->insert([
            [
                'id' => 1,
                'pertanyaan' => 'Bagaimana cara mengajukan?',
                'jawaban' => 'Lampirkan bukti pembayaran tagihan.',
                'id_unit' => 1,
                'status' => 'aktif',
            ],
            [
                'id' => 2,
                'pertanyaan' => 'Apakah ada biaya lain?',
                'jawaban' => 'Silakan hubungi unit terkait.',
                'id_unit' => 1,
                'status' => 'aktif',
            ],
            [
                'id' => 3,
                'pertanyaan' => 'Cara membayar tagihan akademik?',
                'jawaban' => 'Lihat informasi akademik.',
                'id_unit' => 2,
                'status' => 'aktif',
            ],
            [
                'id' => 4,
                'pertanyaan' => 'Tagihan yang tidak aktif?',
                'jawaban' => 'Bukti tagihan.',
                'id_unit' => 1,
                'status' => 'nonaktif',
            ],
            [
                'id' => 5,
                'pertanyaan' => 'Bagaimana status tagihan?',
                'jawaban' => 'Hubungi unit terkait.',
                'id_unit' => 1,
                'status' => 'aktif',
            ],
        ]);

        $response = $this->withoutVite()->get(route('faq.index', [
            'search' => 'tagihan',
            'kategori' => '1',
        ]));

        $response->assertOk()
            ->assertSeeText('Bagaimana cara mengajukan?')
            ->assertSeeText('Lampirkan bukti pembayaran tagihan.')
            ->assertSeeText('Bagaimana status tagihan?')
            ->assertDontSee('Apakah ada biaya lain?')
            ->assertDontSee('Cara membayar tagihan akademik?')
            ->assertDontSee('Tagihan yang tidak aktif?');
    }

    public function test_search_matches_database_questions_and_answers_and_links_to_the_results(): void
    {
        DB::table('unit')->insert([
            ['id' => 1, 'nama_unit' => 'Unit Keuangan'],
        ]);
        DB::table('faq')->insert([
            [
                'id' => 1,
                'pertanyaan' => 'Bagaimana cara membayar UKT?',
                'jawaban' => 'Gunakan kanal pembayaran resmi.',
                'id_unit' => 1,
                'status' => 'aktif',
            ],
            [
                'id' => 2,
                'pertanyaan' => 'Kapan pembayaran dibuka?',
                'jawaban' => 'Jadwal pembayaran UKT diumumkan setiap semester.',
                'id_unit' => 1,
                'status' => 'aktif',
            ],
            [
                'id' => 3,
                'pertanyaan' => 'Bagaimana cara mengajukan surat?',
                'jawaban' => 'Ajukan melalui layanan akademik.',
                'id_unit' => 1,
                'status' => 'aktif',
            ],
        ]);

        $response = $this->withoutVite()->get(route('faq.index', ['search' => 'UKT']));

        $response->assertOk()
            ->assertSeeText('Bagaimana cara membayar UKT?')
            ->assertSeeText('Jadwal pembayaran UKT diumumkan setiap semester.')
            ->assertDontSee('Bagaimana cara mengajukan surat?')
            ->assertSee('action="'.route('faq.index').'#pertanyaan-umum"', false)
            ->assertSee('id="pertanyaan-umum"', false);
    }

    public function test_categories_only_include_units_with_active_faqs_from_the_database(): void
    {
        DB::table('unit')->insert([
            ['id' => 1, 'nama_unit' => 'Unit Keuangan'],
            ['id' => 2, 'nama_unit' => 'Teknologi dan Sistem Informasi'],
        ]);
        DB::table('faq')->insert([
            [
                'id' => 1,
                'pertanyaan' => 'Bagaimana pembayaran UKT?',
                'jawaban' => 'Gunakan kanal pembayaran resmi.',
                'id_unit' => 1,
                'status' => 'aktif',
            ],
            [
                'id' => 2,
                'pertanyaan' => 'Bagaimana mengakses sistem?',
                'jawaban' => 'Informasi sistem.',
                'id_unit' => 2,
                'status' => 'nonaktif',
            ],
        ]);

        $this->withoutVite()
            ->get(route('faq.index'))
            ->assertOk()
            ->assertSee('Unit Keuangan')
            ->assertDontSee('Teknologi dan Sistem Informasi');
    }

    public function test_search_without_matches_shows_search_empty_state(): void
    {
        $this->withoutVite()
            ->get(route('faq.index', ['search' => 'tidak ditemukan']))
            ->assertOk()
            ->assertSeeText('Pertanyaan tidak ditemukan');
    }

    public function test_faq_page_uses_direct_faq_navigation_and_whatsapp_help_link(): void
    {
        $response = $this->withoutVite()->get(route('faq.index'));

        $response->assertOk()
            ->assertSee('ult-navbar--light', false)
            ->assertSee('class="nav-link active"', false)
            ->assertSee('href="'.route('faq.index').'"', false)
            ->assertSee('aria-current="page"', false)
            ->assertSee('href="https://wa.me/6285117479099"', false)
            ->assertSee('target="_blank" rel="noopener noreferrer"', false)
            ->assertDontSee('Kirim E-Ticket')
            ->assertSee('Belum ada pertanyaan yang tersedia.')
            ->assertSee('faq-search-input', false);
    }

    public function test_faq_navbar_link_uses_faq_route_from_other_pages(): void
    {
        $navbar = view('user.navbar', ['theme' => 'light', 'active' => 'home'])->render();

        $this->assertStringContainsString('href="'.route('faq.index').'"', $navbar);
    }
}
