document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('#statusNav .nav-link').forEach((link) => {
        link.addEventListener('click', () => {
            const nav = document.getElementById('statusNav');
            if (nav && nav.classList.contains('show') && window.bootstrap) {
                window.bootstrap.Collapse.getOrCreateInstance(nav).hide();
            }
        });
    });
});