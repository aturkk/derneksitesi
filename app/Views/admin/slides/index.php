<div class="toolbar">
    <span class="muted">Ana sayfadaki hero bölümünde yayında olan slaytlar sırayla döner.</span>
    <a class="btn btn-primary" href="<?= url('/admin/slides/new') ?>">+ Yeni Slayt</a>
</div>

<?php if ($slides === []): ?>
<p class="empty-note">Henüz slayt yok. Slayt eklemezseniz ana sayfada ayarlardaki tanıtım metni görünür.</p>
<?php else: ?>
<?php
$firstId = (int) $slides[0]['id'];
$lastId = (int) $slides[count($slides) - 1]['id'];
?>
<table class="table table-card">
    <thead>
    <tr>
        <th style="width:120px">Görsel</th>
        <th style="width:80px">Sıra</th>
        <th>Başlık</th>
        <th>Metin</th>
        <th>Buton</th>
        <th>Durum</th>
        <th class="right">İşlemler</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($slides as $s): ?>
    <tr>
        <td><img class="slide-thumb" src="<?= upload_url($s['image']) ?>" alt=""></td>
        <td>
            <span class="move-btns">
                <form method="post" action="<?= url('/admin/slides/' . $s['id'] . '/move/up') ?>"><?= csrf_field() ?><button class="icon-btn" title="Yukarı taşı" <?= (int) $s['id'] === $firstId ? 'disabled' : '' ?>>&uarr;</button></form>
                <form method="post" action="<?= url('/admin/slides/' . $s['id'] . '/move/down') ?>"><?= csrf_field() ?><button class="icon-btn" title="Aşağı taşı" <?= (int) $s['id'] === $lastId ? 'disabled' : '' ?>>&darr;</button></form>
            </span>
        </td>
        <td><a class="row-title" href="<?= url('/admin/slides/' . $s['id'] . '/edit') ?>"><?= e($s['title']) ?></a></td>
        <td class="muted"><?= e(make_excerpt((string) ($s['text'] ?? ''), 60)) ?></td>
        <td class="muted"><?= $s['button_text'] ? e($s['button_text']) : '&ndash;' ?></td>
        <td><span class="status status-<?= $s['status'] === 'published' ? 'published' : 'draft' ?>"><?= $s['status'] === 'published' ? 'Yayında' : 'Taslak' ?></span></td>
        <td class="right">
            <a class="btn btn-sm btn-outline" href="<?= url('/admin/slides/' . $s['id'] . '/edit') ?>">Düzenle</a>
            <form class="inline" method="post" action="<?= url('/admin/slides/' . $s['id'] . '/delete') ?>" data-confirm="Bu slayt kalıcı olarak silinecek. Emin misiniz?">
                <?= csrf_field() ?><button class="btn btn-sm btn-danger" type="submit">Sil</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
