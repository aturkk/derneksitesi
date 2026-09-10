<?php
declare(strict_types=1);

namespace App\Core;

final class Auth
{
    /**
     * @return array{ok: bool, throttled: bool}
     */
    public static function attempt(string $email, string $password): array
    {
        $now = time();
        $fails = array_values(array_filter(
            $_SESSION['login_fails'] ?? [],
            static fn ($t) => $t > $now - 600
        ));

        if (count($fails) >= 5) {
            return ['ok' => false, 'throttled' => true];
        }

        $user = Database::fetch('SELECT * FROM users WHERE email = ?', [$email]);

        if ($user === null || !password_verify($password, $user['password_hash'])) {
            $_SESSION['login_fails'] = array_merge($fails, [$now]);
            return ['ok' => false, 'throttled' => false];
        }

        unset($_SESSION['login_fails']);
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user_role'] = $user['role'];
        return ['ok' => true, 'throttled' => false];
    }

    public static function user(): ?array
    {
        if (empty($_SESSION['user_id'])) {
            return null;
        }
        static $cached = null;
        if ($cached === null) {
            $cached = Database::fetch(
                'SELECT id, name, email, role, created_at FROM users WHERE id = ?',
                [(int) $_SESSION['user_id']]
            );
        }
        return $cached;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function isAdmin(): bool
    {
        $user = self::user();
        return $user !== null && $user['role'] === 'admin';
    }

    public static function logout(): void
    {
        unset($_SESSION['user_id'], $_SESSION['user_role']);
        session_regenerate_id(true);
    }
}
