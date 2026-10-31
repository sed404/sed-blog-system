(function () {
    var gallery = document.querySelector('.life-gallery-grid');
    if (!gallery) return;
    var items = Array.prototype.slice.call(gallery.querySelectorAll('.life-gallery-item'));
    var desktop = window.matchMedia('(min-width: 760px)');
    var count = 0;

    function arrange() {
        var nextCount = desktop.matches ? 4 : 2;
        if (nextCount === count) return;
        count = nextCount;
        var columns = [];
        var fragment = document.createDocumentFragment();
        for (var index = 0; index < count; index++) {
            var column = document.createElement('div');
            column.className = 'life-gallery-column';
            columns.push(column);
            fragment.appendChild(column);
        }
        items.forEach(function (item, index) {
            columns[index % count].appendChild(item);
        });
        gallery.textContent = '';
        gallery.appendChild(fragment);
    }

    arrange();
    if (desktop.addEventListener) desktop.addEventListener('change', arrange);
    else desktop.addListener(arrange);

})();
