document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('.pengajuan-form');
    if (!form) return;

    const modal = document.getElementById('confirmModal');
    const cancelBtn = document.getElementById('cancelConfirmBtn');
    const confirmBtn = document.getElementById('confirmSubmitBtn');
    const fileInput = document.getElementById('dokumen');
    const selectedFileList = document.getElementById('selectedFileList');
    const maxBytes = 5 * 1024 * 1024;

    const closeModal = () => {
        if (!modal) return;
        modal.classList.remove('is-visible');
        modal.setAttribute('aria-hidden', 'true');
    };

    const openModal = () => {
        if (!modal) return;
        modal.classList.add('is-visible');
        modal.setAttribute('aria-hidden', 'false');
    };

    const formatFileSize = (size) => {
        if (size < 1024) return `${size} B`;
        if (size < 1024 * 1024) return `${(size / 1024).toFixed(1)} KB`;
        return `${(size / (1024 * 1024)).toFixed(2)} MB`;
    };

    const renderSelectedFiles = () => {
        if (!selectedFileList || !fileInput) return;

        const files = Array.from(fileInput.files || []);
        if (files.length === 0) {
            selectedFileList.innerHTML = '';
            return;
        }

        selectedFileList.innerHTML = files.map((file) => `
            <div class="file-item">
                <span class="file-item__icon" aria-hidden="true">📄</span>
                <span class="file-item__name">${file.name}</span>
                <span class="file-item__size">${formatFileSize(file.size)}</span>
                <button type="button" class="file-item__remove" aria-label="Hapus file">Hapus</button>
            </div>
        `).join('');

        selectedFileList.querySelectorAll('.file-item__remove').forEach((button) => {
            button.addEventListener('click', () => {
                fileInput.value = '';
                renderSelectedFiles();
            });
        });
    };

    const validateRequiredFields = () => {
        const requiredFields = form.querySelectorAll('input[required], select[required], textarea[required]');
        let hasEmpty = false;

        requiredFields.forEach((field) => {
            if (field.type === 'checkbox') {
                if (!field.checked) {
                    hasEmpty = true;
                    field.closest('.statement-box')?.classList.add('is-invalid');
                } else {
                    field.closest('.statement-box')?.classList.remove('is-invalid');
                }
                return;
            }

            if (!field.value.trim()) {
                hasEmpty = true;
                field.classList.add('is-invalid');
            } else {
                field.classList.remove('is-invalid');
            }
        });

        return !hasEmpty;
    };

    if (cancelBtn) {
        cancelBtn.addEventListener('click', closeModal);
    }

    if (modal) {
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal();
            }
        });
    }

    if (fileInput) {
        fileInput.addEventListener('change', () => {
            const file = fileInput.files && fileInput.files[0];
            fileInput.classList.remove('is-invalid');

            if (file && file.size > maxBytes) {
                fileInput.classList.add('is-invalid');
                alert('Ukuran file dokumen melebihi 5 MB. Silakan pilih file dengan ukuran maksimal 5 MB.');
                fileInput.value = '';
                renderSelectedFiles();
                return;
            }

            renderSelectedFiles();
        });
    }

    form.addEventListener('submit', (event) => {
        event.preventDefault();

        if (fileInput && fileInput.files && fileInput.files[0] && fileInput.files[0].size > maxBytes) {
            fileInput.focus();
            fileInput.classList.add('is-invalid');
            return;
        }

        const isValid = validateRequiredFields();

        if (!isValid) {
            const firstInvalid = form.querySelector('.is-invalid, .statement-box.is-invalid');
            if (firstInvalid) {
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                const focusTarget = firstInvalid.querySelector('input, select, textarea') || firstInvalid;
                focusTarget.focus?.();
            }
            return;
        }

        openModal();
    });

    if (confirmBtn) {
        confirmBtn.addEventListener('click', () => {
            closeModal();
            form.submit();
        });
    }
});
