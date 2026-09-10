<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Flash;
use App\Core\View;

abstract class AdminController
{
    protected array $authUser;

    public function __construct()
    {
        $user = Auth::user();
        if ($user === null) {
            Flash::set('error', 'Bu bölüm için giriş yapmalısınız.');
            redirect('/admin/login');
        }
        $this->authUser = $user;
    }

    /** Yalnızca admin rolünün girebildiği bölümler için. */
    protected function requireAdmin(): void
    {
        if ($this->authUser['role'] !== 'admin') {
            Flash::set('error', 'Bu bölüm yalnızca yöneticilere açıktır.');
            redirect('/admin');
        }
    }

    protected function render(string $template, array $data = []): string
    {
        $data['authUser'] = $this->authUser;
        return View::render($template, $data, 'admin');
    }
}
