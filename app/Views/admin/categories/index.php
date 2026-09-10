<div class="form-panel slim">
    <form class="inline-add" method="post" action="<?= url('/admin/categories') ?>">
        <?= csrf_field() ?>
        <input type="text" name="name" placeholder="Yeni kategori adı" required>
        <button class="btn btn-primary" type="submit">Ekle</button>
    </form>
</div>

<?php if ($categories === []): ?>
<p class="empty-note">Henüz kategori yok.</p>
<?php else: ?>
<table class="table table-card">
    <thead>
    <tr>
        <th>Ad</th>
        <th>Adres (slug)</th>
        <th>Yazı Sayısı</th>
        <th class="right">İşlemler</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($categories as $cat): ?>
    <tr>
        <td><span class="row-title"><?= e($cat['name']) ?></span></td>
        <td class="muted"><?= e($cat['slug']) ?></td>
        <td><?= (int) $cat['post_count'] ?></td>
        <td class="right">
            <form class="inline" method="post" action="<?= url('/admin/categories/' . $cat['id'] . '/delete') ?>" data-confirm="Kategori silinsin mi? Bu kategorideki yazılar kategorisiz kalır.">
                <?= csrf_field() ?><button class="btn btn-sm btn-danger" type="submit">Sil</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
