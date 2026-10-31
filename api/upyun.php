<?php

// Legacy endpoint retained for old clients. New uploads use Qiniu browser direct upload.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([['code' => '201', 'msg' => '非法请求']], JSON_UNESCAPED_UNICODE);
    exit;
}

// Do not upload to the retired provider or expose its credentials.
echo json_encode([['code' => '410', 'msg' => '旧对象存储已停用，请使用七牛云直传']], JSON_UNESCAPED_UNICODE);
