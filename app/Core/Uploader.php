<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Yüklenen resimleri doğrular ve public/uploads altına rastgele adla kaydeder.
 * GD yüklüyse büyük resimler 1920 piksel genişliğe küçültülür.
 */
final class Uploader
{
    private const ALLOWED = [
        'image/jpeg' => '.jpg',
        'image/png'  => '.png',
        'image/webp' => '.webp',
        'image/gif'  => '.gif',
    ];

    private const MAX_BYTES = 8 * 1024 * 1024;
    private const MAX_WIDTH = 1920;

    /**
     * @param array|null $file $_FILES içindeki tek dosya dizisi
     * @return string|null uploads köküne göre göreli dosya adı, başarısızsa null
     */
    public static function image(?array $file): ?string
    {
        if ($file === null || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return null;
        }
        if (!is_uploaded_file($file['tmp_name'])) {
            return null;
        }
        if (($file['size'] ?? 0) <= 0 || $file['size'] > self::MAX_BYTES) {
            return null;
        }

        $info = @getimagesize($file['tmp_name']);
        if ($info === false) {
            return null;
        }
        $ext = self::ALLOWED[$info['mime']] ?? null;
        if ($ext === null) {
            return null;
        }

        $name = date('Ymd') . '-' . bin2hex(random_bytes(8)) . $ext;
        $dir = BASE_PATH . '/public/uploads';
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $dest = $dir . '/' . $name;

        if (self::shrink($file['tmp_name'], $dest, $info['mime'])) {
            return $name;
        }
        return move_uploaded_file($file['tmp_name'], $dest) ? $name : null;
    }

    /** GD varsa resmi MAX_WIDTH genişliğinin altına indirir. */
    private static function shrink(string $tmp, string $dest, string $mime): bool
    {
        if (!function_exists('imagecreatetruecolor')) {
            return false;
        }
        try {
            [$width, $height] = getimagesize($tmp);
            if ($width <= self::MAX_WIDTH) {
                return false; // küçültmeye gerek yok, düz taşı
            }
            $ratio = self::MAX_WIDTH / $width;
            $newW = self::MAX_WIDTH;
            $newH = max(1, (int) round($height * $ratio));

            $src = match ($mime) {
                'image/jpeg' => @imagecreatefromjpeg($tmp),
                'image/png'  => @imagecreatefrompng($tmp),
                'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($tmp) : null,
                'image/gif'  => @imagecreatefromgif($tmp),
                default      => null,
            };
            if (!$src) {
                return false;
            }
            $dst = imagecreatetruecolor($newW, $newH);
            if (in_array($mime, ['image/png', 'image/gif'], true)) {
                imagealphablending($dst, false);
                imagesavealpha($dst, true);
                $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
                imagefill($dst, 0, 0, $transparent);
            }
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $width, $height);
            $ok = match ($mime) {
                'image/jpeg' => imagejpeg($dst, $dest, 82),
                'image/png'  => imagepng($dst, $dest, 6),
                'image/webp' => function_exists('imagewebp') ? imagewebp($dst, $dest, 82) : false,
                'image/gif'  => imagegif($dst, $dest),
                default      => false,
            };
            imagedestroy($src);
            imagedestroy($dst);
            return (bool) $ok;
        } catch (\Throwable) {
            return false;
        }
    }
}
