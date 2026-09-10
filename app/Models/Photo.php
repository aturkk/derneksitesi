<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Photo
{
    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM photos WHERE id = ?', [$id]);
    }

    public static function add(int $albumId, string $filename, ?string $title): int
    {
        $sort = (int) (Database::fetchColumn(
            'SELECT COALESCE(MAX(sort_order), 0) + 1 FROM photos WHERE album_id = ?',
            [$albumId]
        ) ?: 1);
        return Database::insert('photos', [
            'album_id'   => $albumId,
            'filename'   => $filename,
            'title'      => $title,
            'sort_order' => $sort,
        ]);
    }

    public static function delete(int $id): void
    {
        $photo = self::find($id);
        if ($photo === null) {
            return;
        }
        // Önce satırı sil: deleteFile, dosyanın başka kayıtta kullanımda olup olmadığını
        // veritabanından kontrol ettiği için sıralama önemli.
        Database::delete('photos', ['id' => $id]);
        self::deleteFile($photo['filename']);
    }

    /** Dosyayı diskten kaldırır; başka kayıtta aynı dosya varsa dokunmaz. */
    public static function deleteFile(string $filename): void
    {
        $inUse = (int) Database::fetchColumn('SELECT COUNT(*) FROM photos WHERE filename = ?', [$filename])
               + (int) Database::fetchColumn('SELECT COUNT(*) FROM posts WHERE cover_image = ?', [$filename]);
        if ($inUse === 0 && is_file(BASE_PATH . '/public/uploads/' . $filename)) {
            @unlink(BASE_PATH . '/public/uploads/' . $filename);
        }
    }

    public static function move(int $id, string $dir): void
    {
        $photo = self::find($id);
        if ($photo === null) {
            return;
        }
        $rows = Database::fetchAll(
            'SELECT id, sort_order FROM photos WHERE album_id = ? ORDER BY sort_order ASC, id ASC',
            [(int) $photo['album_id']]
        );
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
        Database::update('photos', ['sort_order' => (int) $rows[$swap]['sort_order']], ['id' => (int) $rows[$index]['id']]);
        Database::update('photos', ['sort_order' => (int) $rows[$index]['sort_order']], ['id' => (int) $rows[$swap]['id']]);
    }

    public static function count(): int
    {
        return (int) Database::fetchColumn('SELECT COUNT(*) FROM photos');
    }
}
