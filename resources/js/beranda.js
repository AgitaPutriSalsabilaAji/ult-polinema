const normalizeText = (value) => value
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .toLocaleLowerCase('id-ID');

export function initializeServiceSearch({
    form,
    input,
    dropdown,
    emptyMessage,
    optionSelector = '[data-service-option]',
    searchText = (option) => option.dataset.name || option.textContent,
    onEmptySubmit = null,
}) {
    if (!form || !input || !dropdown || !emptyMessage) return;

    const options = Array.from(dropdown.querySelectorAll(optionSelector));
    let activeOptionIndex = -1;

    const matchingOptions = () => options.filter((option) => !option.hidden && !option.classList.contains('d-none'));

    const closeDropdown = () => {
        dropdown.classList.add('d-none');
        input.setAttribute('aria-expanded', 'false');
        input.removeAttribute('aria-activedescendant');
        activeOptionIndex = -1;
        options.forEach((option) => option.setAttribute('aria-selected', 'false'));
    };

    const updateDropdown = () => {
        const query = normalizeText(input.value.trim());

        if (!query) {
            options.forEach((option) => {
                option.hidden = false;
                option.classList.remove('d-none');
            });
            emptyMessage.classList.add('d-none');
            closeDropdown();
            return [];
        }

        const matches = [];
        options.forEach((option) => {
            const isMatch = normalizeText(searchText(option).trim()).includes(query);
            option.hidden = !isMatch;
            option.classList.toggle('d-none', !isMatch);
            option.setAttribute('aria-selected', 'false');
            if (isMatch) matches.push(option);
        });

        emptyMessage.classList.toggle('d-none', matches.length > 0);
        dropdown.classList.remove('d-none');
        input.setAttribute('aria-expanded', 'true');
        input.removeAttribute('aria-activedescendant');
        activeOptionIndex = -1;

        return matches;
    };

    const setActiveOption = (index) => {
        const visibleOptions = matchingOptions();
        if (visibleOptions.length === 0) return;

        activeOptionIndex = (index + visibleOptions.length) % visibleOptions.length;
        options.forEach((option) => {
            option.setAttribute('aria-selected', String(option === visibleOptions[activeOptionIndex]));
        });

        const activeOption = visibleOptions[activeOptionIndex];
        if (!activeOption.id) {
            activeOption.id = `service-option-${activeOptionIndex}`;
        }
        input.setAttribute('aria-activedescendant', activeOption.id);
        activeOption.scrollIntoView({ block: 'nearest' });
    };

    input.addEventListener('input', updateDropdown);
    input.addEventListener('focus', updateDropdown);
    input.addEventListener('keydown', (event) => {
        const visibleOptions = matchingOptions();

        if (event.key === 'Escape') {
            closeDropdown();
        } else if (event.key === 'ArrowDown' && !dropdown.classList.contains('d-none')) {
            event.preventDefault();
            setActiveOption(activeOptionIndex + 1);
        } else if (event.key === 'ArrowUp' && !dropdown.classList.contains('d-none')) {
            event.preventDefault();
            setActiveOption(activeOptionIndex < 0 ? visibleOptions.length - 1 : activeOptionIndex - 1);
        }
    });

    dropdown.addEventListener('pointerover', (event) => {
        const option = event.target.closest(optionSelector);
        if (!option || option.hidden || option.classList.contains('d-none')) return;

        setActiveOption(matchingOptions().indexOf(option));
    });

    dropdown.addEventListener('click', (event) => {
        if (event.target.closest(optionSelector)) closeDropdown();
    });

    form.addEventListener('submit', (event) => {
        const selectedIndex = activeOptionIndex;
        const matches = updateDropdown();
        if (matches.length > 0) {
            event.preventDefault();
            const selectedOption = selectedIndex < 0
                ? matches[0]
                : matches[selectedIndex];
            window.location.assign(selectedOption.href);
            return;
        }

        onEmptySubmit?.(event, input.value.trim());
    });

    document.addEventListener('click', (event) => {
        if (!form.contains(event.target) && !dropdown.contains(event.target)) {
            closeDropdown();
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const searchForm = document.getElementById('serviceSearchForm');
    const searchInput = document.getElementById('serviceSearch');
    const searchResults = document.getElementById('serviceSearchResults');
    const searchEmpty = searchResults?.querySelector('.service-search-empty');

    if (searchForm && searchInput && searchResults) {
        initializeServiceSearch({
            form: searchForm,
            input: searchInput,
            dropdown: searchResults,
            emptyMessage: searchEmpty,
            searchText: (option) => option.textContent,
            onEmptySubmit: (event, query) => {
                event.preventDefault();
                if (!query) {
                    document.getElementById('layanan')?.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start',
                    });
                }
            },
        });
    }

    // Form cek status dikirim ke route Laravel /cek-status.
    // Route tersebut akan mencari nomor e-ticket pada database dan menampilkan hasilnya.

    // Tutup menu navigasi mobile setelah tautan dipilih
    document.querySelectorAll('#mainNav .nav-link').forEach((link) => {
        link.addEventListener('click', () => {
            const nav = document.getElementById('mainNav');

            if (
                nav &&
                nav.classList.contains('show') &&
                window.bootstrap
            ) {
                window.bootstrap.Collapse
                    .getOrCreateInstance(nav)
                    .hide();
            }
        });
    });
});