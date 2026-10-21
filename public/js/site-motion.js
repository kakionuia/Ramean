(() => {
    const reducedMotion = window.matchMedia(
        "(prefers-reduced-motion: reduce)",
    ).matches;
    const splashSeenKey = "washly-splash-seen";

    if (!reducedMotion && !sessionStorage.getItem(splashSeenKey)) {
        const splash = document.createElement("div");
        splash.className = "washly-splash";
        splash.setAttribute("role", "status");
        splash.setAttribute("aria-label", "Washly sedang dimuat");
        splash.innerHTML =
            '<div class="washly-splash__content"><img class="washly-splash__logo" src="/images/washly-logo.png" alt=""><span>Washly</span><span class="washly-splash__track" aria-hidden="true"></span></div>';
        document.body.append(splash);
        window.setTimeout(() => {
            splash.classList.add("is-leaving");
            sessionStorage.setItem(splashSeenKey, "1");
            window.setTimeout(() => splash.remove(), 450);
        }, 850);
        splash.addEventListener("click", () => {
            splash.classList.add("is-leaving");
            sessionStorage.setItem(splashSeenKey, "1");
            window.setTimeout(() => splash.remove(), 450);
        });
    }

    document.body.classList.add("washly-ready");
    const revealTargets = [
        "main > section",
        "main > article",
        "main > .service-grid > *",
        "main > .steps-grid > *",
        "main > .contact-grid > *",
        "main > .discover-grid > *",
        "main > .detail-card",
        "main > .profile-panel",
        "main > .order-card",
        ".dash-stats > *",
        ".lower-grid > *",
        ".support-banner",
        ".main-area > .panel",
        ".main-area > section",
        ".auth-main > *",
        ".auth-card",
        ".hero-photo",
        ".partner-banner-inner",
    ].join(",");
    const targets = [
        ...new Set(document.querySelectorAll(revealTargets)),
    ].filter((element) => !element.closest(".washly-splash"));

    targets.forEach((element, index) => {
        element.dataset.reveal = "";
        if (index % 4) element.dataset.revealDelay = String(index % 4);
    });

    if (reducedMotion || !("IntersectionObserver" in window)) {
        targets.forEach((element) => element.classList.add("is-visible"));
    } else {
        const observer = new IntersectionObserver(
            (entries, currentObserver) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("is-visible");
                        currentObserver.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.12, rootMargin: "0px 0px -35px 0px" },
        );
        targets.forEach((element) => observer.observe(element));
    }

    document.addEventListener("pointerover", (event) => {
        const interactive = event.target.closest(
            "button, a.btn, .service, .contact-card, .step, .stat-card, .discover-card, .laundry-card",
        );
        if (interactive && !interactive.contains(event.relatedTarget))
            interactive.classList.add("motion-hover");
    });
    document.addEventListener("pointerout", (event) => {
        const interactive = event.target.closest(".motion-hover");
        if (interactive && !interactive.contains(event.relatedTarget))
            interactive.classList.remove("motion-hover");
    });
})();
