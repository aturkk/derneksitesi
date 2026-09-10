<p><a class="text-link" href="<?= url('/admin/messages') ?>">&larr; Tüm mesajlar</a></p>

<div class="form-panel">
    <div class="message-head">
        <div>
            <h2 class="row-title"><?= e($message['subject'] ?: '(Konu yok)') ?></h2>
            <p class="muted"><?= e($message['name']) ?> &lt;<a href="mailto:<?= e($message['email']) ?>"><?= e($message['email']) ?></a>&gt;
                <?= $message['phone'] ? ' &middot; ' . e($message['phone']) : '' ?></p>
        </div>
        <span class="muted nowrap"><?= tr_datetime($message['created_at']) ?></span>
    </div>

    <div class="message-body"><?= nl2br(e($message['body'])) ?></div>

    <div class="message-actions">
        <a class="btn btn-primary" href="mailto:<?= e($message['email']) ?>?subject=<?= rawurlencode('Re: ' . ($message['subject'] ?: 'Mesajınız')) ?>">Yanıtla (E-posta)</a>
        <form class="inline" method="post" action="<?= url('/admin/messages/' . $message['id'] . '/delete') ?>" data-confirm="Bu mesaj silinsin mi?">
            <?= csrf_field() ?><button class="btn btn-danger" type="submit">Sil</button>
        </form>
    </div>
</div>
