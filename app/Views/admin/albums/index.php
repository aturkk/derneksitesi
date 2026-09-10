<div class="toolbar">
    <a class="btn btn-primary" href="<?= url('/admin/albums/new') ?>">+ Yeni Albüm</a>
</div>

<?php if ($albums === []): ?>
<p class="empty-note">Henüz albüm yok. "Yeni Albüm" ile ilk galerinizi oluşturun.</p>
<?php else: ?>
<div class="album-admin-grid">
    <?php foreach ($albums as $album): ?>
    <a class="card album-card" href="<?= url('/admin/albums/' . $album['id']) ?>">
        <span class="card-media">
            <img src="<?= upload_url($album['cover'] ?? null) ?>" alt="" loading="lazy">
        </span>
        <span class="card-body">
            <span class="card-title"><?= e($album['title']) ?></span>
            <span class="album-count"><?= (int) $album['photo_count'] ?> fotoğraf</span>
        </span>
    </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>
