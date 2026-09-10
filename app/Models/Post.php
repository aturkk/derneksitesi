<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Post
{
    /**
     * Dinamik filtreli yazı listesi.
     * @param array{published?:bool, category_id?:int, q?:string} $filters
     */
    public static function query(array $filters, int $limit = 20, int $offset = 0): array
    {
        [$where, $params] = self::buildWhere($filters);
        $sql = 'SELECT p.*, c.name AS category_name, c.slug AS category_slug
                FROM posts p LEFT JOIN categories c ON c.id = p.category_id'
            . ($where !== [] ? ' WHERE ' . implode(' AND ', $where) : '')
            . ' ORDER BY p.published_at DESC, p.id DESC'
            . ' LIMIT ' . $limit . ' OFFSET ' . $offset;
        return Database::fetchAll($sql, $params);
    }

    public static function countQuery(array $filters): int
    {
        [$where, $params] = self::buildWhere($filters);
        $sql = 'SELECT COUNT(*) FROM posts p' . ($where !== [] ? ' WHERE ' . implode(' AND ', $where) : '');
        return (int) Database::fetchColumn($sql, $params);
    }

    /** @return array{0: string[], 1: array} */
    private static function buildWhere(array $filters): array
    {
        $where = [];
        $params = [];
        if (!empty($filters['published'])) {
            $where[] = "p.status = 'published'";
        }
        if (!empty($filters['category_id'])) {
            $where[] = 'p.category_id = ?';
            $params[] = (int) $filters['category_id'];
        }
        if (!empty($filters['q'])) {
            $where[] = '(p.title LIKE ? OR p.content LIKE ? OR p.excerpt LIKE ?)';
            $like = '%' . $filters['q'] . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }
        return [$where, $params];
    }

    public static function find(int $id): ?array
    {
        return Database::fetch(
            'SELECT p.*, c.name AS category_name FROM posts p
             LEFT JOIN categories c ON c.id = p.category_id WHERE p.id = ?',
            [$id]
        );
    }

    public static function findPublishedBySlug(string $slug): ?array
    {
        return Database::fetch(
            "SELECT p.*, c.name AS category_name, c.slug AS category_slug FROM posts p
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.slug = ? AND p.status = 'published' LIMIT 1",
            [$slug]
        );
    }

    public static function recent(int $limit = 6): array
    {
        return self::query(['published' => true], $limit);
    }

    public static function create(array $data): int
    {
        return Database::insert('posts', $data);
    }

    public static function update(int $id, array $data): void
    {
        Database::update('posts', $data, ['id' => $id]);
    }

    public static function delete(int $id): void
    {
        Database::delete('posts', ['id' => $id]);
    }

    public static function count(): int
    {
        return (int) Database::fetchColumn('SELECT COUNT(*) FROM posts');
    }
}
