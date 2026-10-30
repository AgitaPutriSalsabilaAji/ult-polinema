document.querySelectorAll('[data-password-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
        const input = document.getElementById(button.dataset.passwordToggle);

        if (!input) {
            return;
        }

        const visible = input.type === 'password';

        input.type = visible ? 'text' : 'password';
        button.textContent = visible ? 'Sembunyikan' : 'Tampilkan';
        button.setAttribute('aria-label', visible ? 'Sembunyikan password' : 'Tampilkan password');
    });
});

document.querySelectorAll('.auth-modal').forEach((modal) => {
    modal.addEventListener('show.bs.modal', (event) => {
        const serviceId = event.relatedTarget?.dataset.serviceId;

        if (!serviceId) {
            return;
        }

        document.querySelectorAll('.auth-modal [name="layanan_id"]').forEach((input) => {
            input.value = serviceId;
        });
    });
});

document.querySelectorAll('[data-auth-modal-switch]').forEach((link) => {
    link.addEventListener('click', (event) => {
        event.preventDefault();

        const sourceModal = link.closest('.modal');
        const targetModal = document.getElementById(`${link.dataset.authModalSwitch}Modal`);

        if (!sourceModal || !targetModal || !window.bootstrap) {
            return;
        }

        const serviceId = sourceModal.querySelector('[name="layanan_id"]')?.value;
        const targetServiceInput = targetModal.querySelector('[name="layanan_id"]');

        if (serviceId && targetServiceInput) {
            targetServiceInput.value = serviceId;
        }

        sourceModal.addEventListener('hidden.bs.modal', () => {
            window.bootstrap.Modal.getOrCreateInstance(targetModal).show();
        }, { once: true });

        window.bootstrap.Modal.getOrCreateInstance(sourceModal).hide();
    });
});

const autoOpenModal = document.querySelector('[data-auth-modal-autostart]');

if (autoOpenModal && window.bootstrap) {
    window.bootstrap.Modal.getOrCreateInstance(autoOpenModal).show();
}
