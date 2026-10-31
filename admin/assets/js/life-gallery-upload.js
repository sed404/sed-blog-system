(function () {
    var form = document.getElementById('life-gallery-upload-form');
    var input = document.getElementById('life-gallery-files');
    var status = document.getElementById('life-gallery-upload-status');
    if (!form || !input) return;
    var submitting = false;
    var libraryPromise;

    function loadConverter() {
        if (window.heic2any) return Promise.resolve();
        if (!libraryPromise) libraryPromise = new Promise(function (resolve, reject) {
            var script = document.createElement('script');
            script.src = './assets/js/vendor/heic2any.min.js';
            script.onload = function () { window.heic2any ? resolve() : reject(new Error('转换器不可用')); };
            script.onerror = function () { reject(new Error('转换器加载失败')); };
            document.head.appendChild(script);
        });
        return libraryPromise;
    }

    form.addEventListener('submit', function (event) {
        if (submitting) return;
        var files = Array.prototype.slice.call(input.files || []);
        var heicFiles = files.filter(function (file) { return /\.(heic|heif)$/i.test(file.name) || /image\/(heic|heif)/i.test(file.type); });
        if (!heicFiles.length) return;
        event.preventDefault();
        var button = form.querySelector('button[type="submit"]');
        button.disabled = true;
        status.textContent = '正在转换 ' + heicFiles.length + ' 张 HEIC/HEIF 图片，请稍候…';
        loadConverter().then(async function () {
            var transfer = new DataTransfer();
            for (var index = 0; index < files.length; index++) {
                var file = files[index];
                if (!/\.(heic|heif)$/i.test(file.name) && !/image\/(heic|heif)/i.test(file.type)) {
                    transfer.items.add(file);
                    continue;
                }
                status.textContent = '正在转换第 ' + (index + 1) + ' / ' + files.length + ' 个文件…';
                var converted = await window.heic2any({ blob: file, toType: 'image/jpeg', quality: 0.85 });
                if (Array.isArray(converted)) converted = converted[0];
                if (!(converted instanceof Blob)) throw new Error('HEIC 转换结果无效');
                transfer.items.add(new File([converted], file.name.replace(/\.(heic|heif)$/i, '') + '.jpg', { type: 'image/jpeg' }));
            }
            input.files = transfer.files;
            status.textContent = '转换完成，正在上传…';
            submitting = true;
            button.disabled = false;
            form.requestSubmit();
        }).catch(function () {
            // The server can still convert HEIC when Imagick and libheif are available.
            status.textContent = '浏览器转换失败，正在尝试服务器转换…';
            submitting = true;
            button.disabled = false;
            form.requestSubmit();
        }).finally(function () { button.disabled = false; });
    });
})();
