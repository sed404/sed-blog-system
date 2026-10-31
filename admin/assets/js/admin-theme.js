/* Synchronous head bootstrap: validated presentation preferences only. */
(function () {
    'use strict';
    var themes = [
        ['default', '默认浅色'], ['dark', '深色'], ['translucent-green', '半透明绿色'],
        ['translucent-blue', '半透明蓝色'], ['translucent-yellow', '半透明黄色'],
        ['translucent-red', '半透明红色'], ['translucent-pink', '半透明粉色'], ['translucent-cyan', '半透明青色']
    ];
    // Exact regional presets from Light Year V5; text contrast is handled separately.
    var colors = [['default', '默认'], ['color_2', '绿色'], ['color_3', '蓝色'], ['color_4', '橙色'], ['color_5', '红色'], ['color_6', '紫色'], ['color_7', '青色'], ['color_8', '深灰']];
    var groups = {theme: themes, logo: colors, header: colors, sidebar: colors};
    var base = (document.currentScript && document.currentScript.src) || location.href;
    var appPath = new URL('../../', base).pathname;
    var key = 'seb.moments.admin.ui.v1:' + appPath;
    function validate(value) {
        var clean = {};
        Object.keys(groups).forEach(function (group) {
            clean[group] = value && groups[group].some(function (item) { return item[0] === value[group]; }) ? value[group] : 'default';
        });
        return clean;
    }
    var current;
    try { current = validate(JSON.parse(localStorage.getItem(key))); } catch (_) { current = validate(null); }
    function apply(value) {
        current = validate(value);
        Object.keys(groups).forEach(function (group) { document.documentElement.setAttribute('data-admin-' + group, current[group]); });
    }
    apply(current);
    window.AdminTheme = {
        groups: groups,
        get: function () { return Object.assign({}, current); },
        set: function (value) {
            apply(value);
            window.dispatchEvent(new Event('admin-theme-change'));
            try { localStorage.setItem(key, JSON.stringify(current)); return true; } catch (_) { return false; }
        }
    };
    window.addEventListener('storage', function (event) {
        if (event.key !== key && event.key !== null) return;
        try { apply(JSON.parse(event.newValue)); } catch (_) { apply(null); }
        window.dispatchEvent(new Event('admin-theme-change'));
    });
})();
