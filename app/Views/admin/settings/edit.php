<form method="post" action="<?= url('/admin/settings') ?>" class="form-panel" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <fieldset class="form-fieldset">
        <legend>Genel</legend>
        <div class="form-grid-2">
            <div class="form-field">
                <label for="s-site_name">Dernek (Site) Adı</label>
                <input id="s-site_name" type="text" name="site_name" value="<?= e($settings['site_name'] ?? '') ?>">
            </div>
            <div class="form-field">
                <label for="s-site_tagline">Slogan</label>
                <input id="s-site_tagline" type="text" name="site_tagline" value="<?= e($settings['site_tagline'] ?? '') ?>">
            </div>
        </div>
        <div class="form-field">
            <label for="s-site_description">Site Açıklaması (SEO)</label>
            <textarea id="s-site_description" name="site_description" rows="2" maxlength="300"><?= e($settings['site_description'] ?? '') ?></textarea>
        </div>
        <div class="form-field">
            <label for="s-logo">Logo</label>
            <?php if (($settings['logo'] ?? '') !== ''): ?>
            <img class="cover-preview" src="<?= upload_url($settings['logo']) ?>" alt="">
            <label class="check-field"><input type="checkbox" name="logo_sil" value="1"> Logoyu kaldır</label>
            <?php endif; ?>
            <input id="s-logo" type="file" name="logo" accept="image/jpeg,image/png,image/webp,image/gif">
        </div>
    </fieldset>

    <fieldset class="form-fieldset">
        <legend>Ana Sayfa Tanıtımı (Hero)</legend>
        <div class="form-field">
            <label for="s-hero_title">Büyük Başlık</label>
            <input id="s-hero_title" type="text" name="hero_title" value="<?= e($settings['hero_title'] ?? '') ?>">
        </div>
        <div class="form-field">
            <label for="s-hero_text">Tanıtım Metni</label>
            <textarea id="s-hero_text" name="hero_text" rows="3"><?= e($settings['hero_text'] ?? '') ?></textarea>
        </div>
    </fieldset>

    <fieldset class="form-fieldset">
        <legend>İletişim Bilgileri</legend>
        <div class="form-grid-2">
            <div class="form-field">
                <label for="s-contact_phone">Telefon</label>
                <input id="s-contact_phone" type="text" name="contact_phone" value="<?= e($settings['contact_phone'] ?? '') ?>">
            </div>
            <div class="form-field">
                <label for="s-contact_email">E-posta</label>
                <input id="s-contact_email" type="email" name="contact_email" value="<?= e($settings['contact_email'] ?? '') ?>">
            </div>
        </div>
        <div class="form-field">
            <label for="s-contact_address">Adres</label>
            <textarea id="s-contact_address" name="contact_address" rows="2"><?= e($settings['contact_address'] ?? '') ?></textarea>
        </div>
        <div class="form-field">
            <label for="s-map_embed">Harita Gömme Adresi (iframe kaynağı)</label>
            <input id="s-map_embed" type="text" name="map_embed" value="<?= e($settings['map_embed'] ?? '') ?>" placeholder="https://www.google.com/maps/embed?pb=...">
            <p class="hint">Google Haritalar'da "Paylaş → Haritayı gömme → src içindeki adresi" kopyalayıp buraya yapıştırın.</p>
        </div>
    </fieldset>

    <fieldset class="form-fieldset">
        <legend>Sosyal Medya</legend>
        <div class="form-grid-2">
            <div class="form-field">
                <label for="s-facebook">Facebook</label>
                <input id="s-facebook" type="url" name="social_facebook" value="<?= e($settings['social_facebook'] ?? '') ?>">
            </div>
            <div class="form-field">
                <label for="s-instagram">Instagram</label>
                <input id="s-instagram" type="url" name="social_instagram" value="<?= e($settings['social_instagram'] ?? '') ?>">
            </div>
            <div class="form-field">
                <label for="s-youtube">YouTube</label>
                <input id="s-youtube" type="url" name="social_youtube" value="<?= e($settings['social_youtube'] ?? '') ?>">
            </div>
            <div class="form-field">
                <label for="s-x">X (Twitter)</label>
                <input id="s-x" type="url" name="social_x" value="<?= e($settings['social_x'] ?? '') ?>">
            </div>
        </div>
    </fieldset>

    <fieldset class="form-fieldset">
        <legend>Alt Bilgi</legend>
        <div class="form-field">
            <label for="s-footer_text">Footer Metni</label>
            <input id="s-footer_text" type="text" name="footer_text" value="<?= e($settings['footer_text'] ?? '') ?>">
        </div>
    </fieldset>

    <button class="btn btn-primary" type="submit">Ayarları Kaydet</button>
</form>
