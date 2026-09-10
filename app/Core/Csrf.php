<?php
declare(strict_types=1);

namespace App\Core;

final class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }

    /** index.php tüm POST istekleri için çağırır; form alanı veya X-CSRF-Token başlığı kabul edilir. */
    public static function validateRequest(): void
    {
        $sent = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!is_string($sent) || $sent === '' || !hash_equals(self::token(), $sent)) {
            http_response_code(419);
            header('Content-Type: text/html; charset=utf-8');
            exit('Güvenlik doğrulaması başarısız oldu. Sayfayı yenileyip tekrar deneyin.');
        }
        unset($_POST['_token']);
    }
}
