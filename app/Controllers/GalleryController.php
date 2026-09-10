<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Album;

final class GalleryController
{
    public function index(): string
    {
        return View::render('front/gallery', [
            'pageTitle'       => 'Fotoğraf Galerisi',
            'metaDescription' => 'Derneğimizin etkinlik ve faaliyetlerinden fotoğraf albümleri.',
            'albums'          => Album::all(),
        ], 'front');
    }

    public function show(string $slug): string
    {
        $album = Album::findBySlug($slug);
        if ($album === null) {
            http_response_code(404);
            return (new ErrorController())->notFound();
        }

        return View::render('front/album', [
            'pageTitle'       => $album['title'],
            'metaDescription' => $album['title'] . ' — fotoğraf albümü.',
            'album'           => $album,
            'photos'          => Album::photos((int) $album['id']),
            'albums'          => Album::all(),
        ], 'front');
    }
}
