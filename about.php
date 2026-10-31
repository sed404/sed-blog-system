<?php
$iteace='0';
require __DIR__.'/config.php';
require __DIR__.'/api/wz.php';
require_once __DIR__.'/includes/life-page.php';
require_once __DIR__.'/includes/life-schema.php';
require_once __DIR__.'/includes/life-profile.php';
require_once __DIR__.'/includes/life-security.php';
$ready=life_ensure_personal_tools_schema($conn);
$profile=life_profile_load($conn);
$escape=static fn($value)=>htmlspecialchars((string)$value,ENT_QUOTES,'UTF-8');
$goals=[]; $memos=[]; $other=[];
$canRecordWeight=(string)($userdlzt??'')==='1' && (string)($user_zh??'')===(string)($glyadmin??'') && hash_equals((string)($passid??''),(string)($user_passid??''));
$weightCsrfToken='';
if ($ready) {
    $result=$conn->query("SELECT * FROM life_planner_items ORDER BY FIELD(status,'in_progress','pending','completed'),updated_at DESC,id DESC");
    while ($result && $row=$result->fetch_assoc()) {
        if ($row['item_type']==='goal' && $row['status']==='in_progress') $goals[]=$row;
        elseif ($row['item_type']==='memo') $memos[]=$row;
        else $other[]=$row;
    }
}
if ($canRecordWeight) foreach ($goals as $goal) {
    if (($goal['template_key']??'standard')==='weight') { $weightCsrfToken=life_csrf_token(); break; }
}
life_page_start('关于我');
echo '<div class="life-profile">';
if ($profile['cover']!=='' && life_profile_url_valid($profile['cover'],true)) echo '<img class="life-profile-cover" src="'.$escape($profile['cover']).'" alt="个人封面">';
else echo '<div class="life-profile-cover"></div>';
echo '<div class="life-profile-identity">';
if ($profile['avatar']!=='' && life_profile_url_valid($profile['avatar'],true)) echo '<img src="'.$escape($profile['avatar']).'" alt="头像">';
else echo '<span class="life-profile-avatar mdi mdi-account-outline" aria-hidden="true"></span>';
echo '<div><h2>'.$escape($profile['display_name']).'</h2><p>'.$escape($profile['tagline']).'</p>';
if ($profile['location']!=='') echo '<p class="life-profile-location"><span class="mdi mdi-map-marker-outline" aria-hidden="true"></span> '.$escape($profile['location']).'</p>';
echo '</div></div><section class="life-section"><h2>我的简介</h2><p class="life-profile-bio">'.$escape($profile['bio']?:'还没有填写简介。').'</p></section>';
echo '<div class="life-planner-panel"><section class="life-section life-section--goals"><h2>正在进行中的目标</h2>';
if (!$goals) echo '<div class="life-empty life-empty--compact">暂时没有进行中的目标。</div>';
foreach ($goals as $goal) {
    $current=max(0,(int)$goal['progress_current']); $total=max(0,(int)$goal['progress_total']);
    echo '<article class="life-goal" id="goal-'.(int)$goal['id'].'"><div class="life-goal__heading"><strong>'.$escape($goal['title']).'</strong><span>'.($total>0?$current.' / '.$total:'进行中').'</span></div>';
    if ($total>0) echo '<div class="life-progress" role="progressbar" aria-label="'.$escape($goal['title']).'" aria-valuemin="0" aria-valuemax="'.$total.'" aria-valuenow="'.min($current,$total).'"><span style="width:'.min(100,round($current/$total*100)).'%"></span></div>';
    if ($goal['details']!=='') echo '<p>'.$escape($goal['details']).'</p>';
    if (($goal['template_key']??'standard')==='weight') {
        $goalId=(int)$goal['id'];
        echo '<div class="life-weight-records">';
        if ($canRecordWeight) {
            if (($_GET['weight_saved']??'')===(string)$goalId) echo '<div class="life-weight-message">体重已记录。</div>';
            if (($_GET['weight_error']??'')===(string)$goalId) echo '<div class="life-weight-message life-weight-message--error">请输入 1 至 2000 斤的体重，最多保留一位小数。</div>';
            echo '<details class="life-weight-entry"><summary>记录体重</summary><form method="post" action="./api/life-goal-weight.php"><input type="hidden" name="csrf_token" value="'.$escape($weightCsrfToken).'"><input type="hidden" name="goal_id" value="'.$goalId.'"><label for="weight-'.$goalId.'">当前体重（斤）</label><div><input id="weight-'.$goalId.'" name="weight_jin" type="number" min="1" max="2000" step="0.1" inputmode="decimal" required><button type="submit">保存记录</button></div></form></details>';
        }
        $weights=$conn->prepare('SELECT weight_jin,recorded_at FROM life_goal_weight_records WHERE goal_id=? ORDER BY recorded_at DESC,id DESC LIMIT 5');
        $weights->bind_param('i',$goalId); $weights->execute(); $weightRows=$weights->get_result();
        echo '<h3>最近五次体重记录</h3>';
        if ($weightRows->num_rows===0) echo '<div class="life-weight-empty">暂无记录</div>';
        else {
            echo '<ol>';
            while ($weight=$weightRows->fetch_assoc()) {
                $recordedAt=(string)$weight['recorded_at'];
                echo '<li><strong>'.$escape(number_format((float)$weight['weight_jin'],1)).' 斤</strong><time datetime="'.$escape(str_replace(' ','T',$recordedAt)).'">'.$escape(substr($recordedAt,0,16)).'</time></li>';
            }
            echo '</ol>';
        }
        $weights->close();
        echo '</div>';
    }
    echo '</article>';
}
echo '</section><section class="life-section life-section--memos"><h2>备忘录</h2>';
if (!$memos) echo '<div class="life-empty life-empty--compact">暂时没有备忘录。</div>';
foreach ($memos as $memo) {
    echo '<article class="life-goal life-memo"><div class="life-goal__heading"><strong>'.$escape($memo['title']).'</strong><span class="life-memo-tag">备忘录</span></div>';
    if ($memo['details']!=='') echo '<p>'.$escape($memo['details']).'</p>';
    echo '<small class="life-memo-status">'.($memo['status']==='completed'?'已完成':'待完成').'</small></article>';
}
echo '</section></div><section class="life-section"><h2>兴趣</h2><div class="life-interest-list">';
$interests=preg_split('/[,，\r\n]+/u',$profile['interests'],-1,PREG_SPLIT_NO_EMPTY);
foreach ($interests as $interest) if (trim($interest)!=='') echo '<span>'.$escape(trim($interest)).'</span>';
if (!$interests) echo '<p class="life-profile-bio">还没有填写兴趣。</p>';
echo '</div></section>';
if ($profile['email']!=='' || $profile['website']!=='') {
    echo '<section class="life-section"><h2>联系我</h2>';
    if (filter_var($profile['email'],FILTER_VALIDATE_EMAIL)) echo '<a class="life-profile-contact" href="mailto:'.$escape($profile['email']).'"><span class="mdi mdi-email-outline" aria-hidden="true"></span>'.$escape($profile['email']).'</a>';
    if ($profile['website']!=='' && life_profile_url_valid($profile['website'])) echo '<a class="life-profile-contact" href="'.$escape($profile['website']).'" target="_blank" rel="noopener noreferrer"><span class="mdi mdi-web" aria-hidden="true"></span>个人网站</a>';
    echo '</section>';
}
if ($other) {
    echo '<section class="life-section"><details><summary>已完成目标（'.count($other).'）</summary>';
    foreach ($other as $item) echo '<article class="life-goal"><strong>'.$escape($item['title']).'</strong> <small>'.($item['status']==='completed'?'已完成':'待完成').'</small><p>'.$escape($item['details']).'</p></article>';
    echo '</details></section>';
}
echo '</div>'; life_page_end($conn,'about.php');
