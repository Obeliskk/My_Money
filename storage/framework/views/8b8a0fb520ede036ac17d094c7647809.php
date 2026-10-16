<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($title ?? 'GastosLocal'); ?></title>
    <script>
        (() => {
            const saved = localStorage.getItem('gastoslocal-theme');
            if (saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.dataset.theme = 'dark';
            }
        })();
    </script>
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
</head>
<body>
    <main class="shell">
        <header class="topbar">
            <div>
                <p class="eyebrow">CONTROL PERSONAL</p>
                <h1>GastosLocal</h1>
            </div>
            <div class="topbar-actions">
                <button id="theme-toggle" class="theme-toggle" type="button" aria-label="Activar modo noche" aria-pressed="false">
                    <span class="theme-icon">☾</span><span class="theme-label">Modo noche</span>
                </button>
                <span class="local-pill">● Solo en tu PC</span>
            </div>
        </header>
        <?php if(session('status')): ?>
            <div class="notice"><?php echo e(session('status')); ?></div>
        <?php endif; ?>
        <?php if(session('error')): ?>
            <div class="notice error"><?php echo e(session('error')); ?></div>
        <?php endif; ?>
        <?php if($errors->any()): ?>
            <div class="notice error">Revisa los datos del formulario e inténtalo de nuevo.</div>
        <?php endif; ?>
        <?php echo e($slot); ?>

    </main>
    <script>
        (() => {
            const root = document.documentElement;
            const button = document.getElementById('theme-toggle');
            if (!button) return;
            const setTheme = (dark, persist = true) => {
                root.dataset.theme = dark ? 'dark' : 'light';
                button.setAttribute('aria-pressed', dark ? 'true' : 'false');
                button.setAttribute('aria-label', dark ? 'Activar modo claro' : 'Activar modo noche');
                button.innerHTML = dark
                    ? '<span class="theme-icon">☀</span><span class="theme-label">Modo claro</span>'
                    : '<span class="theme-icon">☾</span><span class="theme-label">Modo noche</span>';
                if (persist) localStorage.setItem('gastoslocal-theme', dark ? 'dark' : 'light');
            };
            setTheme(root.dataset.theme === 'dark', false);
            button.addEventListener('click', () => setTheme(root.dataset.theme !== 'dark'));
        })();

        (() => {
            const pad = (value) => String(value).padStart(2, '0');
            const toIsoDate = (value) => {
                const match = value.trim().match(/^(\d{1,2})[\/-](\d{1,2})[\/-](\d{4})$/);
                if (!match) return '';
                const day = Number(match[1]);
                const month = Number(match[2]);
                const year = Number(match[3]);
                const date = new Date(Date.UTC(year, month - 1, day));
                if (date.getUTCFullYear() !== year || date.getUTCMonth() !== month - 1 || date.getUTCDate() !== day) return '';
                return `${year}-${pad(month)}-${pad(day)}`;
            };

            document.querySelectorAll('[data-date-display]').forEach((display) => {
                const form = display.closest('form');
                const control = display.closest('[data-date-display]')?.parentElement;
                const hidden = control?.querySelector('[data-date-value]');
                const native = control?.querySelector('[data-date-native]');
                if (!hidden) return;
                const fromIso = (iso) => {
                    const match = (iso || '').match(/^(\d{4})-(\d{2})-(\d{2})$/);
                    return match ? `${match[3]}/${match[2]}/${match[1]}` : '';
                };
                const sync = () => {
                    const value = display.value.trim();
                    const iso = value ? toIsoDate(value) : '';
                    hidden.value = iso;
                    if (native && native.value !== iso) native.value = iso;
                    display.setCustomValidity(value && !iso ? 'Usa el formato dd/mm/aaaa.' : '');
                    return Boolean(iso || !value);
                };
                const syncFromNative = () => {
                    hidden.value = native?.value || '';
                    display.value = fromIso(native?.value || '');
                    display.setCustomValidity('');
                    if (display.dataset.submitForm === 'true' && native?.value) form?.requestSubmit();
                };
                control?.querySelector('[data-date-trigger]')?.addEventListener('click', () => {
                    try {
                        native?.showPicker();
                    } catch {
                        native?.focus();
                    }
                });
                display.addEventListener('click', () => {
                    try {
                        native?.showPicker();
                    } catch {
                        native?.focus();
                    }
                });
                native?.addEventListener('change', syncFromNative);
                display.addEventListener('input', sync);
                display.addEventListener('blur', () => {
                    if (sync() && display.dataset.submitForm === 'true') form?.requestSubmit();
                });
                form?.addEventListener('submit', sync);
            });
        })();

        (() => {
            const monthNames = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
            const formatMonth = (value) => {
                const [year, month] = value.split('-').map(Number);
                return `${monthNames[month - 1]} de ${year}`;
            };
            const currentMonth = () => {
                const now = new Date();
                return `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;
            };

            document.querySelectorAll('[data-month-picker]').forEach((picker) => {
                const form = picker.closest('form');
                const display = picker.querySelector('.month-input');
                const hidden = picker.querySelector('[data-month-hidden]');
                const popover = picker.querySelector('[data-month-popover]');
                const yearLabel = picker.querySelector('[data-month-year]');
                const options = [...picker.querySelectorAll('[data-month-index]')];
                if (!form || !display || !hidden || !popover || !yearLabel) return;

                let visibleYear = Number(hidden.value.slice(0, 4));
                const render = () => {
                    const selected = hidden.value;
                    yearLabel.textContent = visibleYear;
                    options.forEach((option) => {
                        const index = Number(option.dataset.monthIndex);
                        const value = `${visibleYear}-${String(index + 1).padStart(2, '0')}`;
                        option.dataset.month = value;
                        option.classList.toggle('active', value === selected);
                        option.setAttribute('aria-selected', value === selected ? 'true' : 'false');
                    });
                };
                const close = () => {
                    popover.hidden = true;
                    display.setAttribute('aria-expanded', 'false');
                };
                const open = () => {
                    visibleYear = Number((hidden.value || currentMonth()).slice(0, 4));
                    render();
                    popover.hidden = false;
                    display.setAttribute('aria-expanded', 'true');
                };
                const selectMonth = (value, submit = true) => {
                    hidden.value = value;
                    display.value = value ? formatMonth(value) : 'Seleccionar mes';
                    visibleYear = Number((value || currentMonth()).slice(0, 4));
                    render();
                    close();
                    if (submit) form.requestSubmit();
                };

                display.addEventListener('click', () => popover.hidden ? open() : close());
                display.addEventListener('keydown', (event) => {
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        popover.hidden ? open() : close();
                    }
                });
                picker.querySelector('[data-month-prev]')?.addEventListener('click', () => { visibleYear -= 1; render(); });
                picker.querySelector('[data-month-next]')?.addEventListener('click', () => { visibleYear += 1; render(); });
                options.forEach((option) => option.addEventListener('click', () => selectMonth(option.dataset.month)));
                picker.querySelector('[data-month-clear]')?.addEventListener('click', () => selectMonth(''));
                picker.querySelector('[data-month-today]')?.addEventListener('click', () => selectMonth(currentMonth()));
                document.addEventListener('click', (event) => {
                    if (!picker.contains(event.target)) close();
                });
                render();
            });
        })();
    </script>
</body>
</html>
<?php /**PATH C:\Users\diego\Documents\Codex\2026-09-27\qu\resources\views/components/layout.blade.php ENDPATH**/ ?>