<?php

function life_encryption_key(): string
{
    return trim((string) getenv('LIFE_ENCRYPTION_KEY'));
}

function life_encrypt_secret(string $plainText): string
{
    if ($plainText === '') return '';
    $keyText = life_encryption_key();
    if ($keyText === '') throw new RuntimeException('服务器尚未配置 LIFE_ENCRYPTION_KEY。');
    $key = hash('sha256', $keyText, true);
    $iv = random_bytes(12);
    $tag = '';
    $cipher = openssl_encrypt($plainText, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
    if ($cipher === false) throw new RuntimeException('API Key 加密失败。');
    return base64_encode($iv . $tag . $cipher);
}

function life_decrypt_secret(string $encrypted): string
{
    if ($encrypted === '') return '';
    $keyText = life_encryption_key();
    if ($keyText === '') throw new RuntimeException('服务器尚未配置 LIFE_ENCRYPTION_KEY。');
    $raw = base64_decode($encrypted, true);
    if ($raw === false || strlen($raw) < 29) throw new RuntimeException('API Key 密文无效。');
    $iv = substr($raw, 0, 12);
    $tag = substr($raw, 12, 16);
    $cipher = substr($raw, 28);
    $plain = openssl_decrypt($cipher, 'aes-256-gcm', hash('sha256', $keyText, true), OPENSSL_RAW_DATA, $iv, $tag);
    if ($plain === false) throw new RuntimeException('API Key 解密失败。');
    return $plain;
}

