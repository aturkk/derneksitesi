<?php
$siteName = setting('site_name', 'Dernek Sitesi');
$unread = 0;
try {
    $unread = (int) \App\Core\Database::fetchColumn('SELECT COUNT(*) FROM messages WHERE is_read = 0');
} catch (Throwable) {
}
$active = current_path();
function nav_active(string $path, string $active): string
{
    if ($path === '/admin') {
        return $active === '/admin' ? 'active' : '';
    }
    return ($active === $path || str_starts_with($active, $path . '/')) ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title ?? 'Yönetim') ?> | <?= e($siteName) ?></title>
<meta name="csrf-token" content="<?= e(\App\Core\Csrf::token()) ?>">
<meta name="app-base" content="<?= e(base_url()) ?>">
<link rel="icon" href="<?= asset('assets/img/favicon.svg') ?>" type="image/svg+xml">
<link rel="stylesheet" href="<?= asset('assets/css/admin.css') ?>">
<?php if (!empty($editor)): ?>
<script src="<?= asset('assets/vendor/tinymce/tinymce.min.js') ?>"></script>
<?php endif; ?>
</head>
<body class="admin-body">

<aside class="sidebar">
    <a class="sidebar-brand" href="<?= url('/admin') ?>">
        <?php if (setting('logo') !== ''): ?>
            <img src="<?= upload_url(setting('logo')) ?>" alt="">
        <?php else: ?>
            <span class="brand-mark small"><?= e(mb_strtoupper(mb_substr($siteName, 0, 1))) ?></span>
        <?php endif; ?>
        <span><?= e($siteName) ?></span>
    </a>

    <nav class="sidebar-nav">
        <a class="<?= nav_active('/admin', $active) ?>" href="<?= url('/admin') ?>"><span class="ico">&#9636;</span> Genel Bakış</a>

        <p class="nav-group">İçerik</p>
        <a class="<?= nav_active('/admin/pages', $active) ?>" href="<?= url('/admin/pages') ?>"><span class="ico">&#128196;</span> Sayfalar</a>
        <a class="<?= nav_active('/admin/posts', $active) ?>" href="<?= url('/admin/posts') ?>"><span class="ico">&#128240;</span> Yazılar</a>
        <a class="<?= nav_active('/admin/categories', $active) ?>" href="<?= url('/admin/categories') ?>"><span class="ico">&#127991;</span> Kategoriler</a>
        <a class="<?= nav_active('/admin/albums', $active) ?>" href="<?= url('/admin/albums') ?>"><span class="ico">&#128247;</span> Galeri</a>
        <a class="<?= nav_active('/admin/slides', $active) ?>" href="<?= url('/admin/slides') ?>"><span class="ico">&#128444;</span> Slider</a>

        <p class="nav-group">İletişim</p>
        <a class="<?= nav_active('/admin/messages', $active) ?>" href="<?= url('/admin/messages') ?>">
            <span class="ico">&#9993;</span> Mesajlar
            <?php if ($unread > 0): ?><span class="badge"><?= $unread ?></span><?php endif; ?>
        </a>

        <p class="nav-group">Sistem</p>
        <a class="<?= nav_active('/admin/profile', $active) ?>" href="<?= url('/admin/profile') ?>"><span class="ico">&#128100;</span> Profilim</a>
        <?php if (($authUser['role'] ?? '') === 'admin'): ?>
        <a class="<?= nav_active('/admin/settings', $active) ?>" href="<?= url('/admin/settings') ?>"><span class="ico">&#9881;</span> Site Ayarları</a>
        <a class="<?= nav_active('/admin/users', $active) ?>" href="<?= url('/admin/users') ?>"><span class="ico">&#128101;</span> Kullanıcılar</a>
        <?php endif; ?>
    </nav>

    <div class="sidebar-foot">
        <a href="<?= url('/') ?>" target="_blank" rel="noopener">&#127760; Siteyi Görüntüle</a>
        <a href="<?= url('/admin/logout') ?>">&#10162; Güvenli Çıkış</a>
    </div>
</aside>

<div class="admin-main">
    <header class="admin-topbar">
        <h1><?= e($title ?? 'Yönetim Paneli') ?></h1>
        <div class="user-chip" title="<?= e($authUser['email'] ?? '') ?>">
            <span class="user-avatar"><?= e(mb_strtoupper(mb_substr($authUser['name'] ?? '?', 0, 1))) ?></span>
            <span>
                <strong><?= e($authUser['name'] ?? '') ?></strong>
                <small><?= ($authUser['role'] ?? '') === 'admin' ? 'Yönetici' : 'Editör' ?></small>
            </span>
        </div>
    </header>

    <div class="admin-content">
        <?php if ($msg = flash_get('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
        <?php if ($msg = flash_get('error')): ?><div class="alert alert-error"><?= e($msg) ?></div><?php endif; ?>

        <?= $content ?>
    </div>
</div>

<script src="<?= asset('assets/js/admin.js') ?>"></script>
</body>
</html>
