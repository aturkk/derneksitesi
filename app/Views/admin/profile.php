<form method="post" action="<?= url('/admin/profile') ?>" class="form-panel slim">
    <?= csrf_field() ?>
    <div class="form-field">
        <label for="p-name">Ad Soyad</label>
        <input id="p-name" type="text" name="name" value="<?= e(old('name', $editUser['name'] ?? '')) ?>" required>
    </div>
    <div class="form-field">
        <label for="p-email">E-posta</label>
        <input id="p-email" type="email" name="email" value="<?= e(old('email', $editUser['email'] ?? '')) ?>" required>
    </div>
    <fieldset class="form-fieldset">
        <legend>Şifre Değiştir (isteğe bağlı)</legend>
        <div class="form-field">
            <label for="p-current">Mevcut Şifre</label>
            <input id="p-current" type="password" name="current_password" autocomplete="current-password">
        </div>
        <div class="form-field">
            <label for="p-new">Yeni Şifre (en az 8 karakter)</label>
            <input id="p-new" type="password" name="new_password" autocomplete="new-password">
        </div>
    </fieldset>
    <button class="btn btn-primary" type="submit">Profili Güncelle</button>
</form>
