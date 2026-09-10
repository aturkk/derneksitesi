<section class="page-head">
    <div class="container">
        <h1><?= e($category !== null ? $category['name'] : 'Haberler') ?></h1>
        <p>Güncel haber ve duyurularımız</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="chip-row">
            <a class="filter-chip <?= $category === null ? 'active' : '' ?>" href="<?= url('/haberler') ?>">Tümü</a>
            <?php foreach ($categories as $cat): ?>
            <a class="filter-chip <?= ($category !== null && $category['slug'] === $cat['slug']) ? 'active' : '' ?>"
               href="<?= url('/haberler') ?>?kategori=<?= e($cat['slug']) ?>"><?= e($cat['name']) ?></a>
            <?php endforeach; ?>
        </div>

        <?php if ($posts !== []): ?>
        <div class="news-grid">
            <?php foreach ($posts as $post): ?>
                <?= \App\Core\View::render('front/_post-card', ['post' => $post]) ?>
            <?php endforeach; ?>
        </div>
        <?= \App\Core\View::render('front/_pagination', ['pager' => $pager]) ?>
        <?php else: ?>
        <p class="empty-note">Bu kategoride henüz yayınlanmış yazı yok.</p>
        <?php endif; ?>
    </div>
</section>
