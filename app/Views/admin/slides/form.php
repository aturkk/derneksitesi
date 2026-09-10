<?php /** @var array|null $slide */ ?>
<form method="post" action="<?= $slide !== null ? url('/admin/slides/' . $slide['id']) : url('/admin/slides') ?>" class="form-panel" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="form-grid-2">
        <div class="form-main">
            <div class="form-field">
                <label for="sl-title">Slayt Başlığı *</label>
                <input id="sl-title" type="text" name="title" value="<?= e(old('title', $slide['title'] ?? '')) ?>" required>
            </div>
            <div class="form-field">
                <label for="sl-text">Slayt Metni</label>
                <textarea id="sl-text" name="text" rows="4" maxlength="500"><?= e(old('text', $slide['text'] ?? '')) ?></textarea>
                <p class="hint">Kısa bir tanıtım cümlesi; uzun metinler slaytta kesilir.</p>
            </div>
            <div class="form-grid-2">
                <div class="form-field">
                    <label for="sl-btext">Buton Metni</label>
                    <input id="sl-btext" type="text" name="button_text" value="<?= e(old('button_text', $slide['button_text'] ?? '')) ?>" placeholder="örn. Hakkımızda">
                </div>
                <div class="form-field">
                    <label for="sl-burl">Buton Adresi</label>
                    <input id="sl-burl" type="text" name="button_url" value="<?= e(old('button_url', $slide['button_url'] ?? '')) ?>" placeholder="örn. /hakkimizda">
                </div>
            </div>
            <p class="hint">Buton adresi site içi bir sayfa ise "/hakkimizda" biçiminde, dış bağlantı ise "https://…" biçiminde yazın.</p>
        </div>
        <div class="form-side">
            <div class="form-field">
                <label for="sl-image">Slider Görseli <?= $slide === null ? '*' : '' ?></label>
                <?php if (!empty($slide['image'])): ?>
                <img class="cover-preview" src="<?= upload_url($slide['image']) ?>" alt="">
                <?php endif; ?>
                <input id="sl-image" type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif" <?= $slide === null ? 'required' : '' ?>>
                <p class="hint">JPG, PNG, WEBP veya GIF — en fazla 8 MB. İdeal boyut: 1600×900 px (geniş ekran). Yeni seçim eskisinin üzerine yazar.</p>
            </div>
            <div class="form-field">
                <label for="sl-status">Durum</label>
                <select id="sl-status" name="status">
                    <option value="published" <?= old('status', $slide['status'] ?? 'published') === 'published' ? 'selected' : '' ?>>Yayında</option>
                    <option value="draft" <?= old('status', $slide['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Taslak</option>
                </select>
            </div>
            <button class="btn btn-primary btn-block" type="submit"><?= $slide !== null ? 'Değişiklikleri Kaydet' : 'Slaytı Oluştur' ?></button>
        </div>
    </div>
</form>
