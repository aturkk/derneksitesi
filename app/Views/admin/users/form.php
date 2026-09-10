<?php /** @var array|null $editUser */ ?>
<form method="post" action="<?= $editUser !== null ? url('/admin/users/' . $editUser['id']) : url('/admin/users') ?>" class="form-panel slim">
    <?= csrf_field() ?>
    <div class="form-field">
        <label for="u-name">Ad Soyad *</label>
        <input id="u-name" type="text" name="name" value="<?= e(old('name', $editUser['name'] ?? '')) ?>" required>
    </div>
    <div class="form-field">
        <label for="u-email">E-posta *</label>
        <input id="u-email" type="email" name="email" value="<?= e(old('email', $editUser['email'] ?? '')) ?>" required>
    </div>
    <div class="form-field">
        <label for="u-role">Rol</label>
        <select id="u-role" name="role" <?= $editUser !== null && (int) $editUser['id'] === (int) $authUser['id'] ? 'disabled' : '' ?>>
            <option value="editor" <?= old('role', $editUser['role'] ?? '') === 'editor' ? 'selected' : '' ?>>Editör (yalnız içerik yönetir)</option>
            <option value="admin" <?= old('role', $editUser['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Yönetici (tüm yetkiler)</option>
        </select>
        <?php if ($editUser !== null && (int) $editUser['id'] === (int) $authUser['id']): ?>
        <input type="hidden" name="role" value="<?= e($editUser['role']) ?>">
        <p class="hint">Kendi rolünüz değiştirilemez.</p>
        <?php endif; ?>
    </div>
    <div class="form-field">
        <label for="u-pass"><?= $editUser !== null ? 'Yeni Şifre (değiştirmek istemiyorsanız boş bırakın)' : 'Şifre * (en az 8 karakter)' ?></label>
        <input id="u-pass" type="password" name="password" <?= $editUser === null ? 'required' : '' ?> minlength="8">
    </div>
    <button class="btn btn-primary" type="submit"><?= $editUser !== null ? 'Değişiklikleri Kaydet' : 'Kullanıcıyı Oluştur' ?></button>
</form>
