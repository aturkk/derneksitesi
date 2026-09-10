<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Page
{
    public static function all(): array
    {
        return Database::fetchAll('SELECT * FROM pages ORDER BY sort_order ASC, id ASC');
    }

    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM pages WHERE id = ?', [$id]);
    }

    public static function findBySlug(string $slug, bool $onlyPublished = false): ?array
    {
        $sql = 'SELECT * FROM pages WHERE slug = ?';
        if ($onlyPublished) {
            $sql .= " AND status = 'published'";
        }
        return Database::fetch($sql . ' LIMIT 1', [$slug]);
    }

    public static function create(array $data): int
    {
        $data['sort_order'] = (int) (Database::fetchColumn('SELECT COALESCE(MAX(sort_order), 0) + 1 FROM pages') ?: 1);
        return Database::insert('pages', $data);
    }

    public static function update(int $id, array $data): void
    {
        Database::update('pages', $data, ['id' => $id]);
    }

    public static function delete(int $id): void
    {
        Database::delete('pages', ['id' => $id]);
    }

    /** Sayfayı listede bir yukarı/aşağı taşır. */
    public static function move(int $id, string $dir): void
    {
        $rows = Database::fetchAll('SELECT id, sort_order FROM pages ORDER BY sort_order ASC, id ASC');
        $index = null;
        foreach ($rows as $i => $row) {
            if ((int) $row['id'] === $id) {
                $index = $i;
                break;
            }
        }
        if ($index === null) {
            return;
        }
        $swap = $dir === 'up' ? $index - 1 : $index + 1;
        if ($swap < 0 || $swap >= count($rows)) {
            return;
        }
        Database::update('pages', ['sort_order' => (int) $rows[$swap]['sort_order']], ['id' => (int) $rows[$index]['id']]);
        Database::update('pages', ['sort_order' => (int) $rows[$index]['sort_order']], ['id' => (int) $rows[$swap]['id']]);
    }

    public static function count(): int
    {
        return (int) Database::fetchColumn('SELECT COUNT(*) FROM pages');
    }
}
