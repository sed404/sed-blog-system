document.addEventListener('DOMContentLoaded', function () {
    'use strict';
    var editor = document.getElementById('remote-content');
    var form = document.getElementById('remote-file-form');
    var count = document.getElementById('remote-byte-count');
    function measure() {
        var bytes = new TextEncoder().encode(editor.value).length;
        count.textContent = bytes.toLocaleString() + ' / 1,048,576 字节';
        editor.setCustomValidity(bytes > 1048576 ? '文件内容不能超过 1 MB' : '');
    }
    if (editor && form) {
        measure();
        editor.addEventListener('input', measure);
    }
    document.querySelectorAll('[data-remote-url]').forEach(function (link) { link.textContent = link.href; });
    document.querySelectorAll('[data-copy-url]').forEach(function (button) {
        button.addEventListener('click', async function () {
            var address = new URL(button.dataset.copyUrl, location.origin).href;
            var status = document.getElementById('remote-copy-status');
            try {
                if (!navigator.clipboard || !window.isSecureContext) throw new Error('clipboard unavailable');
                await navigator.clipboard.writeText(address);
                status.textContent = '读取地址已复制';
            } catch (_) {
                status.textContent = '请复制此读取地址：' + address;
            }
        });
    });
});
