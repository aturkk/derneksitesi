<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Slide
{
    public static function all(): array
    {
        return Database::fetchAll('SELECT * FROM slides ORDER BY sort_order ASC, id ASC');
    }

    public static function published(): array
    {
        return Database::fetchAll(
            "SELECT * FROM slides WHERE status = 'published' ORDER BY sort_order ASC, id ASC"
        );
    }

    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM slides WHERE id = ?', [$id]);
    }

    public static function create(array $data): int
    {
        $data['sort_order'] = (int) (Database::fetchColumn('SELECT COALESCE(MAX(sort_order), 0) + 1 FROM slides') ?: 1);
        return Database::insert('slides', $data);
    }

    public static function update(int $id, array $data): void
    {
        Database::update('slides', $data, ['id' => $id]);
    }

    public static function delete(int $id): void
    {
        $slide = self::find($id);
        if ($slide === null) {
            return;
        }
        Database::delete('slides', ['id' => $id]);
        self::deleteFile((string) $slide['image'], $id);
    }

    /** Dosyayı diskten kaldırır; $ignoreId dışında başka slaytta aynı dosya kullanılıyorsa dokunmaz. */
    public static function deleteFile(string $filename, int $ignoreId = 0): void
    {
        if ($filename === '') {
            return;
        }
        $inUse = (int) Database::fetchColumn(
            'SELECT COUNT(*) FROM slides WHERE image = ? AND id != ?',
            [$filename, $ignoreId]
        );
        if ($inUse === 0 && is_file(BASE_PATH . '/public/uploads/' . $filename)) {
            @unlink(BASE_PATH . '/public/uploads/' . $filename);
        }
    }

    public static function move(int $id, string $dir): void
    {
        $rows = Database::fetchAll('SELECT id, sort_order FROM slides ORDER BY sort_order ASC, id ASC');
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
        Database::update('slides', ['sort_order' => (int) $rows[$swap]['sort_order']], ['id' => (int) $rows[$index]['id']]);
        Database::update('slides', ['sort_order' => (int) $rows[$index]['sort_order']], ['id' => (int) $rows[$swap]['id']]);
    }

    public static function count(): int
    {
        return (int) Database::fetchColumn('SELECT COUNT(*) FROM slides');
    }
}
