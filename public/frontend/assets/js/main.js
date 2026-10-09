/**
 * BOUESTI Football Club — public site interactions.
 * Each feature initialises only when its markup exists on the page.
 */
(() => {
    'use strict';

    const $ = (sel, root = document) => root.querySelector(sel);
    const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));
    const FOCUSABLE = 'a[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';

    /** Keep keyboard focus inside `container` while it is open. */
    function trapFocus(container, event) {
        if (event.key !== 'Tab') return;
        const items = $$(FOCUSABLE, container).filter((el) => el.offsetParent !== null);
        if (!items.length) return;
        const first = items[0];
        const last = items[items.length - 1];
        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    }

    /* Header state + back-to-top ------------------------------------------ */
    function initScroll() {
        const nav = $('.site-nav');
        const topBtn = $('.backtop');
        let ticking = false;

        const update = () => {
            const y = window.scrollY;
            if (nav && !nav.classList.contains('inner')) nav.classList.toggle('scrolled', y > 20);
            if (topBtn) topBtn.classList.toggle('show', y > 500);
            ticking = false;
        };

        update();
        window.addEventListener('scroll', () => {
            if (!ticking) {
                ticking = true;
                requestAnimationFrame(update);
            }
        }, { passive: true });

        topBtn?.addEventListener('click', () => {
            window.scrollTo({ top: 0 });
            $('#main')?.focus({ preventScroll: true });
        });
    }

    /* Desktop dropdowns: hover (CSS) + click/tap + keyboard ---------------- */
    function initDropdowns() {
        const drops = $$('.drop');
        if (!drops.length) return;

        const close = (drop) => {
            drop.classList.remove('open');
            $('.drop-toggle', drop)?.setAttribute('aria-expanded', 'false');
        };

        drops.forEach((drop) => {
            const toggle = $('.drop-toggle', drop);
            toggle?.addEventListener('click', () => {
                const willOpen = !drop.classList.contains('open');
                drops.forEach(close);
                drop.classList.toggle('open', willOpen);
                toggle.setAttribute('aria-expanded', String(willOpen));
            });
            drop.addEventListener('focusout', (e) => {
                if (!drop.contains(e.relatedTarget)) close(drop);
            });
        });

        document.addEventListener('click', (e) => {
            drops.forEach((drop) => { if (!drop.contains(e.target)) close(drop); });
        });
        document.addEventListener('keydown', (e) => {
            if (e.key !== 'Escape') return;
            const open = drops.find((d) => d.classList.contains('open') || d.contains(document.activeElement));
            if (open) {
                close(open);
                $('.drop-toggle', open)?.focus();
            }
        });
    }

    /* Accordions (mobile menu groups + FAQ) -------------------------------- */
    function initAccordions() {
        const bind = (btnSel, itemSel) => $$(btnSel).forEach((btn) => {
            const item = btn.closest(itemSel);
            if (!item) return;
            btn.setAttribute('aria-expanded', String(item.classList.contains('open')));
            btn.addEventListener('click', () => {
                const open = item.classList.toggle('open');
                btn.setAttribute('aria-expanded', String(open));
            });
        });

        // Expand the group that contains the current page.
        $$('.mobile-acc.active').forEach((acc) => acc.classList.add('open'));
        bind('.mobile-acc-btn', '.mobile-acc');

        $$('.faq-q').forEach((btn, i) => {
            const answer = btn.nextElementSibling;
            if (answer?.classList.contains('faq-a')) {
                answer.id ||= `faq-answer-${i + 1}`;
                btn.setAttribute('aria-controls', answer.id);
            }
        });
        bind('.faq-q', '.faq-item');
    }

    /* Off-canvas mobile menu ----------------------------------------------- */
    function initMobileMenu() {
        const toggle = $('.mobile-toggle');
        const panel = $('.mobile-panel');
        const overlay = $('.mobile-overlay');
        if (!toggle || !panel) return;

        const isOpen = () => panel.classList.contains('open');

        const open = () => {
            panel.classList.add('open');
            overlay?.classList.add('open');
            document.body.classList.add('menu-open');
            toggle.setAttribute('aria-expanded', 'true');
            setTimeout(() => $('.mobile-close', panel)?.focus(), 50);
        };

        const close = (returnFocus = true) => {
            if (!isOpen()) return;
            panel.classList.remove('open');
            overlay?.classList.remove('open');
            document.body.classList.remove('menu-open');
            toggle.setAttribute('aria-expanded', 'false');
            if (returnFocus) toggle.focus();
        };

        toggle.addEventListener('click', open);
        $('.mobile-close', panel)?.addEventListener('click', () => close());
        overlay?.addEventListener('click', () => close());
        panel.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') close();
            trapFocus(panel, e);
        });
        // Close after choosing a destination (covers same-page anchors too).
        panel.addEventListener('click', (e) => {
            if (e.target.closest('a[href]')) close(false);
        });
        // Leaving the mobile breakpoint with the menu open must not leave the page scroll-locked.
        window.matchMedia('(min-width: 901px)').addEventListener('change', (mq) => {
            if (mq.matches) close(false);
        });
    }

    /* Filterable grids (squad, gallery) ------------------------------------ */
    function initFilters() {
        $$('[data-filter-group]').forEach((group) => {
            const buttons = $$('[data-filter]', group);
            const cards = $$('[data-category]', group);
            const empty = $('[data-filter-empty]', group);
            const status = $('[data-filter-status]', group);

            const apply = (value, label) => {
                let shown = 0;
                cards.forEach((card) => {
                    const match = value === 'all' || card.dataset.category === value;
                    card.classList.toggle('is-hidden', !match);
                    card.classList.remove('is-entering');
                    if (match) {
                        shown++;
                        void card.offsetWidth; // restart the entry animation
                        card.classList.add('is-entering');
                    }
                });
                if (empty) empty.hidden = shown > 0;
                if (status) status.textContent = value === 'all' ? '' : `Showing ${shown} result${shown === 1 ? '' : 's'} for “${label}”`;
            };

            buttons.forEach((btn) => {
                btn.setAttribute('aria-pressed', String(btn.classList.contains('active')));
                btn.addEventListener('click', () => {
                    buttons.forEach((b) => {
                        b.classList.toggle('active', b === btn);
                        b.setAttribute('aria-pressed', String(b === btn));
                    });
                    apply(btn.dataset.filter, btn.textContent.trim());
                });
            });

            // "Clear filters" buttons inside the empty state reset to All.
            $$('[data-filter-reset]', group).forEach((reset) => reset.addEventListener('click', () => {
                buttons.find((b) => b.dataset.filter === 'all')?.click();
            }));
        });
    }

    /* Gallery lightbox ------------------------------------------------------ */
    function initLightbox() {
        const box = $('.lightbox');
        const items = $$('.gallery-item');
        if (!box || !items.length) return;

        const img = $('img', box);
        const count = $('.lightbox-count', box);
        let index = 0;
        let opener = null;

        const visible = () => items.filter((item) => !item.classList.contains('is-hidden'));

        const show = (i) => {
            const list = visible();
            if (!list.length) return;
            index = (i + list.length) % list.length;
            const source = $('img', list[index]);
            img.src = source.src; // full-size candidate, not the phone srcset one
            img.alt = source.alt;
            if (count) count.textContent = `${index + 1} / ${list.length}`;
        };

        const open = (item) => {
            opener = item;
            show(visible().indexOf(item));
            box.classList.add('open');
            box.setAttribute('aria-hidden', 'false');
            document.body.classList.add('modal-open');
            $('.lightbox-close', box).focus();
        };

        const close = () => {
            box.classList.remove('open');
            box.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('modal-open');
            opener?.focus();
        };

        items.forEach((item) => item.addEventListener('click', () => open(item)));
        $('.lightbox-close', box).addEventListener('click', close);
        $('.lightbox-prev', box)?.addEventListener('click', () => show(index - 1));
        $('.lightbox-next', box)?.addEventListener('click', () => show(index + 1));
        box.addEventListener('click', (e) => { if (e.target === box) close(); });
        box.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') close();
            if (e.key === 'ArrowLeft') show(index - 1);
            if (e.key === 'ArrowRight') show(index + 1);
            trapFocus(box, e);
        });

        // Swipe left/right on touch screens.
        let startX = null;
        box.addEventListener('touchstart', (e) => { startX = e.touches[0].clientX; }, { passive: true });
        box.addEventListener('touchend', (e) => {
            if (startX === null) return;
            const dx = e.changedTouches[0].clientX - startX;
            if (Math.abs(dx) > 50) show(index + (dx < 0 ? 1 : -1));
            startX = null;
        });
    }

    /* Site search dialog --------------------------------------------------- */
    function initSearch() {
        const dialog = $('#site-search');
        if (!dialog) return;

        const input = $('#site-search-input', dialog);
        const items = $$('[data-search-item]', dialog);
        const empty = $('[data-search-empty]', dialog);
        let opener = null;

        const results = () => items.filter((li) => !li.hidden).map((li) => $('a', li));

        const filter = () => {
            const terms = input.value.toLowerCase().trim().split(/\s+/).filter(Boolean);
            let shown = 0;
            items.forEach((li) => {
                const match = terms.every((t) => li.dataset.searchItem.includes(t));
                li.hidden = !match;
                if (match) shown++;
            });
            empty.hidden = shown > 0;
        };

        const open = (trigger) => {
            opener = trigger || document.activeElement;
            dialog.hidden = false;
            document.body.classList.add('modal-open');
            input.value = '';
            filter();
            requestAnimationFrame(() => input.focus());
        };

        const close = () => {
            dialog.hidden = true;
            document.body.classList.remove('modal-open');
            opener?.focus?.();
        };

        $$('[data-search-open]').forEach((btn) => btn.addEventListener('click', () => open(btn)));
        $$('[data-search-close]', dialog).forEach((el) => el.addEventListener('click', close));
        input.addEventListener('input', filter);

        dialog.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') return close();
            const links = results();
            const pos = links.indexOf(document.activeElement);
            if (e.key === 'ArrowDown' && links.length) {
                e.preventDefault();
                links[Math.min(pos + 1, links.length - 1)].focus();
            } else if (e.key === 'ArrowUp' && links.length) {
                e.preventDefault();
                pos <= 0 ? input.focus() : links[pos - 1].focus();
            } else if (e.key === 'Enter' && document.activeElement === input && links.length) {
                e.preventDefault();
                links[0].click();
            }
            trapFocus(dialog, e);
        });

        // Ctrl/Cmd + K opens search from anywhere.
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                dialog.hidden ? open() : close();
            }
        });
    }

    /* Lazy images fade in once decoded ------------------------------------- */
    function initImages() {
        $$('img[loading="lazy"]').forEach((img) => {
            const done = () => img.classList.add('is-loaded');
            if (img.complete) done();
            else {
                img.addEventListener('load', done, { once: true });
                img.addEventListener('error', done, { once: true });
            }
        });
    }

    /* Alerts / toasts ------------------------------------------------------ */
    function dismissAlert(alert) {
        alert.classList.add('is-leaving');
        setTimeout(() => alert.remove(), 250);
    }

    function initAlerts() {
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-dismiss-alert]');
            if (btn) dismissAlert(btn.closest('.alert'));
        });
        $$('[data-autodismiss]').forEach((alert) => setTimeout(() => alert.isConnected && dismissAlert(alert), 6000));
    }

    /* Forms: inline validation + submit feedback --------------------------- */
    function fieldError(control) {
        const field = control.closest('.field');
        if (!field) return;
        const message = control.validity.valid ? '' : control.validationMessage;
        let error = $('.field-error', field);
        if (!error) {
            error = document.createElement('p');
            error.className = 'field-error';
            field.appendChild(error);
        }
        error.textContent = message;
        field.classList.toggle('has-error', !!message);
        message ? control.setAttribute('aria-invalid', 'true') : control.removeAttribute('aria-invalid');
    }

    function setLoading(button) {
        if (!button || button.classList.contains('is-loading')) return;
        const text = button.dataset.loadingText || 'Please wait…';
        button.dataset.originalHtml = button.innerHTML;
        button.classList.add('is-loading');
        button.setAttribute('aria-disabled', 'true');
        button.innerHTML = `<span class="spinner" aria-hidden="true"></span> ${text}`;
    }

    function initForms() {
        $$('form').forEach((form) => {
            const controls = $$('input, select, textarea', form).filter((c) => c.type !== 'hidden');
            let attempted = false;

            form.noValidate = true; // we render our own accessible messages
            controls.forEach((control) => {
                control.addEventListener('blur', () => { if (attempted || control.value) fieldError(control); });
                control.addEventListener('input', () => { if (attempted) fieldError(control); });
            });

            form.addEventListener('submit', (e) => {
                attempted = true;
                controls.forEach(fieldError);
                const firstInvalid = controls.find((c) => !c.validity.valid);
                if (firstInvalid) {
                    e.preventDefault();
                    firstInvalid.focus();
                    return;
                }

                // Frontend-only forms: no backend yet, so tell the visitor honestly.
                if (form.hasAttribute('data-frontend-only')) {
                    e.preventDefault();
                    const feedback = $('.form-feedback', form);
                    if (feedback) {
                        feedback.innerHTML = `<div class="alert info" role="status"><i class="fa-solid fa-circle-info" aria-hidden="true"></i><div class="alert-body"><strong class="alert-title">Online submissions open soon</strong>${form.dataset.frontendMessage || 'This form is not connected yet. Please contact the club directly in the meantime.'}</div></div>`;
                        feedback.scrollIntoView({ block: 'nearest' });
                    }
                    return;
                }

                // Real submissions: block double clicks and show progress.
                if (form.dataset.submitting) return e.preventDefault();
                form.dataset.submitting = 'true';
                setLoading(e.submitter || $('[type="submit"]', form));
            });
        });

        // Restore buttons when the page is shown again from the back/forward cache.
        window.addEventListener('pageshow', (e) => {
            if (!e.persisted) return;
            $$('form[data-submitting]').forEach((form) => delete form.dataset.submitting);
            $$('.btn.is-loading').forEach((btn) => {
                btn.innerHTML = btn.dataset.originalHtml;
                btn.classList.remove('is-loading');
                btn.removeAttribute('aria-disabled');
            });
        });
    }

    /* Kick-off countdown ([data-countdown] holds an ISO date-time) ---------- */
    function initCountdowns() {
        $$('[data-countdown]').forEach((el) => {
            const target = Date.parse(el.dataset.countdown);
            if (Number.isNaN(target)) return;
            const units = Object.fromEntries($$('[data-unit]', el).map((b) => [b.dataset.unit, b]));
            const pad = (n) => String(n).padStart(2, '0');

            const tick = () => {
                const left = Math.max(0, Math.floor((target - Date.now()) / 1000));
                if (units.days) units.days.textContent = pad(Math.floor(left / 86400));
                if (units.hours) units.hours.textContent = pad(Math.floor((left % 86400) / 3600));
                if (units.minutes) units.minutes.textContent = pad(Math.floor((left % 3600) / 60));
                if (units.seconds) units.seconds.textContent = pad(left % 60);
                return left > 0;
            };

            if (tick()) {
                const timer = setInterval(() => { if (!tick()) clearInterval(timer); }, 1000);
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        initCountdowns();
        initScroll();
        initDropdowns();
        initAccordions();
        initMobileMenu();
        initFilters();
        initLightbox();
        initSearch();
        initImages();
        initAlerts();
        initForms();
    });
})();
