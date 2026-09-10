<section class="page-head">
    <div class="container">
        <h1>Fotoğraf Galerisi</h1>
        <p>Etkinlik ve faaliyetlerimizden kareler</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if ($albums !== []): ?>
        <div class="album-grid">
            <?php foreach ($albums as $album): ?>
            <a class="card album-card" href="<?= url('/galeri/' . $album['slug']) ?>">
                <span class="card-media">
                    <img src="<?= upload_url($album['cover'] ?? null) ?>" alt="<?= e($album['title']) ?>" loading="lazy">
                </span>
                <span class="card-body">
                    <span class="card-title"><?= e($album['title']) ?></span>
                    <span class="album-count"><?= (int) $album['photo_count'] ?> fotoğraf</span>
                </span>
            </a>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="empty-note">Henüz fotoğraf albümü eklenmemiş.</p>
        <?php endif; ?>
    </div>
</section>
