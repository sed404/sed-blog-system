<?php

/** Serialize attempts across PHP workers; cookies cannot reset this IP-based limit. */
function life_password_attempt(string $scope, callable $verify): array
{
    $directory = sys_get_temp_dir() . '/sed-auth-' . hash('sha256', dirname(__DIR__));
    if (!is_dir($directory) && !@mkdir($directory, 0700, true) && !is_dir($directory)) {
        throw new RuntimeException('无法保存登录保护状态');
    }
    $key = hash('sha256', $scope . ':' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    $handle = @fopen($directory . '/' . $key . '.json', 'c+');
    if (!$handle) throw new RuntimeException('无法读取登录保护状态');
    try {
        if (!flock($handle, LOCK_EX)) throw new RuntimeException('无法锁定登录保护状态');
        $state = json_decode(stream_get_contents($handle), true) ?: [];
        $now = time();
        $until = (int) ($state['until'] ?? 0);
        if ($until > $now) return ['success' => false, 'retry' => $until - $now];
        if ($until > 0) $state = [];
        $success = (bool) $verify();
        $failures = $success ? 0 : (int) ($state['failures'] ?? 0) + 1;
        $until = $failures >= 6 ? $now + 600 : 0;
        rewind($handle);
        if (!ftruncate($handle, 0) || fwrite($handle, json_encode(['failures' => $failures, 'until' => $until])) === false || !fflush($handle)) {
            throw new RuntimeException('无法更新登录保护状态');
        }
        return ['success' => $success, 'retry' => max(0, $until - $now)];
    } finally {
        flock($handle, LOCK_UN);
        fclose($handle);
    }
}

function life_check_password(string $scope, callable $verify): array
{
    try { return life_password_attempt($scope, $verify); }
    catch (RuntimeException $error) {
        error_log($error->getMessage());
        http_response_code(503);
        exit('登录保护暂不可用，请稍后重试');
    }
}
