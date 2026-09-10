<?php
declare(strict_types=1);

namespace App\Core;

final class Flash
{
    public static function set(string $key, string $message): void
    {
        $_SESSION['_flash'][$key] = $message;
    }

    public static function pull(string $key): ?string
    {
        if (isset($_SESSION['_flash'][$key])) {
            $message = $_SESSION['_flash'][$key];
            unset($_SESSION['_flash'][$key]);
            return $message;
        }
        return null;
    }
}
