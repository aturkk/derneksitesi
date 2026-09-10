<?php if ($messages === []): ?>
<p class="empty-note">Henüz mesaj yok. İletişim formundan gelen mesajlar burada listelenecek.</p>
<?php else: ?>
<table class="table table-card">
    <thead>
    <tr>
        <th>Kimden</th>
        <th>Konu / Mesaj</th>
        <th>Tarih</th>
        <th class="right">İşlemler</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($messages as $m): ?>
    <tr class="<?= $m['is_read'] ? '' : 'row-unread' ?>">
        <td>
            <span class="row-title"><?= e($m['name']) ?></span>
            <span class="muted block"><?= e($m['email']) ?></span>
        </td>
        <td class="muted"><?= e($m['subject'] ?: make_excerpt($m['body'], 60)) ?></td>
        <td class="muted nowrap"><?= tr_datetime($m['created_at']) ?></td>
        <td class="right">
            <a class="btn btn-sm btn-outline" href="<?= url('/admin/messages/' . $m['id']) ?>"><?= $m['is_read'] ? 'Görüntüle' : 'Oku' ?></a>
            <form class="inline" method="post" action="<?= url('/admin/messages/' . $m['id'] . '/delete') ?>" data-confirm="Bu mesaj silinsin mi?">
                <?= csrf_field() ?><button class="btn btn-sm btn-danger" type="submit">Sil</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?= \App\Core\View::render('front/_pagination', ['pager' => $pager]) ?>
<?php endif; ?>
