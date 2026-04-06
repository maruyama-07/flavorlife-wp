(function () {
    'use strict';

    var TRANSITION_FALLBACK_MS = 450;

    function qs(sel, root) {
        return (root || document).querySelector(sel);
    }

    function closeModalAnimated(dlg) {
        if (!dlg || !dlg.open || dlg.classList.contains('c-school-instructor-modal--closing')) {
            return;
        }
        dlg.classList.add('c-school-instructor-modal--closing');

        var done = false;
        function finish() {
            if (done) {
                return;
            }
            done = true;
            dlg.removeEventListener('transitionend', onTransitionEnd);
            clearTimeout(fallbackTimer);
            dlg.classList.remove('c-school-instructor-modal--closing');
            dlg.close();
        }

        function onTransitionEnd(e) {
            if (e.target !== dlg || e.propertyName !== 'opacity') {
                return;
            }
            finish();
        }

        dlg.addEventListener('transitionend', onTransitionEnd);
        var fallbackTimer = setTimeout(finish, TRANSITION_FALLBACK_MS);
    }

    function openInstructorModal(trigger) {
        var id = trigger.getAttribute('data-instructor-id');
        if (!id) {
            return;
        }
        var dlg = document.getElementById('school-instructor-dialog');
        var payload = document.getElementById('instructor-modal-payload-' + id);
        if (!dlg || !payload || typeof dlg.showModal !== 'function') {
            return;
        }
        var scrollY = window.scrollY || window.pageYOffset || 0;
        fillModalFromPayload(dlg, payload);
        dlg.showModal();
        requestAnimationFrame(function () {
            window.scrollTo(0, scrollY);
        });
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                window.scrollTo(0, scrollY);
            });
        });
    }

    function fillModalFromPayload(dlg, payload) {
        var furiganaEl = qs('.js-school-instructor-modal-furigana', dlg);
        var nameEl = qs('.js-school-instructor-modal-name', dlg);
        var bodyEl = qs('.js-school-instructor-modal-body', dlg);
        var footerEl = qs('.js-school-instructor-modal-footer', dlg);
        var fg = qs('.p-school-about-instructors__modal-payload-furigana', payload);
        var nm = qs('.p-school-about-instructors__modal-payload-name', payload);
        var bd = qs('.p-school-about-instructors__modal-payload-body', payload);
        var linkMeta = qs('.p-school-about-instructors__modal-payload-link', payload);

        if (furiganaEl) {
            furiganaEl.textContent = fg ? fg.textContent.trim() : '';
            furiganaEl.hidden = !furiganaEl.textContent;
        }
        if (nameEl) {
            nameEl.textContent = nm ? nm.textContent.trim() : '';
        }
        if (bodyEl) {
            bodyEl.innerHTML = bd ? bd.innerHTML : '';
        }
        if (footerEl) {
            footerEl.innerHTML = '';
            footerEl.hidden = true;
            if (linkMeta && linkMeta.dataset.url) {
                var a = document.createElement('a');
                a.className = 'c-school-instructor-modal__external-link';
                a.href = linkMeta.dataset.url;
                a.textContent = '関連リンクを開く';
                if (linkMeta.dataset.newTab === '1') {
                    a.target = '_blank';
                    a.rel = 'noopener noreferrer';
                }
                footerEl.appendChild(a);
                footerEl.hidden = false;
            }
        }
    }

    document.addEventListener('click', function (e) {
        var openEl = e.target.closest('.js-school-instructor-modal-open');
        if (openEl) {
            e.preventDefault();
            e.stopPropagation();
            openInstructorModal(openEl);
            return;
        }

        var closeBtn = e.target.closest('.js-school-instructor-modal-close');
        if (closeBtn) {
            var d = document.getElementById('school-instructor-dialog');
            if (d) {
                closeModalAnimated(d);
            }
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Enter' && e.key !== ' ') {
            return;
        }
        var el = e.target && e.target.closest ? e.target.closest('.js-school-instructor-modal-open') : null;
        if (!el || !el.classList.contains('p-school-about-instructors__item')) {
            return;
        }
        e.preventDefault();
        openInstructorModal(el);
    });

    document.addEventListener('DOMContentLoaded', function () {
        var dlg = document.getElementById('school-instructor-dialog');
        if (!dlg) {
            return;
        }
        dlg.addEventListener('click', function (e) {
            if (e.target === dlg) {
                closeModalAnimated(dlg);
            }
        });
        dlg.addEventListener('cancel', function (e) {
            e.preventDefault();
            closeModalAnimated(dlg);
        });
    });
})();
