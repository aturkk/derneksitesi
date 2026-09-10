<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\AdminController;

use App\Core\Uploader;

/** TinyMCE görsel yükleme uç noktası (JSON). */
final class MediaController extends AdminController
{
    public function upload(): never
    {
        header('Content-Type: application/json; charset=utf-8');
        $filename = Uploader::image($_FILES['file'] ?? null);
        if ($filename === null) {
            http_response_code(422);
            echo json_encode(['error' => 'Yükleme başarısız. Yalnızca JPG, PNG, WEBP ve GIF kabul edilir (en fazla 8 MB).']);
            exit;
        }
        echo json_encode(['location' => upload_url($filename)]);
        exit;
    }
}
