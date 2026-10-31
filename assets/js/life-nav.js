(function () {
    if (window.lifeMobileNavReady) return;

    var nav = document.querySelector('.life-floating-nav');
    if (!nav) return;

    window.lifeMobileNavReady = true;
    var mobileQuery = window.matchMedia('(max-width: 759px)');
    var lastScrollY = window.scrollY;
    var ticking = false;

    function setHidden(hidden) {
        nav.classList.toggle('life-floating-nav--hidden', mobileQuery.matches && hidden);
    }

    function isEditing() {
        var active = document.activeElement;
        return active && active.matches('input, textarea, select, [contenteditable="true"]');
    }

    function updateOnScroll() {
        var currentScrollY = window.scrollY;
        var delta = currentScrollY - lastScrollY;

        if (!mobileQuery.matches || currentScrollY <= 8) {
            setHidden(false);
        } else if (isEditing()) {
            setHidden(true);
        } else {
            setHidden(false);
        }

        lastScrollY = currentScrollY;
        ticking = false;
    }

    window.addEventListener('scroll', function () {
        if (ticking) return;
        ticking = true;
        window.requestAnimationFrame(updateOnScroll);
    }, { passive: true });

    document.addEventListener('focusin', function (event) {
        if (event.target.matches('input, textarea, select, [contenteditable="true"]')) {
            setHidden(true);
        }
    });

    document.addEventListener('focusout', function () {
        window.setTimeout(function () {
            if (!isEditing()) setHidden(false);
        }, 120);
    });

    var handleViewportChange = function () {
        lastScrollY = window.scrollY;
        setHidden(false);
    };
    if (mobileQuery.addEventListener) {
        mobileQuery.addEventListener('change', handleViewportChange);
    } else {
        mobileQuery.addListener(handleViewportChange);
    }
})();
