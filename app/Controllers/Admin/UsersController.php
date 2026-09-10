<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\AdminController;

use App\Core\Auth;
use App\Core\Flash;
use App\Models\User;

final class UsersController extends AdminController
{
    public function index(): string
    {
        $this->requireAdmin();
        return $this->render('admin/users/index', [
            'title' => 'Kullanıcılar',
            'users' => User::all(),
        ]);
    }

    public function create(): string
    {
        $this->requireAdmin();
        return $this->render('admin/users/form', [
            'title'    => 'Yeni Kullanıcı',
            'editUser' => null,
        ]);
    }

    public function store(): never
    {
        $this->requireAdmin();
        [$data, $errors] = $this->collect($_POST, true);
        if ($errors !== []) {
            keep_old($_POST);
            Flash::set('error', implode(' ', $errors));
            redirect('/admin/users/new');
        }
        User::create($data);
        clear_old();
        Flash::set('success', 'Kullanıcı oluşturuldu.');
        redirect('/admin/users');
    }

    public function edit(string $id): string
    {
        $this->requireAdmin();
        $editUser = User::find((int) $id);
        if ($editUser === null) {
            Flash::set('error', 'Kullanıcı bulunamadı.');
            redirect('/admin/users');
        }
        return $this->render('admin/users/form', [
            'title'    => 'Kullanıcıyı Düzenle',
            'editUser' => $editUser,
        ]);
    }

    public function update(string $id): never
    {
        $this->requireAdmin();
        $editUser = User::find((int) $id);
        if ($editUser === null) {
            redirect('/admin/users');
        }
        [$data, $errors] = $this->collect($_POST, false, (int) $id, $editUser);
        if ($errors !== []) {
            keep_old($_POST);
            Flash::set('error', implode(' ', $errors));
            redirect('/admin/users/' . (int) $id . '/edit');
        }
        if (($data['password'] ?? '') === '') {
            unset($data['password']);
        } else {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        unset($data['password']);
        User::update((int) $id, $data);
        clear_old();
        Flash::set('success', 'Kullanıcı güncellendi.');
        redirect('/admin/users');
    }

    public function destroy(string $id): never
    {
        $this->requireAdmin();
        $target = User::find((int) $id);
        if ($target === null) {
            redirect('/admin/users');
        }
        if ((int) $id === (int) $this->authUser['id']) {
            Flash::set('error', 'Kendi hesabınızı silemezsiniz.');
            redirect('/admin/users');
        }
        if ($target['role'] === 'admin' && User::adminCount() <= 1) {
            Flash::set('error', 'Son yönetici hesabı silinemez.');
            redirect('/admin/users');
        }
        User::delete((int) $id);
        Flash::set('success', 'Kullanıcı silindi.');
        redirect('/admin/users');
    }

    // --- Profil (kendi hesabı) ---

    public function profile(): string
    {
        return $this->render('admin/profile', [
            'title'    => 'Profilim',
            'editUser' => $this->authUser,
        ]);
    }

    public function updateProfile(): never
    {
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $current = (string) ($_POST['current_password'] ?? '');
        $new = (string) ($_POST['new_password'] ?? '');

        if (mb_strlen($name) < 2) {
            Flash::set('error', 'Ad en az 2 karakter olmalı.');
            redirect('/admin/profile');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || User::emailExists($email, (int) $this->authUser['id'])) {
            Flash::set('error', 'Geçerli ve kullanımda olmayan bir e-posta girin.');
            redirect('/admin/profile');
        }
        User::update((int) $this->authUser['id'], ['name' => $name, 'email' => $email]);

        if ($new !== '') {
            $row = \App\Core\Database::fetch('SELECT password_hash FROM users WHERE id = ?', [(int) $this->authUser['id']]);
            if ($row === null || !password_verify($current, $row['password_hash'])) {
                Flash::set('error', 'Şifre değiştirilemedi: mevcut şifreniz hatalı. Diğer bilgiler güncellendi.');
                redirect('/admin/profile');
            }
            if (strlen($new) < 8) {
                Flash::set('error', 'Yeni şifre en az 8 karakter olmalı. Diğer bilgiler güncellendi.');
                redirect('/admin/profile');
            }
            User::update((int) $this->authUser['id'], ['password_hash' => password_hash($new, PASSWORD_DEFAULT)]);
            Flash::set('success', 'Profiliniz ve şifreniz güncellendi.');
        } else {
            Flash::set('success', 'Profiliniz güncellendi.');
        }
        redirect('/admin/profile');
    }

    /** @return array{0: array, 1: string[]} */
    private function collect(array $input, bool $creating, int $ignoreId = 0, ?array $existing = null): array
    {
        $errors = [];
        $name = trim((string) ($input['name'] ?? ''));
        $email = trim((string) ($input['email'] ?? ''));
        $role = ($input['role'] ?? '') === 'admin' ? 'admin' : 'editor';
        $password = (string) ($input['password'] ?? '');

        if (mb_strlen($name) < 2) {
            $errors[] = 'Ad en az 2 karakter olmalı.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Geçerli bir e-posta girin.';
        } elseif (User::emailExists($email, $ignoreId)) {
            $errors[] = 'Bu e-posta zaten kullanımda.';
        }
        if ($creating && strlen($password) < 8) {
            $errors[] = 'Şifre en az 8 karakter olmalı.';
        }
        if (!$creating && $password !== '' && strlen($password) < 8) {
            $errors[] = 'Yeni şifre en az 8 karakter olmalı.';
        }

        // Kendi rolünü düşürmeyi engelle (son yönetici kalmasın)
        if ($existing !== null && (int) $existing['id'] === (int) $this->authUser['id']) {
            $role = $existing['role'];
        } elseif ($existing !== null && $existing['role'] === 'admin' && $role !== 'admin' && User::adminCount() <= 1) {
            $errors[] = 'Son yöneticinin rolü değiştirilemez.';
        }

        $data = ['name' => $name, 'email' => $email, 'role' => $role];
        if ($creating) {
            $data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        } else {
            $data['password'] = $password; // update() içinde işlenir
        }
        return [$data, $errors];
    }
}
