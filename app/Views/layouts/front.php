<?php
$siteName = setting('site_name', 'Dernek Sitesi');
$tagline = setting('site_tagline');
$fullTitle = (isset($pageTitle) && $pageTitle !== null)
    ? e($pageTitle) . ' | ' . e($siteName)
    : e($siteName) . ($tagline !== '' ? ' — ' . e($tagline) : '');
$menu = main_menu();
$hasContact = setting('contact_phone') !== '' || setting('contact_email') !== '';
$socials = array_filter([
    'Facebook'  => setting('social_facebook'),
    'Instagram' => setting('social_instagram'),
    'YouTube'   => setting('social_youtube'),
    'X'         => setting('social_x'),
]);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $fullTitle ?></title>
<meta name="description" content="<?= e($metaDescription ?? setting('site_description')) ?>">
<link rel="icon" href="<?= asset('assets/img/favicon.svg') ?>" type="image/svg+xml">
<link rel="stylesheet" href="<?= asset('assets/css/site.css') ?>">
</head>
<body>

<header class="site-header">
    <?php if ($hasContact || $socials !== []): ?>
    <div class="topbar">
        <div class="container topbar-inner">
            <div class="topbar-contact">
                <?php if (setting('contact_phone') !== ''): ?><span>&#9742; <?= e(setting('contact_phone')) ?></span><?php endif; ?>
                <?php if (setting('contact_email') !== ''): ?><span>&#9993; <?= e(setting('contact_email')) ?></span><?php endif; ?>
            </div>
            <?php if ($socials !== []): ?>
            <div class="topbar-social">
                <?php foreach ($socials as $label => $link): ?>
                    <a href="<?= e($link) ?>" target="_blank" rel="noopener"><?= e($label) ?></a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="container header-main">
        <a class="brand" href="<?= url('/') ?>">
            <?php if (setting('logo') !== ''): ?>
                <img class="brand-logo" src="<?= upload_url(setting('logo')) ?>" alt="<?= e($siteName) ?>">
            <?php else: ?>
                <span class="brand-mark"><?= e(mb_strtoupper(mb_substr($siteName, 0, 1))) ?></span>
            <?php endif; ?>
            <span class="brand-text">
                <strong><?= e($siteName) ?></strong>
                <?php if ($tagline !== ''): ?><small><?= e($tagline) ?></small><?php endif; ?>
            </span>
        </a>
        <form class="header-search" action="<?= url('/arama') ?>" method="get">
            <input type="search" name="q" placeholder="Sitede ara…" aria-label="Sitede ara">
            <button type="submit" aria-label="Ara">&#128269;</button>
        </form>
        <button class="nav-toggle" type="button" aria-label="Menüyü aç/kapat"><span></span><span></span><span></span></button>
    </div>

    <nav class="main-nav">
        <div class="container">
            <ul>
                <?php foreach ($menu as $item): ?>
                <li><a href="<?= url($item['path']) ?>" class="<?= is_active($item['path']) ? 'active' : '' ?>"><?= e($item['title']) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </nav>
</header>

<main>
    <?= $content ?>
</main>

<footer class="site-footer">
    <div class="container footer-grid">
        <div class="footer-col">
            <h4><?= e($siteName) ?></h4>
            <p><?= e(setting('site_description')) ?></p>
            <?php if ($socials !== []): ?>
            <div class="footer-social">
                <?php foreach ($socials as $label => $link): ?>
                    <a href="<?= e($link) ?>" target="_blank" rel="noopener"><?= e($label) ?></a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        <div class="footer-col">
            <h4>Hızlı Menü</h4>
            <ul class="footer-links">
                <?php foreach (array_slice($menu, 0, 6) as $item): ?>
                <li><a href="<?= url($item['path']) ?>"><?= e($item['title']) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="footer-col">
            <h4>İletişim</h4>
            <ul class="footer-links footer-contact">
                <?php if (setting('contact_address') !== ''): ?><li><?= e(setting('contact_address')) ?></li><?php endif; ?>
                <?php if (setting('contact_phone') !== ''): ?><li><a href="tel:<?= e(preg_replace('~[^0-9++]~', '', setting('contact_phone'))) ?>">&#9742; <?= e(setting('contact_phone')) ?></a></li><?php endif; ?>
                <?php if (setting('contact_email') !== ''): ?><li><a href="mailto:<?= e(setting('contact_email')) ?>">&#9993; <?= e(setting('contact_email')) ?></a></li><?php endif; ?>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            &copy; <?= date('Y') ?> <?= e($siteName) ?>. <?= e(setting('footer_text')) ?>
        </div>
    </div>
</footer>

<div class="lightbox" hidden>
    <button class="lightbox-close" type="button" aria-label="Kapat">&times;</button>
    <button class="lightbox-prev" type="button" aria-label="Önceki">&#10094;</button>
    <img src="" alt="">
    <button class="lightbox-next" type="button" aria-label="Sonraki">&#10095;</button>
    <p class="lightbox-caption"></p>
</div>

<script src="<?= asset('assets/js/site.js') ?>"></script>
</body>
</html>
