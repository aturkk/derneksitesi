<section class="page-head">
    <div class="container">
        <p class="breadcrumb"><a href="<?= url('/') ?>">Ana Sayfa</a> &rsaquo; <a href="<?= url('/haberler') ?>">Haberler</a></p>
        <h1><?= e($post['title']) ?></h1>
        <div class="post-meta">
            <?php if (!empty($post['category_name'])): ?>
            <a class="chip" href="<?= url('/haberler') ?>?kategori=<?= e($post['category_slug'] ?? '') ?>"><?= e($post['category_name']) ?></a>
            <?php endif; ?>
            <time><?= tr_date($post['published_at'] ?? $post['created_at'], true) ?></time>
        </div>
    </div>
</section>

<section class="section">
    <div class="container narrow">
        <?php if (!empty($post['cover_image'])): ?>
        <figure class="post-cover">
            <img src="<?= upload_url($post['cover_image']) ?>" alt="<?= e($post['title']) ?>">
        </figure>
        <?php endif; ?>

        <article class="content-article">
            <?= $post['content'] /* yönetici panelinden girilen içerik */ ?>
        </article>

        <div class="post-footer">
            <a class="btn btn-outline" href="<?= url('/haberler') ?>">&larr; Tüm Haberler</a>
        </div>

        <?php if ($others !== []): ?>
        <div class="section-head" style="margin-top:48px">
            <h2 class="section-title">Diğer Haberler</h2>
        </div>
        <div class="news-grid news-grid-3">
            <?php foreach ($others as $other): ?>
                <?= \App\Core\View::render('front/_post-card', ['post' => $other]) ?>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
