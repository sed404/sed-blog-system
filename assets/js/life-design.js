(function () {
    var toggle = document.querySelector('[data-life-theme]');
    function label() {
        if (!toggle) return;
        var dark = document.body.classList.contains('dark-theme');
        var icon = toggle.querySelector('.iconfont');
        if (icon) icon.className = 'iconfont ' + (dark ? 'icon-yueliang' : 'icon-ai250');
        toggle.setAttribute('aria-label', dark ? '切换白天模式' : '切换暗黑模式');
        toggle.setAttribute('aria-pressed', String(dark));
    }
    if (toggle) toggle.addEventListener('click', function () {
        var dark = !document.body.classList.contains('dark-theme');
        document.documentElement.classList.toggle('dark-theme', dark);
        document.body.classList.toggle('dark-theme', dark);
        document.cookie = 'dark_theme=' + (dark ? 'dark-theme' : 'root') + '; path=/; SameSite=Lax';
        document.cookie = 'dark_theme_manual=1; path=/; SameSite=Lax';
        label();
    });
    label();
    var search = document.querySelector('[data-site-search]');
    var filters = document.querySelectorAll('[data-site-filter]');
    var category = 'all';
    function filterSites() {
        var keyword = search ? search.value.trim().toLocaleLowerCase() : '';
        var visible = 0;
        document.querySelectorAll('[data-site-category]').forEach(function (item) {
            item.hidden = (category !== 'all' && item.dataset.siteCategory !== category) || !item.textContent.toLocaleLowerCase().includes(keyword);
            if (!item.hidden) visible++;
        });
        document.querySelectorAll('[data-site-heading]').forEach(function (heading) {
            heading.hidden = !Array.from(document.querySelectorAll('[data-site-category]')).some(function (item) { return !item.hidden && item.dataset.siteCategory === heading.dataset.siteHeading; });
        });
        var empty = document.querySelector('[data-site-empty]');
        if (empty) empty.hidden = visible !== 0;
    }
    filters.forEach(function (button) { button.addEventListener('click', function () {
        category = button.dataset.siteFilter;
        filters.forEach(function (item) { item.classList.toggle('is-active', item === button); item.setAttribute('aria-pressed', String(item === button)); });
        filterSites();
    }); });
    if (search) search.addEventListener('input', filterSites);
})();
