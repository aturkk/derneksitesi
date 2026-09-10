<section class="page-head">
    <div class="container">
        <h1>Arama</h1>
        <p>Site içeriğinde arama yapın</p>
    </div>
</section>

<section class="section">
    <div class="container narrow">
        <form class="search-form" action="<?= url('/arama') ?>" method="get">
            <input type="search" name="q" value="<?= e($q) ?>" placeholder="Aranacak kelime…" aria-label="Arama" autofocus>
            <button class="btn btn-primary" type="submit">Ara</button>
        </form>

        <?php if ($q !== ''): ?>
            <?php if (mb_strlen($q) < 2): ?>
                <p class="empty-note">En az 2 karakter yazın.</p>
            <?php elseif ($posts === [] && $pages === []): ?>
                <p class="empty-note">"<?= e($q) ?>" için sonuç bulunamadı.</p>
            <?php else: ?>
                <p class="result-count">"<?= e($q) ?>" için <?= count($posts) + count($pages) ?> sonuç bulundu.</p>

                <?php if ($posts !== []): ?>
                <h2 class="section-title">Haberler</h2>
                <div class="news-grid">
                    <?php foreach ($posts as $post): ?>
                        <?= \App\Core\View::render('front/_post-card', ['post' => $post]) ?>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php if ($pages !== []): ?>
                <h2 class="section-title">Sayfalar</h2>
                <ul class="page-results">
                    <?php foreach ($pages as $pageRow): ?>
                    <li>
                        <a href="<?= url('/' . $pageRow['slug']) ?>"><?= e($pageRow['title']) ?></a>
                        <p><?= e(make_excerpt((string) $pageRow['content'], 140)) ?></p>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>
