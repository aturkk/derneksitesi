<?php $post = $post ?? []; ?>
<article class="card post-card">
    <a class="card-media" href="<?= url('/haber/' . $post['slug']) ?>">
        <img src="<?= upload_url($post['cover_image'] ?? null) ?>" alt="<?= e($post['title'] ?? '') ?>" loading="lazy">
    </a>
    <div class="card-body">
        <?php if (!empty($post['category_name'])): ?><span class="chip"><?= e($post['category_name']) ?></span><?php endif; ?>
        <h3 class="card-title"><a href="<?= url('/haber/' . $post['slug']) ?>"><?= e($post['title'] ?? '') ?></a></h3>
        <p class="card-excerpt"><?= e($post['excerpt'] ?: make_excerpt((string) ($post['content'] ?? ''), 130)) ?></p>
        <time class="card-date"><?= tr_date($post['published_at'] ?? $post['created_at'] ?? null) ?></time>
    </div>
</article>
