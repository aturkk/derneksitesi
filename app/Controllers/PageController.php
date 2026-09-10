<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Page;

final class PageController
{
    public function show(string $slug): string
    {
        $page = Page::findBySlug($slug, true);
        if ($page === null) {
            http_response_code(404);
            return (new ErrorController())->notFound();
        }

        return View::render('front/page', [
            'pageTitle'       => $page['title'],
            'metaDescription' => $page['meta_description'] ?: null,
            'page'            => $page,
        ], 'front');
    }
}
