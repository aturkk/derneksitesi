<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\AdminController;

use App\Models\Album;
use App\Models\Message;
use App\Models\Page;
use App\Models\Post;

final class DashboardController extends AdminController
{
    public function index(): string
    {
        return $this->render('admin/dashboard', [
            'title'          => 'Genel Bakış',
            'pageCount'      => Page::count(),
            'postCount'      => Post::count(),
            'albumCount'     => Album::count(),
            'photoCount'     => \App\Models\Photo::count(),
            'messageCount'   => Message::count(),
            'unreadCount'    => Message::unreadCount(),
            'recentMessages' => \App\Core\Database::fetchAll('SELECT * FROM messages ORDER BY created_at DESC, id DESC LIMIT 5'),
            'recentPosts'    => Post::query([], 5),
        ]);
    }
}
