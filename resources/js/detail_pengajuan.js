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

    const ratingForm = document.querySelector('.dp-rating-form');

    if (ratingForm) {
        const starOptions = Array.from(ratingForm.querySelectorAll('.dp-star-option'));
        const updateStarVisualState = (activeValue) => {
            starOptions.forEach((starOption) => {
                const starValue = Number(starOption.dataset.value || 0);
                starOption.classList.toggle('is-active', starValue <= activeValue);
            });
        };

        const getSelectedRating = () => Number(ratingForm.querySelector('input[name="rating"]:checked')?.value || 0);

        const resetStarsToSelection = () => {
            updateStarVisualState(getSelectedRating());
        };

        starOptions.forEach((starOption) => {
            const input = starOption.querySelector('input');
            const starValue = Number(starOption.dataset.value || 0);

            starOption.addEventListener('mouseenter', () => {
                updateStarVisualState(starValue);
            });

            starOption.addEventListener('mouseleave', () => {
                resetStarsToSelection();
            });

            starOption.addEventListener('focusin', () => {
                updateStarVisualState(starValue);
            });

            starOption.addEventListener('focusout', () => {
                resetStarsToSelection();
            });

            input?.addEventListener('change', () => {
                updateStarVisualState(starValue);
            });
        });

        resetStarsToSelection();
    }

    const copyButton = document.querySelector('[data-copy-ticket]');
    const copyFeedback = document.querySelector('[data-copy-feedback]');

    if (copyButton && copyFeedback) {
        copyButton.addEventListener('click', async () => {
            const ticketNumber = copyButton.dataset.copyTicket;

            try {
                await navigator.clipboard.writeText(ticketNumber);
                copyFeedback.textContent = 'Nomor E-Ticket berhasil disalin.';
            } catch {
                const temporaryInput = document.createElement('textarea');
                temporaryInput.value = ticketNumber;
                temporaryInput.setAttribute('readonly', '');
                temporaryInput.style.position = 'fixed';
                temporaryInput.style.opacity = '0';
                document.body.append(temporaryInput);
                temporaryInput.select();
                const copied = document.execCommand('copy');
                temporaryInput.remove();
                copyFeedback.textContent = copied
                    ? 'Nomor E-Ticket berhasil disalin.'
                    : 'Nomor E-Ticket tidak dapat disalin.';
            }
        });
    }

    const fileInput = document.querySelector('#dp-message-file');
    const fileName = document.querySelector('[data-file-name]');

    if (fileInput && fileName) {
        fileInput.addEventListener('change', () => {
            fileName.textContent = fileInput.files?.[0]?.name
                ?? 'PDF, JPG, PNG, DOC, DOCX; maks. 5 MB.';
        });
    }

    const previewButtons = document.querySelectorAll('.dp-message-attachment-toggle');

    previewButtons.forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault();

            const previewTargetId = button.dataset.previewTarget;
            const previewContainer = previewTargetId ? document.getElementById(previewTargetId) : null;
            const previewBody = previewContainer?.querySelector('[data-preview-body]');
            const attachmentUrl = button.dataset.attachmentUrl;
            const attachmentType = button.dataset.attachmentType || 'file';
            const attachmentName = button.dataset.attachmentName || 'Lampiran';

            if (! previewContainer || ! previewBody || ! attachmentUrl) {
                if (previewBody) {
                    previewBody.innerHTML = '<div class="dp-message-preview__message">Lampiran tidak dapat ditampilkan pada halaman ini.</div>';
                }
                previewContainer?.removeAttribute('hidden');
                return;
            }

            const renderPreviewError = (message) => {
                previewBody.innerHTML = '<div class="dp-message-preview__message">'+message+'</div>';
                previewContainer.removeAttribute('hidden');
            };

            previewBody.innerHTML = '';
            previewContainer.removeAttribute('hidden');

            if (attachmentType === 'image') {
                const img = document.createElement('img');
                img.src = attachmentUrl;
                img.alt = attachmentName;
                img.loading = 'lazy';
                img.addEventListener('error', () => renderPreviewError('Dokumen tidak dapat ditampilkan di halaman ini.'));
                previewBody.appendChild(img);
                return;
            }

            if (attachmentType === 'doc') {
                const viewerUrl = 'https://docs.google.com/gview?embedded=true&url=' + encodeURIComponent(attachmentUrl);
                const viewer = document.createElement('iframe');
                viewer.src = viewerUrl;
                viewer.title = 'Preview dokumen lampiran';
                viewer.setAttribute('allowfullscreen', 'true');
                viewer.addEventListener('error', () => renderPreviewError('Dokumen tidak dapat ditampilkan di halaman ini.'));
                previewBody.appendChild(viewer);
                return;
            }

            const iframe = document.createElement('iframe');
            iframe.src = attachmentUrl;
            iframe.title = 'Preview dokumen lampiran';
            iframe.setAttribute('allowfullscreen', 'true');
            iframe.addEventListener('error', () => renderPreviewError('Dokumen tidak dapat ditampilkan di halaman ini.'));
            previewBody.appendChild(iframe);
        });
    });

    const closePreviewButtons = document.querySelectorAll('[data-close-preview]');

    closePreviewButtons.forEach((closeButton) => {
        closeButton.addEventListener('click', () => {
            const targetId = closeButton.dataset.closePreview;
            const preview = targetId ? document.getElementById(targetId) : null;
            if (preview) {
                preview.setAttribute('hidden', 'hidden');
                const body = preview.querySelector('[data-preview-body]');
                if (body) {
                    body.innerHTML = '';
                }
            }
        });
    });
});
