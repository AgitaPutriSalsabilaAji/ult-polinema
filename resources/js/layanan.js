/* resources/js/layanan.js */
document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('.layanan-page');
    if (!page) return;

    const searchForm = page.querySelector('.search-form');
    const searchInput = searchForm ? searchForm.querySelector('input[name="q"]') : null;

    if (searchInput) {
        searchInput.setAttribute('aria-label', 'Cari layanan');
    }

    page.querySelectorAll('.category-tabs a').forEach((link) => {
        link.addEventListener('click', (event) => {
            const href = link.getAttribute('href');
            if (!href) return;

            const targetUrl = new URL(href, window.location.href);
            targetUrl.searchParams.delete('q');

            if (searchInput) {
                searchInput.value = '';
            }

            event.preventDefault();
            window.location.assign(targetUrl.toString());
        });
    });
});
