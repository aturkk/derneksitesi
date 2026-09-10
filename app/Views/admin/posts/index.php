<div class="toolbar">
    <form class="search-inline" method="get" action="<?= url('/admin/posts') ?>">
        <input type="search" name="q" value="<?= e($q) ?>" placeholder="Yazı ara…">
        <button class="btn btn-outline btn-sm" type="submit">Ara</button>
    </form>
    <a class="btn btn-primary" href="<?= url('/admin/posts/new') ?>">+ Yeni Yazı</a>
</div>

<?php if ($posts === []): ?>
<p class="empty-note"><?= $q !== '' ? 'Aramanıza uygun yazı bulunamadı.' : 'Henüz yazı yok. "Yeni Yazı" ile ilk haberinizi ekleyin.' ?></p>
<?php else: ?>
<table class="table table-card">
    <thead>
    <tr>
        <th>Başlık</th>
        <th>Kategori</th>
        <th>Durum</th>
        <th>Yayın Tarihi</th>
        <th class="right">İşlemler</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($posts as $p): ?>
    <tr>
        <td><a class="row-title" href="<?= url('/admin/posts/' . $p['id'] . '/edit') ?>"><?= e($p['title']) ?></a></td>
        <td class="muted"><?= e($p['category_name'] ?? '&ndash;') ?></td>
        <td><span class="status status-<?= $p['status'] === 'published' ? 'published' : 'draft' ?>"><?= $p['status'] === 'published' ? 'Yayında' : 'Taslak' ?></span></td>
        <td class="muted nowrap"><?= tr_datetime($p['published_at'] ?? $p['created_at']) ?></td>
        <td class="right">
            <a class="btn btn-sm btn-outline" href="<?= url('/admin/posts/' . $p['id'] . '/edit') ?>">Düzenle</a>
            <a class="btn btn-sm btn-ghost" href="<?= url('/haber/' . $p['slug']) ?>" target="_blank" rel="noopener">Gör</a>
            <form class="inline" method="post" action="<?= url('/admin/posts/' . $p['id'] . '/delete') ?>" data-confirm="Bu yazı kalıcı olarak silinecek. Emin misiniz?">
                <?= csrf_field() ?><button class="btn btn-sm btn-danger" type="submit">Sil</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?= \App\Core\View::render('front/_pagination', ['pager' => $pager]) ?>
<?php endif; ?>
