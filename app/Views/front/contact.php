<section class="page-head">
    <div class="container">
        <h1>İletişim</h1>
        <p>Bize ulaşabileceğiniz kanallar ve mesaj formu</p>
    </div>
</section>

<section class="section">
    <div class="container contact-grid">
        <div class="contact-info">
            <?php if (setting('contact_address') !== ''): ?>
            <div class="info-card">
                <span class="info-icon">&#128205;</span>
                <div><h3>Adres</h3><p><?= e(setting('contact_address')) ?></p></div>
            </div>
            <?php endif; ?>
            <?php if (setting('contact_phone') !== ''): ?>
            <div class="info-card">
                <span class="info-icon">&#9742;</span>
                <div><h3>Telefon</h3><p><a href="tel:<?= e(preg_replace('~[^0-9++]~', '', setting('contact_phone'))) ?>"><?= e(setting('contact_phone')) ?></a></p></div>
            </div>
            <?php endif; ?>
            <?php if (setting('contact_email') !== ''): ?>
            <div class="info-card">
                <span class="info-icon">&#9993;</span>
                <div><h3>E-posta</h3><p><a href="mailto:<?= e(setting('contact_email')) ?>"><?= e(setting('contact_email')) ?></a></p></div>
            </div>
            <?php endif; ?>

            <?php if (setting('map_embed') !== ''): ?>
            <div class="map-box">
                <iframe src="<?= e(setting('map_embed')) ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Harita"></iframe>
            </div>
            <?php endif; ?>
        </div>

        <div class="card contact-form-card">
            <h2>Bize Mesaj Gönderin</h2>
            <form method="post" action="<?= url('/iletisim') ?>">
                <?= csrf_field() ?>
                <p class="honeypot" aria-hidden="true">
                    <label>Web sitesi<input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                </p>
                <div class="form-row">
                    <div class="form-field">
                        <label for="cf-name">Adınız Soyadınız *</label>
                        <input id="cf-name" type="text" name="name" value="<?= e(old('name')) ?>" required>
                    </div>
                    <div class="form-field">
                        <label for="cf-email">E-posta *</label>
                        <input id="cf-email" type="email" name="email" value="<?= e(old('email')) ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-field">
                        <label for="cf-phone">Telefon</label>
                        <input id="cf-phone" type="tel" name="phone" value="<?= e(old('phone')) ?>">
                    </div>
                    <div class="form-field">
                        <label for="cf-subject">Konu</label>
                        <input id="cf-subject" type="text" name="subject" value="<?= e(old('subject')) ?>">
                    </div>
                </div>
                <div class="form-field">
                    <label for="cf-body">Mesajınız *</label>
                    <textarea id="cf-body" name="body" rows="6" required><?= e(old('body')) ?></textarea>
                </div>
                <button class="btn btn-primary" type="submit">Mesajı Gönder</button>
            </form>
        </div>
    </div>
</section>
