<?php

function life_music_playlist(string $config): array
{
    $tracks = [];
    // Legacy entries may be pasted together: song.mp3下一首|https://...
    $config = preg_replace_callback(
        '~(\.(?:mp3|flac|m4a|wav|ogg|aac|opus))([^|/\r\n?&#=]{1,80})\|(?=https?://|//|\./|/)~iu',
        static fn(array $match): string => $match[1] . "\n" . $match[2] . '|',
        $config
    );
    foreach (preg_split('/\R/u', $config) ?: [] as $line) {
        $parts = array_map('trim', explode('|', $line, 2));
        if (count($parts) !== 2 || $parts[1] === '') continue;
        [$name, $url] = $parts;
        $url = html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $url = preg_replace('/\s+/u', '', $url);
        if (ctype_digit($url)) $url = 'https://music.163.com/song/media/outer/url?id=' . $url . '.mp3';
        if (str_starts_with($url, '//')) $url = 'https:' . $url;
        if (str_starts_with($url, 'http://cccimg.com/')) $url = 'https://' . substr($url, 7);
        $scheme = parse_url($url, PHP_URL_SCHEME);
        if ($scheme !== null) {
            if (!in_array(strtolower($scheme), ['http', 'https'], true) || !filter_var($url, FILTER_VALIDATE_URL)) continue;
        } elseif (!preg_match('~^(?:\./|/)?[\pL\pN_\-][\pL\pN_./%?&=+\-#]*$~uD', $url) || str_starts_with($url, '//') || str_contains($url, '..')) {
            continue;
        }
        $tracks[] = ['name' => $name !== '' ? $name : '未命名歌曲', 'url' => $url];
    }
    return $tracks;
}
