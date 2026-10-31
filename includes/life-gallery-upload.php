<?php
require_once __DIR__ . '/life-image.php';
require_once __DIR__ . '/qiniu.php';

function life_gallery_is_heic(string $path): bool
{
    $handle = @fopen($path, 'rb');
    if (!$handle) return false;
    try { $header = fread($handle, 12); } finally { fclose($handle); }
    return strlen((string) $header) === 12
        && substr($header, 4, 4) === 'ftyp'
        && in_array(substr($header, 8, 4), ['heic','heix','heim','heis','hevc','hevx','mif1','msf1'], true);
}

function life_gallery_convert_heic(string $path): string
{
    if (!class_exists('Imagick')) throw new RuntimeException('HEIC 图片需要服务器安装 Imagick 和 libheif，请联系管理员');
    $target = tempnam(sys_get_temp_dir(), 'life-heic-');
    if ($target === false) throw new RuntimeException('无法创建 HEIC 转换文件');
    try {
        $image = new Imagick();
        $image->readImage($path . '[0]');
        if (method_exists($image, 'autoOrientImage')) $image->autoOrientImage();
        $image->setImageFormat('jpeg');
        $image->setImageCompressionQuality(85);
        $image->stripImage();
        $blob = $image->getImageBlob();
        $image->clear();
        if ($blob === '' || strlen($blob) > 8 * 1024 * 1024 || file_put_contents($target, $blob) === false || @getimagesize($target) === false) {
            throw new RuntimeException('HEIC 转换后的 JPEG 超过 8MB 或无效');
        }
        return $target;
    } catch (Throwable $error) {
        @unlink($target);
        if ($error instanceof RuntimeException) throw $error;
        throw new RuntimeException('HEIC 转换失败，请确认服务器的 Imagick 已启用 libheif');
    }
}

function life_gallery_media_info(string $path): array
{
    if (life_gallery_is_heic($path)) return ['type'=>'image','mime'=>'image/heic','extension'=>'heic','limit'=>8*1024*1024];
    $info = @getimagesize($path);
    $mime = $info ? ($info['mime'] ?? '') : '';
    if ($mime === '' && function_exists('finfo_open')) {
        $f = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $f ? finfo_file($f, $path) : '';
        if ($f) finfo_close($f);
    }
    $images = ['image/jpeg'=>'jpg','image/png'=>'png','image/gif'=>'gif','image/webp'=>'webp'];
    $videos = ['video/mp4'=>'mp4','video/webm'=>'webm','video/ogg'=>'ogv','video/quicktime'=>'mov'];
    if ($info && isset($images[$mime])) return ['type'=>'image','mime'=>$mime,'extension'=>$images[$mime],'limit'=>8*1024*1024];
    if (isset($videos[$mime])) return ['type'=>'video','mime'=>$mime,'extension'=>$videos[$mime],'limit'=>100*1024*1024];
    throw new RuntimeException('请选择 JPEG、PNG、GIF、WebP、HEIC 图片或 MP4、WebM、OGG、MOV 视频');
}

function life_gallery_taken_at(string $value): string
{
    if (trim($value) === '') return date('Y-m-d H:i:s');
    $value = str_replace('T', ' ', trim($value));
    if (strlen($value) === 16) $value .= ':00';
    $date = DateTimeImmutable::createFromFormat('!Y-m-d H:i:s', $value);
    if (!$date || $date->format('Y-m-d H:i:s') !== $value) throw new RuntimeException('拍摄时间格式不正确');
    return $value;
}

function life_gallery_upload(mysqli $conn, array $file, string $source): array
{
    if (!in_array($source, ['local','qiniu'], true)) throw new RuntimeException('请选择本地或七牛云');
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) throw new RuntimeException('请选择文件，并确认文件未超过服务器上传限制');
    $path = (string)($file['tmp_name'] ?? '');
    if (!is_uploaded_file($path)) throw new RuntimeException('上传文件无效');
    $info = life_gallery_media_info($path);
    $size = filesize($path);
    if (!$size || $size > $info['limit']) throw new RuntimeException('图片限 8MB，视频限 100MB');
    $converted = null;
    if ($info['extension'] === 'heic') {
        $converted = life_gallery_convert_heic($path);
        $path = $converted;
        $file['tmp_name'] = $path;
        $info = life_gallery_media_info($path);
    }
    $file['size'] = filesize($path);
    try {
        if ($source === 'local') {
            $url = $info['type'] === 'image' ? life_store_uploaded_image($file, 'gallery') : life_store_uploaded_media($file, 'gallery');
            return ['url'=>$url, 'source'=>'local', 'type'=>$info['type']];
        }
        $settings = qiniu_settings($conn);
        if (!qiniu_valid_settings($settings)) throw new RuntimeException('请先在后台图像设置中完成七牛云配置，或选择本地上传');
        if (!function_exists('curl_init')) throw new RuntimeException('七牛云上传需要服务器启用 cURL 扩展');
        $key = 'gallery/' . date('Y/m/d') . '/' . bin2hex(random_bytes(16)) . '.' . $info['extension'];
        $curl = curl_init($settings['uploadUrl']);
        curl_setopt_array($curl, [CURLOPT_POST=>true, CURLOPT_RETURNTRANSFER=>true, CURLOPT_CONNECTTIMEOUT=>15, CURLOPT_TIMEOUT=>180,
            CURLOPT_POSTFIELDS=>['token'=>qiniu_upload_token($settings,$key), 'key'=>$key, 'file'=>new CURLFile($path,$info['mime'],basename($key))]]);
        $response = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $result = is_string($response) ? json_decode($response,true) : null;
        if ($code < 200 || $code >= 300 || !is_array($result) || ($result['key'] ?? '') !== $key) throw new RuntimeException('七牛云上传失败，请检查空间、上传区域和密钥配置后重试');
        return ['url'=>qiniu_object_url($settings,$key), 'source'=>'external', 'type'=>$info['type']];
    } finally {
        if ($converted !== null) @unlink($converted);
    }
}
