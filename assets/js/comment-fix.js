(function () {
    'use strict';

    function notify(type, message) {
        var handler = window[type + 'pop'];
        if (typeof handler === 'function') {
            handler(message, type === 'load' ? 'ok' : undefined);
            return;
        }
        if (type !== 'load') window.alert(message);
    }

    window.fasong = function () {
        var contentInput = document.getElementById('bletext');
        var articleIdNode = document.getElementById('sh-tieid');
        var replyNameNode = document.getElementById('sh-tiehf');
        var replyAccountNode = document.getElementById('sh-tieea');

        if (!contentInput || !articleIdNode || !replyNameNode || !replyAccountNode) {
            notify('error', '\u8bc4\u8bba\u7ec4\u4ef6\u52a0\u8f7d\u5f02\u5e38\uff0c\u8bf7\u5237\u65b0\u9875\u9762\u540e\u91cd\u8bd5');
            return;
        }

        var comment = contentInput.value.trim();
        if (!comment) {
            notify('warn', '\u8bf7\u8f93\u5165\u8bc4\u8bba\u5185\u5bb9');
            return;
        }

        var guestNameInput = document.getElementById('vis_name');
        var guestEmailInput = document.getElementById('vis_email');
        var guestUrlInput = document.getElementById('vis_url');
        var payload = new URLSearchParams({
            tieid: articleIdNode.innerText.trim(),
            tiehf: replyNameNode.innerText.trim(),
            tieea: replyAccountNode.innerText.trim(),
            pltext: comment,
            vis_name: guestNameInput ? guestNameInput.value.trim() : '',
            vis_email: guestEmailInput ? guestEmailInput.value.trim() : '',
            vis_url: guestUrlInput ? guestUrlInput.value.trim() : ''
        });

        notify('load', '\u6b63\u5728\u53d1\u9001\u8bc4\u8bba\uff0c\u8bf7\u7a0d\u540e...');
        var xhr = new XMLHttpRequest();
        xhr.open('POST', './api/fbpl.php', true);
        xhr.withCredentials = true;
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        xhr.onreadystatechange = function () {
            if (xhr.readyState !== 4) return;
            if (xhr.status !== 200) {
                notify('error', '\u8bc4\u8bba\u53d1\u9001\u5931\u8d25\uff0c\u8bf7\u7a0d\u540e\u91cd\u8bd5');
                return;
            }

            var response;
            try {
                response = JSON.parse(xhr.responseText);
            } catch (error) {
                notify('error', '\u8bc4\u8bba\u63a5\u53e3\u8fd4\u56de\u5f02\u5e38\uff0c\u8bf7\u7a0d\u540e\u91cd\u8bd5');
                return;
            }

            var result = response && response[0] ? response[0] : {};
            if (String(result.code) !== '200') {
                notify('warn', result.msg || '\u8bc4\u8bba\u53d1\u9001\u5931\u8d25');
                return;
            }

            contentInput.value = '';
            notify('success', result.msg || '\u8bc4\u8bba\u53d1\u9001\u6210\u529f');
            window.setTimeout(function () { window.location.reload(); }, 500);
        };
        xhr.onerror = function () {
            notify('error', '\u8bc4\u8bba\u53d1\u9001\u5931\u8d25\uff0c\u8bf7\u68c0\u67e5\u7f51\u7edc\u540e\u91cd\u8bd5');
        };
        xhr.send(payload.toString());
    };
})();
