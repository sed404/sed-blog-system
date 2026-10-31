<?php
$iteace = '0';
require __DIR__ . '/config.php';
require __DIR__ . '/api/wz.php';
require_once __DIR__ . '/includes/life-page.php';
require_once __DIR__ . '/includes/life-schema.php';

life_ensure_personal_tools_schema($conn);

$sites = [];
$categories = [];
$result = $conn->query('SELECT id, urls, url, description, category FROM `link` ORDER BY category ASC, id DESC');
while ($result && $row = $result->fetch_assoc()) {
    $row['category'] = trim((string) $row['category']) ?: '常用';
    $sites[] = $row;
    $categories[(string) $row['category']] = true;
}

life_page_start('网站', '收藏常用网站');
life_page_header('网站', '把常去的网站按分类收好，需要时一键打开。');
echo '<div class="life-search"><input type="search" data-site-search placeholder="搜索网站名称或简介" aria-label="搜索网站名称或简介"></div>';

if ($categories) {
    echo '<nav class="life-category-filter" aria-label="网站分类"><button class="is-active" data-site-filter="all">全部</button>';
    foreach (array_keys($categories) as $category) {
        echo '<button data-site-filter="' . htmlspecialchars($category, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($category, ENT_QUOTES, 'UTF-8') . '</button>';
    }
    echo '</nav>';
}

if (!$sites) {
    echo '<div class="life-empty life-empty--sites"><span class="mdi mdi-link-variant" aria-hidden="true"></span><strong>还没有收藏网站</strong><p>在后台的网站管理中添加后，会显示在这里。</p></div>';
} else {
    echo '<div class="life-site-list">';
    $lastCategory = null;
    foreach ($sites as $site) {
        $nameValue = trim((string) $site['urls']);
        $urlValue = trim((string) $site['url']);
        $descriptionValue = trim((string) $site['description']);
        $categoryValue = trim((string) $site['category']) ?: '常用';
        $host = (string) (parse_url($urlValue, PHP_URL_HOST) ?: $urlValue);
        $host = preg_replace('/^www\./i', '', $host);
        if ($lastCategory !== $categoryValue) {
            echo '<h2 class="life-site-heading" data-site-heading="' . htmlspecialchars($categoryValue, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($categoryValue, ENT_QUOTES, 'UTF-8') . '</h2>';
            $lastCategory = $categoryValue;
        }

        echo '<article class="life-site-item" data-site-category="' . htmlspecialchars($categoryValue, ENT_QUOTES, 'UTF-8') . '">';
        echo '<a class="life-site-item__link" href="' . htmlspecialchars($urlValue, ENT_QUOTES, 'UTF-8') . '" target="_blank" rel="noopener noreferrer">';
        echo '<div class="life-site-item__top">';
        echo '<span class="life-site-item__identity"><strong>' . htmlspecialchars($nameValue, ENT_QUOTES, 'UTF-8') . '</strong><small>' . htmlspecialchars($categoryValue, ENT_QUOTES, 'UTF-8') . '</small></span>';
        echo '<span class="life-site-item__open mdi mdi-open-in-new" aria-hidden="true"></span></div>';
        echo '<p>' . htmlspecialchars($descriptionValue !== '' ? $descriptionValue : '暂无简介', ENT_QUOTES, 'UTF-8') . '</p>';
        echo '<span class="life-site-item__domain">' . htmlspecialchars($host, ENT_QUOTES, 'UTF-8') . '</span>';
        echo '</a></article>';
    }
    echo '</div>';
}

echo '<div class="life-empty" data-site-empty hidden>没有找到符合条件的网站。</div>';
life_page_end($conn, 'links.php');
