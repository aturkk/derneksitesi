<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class User
{
    public static function all(): array
    {
        return Database::fetchAll('SELECT id, name, email, role, created_at FROM users ORDER BY id ASC');
    }

    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT id, name, email, role, created_at FROM users WHERE id = ?', [$id]);
    }

    public static function emailExists(string $email, int $ignoreId = 0): bool
    {
        return (int) Database::fetchColumn(
            'SELECT COUNT(*) FROM users WHERE email = ? AND id != ?',
            [$email, $ignoreId]
        ) > 0;
    }

    public static function create(array $data): int
    {
        return Database::insert('users', $data);
    }

    public static function update(int $id, array $data): void
    {
        Database::update('users', $data, ['id' => $id]);
    }

    public static function delete(int $id): void
    {
        Database::delete('users', ['id' => $id]);
    }

    public static function adminCount(): int
    {
        return (int) Database::fetchColumn("SELECT COUNT(*) FROM users WHERE role = 'admin'");
    }
}
