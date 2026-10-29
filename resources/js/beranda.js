import '../css/beranda.css';

document.addEventListener('DOMContentLoaded', () => {
    // Elemen pencarian layanan
    const searchForm = document.getElementById('serviceSearchForm');
    const searchInput = document.getElementById('serviceSearch');
    const searchResults = document.getElementById('serviceSearchResults');
    const searchOptions = Array.from(
        document.querySelectorAll('[data-service-option]')
    );
    const searchEmpty = searchResults?.querySelector('.service-search-empty');

    function closeSearchResults() {
        if (!searchResults || !searchInput) {
            return;
        }

        searchResults.classList.add('d-none');
        searchInput.setAttribute('aria-expanded', 'false');
    }

    function updateSearchResults() {
        if (!searchInput || !searchResults || !searchEmpty) {
            return [];
        }

        const query = searchInput.value.trim().toLocaleLowerCase('id-ID');

        if (!query) {
            searchOptions.forEach((option) => option.classList.remove('d-none'));
            searchEmpty.classList.add('d-none');
            closeSearchResults();

            return [];
        }

        const matches = searchOptions.filter((option) => {
            const isMatch = option.textContent
                .trim()
                .toLocaleLowerCase('id-ID')
                .includes(query);

            option.classList.toggle('d-none', !isMatch);

            return isMatch;
        });

        searchEmpty.classList.toggle('d-none', matches.length > 0);
        searchResults.classList.remove('d-none');
        searchInput.setAttribute('aria-expanded', 'true');

        return matches;
    }

    if (searchForm && searchInput && searchResults) {
        searchInput.addEventListener('input', updateSearchResults);
        searchInput.addEventListener('focus', updateSearchResults);
        searchInput.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeSearchResults();
            }
        });

        searchForm.addEventListener('submit', (event) => {
            event.preventDefault();

            const matches = updateSearchResults();

            if (matches.length > 0) {
                window.location.assign(matches[0].href);
                return;
            }

            if (!searchInput.value.trim()) {
                document.getElementById('layanan')?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start',
                });
            }
        });

        document.addEventListener('click', (event) => {
            if (!searchForm.contains(event.target)) {
                closeSearchResults();
            }
        });
    }

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