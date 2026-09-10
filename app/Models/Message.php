<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Message
{
    public static function paginate(int $perPage, int $offset): array
    {
        return Database::fetchAll(
            'SELECT * FROM messages ORDER BY created_at DESC, id DESC LIMIT ' . $perPage . ' OFFSET ' . $offset
        );
    }

    public static function count(): int
    {
        return (int) Database::fetchColumn('SELECT COUNT(*) FROM messages');
    }

    public static function unreadCount(): int
    {
        return (int) Database::fetchColumn('SELECT COUNT(*) FROM messages WHERE is_read = 0');
    }

    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM messages WHERE id = ?', [$id]);
    }

    public static function create(array $data): int
    {
        return Database::insert('messages', $data);
    }

    public static function markRead(int $id): void
    {
        Database::update('messages', ['is_read' => 1], ['id' => $id]);
    }

    public static function delete(int $id): void
    {
        Database::delete('messages', ['id' => $id]);
    }
}
