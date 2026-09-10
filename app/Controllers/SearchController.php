<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\View;
use App\Models\Post;

final class SearchController
{
    public function index(): string
    {
        $q = trim((string) ($_GET['q'] ?? ''));
        $posts = [];
        $pages = [];

        if (mb_strlen($q) >= 2) {
            $posts = Post::query(['published' => true, 'q' => $q], 20);
            $like = '%' . $q . '%';
            $pages = Database::fetchAll(
                "SELECT title, slug, content FROM pages
                 WHERE status = 'published' AND (title LIKE ? OR content LIKE ?)
                 ORDER BY title LIMIT 20",
                [$like, $like]
            );
        }

        return View::render('front/search', [
            'pageTitle' => 'Arama',
            'q'         => $q,
            'posts'     => $posts,
            'pages'     => $pages,
        ], 'front');
    }
}
