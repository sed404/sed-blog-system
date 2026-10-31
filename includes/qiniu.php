<?php

function qiniu_settings(mysqli $conn): array
{
    $settings = [
        'accessKey' => '',
        'secretKey' => '',
        'bucket' => '',
        'domain' => '',
        'uploadUrl' => 'https://upload.qiniup.com',
    ];
    $stmt = $conn->prepare("SELECT text FROM configx WHERE title='qiniu' LIMIT 1");
    if ($stmt) {
        $stmt->execute();
        $stmt->bind_result($text);
        if ($stmt->fetch()) {
            $decoded = json_decode((string) $text, true);
            if (is_array($decoded)) {
                $settings = array_merge($settings, $decoded);
            }
        }
        $stmt->close();
    }
    foreach ($settings as $key => $value) {
        $settings[$key] = trim((string) $value);
    }
    $settings['domain'] = rtrim($settings['domain'], '/');
    $settings['uploadUrl'] = rtrim($settings['uploadUrl'], '/');
    return $settings;
}

function qiniu_base64_urlsafe(string $value): string
{
    // Qiniu's upload-token format uses padded URL-safe Base64. Removing the
    // trailing "=" changes the signed token and causes the upload API to
    // reject it with HTTP 401 (bad token).
    return strtr(base64_encode($value), '+/', '-_');
}

function qiniu_upload_token(array $settings, string $key, int $expires = 3600): string
{
    $deadline = time() + max(60, $expires);
    $policy = json_encode([
        'scope' => $settings['bucket'] . ':' . $key,
        'deadline' => $deadline,
        'insertOnly' => 1,
    ], JSON_UNESCAPED_SLASHES);
    $encodedPolicy = qiniu_base64_urlsafe((string) $policy);
    $sign = hash_hmac('sha1', $encodedPolicy, $settings['secretKey'], true);
    return $settings['accessKey'] . ':' . qiniu_base64_urlsafe($sign) . ':' . $encodedPolicy;
}

function qiniu_object_key(string $username, string $extension = 'bin'): string
{
    $safeUser = preg_replace('/[^a-zA-Z0-9_-]+/', '-', $username) ?: 'user';
    $safeExtension = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', $extension));
    if (!in_array($safeExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
        $safeExtension = 'bin';
    }
    return 'articles/' . $safeUser . '/' . gmdate('Y/m/d') . '/' . bin2hex(random_bytes(12)) . '.' . $safeExtension;
}

function qiniu_object_url(array $settings, string $key): string
{
    return rtrim($settings['domain'], '/') . '/' . str_replace('%2F', '/', rawurlencode($key));
}

function qiniu_key_matches_domain(string $url, array $settings): bool
{
    $domain = rtrim($settings['domain'], '/');
    return $domain !== '' && strncmp(rtrim($url, '/'), $domain . '/', strlen($domain) + 1) === 0;
}

function qiniu_valid_settings(array $settings): bool
{
    return $settings['accessKey'] !== '' && $settings['secretKey'] !== '' && $settings['bucket'] !== '' && qiniu_valid_url($settings['domain']) && qiniu_valid_url($settings['uploadUrl']);
}

function qiniu_valid_url(string $url): bool
{
    $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
    return filter_var($url, FILTER_VALIDATE_URL) !== false && in_array($scheme, ['http', 'https'], true);
}
