document.addEventListener('DOMContentLoaded', () => {
    /* ======================================================
       TEMA: claro / oscuro
       Se conserva la identidad púrpura de ProgressICFES y solo
       cambia la superficie para mejorar la lectura nocturna.
       ====================================================== */
    const themeToggle = document.getElementById('theme-toggle');
    const root = document.documentElement;
    const savedTheme = localStorage.getItem('progressicfes-theme');

    const applyTheme = (theme) => {
        const dark = theme === 'dark';
        root.setAttribute('data-theme', dark ? 'dark' : 'light');
        if (themeToggle) {
            themeToggle.textContent = dark ? '☾' : '☼';
            themeToggle.setAttribute('aria-pressed', String(dark));
            themeToggle.setAttribute('aria-label', dark ? 'Activar modo claro' : 'Activar modo oscuro');
            themeToggle.setAttribute('title', dark ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro');
        }
    };

    applyTheme(savedTheme === 'dark' ? 'dark' : 'light');

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const nextTheme = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            localStorage.setItem('progressicfes-theme', nextTheme);
            applyTheme(nextTheme);
        });
    }

    const sidebar = document.querySelector('#sidebar');
    const sidebarToggles = document.querySelectorAll('[data-sidebar-toggle]');

    // Estado persistente del menú lateral en escritorio.
    if (sidebar && window.innerWidth > 820 && localStorage.getItem('progressicfes-sidebar-collapsed') === '1') {
        sidebar.classList.add('is-collapsed');
        sidebarToggles.forEach((toggle) => toggle.setAttribute('aria-expanded', 'false'));
    }

    // Overlay móvil para cerrar el menú sin perder contexto.
    let sidebarOverlay = document.querySelector('.sidebar-overlay');
    if (sidebar && !sidebarOverlay) {
        sidebarOverlay = document.createElement('div');
        sidebarOverlay.className = 'sidebar-overlay';
        sidebarOverlay.setAttribute('aria-hidden', 'true');
        document.body.appendChild(sidebarOverlay);
        sidebarOverlay.addEventListener('click', () => {
            sidebar.classList.remove('is-open');
            sidebarOverlay.classList.remove('is-visible');
            sidebarToggles.forEach((toggle) => toggle.setAttribute('aria-expanded', 'false'));
        });
    }
    const questionOptions = document.querySelectorAll('[data-question-count]');

    sidebarToggles.forEach((toggle) => {
        toggle.addEventListener('click', () => {
            if (!sidebar) return;

            if (window.innerWidth <= 820) {
                sidebar.classList.toggle('is-open');
                const open = sidebar.classList.contains('is-open');
                toggle.setAttribute('aria-expanded', String(open));
                if (sidebarOverlay) sidebarOverlay.classList.toggle('is-visible', open);
                return;
            }

            sidebar.classList.toggle('is-collapsed');
            const expanded = !sidebar.classList.contains('is-collapsed');
            toggle.setAttribute('aria-expanded', String(expanded));
            localStorage.setItem('progressicfes-sidebar-collapsed', expanded ? '0' : '1');
        });
    });

    questionOptions.forEach((option) => {
        option.addEventListener('click', () => {
            const group = option.closest('[data-quantity-input]');
            const siblings = group ? group.querySelectorAll('[data-question-count]') : questionOptions;

            siblings.forEach((item) => item.classList.remove('is-selected'));
            option.classList.add('is-selected');

            if (group) {
                const hiddenInput = document.getElementById(group.getAttribute('data-quantity-input'));
                if (hiddenInput) hiddenInput.value = option.getAttribute('data-question-count');
            }
        });
    });

    const materiaSelect = document.getElementById('materia_id');
    const temaSelect = document.getElementById('tema_id');

    if (materiaSelect && temaSelect) {
        materiaSelect.addEventListener('change', () => {
            const temas = (window.temasPorMateria && window.temasPorMateria[materiaSelect.value]) || [];
            temaSelect.innerHTML = '';

            const opcionTodos = document.createElement('option');
            opcionTodos.value = '';
            opcionTodos.textContent = temas.length ? 'Todos los temas de la materia' : 'Esta materia aún no tiene temas cargados';
            temaSelect.appendChild(opcionTodos);

            temas.forEach((tema) => {
                const opcion = document.createElement('option');
                opcion.value = tema.id;
                opcion.textContent = tema.nombre;
                temaSelect.appendChild(opcion);
            });

            temaSelect.disabled = temas.length === 0;
        });
    }

    const timerElement = document.querySelector('[data-tiempo-limite-min]');

    if (timerElement) {
        const minutosLimite = parseInt(timerElement.getAttribute('data-tiempo-limite-min'), 10) || 0;
        let segundosRestantes = minutosLimite * 60;
        const display = timerElement.querySelector('[data-timer-display]');
        const formulario = document.getElementById('simulacro-form');

        const actualizarDisplay = () => {
            if (!display) return;
            const mins = Math.floor(segundosRestantes / 60);
            const secs = segundosRestantes % 60;
            display.textContent = String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
            if (segundosRestantes <= 60) timerElement.classList.add('is-warning');
        };

        actualizarDisplay();

        const intervalo = setInterval(() => {
            segundosRestantes -= 1;
            if (segundosRestantes <= 0) {
                clearInterval(intervalo);
                segundosRestantes = 0;
                actualizarDisplay();
                if (formulario) {
                    if (typeof formulario.__setLoading === 'function') formulario.__setLoading();
                    formulario.submit();
                }
                return;
            }
            actualizarDisplay();
        }, 1000);
    }

    /* ======================================================
       COPIAR PROMPT PARA IA (resultado de práctica/simulacro)
       ====================================================== */
    document.querySelectorAll('[data-copy-prompt]').forEach((boton) => {
        boton.addEventListener('click', () => {
            const caja = boton.closest('.prompt-ia-card');
            const textarea = caja ? caja.querySelector('[data-prompt-ia]') : null;
            if (!textarea) return;

            const feedback = caja.querySelector('[data-copy-feedback]');
            const textoOriginal = boton.textContent;

            const mostrarExito = () => {
                boton.textContent = '¡Copiado!';
                if (feedback) feedback.hidden = false;
                setTimeout(() => {
                    boton.textContent = textoOriginal;
                    if (feedback) feedback.hidden = true;
                }, 2000);
            };

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(textarea.value).then(mostrarExito).catch(() => {
                    textarea.select();
                    document.execCommand('copy');
                    mostrarExito();
                });
            } else {
                textarea.select();
                document.execCommand('copy');
                mostrarExito();
            }
        });
    });

    /* ======================================================
       LOADING EN EL BOTÓN AL TERMINAR PRÁCTICA / SIMULACRO
       Evita doble envío y muestra que se está calificando.
       ====================================================== */
    document.querySelectorAll('.practica-quiz-form').forEach((form) => {
        const boton = form.querySelector('button[type="submit"]');
        if (!boton) return;

        const textoOriginal = boton.innerHTML;
        let enviando = false;

        const activarLoading = () => {
            if (enviando) return;
            enviando = true;
            boton.classList.add('is-loading');
            boton.setAttribute('aria-busy', 'true');
            boton.innerHTML = '<span class="btn-spinner" aria-hidden="true"></span>Calculando resultado...';
            // Se deshabilita en el siguiente ciclo para no cancelar el envío.
            setTimeout(() => { boton.disabled = true; }, 0);
        };

        form.__setLoading = activarLoading;

        form.addEventListener('submit', (event) => {
            if (enviando) {
                event.preventDefault();
                return;
            }
            activarLoading();
        });

        // Si el usuario vuelve con el botón "atrás", se restaura el botón.
        window.addEventListener('pageshow', (event) => {
            if (!event.persisted) return;
            enviando = false;
            boton.disabled = false;
            boton.classList.remove('is-loading');
            boton.removeAttribute('aria-busy');
            boton.innerHTML = textoOriginal;
        });
    });

    document.querySelectorAll('[data-toggle-password]').forEach((toggle) => {
        toggle.addEventListener('click', () => {
            const input = document.getElementById(toggle.getAttribute('data-toggle-password'));
            if (!input) return;

            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            toggle.setAttribute('aria-pressed', String(isHidden));
            toggle.setAttribute('aria-label', isHidden ? 'Ocultar contraseña' : 'Mostrar contraseña');
            toggle.classList.toggle('is-visible', isHidden);
        });
    });

    /* ======================================================
       MICRO-INTERACCIONES VISUALES
       ====================================================== */
    const progress = document.createElement('div');
    progress.className = 'scroll-progress';
    document.body.appendChild(progress);

    const backTop = document.createElement('button');
    backTop.className = 'back-to-top';
    backTop.type = 'button';
    backTop.setAttribute('aria-label', 'Volver arriba');
    backTop.title = 'Volver arriba';
    backTop.innerHTML = '↑';
    document.body.appendChild(backTop);

    const updateScrollUI = () => {
        const doc = document.documentElement;
        const scrollable = doc.scrollHeight - window.innerHeight;
        const percentage = scrollable > 0 ? (window.scrollY / scrollable) * 100 : 0;
        progress.style.width = `${Math.min(100, Math.max(0, percentage))}%`;
        backTop.classList.toggle('is-visible', window.scrollY > 520);
    };

    window.addEventListener('scroll', updateScrollUI, { passive: true });
    updateScrollUI();

    backTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

    /* Revelado progresivo: convierte las secciones en una experiencia con scroll. */
    const revealTargets = document.querySelectorAll(
        '.page-content > section, .page-content > .information-card, .page-content > .activity-callout, .page-content > .admin-links, .page-content > .resumen-grid, .page-content > .materia-card-grid, .page-content > .simulacro-grid, .page-content > .practica-form, .page-content > .practica-quiz-form, .page-content > .practica-review, .progressia-docente-integrado'
    );

    // Los formularios de preguntas (práctica/simulacro) y la revisión pueden medir
    // miles de píxeles. Con threshold 0.08 el observador nunca los marcaba como
    // visibles (necesitaría ver el 8 % de una altura enorme) y quedaban en opacity 0,
    // por eso el simulacro de 40 preguntas aparecía en blanco. Se muestran siempre.
    const siempreVisibles = '.practica-quiz-form, .practica-review';

    revealTargets.forEach((element, index) => {
        element.classList.add('reveal-on-scroll');
        element.style.setProperty('--reveal-delay', `${Math.min(index * 55, 330)}ms`);

        if (element.matches(siempreVisibles) || element.offsetHeight > window.innerHeight * 3) {
            element.classList.add('is-revealed');
        }
    });

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries, instance) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-revealed');
                instance.unobserve(entry.target);
            });
        }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });

        revealTargets.forEach((element) => {
            if (!element.classList.contains('is-revealed')) observer.observe(element);
        });
        document.querySelectorAll('.subjects-grid, .simulacro-grid, .resumen-grid, .materia-card-grid, .admin-links').forEach((grid) => {
            [...grid.children].forEach((card) => observer.observe(card));
        });
    } else {
        revealTargets.forEach((element) => element.classList.add('is-revealed'));
    }

    /* Cascada suave dentro de rejillas tipo collage. */
    document.querySelectorAll('.subjects-grid, .simulacro-grid, .resumen-grid, .materia-card-grid, .admin-links').forEach((grid) => {
        [...grid.children].forEach((card, index) => {
            card.classList.add('reveal-on-scroll');
            card.style.setProperty('--reveal-delay', `${Math.min(index * 45, 300)}ms`);
            if (!('IntersectionObserver' in window)) card.classList.add('is-revealed');
        });
    });

    /* Rail contextual: resalta la sección visible al hacer scroll. */
    const railLinks = document.querySelectorAll('.context-rail-nav a[href^="#"]');
    const railSections = [...railLinks]
        .map((link) => document.querySelector(link.getAttribute('href')))
        .filter(Boolean);

    if (railLinks.length && 'IntersectionObserver' in window) {
        const railObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                railLinks.forEach((link) => link.classList.remove('is-current'));
                const current = document.querySelector(`.context-rail-nav a[href="#${entry.target.id}"]`);
                if (current) current.classList.add('is-current');
            });
        }, { threshold: 0, rootMargin: '-20% 0px -65% 0px' });
        railSections.forEach((section) => railObserver.observe(section));
    }

    /* Efecto de profundidad discreto en tarjetas, desactivado en táctiles. */
    const canHover = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
    if (canHover) {
        document.querySelectorAll('.subject-card, .simulacro-card, .admin-link-card').forEach((card) => {
            card.addEventListener('pointermove', (event) => {
                const rect = card.getBoundingClientRect();
                const x = (event.clientX - rect.left) / rect.width - 0.5;
                const y = (event.clientY - rect.top) / rect.height - 0.5;
                card.style.transform = `perspective(900px) rotateX(${y * -2.2}deg) rotateY(${x * 2.2}deg) translateY(-4px)`;
            });
            card.addEventListener('pointerleave', () => {
                card.style.transform = '';
            });
        });
    }

    /* Desplazamiento lateral con teclado para tablas anchas. */
    document.querySelectorAll('.admin-table-wrap').forEach((table) => {
        table.setAttribute('tabindex', '0');
        table.setAttribute('aria-label', 'Tabla desplazable horizontalmente');
    });
});