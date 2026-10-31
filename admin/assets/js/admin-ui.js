/* Shared admin interactions; no public-site assets or business requests. */
document.addEventListener('DOMContentLoaded', function () {
    'use strict';
    var body = document.body;
    if (window.jQuery && window.matchMedia('(prefers-reduced-motion: reduce)').matches) window.jQuery.fx.off = true;
    var toggle = document.getElementById('admin-sidebar-toggle');
    var sidebar = document.getElementById('admin-sidebar');
    var backdrop = document.querySelector('.admin-drawer-backdrop');
    var mobile = window.matchMedia('(max-width: 991px)');
    var preference = null;
    var sidebarKey = 'seb.moments.admin.sidebar.v1:' + new URL('../../', document.querySelector('script[src*="admin-ui.js"]').src).pathname;
    try { preference = localStorage.getItem(sidebarKey); } catch (_) {}
    var collapsed = preference === 'collapsed';
    var inertNodes = [];
    function closeDrawer(restoreFocus) {
        body.classList.remove('admin-drawer-open');
        if (backdrop) backdrop.hidden = true;
        inertNodes.forEach(function (node) { node.inert = false; });
        inertNodes = [];
        if (sidebar) { sidebar.inert = mobile.matches; sidebar.removeAttribute('aria-modal'); sidebar.removeAttribute('role'); }
        if (toggle) { toggle.setAttribute('aria-expanded', String(!mobile.matches && !collapsed)); toggle.setAttribute('aria-label', mobile.matches ? '打开导航' : collapsed ? '展开侧栏' : '折叠侧栏'); }
        if (toggle && (restoreFocus || (mobile.matches && sidebar && sidebar.contains(document.activeElement)))) toggle.focus();
    }
    function openDrawer() {
        body.classList.add('admin-drawer-open');
        backdrop.hidden = false;
        sidebar.inert = false;
        sidebar.setAttribute('role', 'dialog'); sidebar.setAttribute('aria-modal', 'true');
        document.querySelectorAll('.lyear-layout-content, .lyear-layout-header').forEach(function (node) {
            if (!node.inert) { node.inert = true; inertNodes.push(node); }
        });
        toggle.setAttribute('aria-expanded', 'true');
        sidebar.querySelector('.admin-drawer-close').focus();
    }
    function setCollapsed(collapsed) {
        body.classList.toggle('admin-collapsed', !mobile.matches && collapsed);
        if (toggle) {
            toggle.setAttribute('aria-expanded', String(!collapsed));
            toggle.setAttribute('aria-label', collapsed ? '展开侧栏' : '折叠侧栏');
        }
    }
    setCollapsed(collapsed); closeDrawer(false);
    mobile.addEventListener('change', function () { closeDrawer(false); setCollapsed(collapsed); closeDrawer(false); });
    if (backdrop) backdrop.addEventListener('click', function () { closeDrawer(true); });
    var drawerClose = document.querySelector('.admin-drawer-close');
    if (drawerClose) drawerClose.addEventListener('click', function () { closeDrawer(true); });
    document.addEventListener('keydown', function (event) {
        if (!body.classList.contains('admin-drawer-open')) return;
        if (event.key === 'Escape') { event.preventDefault(); closeDrawer(true); }
        if (event.key === 'Tab') {
            var nodes = Array.from(sidebar.querySelectorAll('a[href], button:not(:disabled)')).filter(function (node) { return node.getClientRects().length; });
            var first = nodes[0], last = nodes[nodes.length - 1];
            if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
            else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
        }
    });
    if (toggle) toggle.addEventListener('click', function () {
        if (mobile.matches) { openDrawer(); return; }
        collapsed = !collapsed; setCollapsed(collapsed);
        try { localStorage.setItem(sidebarKey, collapsed ? 'collapsed' : 'expanded'); } catch (_) {}
    });
    document.querySelectorAll('.admin-nav-toggle').forEach(function (button) {
        button.addEventListener('click', function () {
            if (body.classList.contains('admin-collapsed')) {
                collapsed = false;
                setCollapsed(false);
                button.parentElement.classList.add('open');
            } else {
                button.parentElement.classList.toggle('open');
            }
            button.setAttribute('aria-expanded', String(button.parentElement.classList.contains('open')));
        });
    });
    var themePanel = document.getElementById('admin-theme-panel');
    var themeToggle = document.getElementById('admin-theme-toggle');
    if (themePanel && themeToggle && window.AdminTheme) {
        var names = {theme: '整体主题', logo: 'Logo／品牌区域', header: '顶部导航', sidebar: '侧边栏'};
        var options = document.getElementById('admin-theme-options');
        Object.keys(AdminTheme.groups).forEach(function (group) {
            var fieldset = document.createElement('fieldset');
            var legend = document.createElement('legend'); legend.textContent = names[group]; fieldset.appendChild(legend);
            var grid = document.createElement('div'); grid.className = 'admin-theme-grid';
            AdminTheme.groups[group].forEach(function (item) {
                var label = document.createElement('label'); label.className = 'admin-theme-choice';
                var input = document.createElement('input'); input.type = 'radio'; input.name = 'admin-theme-' + group; input.value = item[0]; input.dataset.group = group;
                var swatch = document.createElement('span'); swatch.className = 'admin-theme-swatch'; swatch.dataset.swatch = item[0]; swatch.setAttribute('aria-hidden', 'true');
                var text = document.createElement('span'); text.textContent = item[1];
                label.append(input, swatch, text); grid.appendChild(label);
            });
            fieldset.appendChild(grid); options.appendChild(fieldset);
        });
        function syncTheme() { var value = AdminTheme.get(); options.querySelectorAll('input').forEach(function (input) { input.checked = value[input.dataset.group] === input.value; }); }
        syncTheme(); window.addEventListener('admin-theme-change', syncTheme);
        options.addEventListener('change', function (event) {
            var input = event.target; if (!input.dataset.group) return;
            var value = AdminTheme.get(); value[input.dataset.group] = input.value;
            var saved = AdminTheme.set(value); syncTheme();
            document.getElementById('admin-theme-status').textContent = saved ? '已保存' : '已应用；浏览器不允许保存偏好';
        });
        themeToggle.addEventListener('click', function () { themePanel.showModal(); themeToggle.setAttribute('aria-expanded', 'true'); document.getElementById('admin-theme-close').focus(); });
        document.getElementById('admin-theme-close').addEventListener('click', function () { themePanel.close(); });
        themePanel.addEventListener('close', function () { themeToggle.setAttribute('aria-expanded', 'false'); themeToggle.focus(); });
        themePanel.addEventListener('click', function (event) { if (event.target !== themePanel) return; var rect = themePanel.getBoundingClientRect(); if (event.clientX < rect.left || event.clientX > rect.right || event.clientY < rect.top || event.clientY > rect.bottom) themePanel.close(); });
        document.getElementById('admin-theme-reset').addEventListener('click', function () { var saved = AdminTheme.set(null); syncTheme(); document.getElementById('admin-theme-status').textContent = saved ? '已恢复默认' : '已恢复默认；浏览器不允许保存偏好'; });
    }
    var refresh = document.getElementById('admin-refresh');
    if (refresh) refresh.addEventListener('click', function () { window.location.reload(); });
    var fullscreen = document.getElementById('admin-fullscreen');
    if (fullscreen) {
        fullscreen.hidden = !document.fullscreenEnabled;
        fullscreen.addEventListener('click', async function () {
            try {
                if (document.fullscreenElement) await document.exitFullscreen();
                else await document.documentElement.requestFullscreen();
            } catch (_) { fullscreen.setAttribute('title', '当前浏览器不支持全屏'); }
        });
        document.addEventListener('fullscreenchange', function () {
            var active = Boolean(document.fullscreenElement);
            fullscreen.setAttribute('aria-pressed', String(active));
            fullscreen.setAttribute('aria-label', active ? '退出全屏' : '全屏');
            fullscreen.setAttribute('title', active ? '退出全屏' : '全屏');
        });
    }
    // Retain the server-rendered success message instead of a blocking browser alert.
    var params = new URLSearchParams(window.location.search);
    if (params.get('saved') === '1') {
        if (!document.querySelector('.alert-success')) {
            var notice = document.createElement('div');
            notice.className = 'alert alert-success';
            notice.textContent = '保存成功';
            var content = document.querySelector('.lyear-layout-content .container-fluid');
            if (content) content.prepend(notice);
        }
        params.delete('saved');
        var query = params.toString();
        window.history.replaceState(null, '', window.location.pathname + (query ? '?' + query : '') + window.location.hash);
    }
    document.querySelectorAll('.alert-success, .status-message').forEach(function (notice) { notice.setAttribute('role', 'status'); });
    // Associate existing labels without changing submitted field names or values.
    document.querySelectorAll('.form-group').forEach(function (group, index) {
        var label = group.querySelector('label:not([for])');
        var control = group.querySelector('input:not([type="hidden"]), select, textarea');
        if (!label || !control || group.querySelectorAll('input:not([type="hidden"]), select, textarea').length !== 1) return;
        if (!control.id) control.id = 'admin-field-' + index;
        label.htmlFor = control.id;
    });
    document.querySelectorAll('.lyear-layout-content table').forEach(function (table) {
        if (!table.closest('.table-responsive')) {
            var wrapper = document.createElement('div');
            wrapper.className = 'table-responsive';
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        }
        var wrapper = table.closest('.table-responsive');
        wrapper.tabIndex = 0; wrapper.setAttribute('role', 'region'); wrapper.setAttribute('aria-label', '数据表格，可横向滚动');
        if (table.tBodies.length && !table.tBodies[0].rows.length) {
            var row = table.tBodies[0].insertRow(); var cell = row.insertCell();
            cell.colSpan = table.tHead && table.tHead.rows.length ? table.tHead.rows[0].cells.length : 1;
            cell.className = 'admin-empty'; cell.textContent = '暂无数据';
        }
    });
    var content = document.querySelector('.lyear-layout-content > .container-fluid');
    var breadcrumbTitle = document.querySelector('.admin-breadcrumb strong');
    if (content && breadcrumbTitle && !content.querySelector('h1')) {
        var heading = document.createElement('h1'); heading.className = 'admin-page-title'; heading.textContent = breadcrumbTitle.textContent; content.prepend(heading);
    }
    // Guard native navigation submissions without dropping the submitter's name/value.
    document.addEventListener('submit', function (event) {
        if (event.defaultPrevented || event.target.target === '_blank') return;
        var form = event.target;
        if (form.dataset.adminSubmitting === 'true') { event.preventDefault(); return; }
        form.dataset.adminSubmitting = 'true'; form.setAttribute('aria-busy', 'true');
        form.querySelectorAll('button[type="submit"], button:not([type]), input[type="submit"]').forEach(function (button) { button.setAttribute('aria-disabled', 'true'); });
    });
    window.addEventListener('pageshow', function () {
        document.querySelectorAll('[data-admin-submitting]').forEach(function (form) { delete form.dataset.adminSubmitting; form.removeAttribute('aria-busy'); form.querySelectorAll('[aria-disabled="true"]').forEach(function (button) { button.removeAttribute('aria-disabled'); }); });
    });
});
