<?php /** @var array|null $page */ ?>
<form method="post" action="<?= $page !== null ? url('/admin/pages/' . $page['id']) : url('/admin/pages') ?>" class="form-panel">
    <?= csrf_field() ?>
    <div class="form-grid-2">
        <div class="form-main">
            <div class="form-field">
                <label for="f-title">Sayfa Başlığı *</label>
                <input id="f-title" type="text" name="title" value="<?= e(old('title', $page['title'] ?? '')) ?>" required>
            </div>
            <div class="form-field">
                <label for="f-content">İçerik</label>
                <textarea id="f-content" class="tinymce" name="content" rows="16"><?= e(old('content', $page['content'] ?? '')) ?></textarea>
            </div>
        </div>
        <div class="form-side">
            <div class="form-field">
                <label for="f-status">Durum</label>
                <select id="f-status" name="status">
                    <option value="published" <?= old('status', $page['status'] ?? '') === 'published' ? 'selected' : '' ?>>Yayında</option>
                    <option value="draft" <?= old('status', $page['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Taslak</option>
                </select>
            </div>
            <label class="check-field">
                <input type="checkbox" name="show_in_menu" value="1" <?= old('show_in_menu', $page['show_in_menu'] ?? 0) ? 'checked' : '' ?>>
                Menüde göster
            </label>
            <div class="form-field">
                <label for="f-slug">Adres (slug)</label>
                <input id="f-slug" type="text" name="slug" value="<?= e(old('slug', $page['slug'] ?? '')) ?>" placeholder="boş bırakılırsa başlıktan üretilir">
                <?php if ($page !== null): ?><p class="hint">Boş bırakırsanız mevcut adres korunur: /<?= e($page['slug']) ?></p><?php endif; ?>
            </div>
            <div class="form-field">
                <label for="f-meta">SEO Açıklaması</label>
                <textarea id="f-meta" name="meta_description" rows="3" maxlength="300"><?= e(old('meta_description', $page['meta_description'] ?? '')) ?></textarea>
                <p class="hint">Arama motorlarında sayfa açıklaması olarak görünür.</p>
            </div>
            <button class="btn btn-primary btn-block" type="submit"><?= $page !== null ? 'Değişiklikleri Kaydet' : 'Sayfayı Oluştur' ?></button>
        </div>
    </div>
</form>
