<?php

function life_csrf_token(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (empty($_SESSION['life_csrf_token'])) {
        $_SESSION['life_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['life_csrf_token'];
}

function life_verify_csrf(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $provided = (string) ($_POST['csrf_token'] ?? '');
    $expected = (string) ($_SESSION['life_csrf_token'] ?? '');
    if ($provided === '' || $expected === '' || !hash_equals($expected, $provided)) {
        http_response_code(419);
        exit('请求已失效，请刷新页面后重试。');
    }
}

function life_is_safe_navigation_url(string $url): bool
{
    $url = trim($url);
    if ($url === '' || preg_match('/[\x00-\x1F\x7F]/', $url)) {
        return false;
    }
    if (str_starts_with($url, './') || str_starts_with($url, '../') || str_starts_with($url, '/')) {
        return !str_starts_with($url, '//');
    }
    $parts = parse_url($url);
    return is_array($parts) && in_array(strtolower((string) ($parts['scheme'] ?? '')), ['http', 'https'], true);
}

