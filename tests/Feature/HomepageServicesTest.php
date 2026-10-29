<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class HomepageServicesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('unit', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama_unit');
        });

        Schema::create('layanan', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama_layanan');
            $table->integer('id_unit');
            $table->string('estimasi_waktu')->nullable();
            $table->text('deskripsi_singkat')->nullable();
            $table->string('tarif')->nullable();
            $table->string('jam_operasional')->nullable();
        });

        Schema::create('bagian_layanan', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_layanan');
            $table->string('judul_bagian');
            $table->text('konten');
            $table->string('gambar_pendukung')->nullable();
            $table->integer('urutan_bagian')->nullable();
        });
    }

    public function test_user_navbar_supports_both_themes_without_check_services_menu(): void
    {
        $lightNavbar = view('user.navbar', ['theme' => 'light', 'active' => 'home'])->render();
        $purpleNavbar = view('user.navbar', ['theme' => 'purple', 'active' => 'services'])->render();
        $ticketNavbar = view('user.navbar', ['theme' => 'purple', 'active' => 'ticket'])->render();
        $defaultNavbar = view('user.navbar')->render();

        $this->assertStringContainsString('ult-navbar--light', $lightNavbar);
        $this->assertStringContainsString('ult-navbar--purple', $purpleNavbar);
        $this->assertStringContainsString('ult-navbar--purple', $defaultNavbar);
        $this->assertStringContainsString('navbar-nav ms-lg-auto', $purpleNavbar);
        $this->assertStringContainsString('href="'.route('layanan.index').'"', $ticketNavbar);
        $this->assertStringNotContainsString('href="'.route('beranda').'#layanan"', $ticketNavbar);
        $this->assertStringNotContainsString('Cek Layanan', $lightNavbar.$purpleNavbar);
    }

    public function test_public_pages_render_one_shared_navbar_and_footer_with_the_expected_theme(): void
    {
        $this->withoutVite();

        $pages = [
            'homepage' => [route('beranda'), 'ult-navbar--light'],
            'service list' => [route('layanan.index'), 'ult-navbar--purple'],
            'service detail' => [route('layanan.detail', ['slug' => 'kunjungan-sekolah']), 'ult-navbar--purple'],
            'ticket status' => [route('cek-status'), 'ult-navbar--purple'],
        ];

        foreach ($pages as [$url, $theme]) {
            $response = $this->get($url);

            $response->assertOk()
                ->assertSee($theme)
                ->assertSee('<footer class="ult-footer">', false)
                ->assertDontSee('Hubungi via WA')
                ->assertDontSee('Cek Layanan', false);

            if ($url === route('layanan.index') || $url === route('layanan.detail', ['slug' => 'kunjungan-sekolah'])) {
                $response->assertDontSee('class="breadcrumb"', false);
            }

            $this->assertSame(1, substr_count($response->getContent(), 'class="navbar navbar-expand-lg'));
            $this->assertSame(1, substr_count($response->getContent(), '<footer class="ult-footer">'));
            $response->assertDontSee('Profil ULT');

            foreach (['akademik', 'keuangan', 'kemahasiswaan', 'kepegawaian', 'humas'] as $category) {
                $response->assertSee(
                    'href="'.route('layanan.index', ['kategori' => $category]).'"',
                    false
                );
            }
        }
    }

    public function test_service_list_autocomplete_uses_current_category_services_and_their_detail_routes(): void
    {
        $response = $this->withoutVite()
            ->get(route('layanan.index', ['kategori' => 'humas']));

        $response->assertOk()
            ->assertSee('id="serviceListSearchResults"', false)
            ->assertSee('role="combobox"', false)
            ->assertSee('aria-autocomplete="list"', false)
            ->assertSee('Layanan tidak ditemukan')
            ->assertSeeText('Kunjungan Sekolah')
            ->assertSee(
                'href="'.route('layanan.detail', ['slug' => 'kunjungan-sekolah']).'"',
                false
            )
            ->assertDontSee('Surat Keterangan Kuliah')
            ->assertDontSee('class="breadcrumb"', false);
    }

    public function test_homepage_lists_only_humas_services_with_detail_routes(): void
    {
        $response = $this->withoutVite()->get(route('beranda'));
        $serviceCardText = $this->serviceCardText($response);

        $response->assertOk()
            ->assertSee('href="https://wa.me/6285117479099"', false)
            ->assertSee('Hubungi via WhatsApp')
            ->assertSeeText('Kunjungan Sekolah')
            ->assertSeeText('Edufair')
            ->assertSeeText('Liputan Kegiatan')
            ->assertSeeText('Informasi SPMB')
            ->assertSeeText('Informasi & Pengaduan')
            ->assertSee('<h2 class="section-heading text-start">Layanan</h2>', false)
            ->assertSee('href="'.route('layanan.index').'" class="see-all">LIHAT SEMUA LAYANAN', false)
            ->assertSee('href="'.route('layanan.index').'" class="btn btn-orange">', false)
            ->assertDontSee('data-bs-toggle="modal"', false)
            ->assertSee('href="'.route('beranda', ['kategori' => 'humas']).'#layanan"', false);

        $this->assertStringNotContainsString('Surat Keterangan Kuliah', $serviceCardText);
        $this->assertStringNotContainsString('Informasi Pembayaran', $serviceCardText);
        $this->assertSame(5, substr_count($response->getContent(), 'class="service-link"'));
        $this->assertSame(5, substr_count($response->getContent(), 'class="category-card"'));

        foreach (['akademik', 'keuangan', 'kemahasiswaan', 'humas', 'kepegawaian'] as $category) {
            $response->assertSee(
                'href="'.route('beranda', ['kategori' => $category]).'#layanan"',
                false
            );
        }

        foreach ([
            'kunjungan-sekolah',
            'edufair',
            'liputan-kegiatan',
            'informasi-spmb',
            'informasi-pengaduan',
        ] as $slug) {
            $response->assertSee(
                'href="'.route('layanan.detail', ['slug' => $slug]).'" class="service-link"',
                false
            );
        }
    }

    #[DataProvider('homepageCategories')]
    public function test_selecting_a_category_shows_its_services_on_the_homepage(
        string $category,
        string $includedService,
        string $excludedService,
        int $serviceCount
    ): void {
        $response = $this->withoutVite()->get(route('beranda', ['kategori' => $category]));
        $serviceCardText = $this->serviceCardText($response);

        $response->assertOk()
            ->assertSeeText($includedService)
            ->assertDontSee('data-bs-toggle="modal"', false);

        $this->assertStringContainsString($includedService, $serviceCardText);
        $this->assertStringNotContainsString($excludedService, $serviceCardText);

        if ($category !== 'humas') {
            $response->assertSee(
                'href="'.route('layanan.index', ['kategori' => $category]).'" class="service-link"',
                false
            );
        }

        $this->assertSame($serviceCount, substr_count($response->getContent(), 'class="service-link"'));
    }

    public function test_homepage_search_links_services_to_their_own_detail_pages(): void
    {
        $response = $this->withoutVite()->get(route('beranda'));

        $response->assertOk()
            ->assertSee('id="serviceSearchResults"', false)
            ->assertSee('Layanan tidak ditemukan')
            ->assertSee('src="'.asset('images/ilustrasi layanan.png').'"', false)
            ->assertSee('alt="Ilustrasi Layanan ULT Polinema"', false)
            ->assertDontSee('38+')
            ->assertDontSee('1–3 Hari Kerja')
            ->assertDontSee('100% Terbuka');

        foreach ([
            'kunjungan-sekolah' => 'Kunjungan Sekolah',
            'edufair' => 'Edufair',
            'surat-keterangan-kuliah' => 'Surat Keterangan Kuliah',
            'pembayaran-ukt' => 'Pembayaran UKT',
        ] as $slug => $name) {
            $response->assertSee(
                'href="'.route('layanan.detail', ['slug' => $slug]).'"',
                false
            )->assertSeeText($name);
        }
    }

    public function test_service_detail_route_renders_different_catalog_services(): void
    {
        $this->withoutVite();

        $homepage = $this->get(route('beranda'))->assertOk();
        preg_match_all(
            '/<a\b(?=[^>]*data-service-option)[^>]*href="([^"]+)"[^>]*>\s*([^<]+)\s*<\/a>/s',
            $homepage->getContent(),
            $serviceOptions,
            PREG_SET_ORDER
        );

        $this->assertGreaterThan(0, count($serviceOptions));

        foreach ($serviceOptions as $serviceOption) {
            $name = html_entity_decode(trim($serviceOption[2]), ENT_QUOTES | ENT_HTML5);

            $this->get($serviceOption[1])
                ->assertOk()
                ->assertSeeText($name);
        }
    }

    public function test_service_detail_displays_only_saved_sections_for_that_service_in_order(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('layanan/pendukung.png', 'image-content');

        DB::table('unit')->insert([
            'id' => 1,
            'nama_unit' => 'Unit dari Admin',
        ]);

        DB::table('layanan')->insert([
            [
                'id' => 1,
                'nama_layanan' => 'KUNJUNGAN SEKOLAH',
                'id_unit' => 1,
                'estimasi_waktu' => 'Estimasi dari Admin',
                'deskripsi_singkat' => 'Deskripsi layanan dari Admin',
                'tarif' => 'Tarif dari Admin',
                'jam_operasional' => 'Jam dari Admin',
            ],
            [
                'id' => 2,
                'nama_layanan' => 'EDUFAIR',
                'id_unit' => 1,
                'estimasi_waktu' => null,
                'deskripsi_singkat' => null,
                'tarif' => null,
                'jam_operasional' => null,
            ],
        ]);

        DB::table('bagian_layanan')->insert([
            [
                'id' => 1,
                'id_layanan' => 1,
                'judul_bagian' => 'Tentang Layanan Dinamis',
                'konten' => '<script>alert(1)</script>'."\n".'Baris kedua',
                'gambar_pendukung' => null,
                'urutan_bagian' => 2,
            ],
            [
                'id' => 2,
                'id_layanan' => 1,
                'judul_bagian' => 'Informasi Pertama',
                'konten' => "Baris satu\nBaris dua",
                'gambar_pendukung' => 'layanan/pendukung.png',
                'urutan_bagian' => 1,
            ],
            [
                'id' => 3,
                'id_layanan' => 2,
                'judul_bagian' => 'Bagian Milik Edufair',
                'konten' => 'Konten layanan lain',
                'gambar_pendukung' => null,
                'urutan_bagian' => 1,
            ],
        ]);

        $response = $this->withoutVite()
            ->get(route('layanan.detail', ['slug' => 'kunjungan-sekolah']));
        $html = $response->getContent();

        $response->assertOk()
            ->assertSee('id="serviceInformationAccordion"', false)
            ->assertSee('class="accordion-button collapsed"', false)
            ->assertSee('data-bs-toggle="collapse"', false)
            ->assertSeeText('Tentang Layanan Dinamis')
            ->assertSeeText('Informasi Pertama')
            ->assertSeeText('Deskripsi layanan dari Admin')
            ->assertSeeText('Unit dari Admin')
            ->assertSeeText('Estimasi dari Admin')
            ->assertSeeText('Tarif dari Admin')
            ->assertSeeText('Jam dari Admin')
            ->assertDontSee('Bagian Milik Edufair')
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertSee(
                'src="'.Storage::disk('public')->url('layanan/pendukung.png').'"',
                false
            )
            ->assertSee(
                'href="https://wa.me/6285117479099" target="_blank" rel="noopener noreferrer"',
                false
            )
            ->assertDontSee('LAYANAN TERKAIT')
            ->assertDontSee('related-section', false)
            ->assertDontSee('Data Identitas Pemohon');

        $this->assertLessThan(
            strpos($html, 'Tentang Layanan Dinamis'),
            strpos($html, 'Informasi Pertama')
        );
    }

    public static function homepageCategories(): array
    {
        return [
            'akademik' => ['akademik', 'Surat Keterangan Kuliah', 'Pembayaran UKT', 16],
            'keuangan' => ['keuangan', 'Pembayaran UKT', 'Surat Keterangan Kuliah', 4],
            'kemahasiswaan' => ['kemahasiswaan', 'Beasiswa', 'Pembayaran UKT', 4],
            'humas' => ['humas', 'Kunjungan Sekolah', 'Surat Keterangan Kuliah', 5],
            'kepegawaian' => ['kepegawaian', 'Internal', 'Surat Keterangan Kuliah', 2],
        ];
    }

    private function serviceCardText(TestResponse $response): string
    {
        preg_match_all(
            '/<a\b[^>]*class="service-link"[^>]*>.*?<\/a>/s',
            $response->getContent(),
            $serviceCards
        );

        return html_entity_decode(
            strip_tags(implode('', $serviceCards[0])),
            ENT_QUOTES | ENT_HTML5
        );
    }
}
