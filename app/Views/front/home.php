<?php if ($heroSlides !== []): ?>
<section class="hero-slider" id="heroSlider" data-interval="6000">
    <?php foreach ($heroSlides as $i => $s): ?>
    <div class="slide <?= $i === 0 ? 'is-active' : '' ?>">
        <img class="slide-bg" src="<?= upload_url($s['image']) ?>" alt="<?= e($s['title']) ?>">
        <div class="slide-overlay"></div>
        <div class="container slide-content">
            <h1><?= e($s['title']) ?></h1>
            <?php if (!empty($s['text'])): ?><p><?= e($s['text']) ?></p><?php endif; ?>
            <?php if (!empty($s['button_text'])):
                $btnUrl = (string) ($s['button_url'] ?? '');
                $href = preg_match('~^https?://~i', $btnUrl) ? $btnUrl : url($btnUrl !== '' ? $btnUrl : '/');
            ?>
            <div class="slide-actions"><a class="btn btn-accent" href="<?= e($href) ?>"><?= e($s['button_text']) ?></a></div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>

    <?php if (count($heroSlides) > 1): ?>
    <button class="slider-nav slider-prev" type="button" aria-label="Önceki slayt">&#10094;</button>
    <button class="slider-nav slider-next" type="button" aria-label="Sonraki slayt">&#10095;</button>
    <div class="slider-dots">
        <?php foreach ($heroSlides as $i => $s): ?>
        <button type="button" class="slider-dot <?= $i === 0 ? 'active' : '' ?>" data-index="<?= $i ?>" aria-label="Slayt <?= $i + 1 ?>"></button>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>
<?php else: ?>
<section class="hero">
    <div class="container hero-inner">
        <h1><?= e(setting('hero_title', 'Hoş geldiniz')) ?></h1>
        <p><?= e(setting('hero_text')) ?></p>
        <div class="hero-actions">
            <a class="btn btn-accent" href="<?= url('/haberler') ?>">Haberleri Gör</a>
            <a class="btn btn-ghost" href="<?= url('/iletisim') ?>">Bize Ulaşın</a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($about)): ?>
<section class="section">
    <div class="container about-grid">
        <div class="about-text">
            <h2 class="section-title">Hakkımızda</h2>
            <p><?= e(make_excerpt((string) $about['content'], 420)) ?></p>
            <a class="text-link" href="<?= url('/' . $about['slug']) ?>">Sayfanın tamamını oku &rarr;</a>
        </div>
        <div class="about-side">
            <div class="about-badge">
                <strong><?= date('Y') ?></strong>
                <span>yılında faaliyetlerimize<br>bu siteden ulaşabilirsiniz</span>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="section section-alt">
    <div class="container">
        <div class="section-head">
            <h2 class="section-title">Güncel Haberler</h2>
            <a class="text-link" href="<?= url('/haberler') ?>">Tümü &rarr;</a>
        </div>
        <?php if ($posts !== []): ?>
        <div class="news-grid">
            <?php foreach ($posts as $post): ?>
                <?= \App\Core\View::render('front/_post-card', ['post' => $post]) ?>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="empty-note">Henüz yayınlanmış bir haber yok.</p>
        <?php endif; ?>
    </div>
</section>

<?php if ($galleryPhotos !== []): ?>
<section class="section">
    <div class="container">
        <div class="section-head">
            <h2 class="section-title"><?= e($galleryTitle ?? 'Galeriden Seçmeler') ?></h2>
            <a class="text-link" href="<?= url('/galeri') ?>">Tüm Albümler &rarr;</a>
        </div>
        <div class="gallery-grid" data-lightbox="1">
            <?php foreach ($galleryPhotos as $photo): ?>
            <a class="gallery-item" href="<?= upload_url($photo['filename']) ?>" data-caption="<?= e($photo['title'] ?? '') ?>">
                <img src="<?= upload_url($photo['filename']) ?>" alt="<?= e($photo['title'] ?? '') ?>" loading="lazy">
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="cta-band">
    <div class="container cta-inner">
        <div>
            <h2>Sorularınız mı var?</h2>
            <p>Bize yazın; en kısa sürede dönüş yapalım.</p>
        </div>
        <a class="btn btn-light" href="<?= url('/iletisim') ?>">İletişim Sayfası</a>
    </div>
</section>
