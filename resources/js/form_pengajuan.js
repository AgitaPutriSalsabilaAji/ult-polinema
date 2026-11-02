document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('.pengajuan-form');
    if (!form) return;

    const modal = document.getElementById('confirmModal');
    const cancelBtn = document.getElementById('cancelConfirmBtn');
    const confirmBtn = document.getElementById('confirmSubmitBtn');
    const fileInput = document.getElementById('dokumen');
    const selectedFileList = document.getElementById('selectedFileList');
    const uploadZone = document.querySelector('.upload-zone');
    const fileValidationMessage = document.getElementById('documentValidationMessage');
    const maxBytes = 5 * 1024 * 1024;
    const allowedMimeTypes = {
        pdf: ['application/pdf'],
        jpg: ['image/jpeg'],
        jpeg: ['image/jpeg'],
        png: ['image/png'],
    };
    const temporaryPreviewUrls = new Set();

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

    const setFileValidationError = (message = '') => {
        if (!fileValidationMessage) return;
        fileValidationMessage.textContent = message;
        fileValidationMessage.hidden = message === '';
    };

    const fileValidationError = (file) => {
        if (file.size > maxBytes) {
            return 'Ukuran file maksimal 5 MB.';
        }
        const extension = file.name.split('.').pop()?.toLowerCase() || '';
        const supportedMimeTypes = allowedMimeTypes[extension];

        if (!supportedMimeTypes || (file.type && !supportedMimeTypes.includes(file.type.toLowerCase()))) {
            return 'Format file tidak didukung. Silakan upload PDF, JPG, JPEG, atau PNG.';
        }

        return '';
    };

    const openPreview = (file) => {
        const validationError = fileValidationError(file);
        if (validationError) {
            setFileValidationError(validationError);
            return;
        }

        const objectUrl = URL.createObjectURL(file);
        const previewWindow = window.open('', '_blank');

        if (!previewWindow) {
            URL.revokeObjectURL(objectUrl);
            setFileValidationError('Tab preview diblokir oleh browser. Izinkan pop-up untuk melihat dokumen.');
            return;
        }

        previewWindow.opener = null;
        temporaryPreviewUrls.add(objectUrl);
        const extension = file.name.split('.').pop()?.toLowerCase();

        if (extension === 'pdf') {
            previewWindow.location.href = objectUrl;
            return;
        }

        const previewDocument = previewWindow.document;
        previewDocument.title = file.name;

        const style = previewDocument.createElement('style');
        style.textContent = 'body{display:flex;min-height:100vh;flex-direction:column;margin:0;background:#f3f3f5;color:#1f2230;font-family:Inter,Arial,sans-serif}header,footer{display:flex;min-height:64px;align-items:center;justify-content:space-between;padding:12px 24px;background:#fff}header{border-bottom:1px solid #e5e7eb}footer{justify-content:flex-end;border-top:1px solid #e5e7eb}h1{overflow-wrap:anywhere;margin:0;color:#302474;font-size:15px}main{display:grid;min-height:0;flex:1;place-items:center;padding:24px}img{display:block;width:auto;height:auto;max-width:100%;max-height:calc(100vh - 160px);object-fit:contain}a{display:inline-flex;min-height:40px;align-items:center;justify-content:center;padding:9px 16px;border-radius:9px;background:#302474;color:#fff;font-size:13px;font-weight:700;text-decoration:none}@media(max-width:600px){header,footer{padding-right:16px;padding-left:16px}main{padding:16px}}';
        previewDocument.head.append(style);

        const header = previewDocument.createElement('header');
        const title = previewDocument.createElement('h1');
        title.textContent = file.name;
        header.append(title);

        const main = previewDocument.createElement('main');
        const image = previewDocument.createElement('img');
        image.src = objectUrl;
        image.alt = file.name;
        main.append(image);

        const footer = previewDocument.createElement('footer');
        const download = previewDocument.createElement('a');
        download.href = objectUrl;
        download.download = file.name;
        download.textContent = 'Unduh Gambar';
        footer.append(download);

        previewDocument.body.replaceChildren(header, main, footer);
    };

    const formatFileSize = (size) => {
        if (size < 1024) return `${size} B`;
        if (size < 1024 * 1024) return `${(size / 1024).toFixed(1)} KB`;
        return `${(size / (1024 * 1024)).toFixed(2)} MB`;
    };

    const renderSelectedFiles = () => {
        if (!selectedFileList || !fileInput) return;

        const files = Array.from(fileInput.files || []);
        selectedFileList.replaceChildren();

        files.forEach((file) => {
            const item = document.createElement('div');
            item.className = 'file-item';

            const icon = document.createElement('span');
            icon.className = 'file-item__icon';
            icon.setAttribute('aria-hidden', 'true');
            icon.textContent = '📄';

            const name = document.createElement('span');
            name.className = 'file-item__name';
            name.textContent = file.name;
            name.title = file.name;

            const size = document.createElement('span');
            size.className = 'file-item__size';
            size.textContent = formatFileSize(file.size);

            const previewButton = document.createElement('button');
            previewButton.type = 'button';
            previewButton.className = 'file-item__preview';
            previewButton.textContent = 'Lihat Dokumen';
            previewButton.addEventListener('click', () => openPreview(file));

            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.className = 'file-item__remove';
            removeButton.textContent = 'Hapus';
            removeButton.setAttribute('aria-label', `Hapus ${file.name}`);
            removeButton.addEventListener('click', () => {
                fileInput.value = '';
                setFileValidationError();
                renderSelectedFiles();
            });

            item.append(icon, name, size, previewButton, removeButton);
            selectedFileList.append(item);
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

    window.addEventListener('beforeunload', () => {
        temporaryPreviewUrls.forEach((url) => URL.revokeObjectURL(url));
    });

    if (fileInput) {
        fileInput.addEventListener('change', () => {
            const file = fileInput.files && fileInput.files[0];
            fileInput.classList.remove('is-invalid');

            const validationError = file ? fileValidationError(file) : '';

            if (validationError) {
                fileInput.classList.add('is-invalid');
                fileInput.value = '';
                setFileValidationError(validationError);
                renderSelectedFiles();
                return;
            }

            setFileValidationError();
            renderSelectedFiles();
        });
    }

    if (uploadZone && fileInput) {
        uploadZone.addEventListener('dragover', (event) => {
            event.preventDefault();
            uploadZone.classList.add('is-dragging');
        });

        uploadZone.addEventListener('dragleave', () => {
            uploadZone.classList.remove('is-dragging');
        });

        uploadZone.addEventListener('drop', (event) => {
            event.preventDefault();
            uploadZone.classList.remove('is-dragging');

            const [file] = Array.from(event.dataTransfer?.files || []);
            if (!file) return;

            const transfer = new DataTransfer();
            transfer.items.add(file);
            fileInput.files = transfer.files;
            fileInput.dispatchEvent(new Event('change', { bubbles: true }));
        });
    }

    form.addEventListener('submit', (event) => {
        event.preventDefault();

        if (fileInput?.files?.[0]) {
            const validationError = fileValidationError(fileInput.files[0]);

            if (validationError) {
                fileInput.focus();
                fileInput.classList.add('is-invalid');
                setFileValidationError(validationError);
                return;
            }
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
