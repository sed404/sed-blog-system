<?php
function life_profile_fields(): array
{
    return ['display_name'=>'显示名称', 'avatar'=>'头像地址', 'cover'=>'封面地址', 'tagline'=>'一句话介绍', 'location'=>'所在地', 'bio'=>'我的简介', 'interests'=>'兴趣（用逗号分隔）', 'email'=>'公开联系邮箱', 'website'=>'个人网站'];
}
function life_profile_load(mysqli $conn): array
{
    global $name, $glyname, $glyimg, $logo, $homimg, $sign, $subtitle;
    $profile = ['display_name'=>(string)($glyname??$name??''), 'avatar'=>(string)($glyimg??$logo??''), 'cover'=>(string)($homimg??''), 'tagline'=>(string)($sign??''), 'bio'=>(string)($subtitle??''), 'location'=>'', 'interests'=>'', 'email'=>'', 'website'=>''];
    $result = $conn->query("SELECT text FROM configx WHERE title='life_profile' LIMIT 1");
    if ($result && ($row=$result->fetch_assoc())) {
        $saved=json_decode((string)$row['text'],true);
        if (is_array($saved)) foreach ($profile as $key=>$value) if (isset($saved[$key]) && is_string($saved[$key])) $profile[$key]=$saved[$key];
    }
    return $profile;
}
function life_profile_url_valid(string $url, bool $media=false): bool
{
    if ($url==='') return true;
    if (preg_match('~^https?://~i',$url) && filter_var($url,FILTER_VALIDATE_URL)) return true;
    return $media && preg_match('~^(?:\./|/(?!/))[a-zA-Z0-9_./% -]+$~D',$url)===1;
}
