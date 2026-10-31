<?php
require_once __DIR__ . '/require-login.php';
$iteace = '1';
require_once __DIR__ . '/../config.php';
require __DIR__ . '/../api/wz.php';
require_once __DIR__ . '/../includes/life-admin.php';
life_require_admin();
$hasTable = life_table_exists($conn, 'life_gallery_items');
$csrf = life_csrf_token();
life_admin_start('画廊管理', 'gallery');
if (isset($_SESSION['gallery_upload_result'])) {
    $uploadResult = $_SESSION['gallery_upload_result'];
    unset($_SESSION['gallery_upload_result']);
    echo '<div class="alert ' . ((int)$uploadResult['failed'] > 0 ? 'alert-warning' : 'alert-success') . '">成功上传 ' . (int)$uploadResult['uploaded'] . ' 个文件，失败 ' . (int)$uploadResult['failed'] . ' 个。';
    foreach ($uploadResult['errors'] as $error) echo '<br>' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8');
    echo '</div>';
} elseif (isset($_GET['saved'])) echo '<div class="alert alert-success">画廊已更新。</div>';
if (!$hasTable) { echo '<div class="alert alert-warning">请先初始化数据表。</div>'; life_admin_end(); exit; }
$albums = [];
$albumResult = $conn->query('SELECT * FROM life_gallery_albums ORDER BY sort_order,id');
while ($albumResult && $row = $albumResult->fetch_assoc()) $albums[] = $row;
?>
<div class="row"><div class="col-lg-5"><div class="card"><header class="card-header"><div class="card-title">新建相册</div></header><div class="card-body"><form method="post" action="./api/life-gallery.php"><input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>"><input type="hidden" name="action" value="album_create"><div class="form-group"><label>相册名称</label><input class="form-control" name="name" required></div><div class="form-group"><label>描述</label><textarea class="form-control" name="description"></textarea></div><div class="form-group"><label>排序</label><input class="form-control" type="number" name="sort_order" value="0"></div><button class="btn btn-primary">保存相册</button></form></div></div></div>
<div class="col-lg-7"><div class="card"><header class="card-header"><div class="card-title">添加媒体</div></header><div class="card-body">
<form id="life-gallery-upload-form" method="post" action="./api/life-gallery.php" enctype="multipart/form-data">
<input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>"><input type="hidden" name="action" value="item_create">
<div class="form-group"><label>标题（单张上传可选）</label><input class="form-control" name="title" maxlength="255"><small class="text-muted">批量上传时自动使用各文件名作为标题；画廊列表不显示标题。</small></div>
<div class="form-group"><label>描述</label><textarea class="form-control" name="description" rows="3"></textarea><small class="text-muted">批量上传时这段描述会用于所选文件。</small></div>
<div class="form-row"><div class="form-group col-md-6"><label>来源</label><select class="form-control" name="image_source"><option value="qiniu" selected>七牛云</option><option value="local">本地</option></select></div>
<div class="form-group col-md-6"><label>相册分类</label><select class="form-control" name="album_id"><option value="">未分类</option><?php foreach($albums as $album): ?><option value="<?php echo (int)$album['id']; ?>"><?php echo htmlspecialchars($album['name'],ENT_QUOTES,'UTF-8'); ?></option><?php endforeach; ?></select></div></div>
<div class="form-group"><label>上传文件</label><input id="life-gallery-files" class="form-control" type="file" name="image_files[]" accept=".jpg,.jpeg,.png,.gif,.webp,.heic,.heif,image/heic,image/heif,video/mp4,video/webm,video/ogg,video/quicktime" multiple required><small class="text-muted">每次最多 20 个文件。图片限 8MB，视频限 100MB。HEIC/HEIF 上传前会在浏览器中转为 JPEG。</small><div id="life-gallery-upload-status" class="text-muted mt-2" role="status" aria-live="polite"></div></div>
<div class="form-group"><label>拍摄时间</label><input class="form-control" type="datetime-local" name="taken_at"><small class="text-muted">留空按上传时间保存，也可以自定义。</small></div>
<button class="btn btn-primary" type="submit">上传媒体</button>
</form></div></div></div></div>
<div class="card"><header class="card-header"><div class="card-title">相册</div></header><div class="card-body"><?php if(!$albums): ?>暂无相册<?php endif; ?><?php foreach($albums as $album): ?><div class="d-flex justify-content-between border-bottom py-2"><span><?php echo htmlspecialchars($album['name'],ENT_QUOTES,'UTF-8'); ?></span><form method="post" action="./api/life-gallery.php" onsubmit="return confirm('删除相册后，图片会保留为未分类。确定继续吗？')"><input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>"><input type="hidden" name="action" value="album_delete"><input type="hidden" name="id" value="<?php echo (int)$album['id']; ?>"><button class="btn btn-sm btn-danger">删除</button></form></div><?php endforeach; ?></div></div>
<div class="card"><header class="card-header"><div class="card-title">媒体列表</div></header><div class="card-body table-responsive"><table class="table"><thead><tr><th>预览</th><th>标题</th><th>类型</th><th>相册</th><th>时间</th><th>操作</th></tr></thead><tbody><?php $result=$conn->query('SELECT i.*,a.name album_name FROM life_gallery_items i LEFT JOIN life_gallery_albums a ON a.id=i.album_id WHERE i.deleted_at IS NULL ORDER BY i.sort_order,i.id DESC LIMIT 100'); while($result && $item=$result->fetch_assoc()): ?><tr><td><?php echo $item['media_type']==='video' ? '<video src="'.htmlspecialchars($item['image_url'],ENT_QUOTES,'UTF-8').'" style="width:52px;height:52px;object-fit:cover" muted></video>' : '<img src="'.htmlspecialchars($item['image_url'],ENT_QUOTES,'UTF-8').'" alt="" style="width:52px;height:52px;object-fit:cover;border-radius:4px">'; ?></td><td><?php echo htmlspecialchars($item['title'],ENT_QUOTES,'UTF-8'); ?></td><td><?php echo htmlspecialchars($item['media_type'],ENT_QUOTES,'UTF-8'); ?></td><td><?php echo htmlspecialchars((string)$item['album_name'],ENT_QUOTES,'UTF-8'); ?></td><td><?php echo htmlspecialchars((string)$item['taken_at'],ENT_QUOTES,'UTF-8'); ?></td><td><form method="post" action="./api/life-gallery.php" onsubmit="return confirm('确定删除吗？')"><input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>"><input type="hidden" name="action" value="item_delete"><input type="hidden" name="id" value="<?php echo (int)$item['id']; ?>"><button class="btn btn-sm btn-danger">删除</button></form></td></tr><?php endwhile; ?></tbody></table></div></div>
<script defer src="./assets/js/life-gallery-upload.js?v=<?php echo filemtime(__DIR__ . '/assets/js/life-gallery-upload.js'); ?>"></script>
<?php life_admin_end(); ?>
