document.addEventListener('DOMContentLoaded', () => {
    const statusBadges = document.querySelectorAll('.status-status');
    statusBadges.forEach((el) => {
        const text = el.textContent.trim().toLowerCase();
        if (text.includes('diajukan')) {
            el.classList.add('status-diajukan');
        } else if (text.includes('disposisi') || text.includes('proses')) {
            el.classList.add('status-didisposisikan');
        } else if (text.includes('selesai')) {
            el.classList.add('status-selesai');
        } else if (text.includes('tolak')) {
            el.classList.add('status-ditolak');
        }
    });
});
