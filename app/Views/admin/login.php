<?php $siteName = setting('site_name', 'Dernek Sitesi'); ?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Giriş | <?= e($siteName) ?></title>
<link rel="icon" href="<?= asset('assets/img/favicon.svg') ?>" type="image/svg+xml">
<link rel="stylesheet" href="<?= asset('assets/css/admin.css') ?>">
</head>
<body class="login-body">
<div class="login-card">
    <div class="login-brand">
        <span class="brand-mark"><?= e(mb_strtoupper(mb_substr($siteName, 0, 1))) ?></span>
        <strong><?= e($siteName) ?></strong>
        <small>Yönetim Paneli</small>
    </div>

    <?php if ($msg = flash_get('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
    <?php if ($msg = flash_get('error')): ?><div class="alert alert-error"><?= e($msg) ?></div><?php endif; ?>

    <form method="post" action="<?= url('/admin/login') ?>">
        <?= csrf_field() ?>
        <div class="form-field">
            <label for="login-email">E-posta</label>
            <input id="login-email" type="email" name="email" value="<?= e(old('email')) ?>" required autofocus>
        </div>
        <div class="form-field">
            <label for="login-pass">Şifre</label>
            <input id="login-pass" type="password" name="password" required>
        </div>
        <button class="btn btn-primary btn-block" type="submit">Giriş Yap</button>
    </form>

    <p class="login-foot"><a href="<?= url('/') ?>">&larr; Siteye dön</a></p>
</div>
</body>
</html>
