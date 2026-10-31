<?php
/** Shared presentation only. Authentication stays in each existing controller. */
function admin_ui_brand(): string
{
    return 'Seb朋友圈系统';
}

function admin_ui_escape($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function admin_ui_icon(string $name): string
{
    $paths = [
        'home' => '<path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1z"/><path d="M9 21v-8h6v8"/>',
        'file' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M8 13h8M8 17h6"/>',
        'settings' => '<path d="M4 5h16M4 12h16M4 19h16"/><circle cx="8" cy="5" r="2"/><circle cx="16" cy="12" r="2"/><circle cx="10" cy="19" r="2"/>',
        'users' => '<circle cx="9" cy="8" r="3"/><path d="M3 21v-3a6 6 0 0 1 12 0v3M16 5a3 3 0 0 1 0 6M21 21v-3a6 6 0 0 0-3-5"/>',
        'check' => '<rect x="4" y="3" width="16" height="18" rx="2"/><path d="m8 12 3 3 5-6"/>',
        'grid' => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>',
        'folder' => '<path d="M3 7V4h6l2 3h10v13H3z"/>',
        'menu' => '<path d="M4 5h16M4 12h16M4 19h16"/>',
        'refresh' => '<path d="M20 7v5h-5M4 17v-5h5"/><path d="M6 6a8 8 0 0 1 14 6M18 18a8 8 0 0 1-14-6"/>',
        'expand' => '<path d="M8 3H3v5M16 3h5v5M21 16v5h-5M8 21H3v-5"/>',
        'arrow' => '<path d="m9 5 7 7-7 7"/>',
    ];
    return '<svg class="admin-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ($paths[$name] ?? $paths['file']) . '</svg>';
}

function admin_ui_assets(): void
{
    $base = __DIR__ . '/../admin/assets/';
    echo '<script src="./assets/js/admin-theme.js?v=' . filemtime($base . 'js/admin-theme.js') . '"></script>';
    echo '<link rel="stylesheet" href="./assets/css/admin-ui.css?v=' . filemtime($base . 'css/admin-ui.css') . '">';
    echo '<link rel="stylesheet" href="./assets/css/admin-theme.css?v=' . filemtime($base . 'css/admin-theme.css') . '">';
    echo '<script defer src="./assets/js/admin-ui.js?v=' . filemtime($base . 'js/admin-ui.js') . '"></script>';
}

function admin_ui_theme_button(): void
{
    echo '<button type="button" class="admin-icon-button" id="admin-theme-toggle" aria-label="主题设置" title="主题设置" aria-haspopup="dialog" aria-expanded="false" aria-controls="admin-theme-panel">' . admin_ui_icon('settings') . '</button>';
}

function admin_ui_theme_panel(): void
{
    echo '<dialog class="admin-theme-panel" id="admin-theme-panel" aria-labelledby="admin-theme-title"><div class="admin-theme-heading"><h2 id="admin-theme-title">主题设置</h2><button type="button" class="admin-icon-button" id="admin-theme-close" aria-label="关闭主题设置">×</button></div><p class="admin-theme-hint">各区域可独立配色，仅保存到当前浏览器。</p><div id="admin-theme-options"></div><div class="admin-theme-footer"><button type="button" class="btn btn-secondary" id="admin-theme-reset">恢复默认</button><span class="admin-theme-hint" id="admin-theme-status" role="status"></span></div></dialog>';
}

function admin_ui_shell(): void
{
    global $conn, $name, $user_name, $user_img;
    $current = basename((string) ($_SERVER['SCRIPT_NAME'] ?? 'index.php'));
    $groups = [
        ['工作台', 'home', [['index.php', '后台首页']]],
        ['后端管理', 'grid', [['remote-files.php', '后端管理']]],
        ['内容管理', 'file', [['life-gallery.php', '画廊管理'], ['life-sites.php', '网站管理'], ['life-planner.php', '备忘管理']]],
        ['关于设置', 'settings', [['life-profile.php', '关于设置']]],
        ['用户管理', 'users', [['userlist.php', '用户列表']]],
        ['审核中心', 'check', [['audites.php', '文章审核'], ['auditco.php', '评论审核']]],
        ['系统设置', 'settings', [['basic.php', '基本设置'], ['authority.php', '权限设置'], ['imgset.php', '图像设置'], ['emailset.php', '邮箱设置'], ]],
        ['资源管理', 'folder', [['rm.php', '资源列表']]],
    ];
    $counts = [];
    if (isset($conn)) {
        foreach (['audites.php' => "SELECT COUNT(*) AS total FROM essay WHERE ptpaud='0'", 'auditco.php' => "SELECT COUNT(*) AS total FROM comm WHERE comaud='0'"] as $key => $sql) {
            $result = $conn->query($sql);
            $counts[$key] = $result ? (int) ($result->fetch_assoc()['total'] ?? 0) : 0;
        }
    }
    $title = '后台管理';
    $section = '管理中心';
    echo '<div class="admin-drawer-backdrop" hidden></div><aside class="lyear-layout-sidebar" id="admin-sidebar" tabindex="-1" aria-label="主导航"><div class="sidebar-header"><a href="./index.php" class="admin-brand" title="后台首页"><span class="admin-brand-mark">' . admin_ui_icon('grid') . '</span><span class="admin-brand-text">' . admin_ui_escape(admin_ui_brand()) . '</span></a><button type="button" class="admin-icon-button admin-drawer-close" aria-label="关闭导航">×</button></div><div class="lyear-layout-sidebar-info"><nav class="sidebar-main"><ul class="nav-drawer">';
    foreach ($groups as [$label, $icon, $items]) {
        $active = in_array($current, array_column($items, 0), true);
        foreach ($items as [$url, $text]) {
            if ($url === $current) { $title = $text; $section = $label; }
        }
        if (count($items) === 1) {
            [$url, $text] = $items[0];
            echo '<li class="nav-item' . ($active ? ' active' : '') . '"><a href="./' . $url . '" title="' . $text . '"' . ($active ? ' aria-current="page"' : '') . '>' . admin_ui_icon($icon) . '<span>' . $text . '</span></a></li>';
            continue;
        }
        $id = 'admin-group-' . $icon;
        echo '<li class="nav-item nav-item-has-subnav' . ($active ? ' open' : '') . '"><button type="button" class="admin-nav-toggle" aria-expanded="' . ($active ? 'true' : 'false') . '" aria-controls="' . $id . '" title="' . $label . '">' . admin_ui_icon($icon) . '<span>' . $label . '</span>' . admin_ui_icon('arrow') . '</button><ul class="nav nav-subnav" id="' . $id . '">';
        foreach ($items as [$url, $text]) {
            $selected = $url === $current;
            echo '<li class="' . ($selected ? 'active' : '') . '"><a href="./' . $url . '"' . ($selected ? ' aria-current="page"' : '') . '>' . $text;
            if (!empty($counts[$url])) echo '<span class="badge badge-danger">' . $counts[$url] . '</span>';
            echo '</a></li>';
        }
        echo '</ul></li>';
    }
    echo '</ul></nav><div class="admin-sidebar-footer">内容与系统管理</div></div></aside>';
    $avatar = (string) ($user_img ?? '');
    if (str_starts_with($avatar, './')) $avatar = '.' . $avatar;
    $safeAvatar = preg_match('~^(https?://|\./|\.\./|/(?!/))~i', $avatar);
    ?>
    <header class="lyear-layout-header">
      <nav class="navbar" aria-label="顶部导航">
        <div class="navbar-left">
          <button type="button" class="admin-icon-button" id="admin-sidebar-toggle" aria-controls="admin-sidebar" aria-label="折叠侧栏" aria-expanded="true"><?php echo admin_ui_icon('menu'); ?></button>
          <nav class="admin-breadcrumb" aria-label="面包屑"><a href="./index.php">管理中心</a><?php if ($section !== $title): ?><span>/</span><span><?php echo admin_ui_escape($section); ?></span><?php endif; ?><span>/</span><strong><?php echo admin_ui_escape($title); ?></strong></nav>
        </div>
        <div class="admin-header-actions">
          <a class="admin-front-link" href="../index.php">返回前台</a>
          <?php admin_ui_theme_button(); ?>
          <button type="button" class="admin-icon-button" id="admin-refresh" title="刷新页面" aria-label="刷新页面"><?php echo admin_ui_icon('refresh'); ?></button>
          <button type="button" class="admin-icon-button" id="admin-fullscreen" title="全屏" aria-label="全屏" aria-pressed="false"><?php echo admin_ui_icon('expand'); ?></button>
          <div class="dropdown admin-user">
            <button class="admin-user-button dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
              <?php if ($safeAvatar): ?><img class="admin-avatar" src="<?php echo admin_ui_escape($avatar); ?>" alt=""><?php else: ?><span class="admin-avatar admin-avatar-placeholder"><?php echo admin_ui_icon('users'); ?></span><?php endif; ?>
              <span><?php echo admin_ui_escape($user_name ?: '管理员'); ?></span>
            </button>
            <div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" href="../setup.php">个人设置</a><a class="dropdown-item" href="../edit.php">发布动态</a><div class="dropdown-divider"></div><a class="dropdown-item" href="./logout.php">退出登录</a></div>
          </div>
        </div>
      </nav>
    </header>
    <?php admin_ui_theme_panel(); ?>
    <?php
}
