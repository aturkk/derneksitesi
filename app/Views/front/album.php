<section class="page-head">
    <div class="container">
        <p class="breadcrumb"><a href="<?= url('/') ?>">Ana Sayfa</a> &rsaquo; <a href="<?= url('/galeri') ?>">Galeri</a></p>
        <h1><?= e($album['title']) ?></h1>
        <p><?= count($photos) ?> fotoğraf</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if ($photos !== []): ?>
        <div class="gallery-grid gallery-grid-4" data-lightbox="1">
            <?php foreach ($photos as $photo): ?>
            <a class="gallery-item" href="<?= upload_url($photo['filename']) ?>" data-caption="<?= e($photo['title'] ?? '') ?>">
                <img src="<?= upload_url($photo['filename']) ?>" alt="<?= e($photo['title'] ?? '') ?>" loading="lazy">
            </a>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="empty-note">Bu albümde henüz fotoğraf yok.</p>
        <?php endif; ?>

        <?php
        $otherAlbums = array_values(array_filter($albums, static fn ($a) => (int) $a['id'] !== (int) $album['id']));
        if ($otherAlbums !== []):
        ?>
        <div class="section-head" style="margin-top:48px">
            <h2 class="section-title">Diğer Albümler</h2>
        </div>
        <div class="chip-row">
            <?php foreach ($otherAlbums as $other): ?>
            <a class="filter-chip" href="<?= url('/galeri/' . $other['slug']) ?>"><?= e($other['title']) ?></a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
