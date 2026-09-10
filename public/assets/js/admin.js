/* Dernek Sitesi — yönetim paneli etkileşimleri */
(function () {
    'use strict';

    // Silme onayları
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (form && form.matches && form.matches('form[data-confirm]')) {
            if (!window.confirm(form.getAttribute('data-confirm'))) {
                e.preventDefault();
            }
        }
    }, true);

    // Türkçe slug üretici (başlıktan öneri)
    var trMap = { 'ç': 'c', 'ğ': 'g', 'ı': 'i', 'İ': 'i', 'ö': 'o', 'ş': 's', 'ü': 'u' };
    function slugify(text) {
        text = String(text).replace(/[çğıİöşüÇĞÖŞÜ]/g, function (ch) {
            return trMap[ch.toLowerCase()] !== undefined ? trMap[ch.toLowerCase()] : trMap[ch] || ch;
        }).toLowerCase();
        return text.replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
    }

    var title = document.getElementById('f-title');
    var slug = document.getElementById('f-slug');
    if (title && slug) {
        title.addEventListener('input', function () {
            if (slug.dataset.touched === '1') return;
            slug.placeholder = slugify(title.value);
        });
        slug.addEventListener('input', function () {
            slug.dataset.touched = '1';
        });
    }

    // Zengin metin editörü
    var editorArea = document.querySelector('textarea.tinymce');
    if (editorArea && window.tinymce) {
        var csrfMeta = document.querySelector('meta[name="csrf-token"]');
        var appBaseMeta = document.querySelector('meta[name="app-base"]');
        var csrf = csrfMeta ? csrfMeta.content : '';
        var base = appBaseMeta ? appBaseMeta.content : '';

        tinymce.init({
            target: editorArea,
            language: 'tr',
            height: 430,
            menubar: false,
            branding: false,
            plugins: 'lists link image table code fullscreen',
            toolbar: 'undo redo | blocks | bold italic underline | bullist numlist | link image table | code',
            paste_data_images: true,
            automatic_uploads: true,
            images_upload_handler: function (blobInfo) {
                return new Promise(function (resolve, reject) {
                    var fd = new FormData();
                    fd.append('file', blobInfo.blob(), blobInfo.filename());
                    fetch(base + '/admin/upload', {
                        method: 'POST',
                        headers: { 'X-CSRF-Token': csrf },
                        body: fd
                    })
                        .then(function (r) { return r.json(); })
                        .then(function (data) {
                            if (data.location) resolve(data.location);
                            else reject(data.error || 'Yükleme başarısız.');
                        })
                        .catch(function () { reject('Yükleme hatası oluştu.'); });
                });
            }
        });
    }
})();
