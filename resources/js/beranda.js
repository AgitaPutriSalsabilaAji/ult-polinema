import '../css/beranda.css';

document.addEventListener('DOMContentLoaded', () => {
    // Elemen pencarian layanan
    const searchForm = document.getElementById('serviceSearchForm');
    const searchInput = document.getElementById('serviceSearch');
    const searchFeedback = document.getElementById('searchFeedback');
    const serviceItems = Array.from(
        document.querySelectorAll('.service-item')
    );
    const noResults = document.getElementById('noResults');

    // Filter daftar layanan berdasarkan kata kunci
    function filterServices(query) {
        const normalized = query.trim().toLocaleLowerCase('id-ID');
        let visible = 0;

        serviceItems.forEach((item) => {
            const text = (
                item.innerText + ' ' + (item.dataset.search || '')
            ).toLocaleLowerCase('id-ID');

            const matches = !normalized || text.includes(normalized);

            item.classList.toggle('d-none', !matches);

            if (matches) {
                visible += 1;
            }
        });

        if (noResults) {
            noResults.classList.toggle('d-none', visible !== 0);
        }

        if (searchFeedback) {
            searchFeedback.textContent = normalized
                ? `${visible} layanan ditampilkan dari ${serviceItems.length} layanan yang tersedia pada halaman ini.`
                : '';
        }
    }

    // Proses pencarian ketika formulir dikirim
    if (searchForm && searchInput) {
        searchForm.addEventListener('submit', (event) => {
            event.preventDefault();

            filterServices(searchInput.value);

            document.getElementById('layanan')?.scrollIntoView({
                behavior: 'smooth',
                block: 'start',
            });
        });

        // Reset hasil pencarian ketika input dikosongkan
        searchInput.addEventListener('input', () => {
            if (searchInput.value.trim() === '') {
                filterServices('');
            }
        });
    }

    // Filter berdasarkan kategori layanan
    document.querySelectorAll('.category-card').forEach((card) => {
        card.addEventListener('click', () => {
            const category = card.dataset.category;

            const categoryTerms = {
                akademik: 'akademik',
                keuangan: 'keuangan',
                kemahasiswaan: 'kemahasiswaan beasiswa',
                humas: 'humas kunjungan sekolah kerja sama',
                kepegawaian: 'kepegawaian internal',
            };

            if (searchInput) {
                searchInput.value = categoryTerms[category] || '';
                filterServices(searchInput.value);
            }
        });
    });

    // Form cek status dikirim ke route Laravel /cek-status.
    // Route tersebut akan mencari nomor e-ticket pada database dan menampilkan hasilnya.

    // Tutup menu navigasi mobile setelah tautan dipilih
    document.querySelectorAll('#mainNav .nav-link').forEach((link) => {
        link.addEventListener('click', () => {
            const nav = document.getElementById('mainNav');

            if (
                nav &&
                nav.classList.contains('show') &&
                window.bootstrap
            ) {
                window.bootstrap.Collapse
                    .getOrCreateInstance(nav)
                    .hide();
            }
        });
    });
});