(() => {
    const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");

    if (reducedMotion.matches || !("IntersectionObserver" in window)) {
        return;
    }

    const roots = document.querySelectorAll(
        "body > header, main, body > section, body > footer, #admin-dashboard-wrapper",
    );
    const targetSelector = [
        "h1, h2, h3, h4, p, span, strong, em, small, b, i, a, button, input, select, textarea, label, img, li, article, table, tr, td, th, summary, blockquote, figure, div",
        "[class*='rounded-'][class*='bg-']",
    ].join(", ");
    const groupedTargets = new Map();
    const seenTargets = new Set();
    const parentTextSelector = "h1, h2, h3, h4, p, a, button, label, li";
    const groupSelector =
        "section, main, article, form, #admin-dashboard-wrapper";

    roots.forEach((root) => {
        root.querySelectorAll(targetSelector).forEach((element) => {
            const isCard = element.matches("[class*='rounded-'][class*='bg-']");
            const hasDirectText = Array.from(element.childNodes).some(
                (node) => node.nodeType === 3 && node.textContent.trim(),
            );
            const isPositioned =
                element.classList.contains("absolute") ||
                element.classList.contains("fixed");

            if (
                seenTargets.has(element) ||
                element.closest('[aria-hidden="true"], [hidden], [data-no-reveal], .no-reveal') ||
                (isPositioned && !hasDirectText) ||
                (element.tagName === "IMG" && !element.getAttribute("alt")) ||
                element.parentElement?.closest(parentTextSelector) ||
                (element.tagName === "DIV" && !isCard && !hasDirectText)
            ) {
                return;
            }

            seenTargets.add(element);

            const group = element.closest(groupSelector) || root;
            const groupIndex = groupedTargets.get(group) || 0;
            groupedTargets.set(group, groupIndex + 1);

            const isControl = element.matches(
                "a, button, input, select, textarea, label",
            );
            const isMedia = element.tagName === "IMG";
            const isInlineText = element.matches(
                "span, strong, em, small, b, i",
            );
            const variant = isControl
                ? "control"
                : isMedia
                  ? "media"
                  : isCard
                    ? "card"
                    : isInlineText
                      ? "inline"
                      : "text";

            element.classList.add(
                "scroll-reveal-item",
                `scroll-reveal-${variant}`,
            );
            element.style.setProperty(
                "--scroll-reveal-delay",
                `${Math.min(groupIndex, 5) * 45}ms`,
            );
        });
    });

    if (seenTargets.size === 0) {
        return;
    }

    const style = document.createElement("style");
    style.textContent = `
    .scroll-reveal-enabled .scroll-reveal-item {
      opacity: 0;
            transition: opacity 520ms cubic-bezier(0.2, 0.7, 0.2, 1) var(--scroll-reveal-delay, 0ms),
                translate 520ms cubic-bezier(0.2, 0.7, 0.2, 1) var(--scroll-reveal-delay, 0ms);
        }
        .scroll-reveal-enabled .scroll-reveal-text {
            translate: 0 10px;
        }
        .scroll-reveal-enabled .scroll-reveal-card {
            translate: 0 16px;
        }
        .scroll-reveal-enabled .scroll-reveal-control {
            translate: 0 7px;
        }
        .scroll-reveal-enabled .scroll-reveal-media {
            translate: 0 10px;
    }
    .scroll-reveal-enabled .scroll-reveal-item.is-visible {
      opacity: 1;
            translate: 0 0;
    }
  `;
    document.head.append(style);

    const observer = new IntersectionObserver(
        (entries, currentObserver) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("is-visible");
                    currentObserver.unobserve(entry.target);
                }
            });
        },
        { rootMargin: "0px 0px -4% 0px", threshold: 0.04 },
    );

    seenTargets.forEach((element) => {
        observer.observe(element);
    });

    const revealAllRemaining = () => {
        if ((window.innerHeight + window.scrollY) >= (document.documentElement.scrollHeight - 150)) {
            seenTargets.forEach((element) => {
                element.classList.add("is-visible");
            });
        }
    };
    window.addEventListener("scroll", revealAllRemaining, { passive: true });
    window.addEventListener("resize", revealAllRemaining, { passive: true });

    document.documentElement.classList.add("scroll-reveal-enabled");
})();
