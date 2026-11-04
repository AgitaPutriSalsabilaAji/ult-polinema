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

});
