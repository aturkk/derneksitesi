<div class="toolbar">
    <a class="btn btn-primary" href="<?= url('/admin/pages/new') ?>">+ Yeni Sayfa</a>
</div>

<?php if ($pages === []): ?>
<p class="empty-note">Henüz sayfa yok. "Yeni Sayfa" ile ilk sayfanızı oluşturun.</p>
<?php else: ?>
<?php
$firstId = (int) $pages[0]['id'];
$lastId = (int) $pages[count($pages) - 1]['id'];
?>
<table class="table table-card">
    <thead>
    <tr>
        <th style="width:90px">Sıra</th>
        <th>Başlık</th>
        <th>Adres (slug)</th>
        <th>Menü</th>
        <th>Durum</th>
        <th>Güncelleme</th>
        <th class="right">İşlemler</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($pages as $p): ?>
    <tr>
        <td>
            <span class="move-btns">
                <form method="post" action="<?= url('/admin/pages/' . $p['id'] . '/move/up') ?>"><?= csrf_field() ?><button class="icon-btn" title="Yukarı taşı" <?= (int) $p['id'] === $firstId ? 'disabled' : '' ?>>&uarr;</button></form>
                <form method="post" action="<?= url('/admin/pages/' . $p['id'] . '/move/down') ?>"><?= csrf_field() ?><button class="icon-btn" title="Aşağı taşı" <?= (int) $p['id'] === $lastId ? 'disabled' : '' ?>>&darr;</button></form>
            </span>
        </td>
        <td><a class="row-title" href="<?= url('/admin/pages/' . $p['id'] . '/edit') ?>"><?= e($p['title']) ?></a></td>
        <td class="muted">/<?= e($p['slug']) ?></td>
        <td><?= $p['show_in_menu'] ? '&#10003;' : '&ndash;' ?></td>
        <td><span class="status status-<?= $p['status'] === 'published' ? 'published' : 'draft' ?>"><?= $p['status'] === 'published' ? 'Yayında' : 'Taslak' ?></span></td>
        <td class="muted nowrap"><?= tr_date($p['updated_at']) ?></td>
        <td class="right">
            <a class="btn btn-sm btn-outline" href="<?= url('/admin/pages/' . $p['id'] . '/edit') ?>">Düzenle</a>
            <a class="btn btn-sm btn-ghost" href="<?= url('/' . $p['slug']) ?>" target="_blank" rel="noopener">Gör</a>
            <form class="inline" method="post" action="<?= url('/admin/pages/' . $p['id'] . '/delete') ?>" data-confirm="Bu sayfa kalıcı olarak silinecek. Emin misiniz?">
                <?= csrf_field() ?><button class="btn btn-sm btn-danger" type="submit">Sil</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
