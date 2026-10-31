<?php

require_once __DIR__ . '/life-navigation.php';

function life_page_start(string $title, string $description = ''): void
{
    global $name, $subtitle, $icon, $iconurl_url;
    $siteName = trim((string) ($name ?? '个人数字生活'));
    $pageTitle = $title === '' ? $siteName : $title . ' - ' . $siteName;
    $description = $description !== '' ? $description : (string) ($subtitle ?? $siteName);
    $favicon = (string) ($icon ?? './assets/img/favicon.png');
    global $conn;
    $isDark = (string) ($_COOKIE['dark_theme'] ?? '') === 'dark-theme';
    $themeClass = $isDark ? ' dark-theme' : '';

    echo '<!DOCTYPE html><html class="life-content-document' . $themeClass . '" lang="zh-CN"><head>';
    echo '<meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">';
    echo '<meta name="description" content="' . htmlspecialchars($description, ENT_QUOTES, 'UTF-8') . '">';
    echo '<title>' . htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') . '</title>';
    echo '<link rel="icon" href="' . htmlspecialchars($favicon, ENT_QUOTES, 'UTF-8') . '">';
    echo '<link rel="stylesheet" href="./assets/css/style.css">';
    echo '<link rel="stylesheet" href="' . htmlspecialchars((string)($iconurl_url ?? '//at.alicdn.com/t/c/font_3781624_acf7eqdy5ke.css'), ENT_QUOTES, 'UTF-8') . '">';
    echo '<link rel="stylesheet" href="./assets/css/life.css?v=20260815-15">';
    if ($title === '画廊') {
        echo '<link rel="stylesheet" href="./assets/css/jquery.fancybox.min.css">';
    }
    echo '<link rel="stylesheet" href="./assets/css/life-design.css?v=' . filemtime(__DIR__ . '/../assets/css/life-design.css') . '">';
    echo '<script defer src="./assets/js/life-design.js?v=' . filemtime(__DIR__ . '/../assets/js/life-design.js') . '"></script>';
    echo '</head><body class="life-content-page' . $themeClass . '"><div class="centent">';
    life_render_navigation($conn);
    echo '<main class="life-page">';
}

function life_page_header(string $title, string $intro = '', ?string $backUrl = null): void
{
    global $userdlzt, $user_zh, $glyadmin, $passid, $user_passid;
    $adminUrl = ($title === '画廊' ? './admin/life-gallery.php' : ($title === '网站' ? './admin/life-sites.php' : ''));
    $adminLabel = ($title === '画廊' ? '添加图片' : '添加网站');
    $isAdmin = (string) ($userdlzt ?? '') === '1'
        && (string) ($user_zh ?? '') === (string) ($glyadmin ?? '')
        && hash_equals((string) ($passid ?? ''), (string) ($user_passid ?? ''));
    $headerClass = $backUrl === null ? ' life-page__header--primary' : ' life-page__header--detail';

    echo '<header class="life-page__header' . $headerClass . '">';
    if ($backUrl !== null) {
        echo '<a class="life-page__home" href="' . htmlspecialchars($backUrl, ENT_QUOTES, 'UTF-8') . '" aria-label="返回上一页"><span aria-hidden="true">‹</span></a>';
    }
    echo '<div class="life-page__heading"><h1>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h1>';
    if ($intro !== '') {
        echo '<p>' . htmlspecialchars($intro, ENT_QUOTES, 'UTF-8') . '</p>';
    }
    echo '</div>';
    if ($isAdmin && $adminUrl !== '') {
        echo '<a class="life-page__admin-action" href="' . $adminUrl . '">' . $adminLabel . '</a>';
    }
    $dark = ($_COOKIE['dark_theme'] ?? '') === 'dark-theme';
    echo '<button type="button" class="life-theme-toggle" data-life-theme aria-label="' . ($dark ? '切换白天模式' : '切换暗黑模式') . '"><i class="iconfont ' . ($dark ? 'icon-yueliang' : 'icon-ai250') . '" aria-hidden="true"></i></button></header>';
}

function life_page_end(mysqli $conn, string $currentPage): void
{
    echo '</main>';
    echo '</div></body></html>';
}
