<?php if (($pager['pages'] ?? 1) > 1): ?>
<nav class="pagination" aria-label="Sayfalama">
    <?php if ($pager['page'] > 1): ?>
        <a class="page-link" href="<?= e(page_url($pager['page'] - 1)) ?>">&laquo; Önceki</a>
    <?php endif; ?>
    <?php for ($i = 1; $i <= $pager['pages']; $i++): ?>
        <?php if ($i === $pager['page']): ?>
            <span class="page-link current"><?= $i ?></span>
        <?php else: ?>
            <a class="page-link" href="<?= e(page_url($i)) ?>"><?= $i ?></a>
        <?php endif; ?>
    <?php endfor; ?>
    <?php if ($pager['page'] < $pager['pages']): ?>
        <a class="page-link" href="<?= e(page_url($pager['page'] + 1)) ?>">Sonraki &raquo;</a>
    <?php endif; ?>
</nav>
<?php endif; ?>
