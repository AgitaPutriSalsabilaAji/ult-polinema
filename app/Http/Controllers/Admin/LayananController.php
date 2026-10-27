
public function index(Request $request)
{
    $semuaLayanan = collect($this->dataLayanan());

    $kategoriMap = [
        'semua' => null,
        'akademik' => 'Akademik',
        'keuangan' => 'Keuangan',
        'kemahasiswaan' => 'Kemahasiswaan',
        'humas' => 'Kerja Sama & Humas',
        'kepegawaian' => 'Kepegawaian',
    ];

    $kategori = strtolower($request->query('kategori', 'semua'));
    $keyword = trim($request->query('q', ''));

    if (!array_key_exists($kategori, $kategoriMap)) {
        $kategori = 'semua';
    }

    // Filter berdasarkan kategori.
    $hasil = $semuaLayanan
        ->when($kategori !== 'semua', function ($items) use ($kategoriMap, $kategori) {
            return $items->where('kategori', $kategoriMap[$kategori]);
        })
        ->when($keyword !== '', function ($items) use ($keyword) {
            return $items->filter(function ($item) use ($keyword) {
                return Str::contains(
                    Str::lower($item['nama'] . ' ' . $item['deskripsi']),
                    Str::lower($keyword)
                );
            });
        })
        ->values();

    // 12 kartu per halaman.
    $services = new \Illuminate\Pagination\LengthAwarePaginator(
        $hasil->forPage($request->integer('page', 1), 12)->values(),
        $hasil->count(),
        12,
        $request->integer('page', 1),
        [
            'path' => $request->url(),
            'query' => $request->query(),
        ]
    );

    $jumlahKategori = [
        'semua' => 38,
        'akademik' => 16,
        'keuangan' => 6,
        'kemahasiswaan' => 9,
        'humas' => 7,
        'kepegawaian' => 5,
    ];

    return view('user.layanan.index', compact(
        'services',
        'kategori',
        'keyword',
        'jumlahKategori'
    ));
}
