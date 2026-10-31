<?php

function life_default_navigation(): array
{
    return [
        ['name' => '首页', 'icon' => 'mdi-home-outline', 'url' => './index.php', 'target' => '_self'],
        ['name' => '画廊', 'icon' => 'mdi-image-outline', 'url' => './gallery.php', 'target' => '_self'],
        ['name' => '网站', 'icon' => 'mdi-link-variant', 'url' => './links.php', 'target' => '_self'],
        ['name' => '关于', 'icon' => 'mdi-account-outline', 'url' => './about.php', 'target' => '_self'],
    ];
}

function life_navigation_items(mysqli $conn): array
{
    $table = $conn->query("SHOW TABLES LIKE 'life_navigation'");
    if (!$table || $table->num_rows === 0) {
        return life_default_navigation();
    }

    $result = $conn->query(
        "SELECT name, icon, url, target FROM life_navigation " .
        "WHERE is_enabled = 1 ORDER BY sort_order ASC, id ASC"
    );
    if (!$result) {
        return life_default_navigation();
    }

    $items = [];
    while ($row = $result->fetch_assoc()) {
        if (life_normalize_path((string) ($row['url'] ?? '')) === 'about.php') {
            $row['name'] = '关于';
            $row['icon'] = 'mdi-account-outline';
        }
        $items[] = $row;
    }

    return $items ?: life_default_navigation();
}

function life_normalize_path(string $path): string
{
    $path = basename(parse_url($path, PHP_URL_PATH) ?: 'index.php');
    return $path === '' ? 'index.php' : $path;
}

function life_render_navigation(mysqli $conn, string $currentPage = ''): void
{
    static $rendered = false;
    if ($rendered) return;
    $rendered = true;
    global $lifeConfig;
    $snowEnabled = $lifeConfig['snow_enabled'] ?? '0';
    if (!isset($lifeConfig)) {
        $snowResult = $conn->query("SELECT text FROM configx WHERE title='snow_enabled' LIMIT 1");
        if ($snowResult && ($snow = $snowResult->fetch_assoc())) $snowEnabled = (string) $snow['text'];
    }
    if ($snowEnabled === '1') {
        echo '<link rel="stylesheet" href="./assets/css/life-snow.css?v=' . filemtime(__DIR__ . '/../assets/css/life-snow.css') . '">';
    }
    $currentPage = $currentPage !== ''
        ? life_normalize_path($currentPage)
        : life_normalize_path($_SERVER['SCRIPT_NAME'] ?? 'index.php');
    $items = life_navigation_items($conn);
    $pageIcons = [
        'index.php' => 'mdi-home-outline',
        'home.php' => 'mdi-home-outline',
        'gallery.php' => 'mdi-image-outline',
        'links.php' => 'mdi-link-variant',
        'about.php' => 'mdi-account-outline',
    ];

    echo '<link rel="stylesheet" href="./assets/css/life-navigation.css?v=' . filemtime(__DIR__ . '/../assets/css/life-navigation.css') . '">';
    echo '<nav class="life-floating-nav" aria-label="主要栏目"><div class="life-floating-nav__inner"><a class="life-nav-brand" href="./index.php" aria-label="Life 首页">Life</a>';
    foreach ($items as $item) {
        $name = htmlspecialchars((string) ($item['name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $url = (string) ($item['url'] ?? './index.php');
        $normalizedUrl = life_normalize_path($url);
        $storedIcon = trim((string) ($item['icon'] ?? ''));
        $iconClass = $pageIcons[$normalizedUrl] ?? (preg_match('/^mdi-[a-z0-9-]+$/', $storedIcon) ? $storedIcon : 'mdi-circle-outline');
        $target = ($item['target'] ?? '_self') === '_blank' ? '_blank' : '_self';
        $active = $normalizedUrl === $currentPage;
        $class = $active ? ' life-floating-nav__item--active' : '';
        $rel = $target === '_blank' ? ' rel="noopener noreferrer"' : '';

        echo '<a class="life-floating-nav__item' . $class . '" href="' .
            htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" target="' . $target . '"' . $rel . ($active ? ' aria-current="page"' : '') . '>';
        echo '<span class="life-floating-nav__icon mdi ' . htmlspecialchars($iconClass, ENT_QUOTES, 'UTF-8') . '" aria-hidden="true"></span>';
        echo '<span class="life-floating-nav__label">' . $name . '</span></a>';
    }
    echo '</div></nav><link rel="stylesheet" href="./admin/assets/css/materialdesignicons.min.css"><script src="./assets/js/life-nav.js"></script>';
}

function life_render_site_navigation(string $currentPage = ''): void
{
    global $servername, $username, $password, $dbname;
    $navConn = new mysqli($servername, $username, $password, $dbname);
    if ($navConn->connect_error) return;
    $navConn->set_charset('utf8mb4');
    life_render_navigation($navConn, $currentPage);
    $navConn->close();
}

