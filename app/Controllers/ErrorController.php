<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

final class ErrorController
{
    public function notFound(): string
    {
        return View::render('errors/404', ['pageTitle' => 'Sayfa Bulunamadı'], 'front');
    }
}
