<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Flash;
use App\Core\View;

final class AuthController
{
    public function showLogin(): string
    {
        if (Auth::check()) {
            redirect('/admin');
        }
        if (isset($_GET['installed'])) {
            Flash::set('success', 'Kurulum tamamlandı! Yönetici hesabınızla giriş yapabilirsiniz.');
        }
        return View::render('admin/login');
    }

    public function login(): never
    {
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        $result = Auth::attempt($email, $password);
        if ($result['ok']) {
            clear_old();
            Flash::set('success', 'Hoş geldiniz, ' . (Auth::user()['name'] ?? '') . '!');
            redirect('/admin');
        }
        Flash::set('error', $result['throttled']
            ? 'Çok fazla hatalı deneme yaptınız. 10 dakika sonra tekrar deneyin.'
            : 'E-posta veya şifre hatalı.');
        keep_old(['email' => $email]);
        redirect('/admin/login');
    }

    public function logout(): never
    {
        Auth::logout();
        Flash::set('success', 'Güvenli çıkış yapıldı.');
        redirect('/admin/login');
    }
}
