<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Album
{
    public static function all(): array
    {
        return Database::fetchAll(
            'SELECT a.*,
                    (SELECT COUNT(*) FROM photos ph WHERE ph.album_id = a.id) AS photo_count,
                    (SELECT ph2.filename FROM photos ph2 WHERE ph2.album_id = a.id
                     ORDER BY ph2.sort_order ASC, ph2.id ASC LIMIT 1) AS cover
             FROM albums a
             ORDER BY a.sort_order ASC, a.id ASC'
        );
    }

    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM albums WHERE id = ?', [$id]);
    }

    public static function findBySlug(string $slug): ?array
    {
        return Database::fetch('SELECT * FROM albums WHERE slug = ? LIMIT 1', [$slug]);
    }

    public static function photos(int $albumId, int $limit = 0): array
    {
        $sql = 'SELECT * FROM photos WHERE album_id = ? ORDER BY sort_order ASC, id ASC';
        if ($limit > 0) {
            $sql .= ' LIMIT ' . $limit;
        }
        return Database::fetchAll($sql, [$albumId]);
    }

    public static function create(array $data): int
    {
        $data['sort_order'] = (int) (Database::fetchColumn('SELECT COALESCE(MAX(sort_order), 0) + 1 FROM albums') ?: 1);
        return Database::insert('albums', $data);
    }

    public static function update(int $id, array $data): void
    {
        Database::update('albums', $data, ['id' => $id]);
    }

    /** Albümü ve fotoğraflarını siler (dosyalar da diskten kaldırılır). */
    public static function delete(int $id): void
    {
        $filenames = array_column(self::photos($id), 'filename');
        // Satırlar CASCADE ile gittikten sonra dosyaları temizle (kullanım kontrolü doğru çalışsın).
        Database::delete('albums', ['id' => $id]);
        foreach ($filenames as $filename) {
            Photo::deleteFile($filename);
        }
    }

    public static function move(int $id, string $dir): void
    {
        $rows = Database::fetchAll('SELECT id, sort_order FROM albums ORDER BY sort_order ASC, id ASC');
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
        Database::update('albums', ['sort_order' => (int) $rows[$swap]['sort_order']], ['id' => (int) $rows[$index]['id']]);
        Database::update('albums', ['sort_order' => (int) $rows[$index]['sort_order']], ['id' => (int) $rows[$swap]['id']]);
    }

    public static function count(): int
    {
        return (int) Database::fetchColumn('SELECT COUNT(*) FROM albums');
    }
}
