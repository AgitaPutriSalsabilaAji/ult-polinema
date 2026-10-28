/* resources/js/layanan.js */
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.querySelector('.layanan-page .search-form input[name="q"]');
    if (searchInput) searchInput.setAttribute('aria-label', 'Cari layanan');

    document.querySelectorAll('.layanan-page .category-tabs a').forEach((link) => {
        link.addEventListener('click', () => {
            // Filter kategori diproses Laravel melalui query string.
        });
    });
});
