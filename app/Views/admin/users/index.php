<div class="toolbar">
    <a class="btn btn-primary" href="<?= url('/admin/users/new') ?>">+ Yeni Kullanıcı</a>
</div>

<table class="table table-card">
    <thead>
    <tr>
        <th>Ad</th>
        <th>E-posta</th>
        <th>Rol</th>
        <th>Kayıt Tarihi</th>
        <th class="right">İşlemler</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($users as $u): ?>
    <tr>
        <td><span class="row-title"><?= e($u['name']) ?></span></td>
        <td class="muted"><?= e($u['email']) ?></td>
        <td><span class="status <?= $u['role'] === 'admin' ? 'status-published' : 'status-draft' ?>"><?= $u['role'] === 'admin' ? 'Yönetici' : 'Editör' ?></span></td>
        <td class="muted nowrap"><?= tr_date($u['created_at']) ?></td>
        <td class="right">
            <a class="btn btn-sm btn-outline" href="<?= url('/admin/users/' . $u['id'] . '/edit') ?>">Düzenle</a>
            <?php if ((int) $u['id'] !== (int) $authUser['id']): ?>
            <form class="inline" method="post" action="<?= url('/admin/users/' . $u['id'] . '/delete') ?>" data-confirm="Bu kullanıcı silinsin mi?">
                <?= csrf_field() ?><button class="btn btn-sm btn-danger" type="submit">Sil</button>
            </form>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
