<?php /** @var array $album */ ?>
<p><a class="text-link" href="<?= url('/admin/albums') ?>">&larr; Tüm albümler</a></p>

<div class="form-panel slim">
    <form class="inline-add" method="post" action="<?= url('/admin/albums/' . $album['id'] . '/update') ?>">
        <?= csrf_field() ?>
        <input type="text" name="title" value="<?= e($album['title']) ?>" required>
        <button class="btn btn-outline" type="submit">Adı Güncelle</button>
    </form>
</div>

<div class="form-panel slim upload-box">
    <form method="post" action="<?= url('/admin/albums/' . $album['id'] ) ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <label class="upload-label" for="f-photos">Fotoğraf yükle (birden fazla seçebilirsiniz)</label>
        <input id="f-photos" type="file" name="photos[]" accept="image/jpeg,image/png,image/webp,image/gif" multiple>
        <button class="btn btn-primary" type="submit">Yüklenenleri Ekle</button>
        <p class="hint">JPG, PNG, WEBP veya GIF — dosya başına en fazla 8 MB.</p>
    </form>
</div>

<?php if ($photos === []): ?>
<p class="empty-note">Bu albümde henüz fotoğraf yok.</p>
<?php else: ?>
<?php $firstId = (int) $photos[0]['id']; $lastId = (int) $photos[count($photos) - 1]['id']; ?>
<div class="photo-grid">
    <?php foreach ($photos as $photo): ?>
    <div class="photo-tile">
        <img src="<?= upload_url($photo['filename']) ?>" alt="<?= e($photo['title'] ?? '') ?>" loading="lazy">
        <div class="photo-actions">
            <form method="post" action="<?= url('/admin/photos/' . $photo['id'] . '/move/up') ?>"><?= csrf_field() ?><button class="icon-btn" title="Sola taşı" <?= (int) $photo['id'] === $firstId ? 'disabled' : '' ?>>&larr;</button></form>
            <form method="post" action="<?= url('/admin/photos/' . $photo['id'] . '/move/down') ?>"><?= csrf_field() ?><button class="icon-btn" title="Sağa taşı" <?= (int) $photo['id'] === $lastId ? 'disabled' : '' ?>>&rarr;</button></form>
            <form method="post" action="<?= url('/admin/photos/' . $photo['id'] . '/delete') ?>" data-confirm="Bu fotoğraf silinsin mi?">
                <?= csrf_field() ?><button class="icon-btn icon-danger" title="Sil">&#10005;</button>
            </form>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="danger-zone">
    <form method="post" action="<?= url('/admin/albums/' . $album['id'] . '/delete') ?>" data-confirm="Albüm ve içindeki TÜM fotoğraflar kalıcı olarak silinecek. Emin misiniz?">
        <?= csrf_field() ?><button class="btn btn-danger" type="submit">Albümü Sil</button>
    </form>
</div>
