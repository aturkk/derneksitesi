<form method="post" action="<?= url('/admin/albums') ?>" class="form-panel slim">
    <?= csrf_field() ?>
    <div class="form-field">
        <label for="f-title">Albüm Adı *</label>
        <input id="f-title" type="text" name="title" value="<?= e(old('title')) ?>" required>
        <p class="hint">Albümü oluşturduktan sonra fotoğraflarını yükleyebileceksiniz.</p>
    </div>
    <button class="btn btn-primary" type="submit">Albümü Oluştur</button>
</form>
