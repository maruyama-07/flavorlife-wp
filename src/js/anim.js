document.addEventListener("DOMContentLoaded", () => {
    function clearScrollAnimFouc() {
        document.documentElement.classList.remove("js-scroll-anim-pending");
        document.documentElement.classList.add("js-scroll-anim-ready");
    }

    function clearScrollAnimFoucAfterPaint() {
        requestAnimationFrame(() => {
            requestAnimationFrame(clearScrollAnimFouc);
        });
    }

    if (typeof gsap === "undefined" || typeof ScrollTrigger === "undefined") {
        clearScrollAnimFouc();
        return;
    }

    gsap.registerPlugin(ScrollTrigger);

    const prefersReduced =
        typeof window.matchMedia === "function" &&
        window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    /**
     * @param {Element} wrap
     * @returns {NodeListOf<Element>|Element[]}
     */
    function getScrollAnimateTargets(wrap) {
        if (wrap.classList.contains("p-school-top-intro")) {
            return wrap.querySelectorAll(
                ".p-school-top-intro__brand, .p-school-top-intro__lead, .p-school-top-intro__body"
            );
        }
        if (wrap.classList.contains("p-school-top-cards")) {
            return wrap.querySelectorAll(".p-school-top-cards__item");
        }
        if (wrap.classList.contains("p-school-category")) {
            return wrap.querySelectorAll(".p-school-category__header > *, .p-school-category__item");
        }
        if (wrap.classList.contains("p-school-seasonal-topics")) {
            return wrap.querySelectorAll(
                ".p-school-seasonal-topics__header > *, .p-school-seasonal-topics__media, .p-school-seasonal-topics__body > *"
            );
        }
        if (wrap.classList.contains("p-school-course-top")) {
            return wrap.querySelectorAll(
                ".p-school-course-top__intro, .p-school-course-top__section, .p-school-course-top__card-item"
            );
        }
        if (wrap.classList.contains("p-school-course__layout")) {
            return wrap.querySelectorAll(
                ".p-school-course__aside, .p-school-course__item, .p-school-course__pagination, .p-school-course__empty"
            );
        }
        if (wrap.classList.contains("p-school-voice")) {
            return wrap.querySelectorAll(
                ".p-school-voice__intro, .p-school-voice-card, .p-school-voice__pagination, .p-school-voice__empty, .p-school-voice__footer-note"
            );
        }
        if (wrap.classList.contains("p-school-about-intro")) {
            return wrap.querySelectorAll(".p-school-about-intro__text, .p-school-about-intro__media");
        }
        if (wrap.classList.contains("p-school-about-bottom-links")) {
            return wrap.querySelectorAll(".p-school-about-bottom-links__card");
        }
        return wrap.querySelectorAll(
            "h1, h2, h3, p, li, figure, img, .c-custom-button, .p-top-mv-topics"
        );
    }

    /**
     * スクール下層 .page-content … .l-inner / .p-index の直下ブロック単位（本文・ブロックエディタの塊）
     *
     * @param {Element} wrap
     * @returns {Element[]}
     */
    function getSchoolPageContentTargets(wrap) {
        let root = wrap;
        for (let i = 0; i < wrap.children.length; i++) {
            const el = wrap.children[i];
            if (el.classList.contains("l-inner") || el.classList.contains("p-index")) {
                root = el;
                break;
            }
        }
        const out = [];
        for (let i = 0; i < root.children.length; i++) {
            const el = root.children[i];
            const tag = el.tagName;
            if (tag === "SCRIPT" || tag === "STYLE") continue;
            out.push(el);
        }
        return out;
    }

    /**
     * 各要素ごとに ScrollTrigger を張る。
     *
     * @param {NodeListOf<Element>|Element[]} targets
     * @param {string} scrollStart
     */
    function animateEachWithScrollTrigger(targets, scrollStart) {
        Array.prototype.forEach.call(targets, (el) => {
            if (!el || el.nodeType !== 1) return;

            gsap.fromTo(
                el,
                { autoAlpha: 0, y: 16 },
                {
                    autoAlpha: 1,
                    y: 0,
                    duration: 0.65,
                    ease: "power2.out",
                    scrollTrigger: {
                        trigger: el,
                        start: scrollStart,
                        toggleActions: "play none none none",
                    },
                }
            );
        });
    }

    function runScrollAnimations(scrollStart) {
        document.querySelectorAll(".js-animate-content").forEach((wrap) => {
            const targets = getScrollAnimateTargets(wrap);
            if (!targets.length) return;
            animateEachWithScrollTrigger(targets, scrollStart);
        });
    }

    function runSchoolPageContentAnimations(scrollStart) {
        document.querySelectorAll("body.school-section .page-content").forEach((wrap) => {
            const targets = getSchoolPageContentTargets(wrap);
            if (!targets.length) return;
            animateEachWithScrollTrigger(targets, scrollStart);
        });
    }

    if (prefersReduced) {
        clearScrollAnimFouc();
        document.querySelectorAll(".js-animate-content").forEach((wrap) => {
            const targets = getScrollAnimateTargets(wrap);
            if (targets.length) {
                gsap.set(targets, { autoAlpha: 1, y: 0 });
            }
        });
        document.querySelectorAll("body.school-section .page-content").forEach((wrap) => {
            const targets = getSchoolPageContentTargets(wrap);
            if (targets.length) {
                gsap.set(targets, { autoAlpha: 1, y: 0 });
            }
        });
        return;
    }

    const scrollStart =
        typeof window.matchMedia === "function" && window.matchMedia("(max-width: 767px)").matches
            ? "top 90%"
            : "top 70%";
    runScrollAnimations(scrollStart);
    runSchoolPageContentAnimations(scrollStart);
    ScrollTrigger.refresh();
    clearScrollAnimFoucAfterPaint();
});

document.addEventListener("DOMContentLoaded", () => {
    if (typeof gsap === "undefined") return;

    gsap.fromTo(
        ".splide__slide-text",
        { autoAlpha: 0 },
        {
            autoAlpha: 1,
            y: 0,
            duration: 0.4,
            ease: "power2.out",
            delay: 0.2,
        }
    );
});
