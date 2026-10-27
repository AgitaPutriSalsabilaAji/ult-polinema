import '../css/cek-status.css';

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('cekStatusForm');
    const ticketInput = document.getElementById('ticket');

    if (form && ticketInput) {
        ticketInput.addEventListener('input', () => {
            ticketInput.value = ticketInput.value.toUpperCase().replace(/\s+/g, '');
        });

        form.addEventListener('submit', (event) => {
            const ticket = ticketInput.value.trim();
            if (!ticket) {
                event.preventDefault();
                ticketInput.focus();
                return;
            }
            ticketInput.value = ticket.toUpperCase();
        });
    }

    document.querySelectorAll('#statusNav .nav-link').forEach((link) => {
        link.addEventListener('click', () => {
            const nav = document.getElementById('statusNav');
            if (nav && nav.classList.contains('show') && window.bootstrap) {
                window.bootstrap.Collapse.getOrCreateInstance(nav).hide();
            }
        });
    });
});