<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class HomepageServicesTest extends TestCase
{
    public function test_homepage_lists_only_humas_services_with_detail_routes(): void
    {
        $response = $this->withoutVite()->get(route('beranda'));

        $response->assertOk()
            ->assertSeeText('Kunjungan Sekolah')
            ->assertSeeText('Edufair')
            ->assertSeeText('Liputan Kegiatan')
            ->assertSeeText('Informasi SPMB')
            ->assertSeeText('Informasi & Pengaduan')
            ->assertDontSeeText('Surat Keterangan Kuliah')
            ->assertDontSeeText('Informasi Pembayaran')
            ->assertSee('<h2 class="section-heading text-start">Layanan</h2>', false)
            ->assertSee('href="'.route('layanan.index').'" class="see-all">LIHAT SEMUA LAYANAN', false)
            ->assertSee('href="'.route('layanan.index').'" class="btn btn-orange">', false)
            ->assertDontSee('data-bs-toggle="modal"', false)
            ->assertSee('href="'.route('beranda', ['kategori' => 'humas']).'#layanan"', false);

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

        $response->assertOk()
            ->assertSeeText($includedService)
            ->assertDontSeeText($excludedService)
            ->assertDontSee('data-bs-toggle="modal"', false);

        if ($category !== 'humas') {
            $response->assertSee(
                'href="'.route('layanan.index', ['kategori' => $category]).'" class="service-link"',
                false
            );
        }

        $this->assertSame($serviceCount, substr_count($response->getContent(), 'class="service-link"'));
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
}
