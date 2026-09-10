<div class="stat-grid">
    <a class="stat-card" href="<?= url('/admin/pages') ?>">
        <span class="stat-num"><?= (int) $pageCount ?></span><span class="stat-label">Sayfa</span>
    </a>
    <a class="stat-card" href="<?= url('/admin/posts') ?>">
        <span class="stat-num"><?= (int) $postCount ?></span><span class="stat-label">Yazı</span>
    </a>
    <a class="stat-card" href="<?= url('/admin/albums') ?>">
        <span class="stat-num"><?= (int) $albumCount ?></span><span class="stat-label">Albüm</span>
    </a>
    <a class="stat-card" href="<?= url('/admin/albums') ?>">
        <span class="stat-num"><?= (int) $photoCount ?></span><span class="stat-label">Fotoğraf</span>
    </a>
    <a class="stat-card <?= $unreadCount > 0 ? 'stat-alert' : '' ?>" href="<?= url('/admin/messages') ?>">
        <span class="stat-num"><?= (int) $messageCount ?></span><span class="stat-label">Mesaj<?= $unreadCount > 0 ? ' (' . (int) $unreadCount . ' okunmamış)' : '' ?></span>
    </a>
</div>

<div class="panel-grid">
    <section class="panel">
        <div class="panel-head">
            <h2>Son Mesajlar</h2>
            <a class="text-link" href="<?= url('/admin/messages') ?>">Tümü &rarr;</a>
        </div>
        <?php if ($recentMessages === []): ?>
            <p class="empty-note">Henüz mesaj yok.</p>
        <?php else: ?>
        <table class="table">
            <tbody>
            <?php foreach ($recentMessages as $m): ?>
            <tr class="<?= $m['is_read'] ? '' : 'row-unread' ?>">
                <td><a href="<?= url('/admin/messages/' . $m['id']) ?>"><?= e($m['name']) ?></a></td>
                <td class="muted"><?= e($m['subject'] ?: make_excerpt($m['body'], 40)) ?></td>
                <td class="muted nowrap"><?= tr_datetime($m['created_at']) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </section>

    <section class="panel">
        <div class="panel-head">
            <h2>Son Yazılar</h2>
            <a class="text-link" href="<?= url('/admin/posts') ?>">Tümü &rarr;</a>
        </div>
        <?php if ($recentPosts === []): ?>
            <p class="empty-note">Henüz yazı yok.</p>
        <?php else: ?>
        <table class="table">
            <tbody>
            <?php foreach ($recentPosts as $p): ?>
            <tr>
                <td><a href="<?= url('/admin/posts/' . $p['id'] . '/edit') ?>"><?= e($p['title']) ?></a></td>
                <td><span class="status status-<?= $p['status'] === 'published' ? 'published' : 'draft' ?>"><?= $p['status'] === 'published' ? 'Yayında' : 'Taslak' ?></span></td>
                <td class="muted nowrap"><?= tr_date($p['published_at'] ?? $p['created_at']) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </section>
</div>
