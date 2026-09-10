<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Album;
use App\Models\Page;
use App\Models\Post;
use App\Models\Slide;

final class HomeController
{
    public function index(): string
    {
        $posts = Post::recent(6);
        $albums = Album::all();
        $galleryPhotos = $albums !== [] ? Album::photos((int) $albums[0]['id'], 8) : [];
        $about = Page::findBySlug('hakkimizda', true);

        return View::render('front/home', [
            'pageTitle'     => null,
            'posts'         => $posts,
            'galleryPhotos' => $galleryPhotos,
            'galleryTitle'  => $albums !== [] ? $albums[0]['title'] : null,
            'about'         => $about,
            'heroSlides'    => Slide::published(),
        ], 'front');
    }
}
