<?php /** @var array|null $post */ ?>
<form method="post" action="<?= $post !== null ? url('/admin/posts/' . $post['id']) : url('/admin/posts') ?>" class="form-panel" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="form-grid-2">
        <div class="form-main">
            <div class="form-field">
                <label for="f-title">Yazı Başlığı *</label>
                <input id="f-title" type="text" name="title" value="<?= e(old('title', $post['title'] ?? '')) ?>" required>
            </div>
            <div class="form-field">
                <label for="f-excerpt">Kısa Özet</label>
                <textarea id="f-excerpt" name="excerpt" rows="3" maxlength="500" placeholder="Boş bırakılırsa içerikten otomatik üretilir"><?= e(old('excerpt', $post['excerpt'] ?? '')) ?></textarea>
            </div>
            <div class="form-field">
                <label for="f-content">İçerik</label>
                <textarea id="f-content" class="tinymce" name="content" rows="16"><?= e(old('content', $post['content'] ?? '')) ?></textarea>
            </div>
        </div>
        <div class="form-side">
            <div class="form-field">
                <label for="f-status">Durum</label>
                <select id="f-status" name="status">
                    <option value="published" <?= old('status', $post['status'] ?? '') === 'published' ? 'selected' : '' ?>>Yayında</option>
                    <option value="draft" <?= old('status', $post['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Taslak</option>
                </select>
            </div>
            <div class="form-field">
                <label for="f-category">Kategori</label>
                <select id="f-category" name="category_id">
                    <option value="">Kategorisiz</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?= (int) $cat['id'] ?>" <?= (string) old('category_id', $post['category_id'] ?? '') === (string) $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-field">
                <label for="f-date">Yayın Tarihi</label>
                <input id="f-date" type="datetime-local" name="published_at" value="<?= e(old('published_at', !empty($post['published_at']) ? date('Y-m-d\TH:i', strtotime($post['published_at'])) : '')) ?>">
                <p class="hint">Boş bırakılırsa yayına alırken şimdi olarak ayarlanır.</p>
            </div>
            <div class="form-field">
                <label for="f-cover">Kapak Görseli</label>
                <?php if (!empty($post['cover_image'])): ?>
                <img class="cover-preview" src="<?= upload_url($post['cover_image']) ?>" alt="">
                <?php endif; ?>
                <input id="f-cover" type="file" name="cover" accept="image/jpeg,image/png,image/webp,image/gif">
                <p class="hint">JPG, PNG, WEBP veya GIF — en fazla 8 MB. Yeni seçim eskisinin üzerine yazar.</p>
            </div>
            <div class="form-field">
                <label for="f-meta">SEO Açıklaması</label>
                <textarea id="f-meta" name="meta_description" rows="3" maxlength="300"><?= e(old('meta_description', $post['meta_description'] ?? '')) ?></textarea>
            </div>
            <button class="btn btn-primary btn-block" type="submit"><?= $post !== null ? 'Değişiklikleri Kaydet' : 'Yazıyı Oluştur' ?></button>
        </div>
    </div>
</form>
