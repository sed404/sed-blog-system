<?php
$iteace = '0';
require __DIR__ . '/config.php';
require __DIR__ . '/api/wz.php';
require_once __DIR__ . '/includes/life-page.php';

life_page_start('画廊', '照片与视觉记忆');
life_page_header('画廊', '照片、片段和被记住的瞬间。');
$table = $conn->query("SHOW TABLES LIKE 'life_gallery_items'");
if (!$table || $table->num_rows === 0) {
    echo '<div class="life-empty">画廊已准备好。执行数据库升级脚本后，即可从后台添加照片。</div>';
} else {
    $albumId = filter_input(INPUT_GET, 'album', FILTER_VALIDATE_INT) ?: 0;
    $page = max(1, (int) ($_GET['page'] ?? 1)); $pageSize = 10; $offset = ($page - 1) * $pageSize;
    $albums = $conn->query('SELECT id,name FROM life_gallery_albums WHERE is_visible=1 ORDER BY sort_order,id');
    if ($albums && $albums->num_rows > 0) {
        echo '<nav class="life-filter"><a class="' . ($albumId === 0 ? 'is-active' : '') . '" href="./gallery.php">全部</a>';
        while ($album = $albums->fetch_assoc()) {
            echo '<a class="' . ($albumId === (int)$album['id'] ? 'is-active' : '') . '" href="./gallery.php?album=' . (int)$album['id'] . '">' . htmlspecialchars($album['name'], ENT_QUOTES, 'UTF-8') . '</a>';
        }
        echo '</nav>';
    }
    if ($albumId > 0) {
        $stmt = $conn->prepare('SELECT id,title,image_url,description,taken_at,media_type,display_ratio FROM life_gallery_items WHERE album_id=? AND is_visible=1 AND deleted_at IS NULL ORDER BY sort_order,taken_at DESC,id DESC LIMIT ? OFFSET ?');
        $stmt->bind_param('iii',$albumId,$pageSize,$offset); $stmt->execute(); $result=$stmt->get_result();
        $countStmt = $conn->prepare('SELECT COUNT(*) total FROM life_gallery_items WHERE album_id=? AND is_visible=1 AND deleted_at IS NULL'); $countStmt->bind_param('i',$albumId); $countStmt->execute(); $total=(int)$countStmt->get_result()->fetch_assoc()['total']; $countStmt->close();
    } else {
        $stmt = $conn->prepare('SELECT id,title,image_url,description,taken_at,media_type,display_ratio FROM life_gallery_items WHERE is_visible=1 AND deleted_at IS NULL ORDER BY sort_order ASC,taken_at DESC,id DESC LIMIT ? OFFSET ?'); $stmt->bind_param('ii',$pageSize,$offset); $stmt->execute(); $result=$stmt->get_result();
        $countResult=$conn->query('SELECT COUNT(*) total FROM life_gallery_items WHERE is_visible=1 AND deleted_at IS NULL'); $total=(int)$countResult->fetch_assoc()['total'];
    }
    if (!$result || $result->num_rows === 0) {
        echo '<div class="life-empty">画廊里还没有照片。</div>';
    } else {
        echo '<div class="life-gallery-grid">';
        while ($item = $result->fetch_assoc()) {
            $url=htmlspecialchars((string)$item['image_url'],ENT_QUOTES,'UTF-8'); $ratio=(string)($item['display_ratio'] ?? 'auto');
            // Reserve space before lazy media loads, including legacy auto-ratio items.
            if (!preg_match('/^[1-9][0-9]*:[1-9][0-9]*$/D', $ratio)) $ratio='3:4';
            $isVideo=($item['media_type'] ?? 'image')==='video';
            $title=htmlspecialchars((string)$item['title'],ENT_QUOTES,'UTF-8');
            $previewAttributes=' data-fancybox="life-gallery" data-caption=""'.($isVideo ? ' data-type="video"' : '');
            echo '<figure class="life-gallery-item" style="--life-media-ratio:' . ($ratio==='auto'?'auto':str_replace(':',' / ',$ratio)) . '"><a href="' . $url . '"'.$previewAttributes.' aria-label="查看'.($title!==''?$title:'图片').'">';
            echo $isVideo ? '<video preload="metadata" src="'.$url.'" muted playsinline></video>' : '<img class="life-gallery-lazy" data-src="'.$url.'" alt="'.$title.'">'; echo '</a></figure>';
        }
        echo '</div>';
        $pages=max(1,(int)ceil($total/$pageSize)); if($pages>1){echo '<nav class="life-pagination">'; for($n=1;$n<=$pages;$n++){echo '<a class="'.($n===$page?'is-active':''). '" href="./gallery.php?'.http_build_query(['album'=>$albumId?:null,'page'=>$n]).'">'.$n.'</a>';} echo '</nav>';}
    }
}
echo '<script src="./assets/js/life-gallery.js?v=' . filemtime(__DIR__ . '/assets/js/life-gallery.js') . '"></script>';
echo '<script src="./assets/js/jquery.min.js"></script><script src="./assets/js/jquery.fancybox.min.js"></script>';
echo '<script>(function(){var images=[].slice.call(document.querySelectorAll("img.life-gallery-lazy[data-src]"));function load(img){var src=img.getAttribute("data-src");if(!src)return;img.onload=function(){img.classList.add("is-loaded")};img.src=src;img.removeAttribute("data-src")}if(!("IntersectionObserver" in window)){images.forEach(load);return}var observer=new IntersectionObserver(function(entries){entries.forEach(function(entry){if(entry.isIntersecting){observer.unobserve(entry.target);load(entry.target)}})},{rootMargin:"300px 0px",threshold:0.01});images.forEach(function(img){observer.observe(img)});window.addEventListener("load",function(){images.forEach(function(img){if(img.hasAttribute("data-src")){var rect=img.getBoundingClientRect();if(rect.top<window.innerHeight+300&&rect.bottom>-300)load(img)}})})})();</script>';
life_page_end($conn, 'gallery.php');
