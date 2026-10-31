<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0,minimum-scale=1.0, user-scalable=no minimal-ui">
    <title>{{ wz_name }} - 邮件通知</title>
    <style>html,body {width: 100%;height: 100%;margin: 0;padding: 0;display: flex;justify-content: center;background: #ecf1f3;}.main {width: 100%;display: flex;justify-content: center;background: #ecf1f3;}.main-con{width: 800px;display: flex;flex-direction: column;align-items: center;background: #ffffff;box-shadow: 0px 0px 5px 2px #f1f1f1;margin-top: 20px;margin-bottom: 20px;}.sh-tu {width: 100%;display: flex;justify-content: center;align-items: center;}.sh-tu-logo {width: 200px;height: 100px;object-fit: contain;}.sh-wz {width: 80%;color: #55798d;}.sh-wz-t {margin-bottom: 10px;margin-left: 2px;font-size: 15px;}.sh-wz-n {width: 100%;display: flex;min-height: 40px;background: #f7f7f7;font-size: 15px;border-radius: 4px;}.sh-wz-n-nr {width: 100%;margin: 10px;}.sh-wz-b {margin-bottom: 10px;margin-left: 2px;font-size: 15px;margin-top: 15px;}.sh-wz-b2 {margin-bottom: 10px;margin-left: 2px;font-size: 15px;margin-top: 50px;}a {text-decoration: none;color: #1e5494;text-decoration: underline;}</style>
</head>
<body>
    <div class="main">
        <div class="main-con">
        <div class="sh-tu">
            <p style="font-size: 22px;font-weight: bold;color: #55798d;">{{ wz_name }}</p>
        </div>
        <div class="sh-wz">
            <div class="sh-wz-t" style="color: #55798d;">{{ title }}</div>
            <div class="sh-wz-n" style="background:#f7f7f7">
                <p class="sh-wz-n-nr" style="color: #000000;">{{ text }}</p>
            </div>
            <div style="background: #eaf2ff;width: 100%;height: 1px;margin-top: 50px;"></div>
            <div style="font-size: 22px;font-weight: bold;margin-top: 20px;color: #55798d;">{{ wz_name }}</div>
            <div style="margin:20px 0;color: #6a8895;min-height:4.2em;white-space: pre-wrap;">此信为系统邮件，请不要直接回复。</div>
            <div style="margin-bottom: 50px;"><a href="{{ url }}" rel="noopener" target="_blank">访问网站</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>