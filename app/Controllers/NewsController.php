<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Post;

final class NewsController
{
    public function index(): string
    {
        $category = null;
        $categoryId = null;
        $catSlug = trim((string) ($_GET['kategori'] ?? ''));
        if ($catSlug !== '') {
            $category = \App\Core\Database::fetch('SELECT * FROM categories WHERE slug = ?', [$catSlug]);
            if ($category !== null) {
                $categoryId = (int) $category['id'];
            }
        }

        $total = Post::countQuery(['published' => true, 'category_id' => $categoryId]);
        $pager = paginate($total, 6);
        $posts = Post::query(['published' => true, 'category_id' => $categoryId], $pager['perPage'], $pager['offset']);
        $categories = \App\Core\Database::fetchAll('SELECT name, slug FROM categories ORDER BY name');

        return View::render('front/news', [
            'pageTitle'       => $category !== null ? $category['name'] : 'Haberler',
            'metaDescription' => 'Derneğimizin güncel haberleri ve duyuruları.',
            'posts'           => $posts,
            'categories'      => $categories,
            'category'        => $category,
            'pager'           => $pager,
        ], 'front');
    }

    public function show(string $slug): string
    {
        $post = Post::findPublishedBySlug($slug);
        if ($post === null) {
            http_response_code(404);
            return (new ErrorController())->notFound();
        }
        $others = array_values(array_filter(
            Post::recent(4),
            static fn ($row) => (int) $row['id'] !== (int) $post['id']
        ));

        return View::render('front/post', [
            'pageTitle'       => $post['title'],
            'metaDescription' => $post['meta_description'] ?: ($post['excerpt'] ?: null),
            'post'            => $post,
            'others'          => array_slice($others, 0, 3),
        ], 'front');
    }
}
