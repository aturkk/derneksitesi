<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\AdminController;

use App\Core\Flash;
use App\Models\Message;

final class MessagesController extends AdminController
{
    public function index(): string
    {
        $total = Message::count();
        $pager = paginate($total, 15);

        return $this->render('admin/messages/index', [
            'title'    => 'Mesajlar',
            'messages' => Message::paginate($pager['perPage'], $pager['offset']),
            'pager'    => $pager,
        ]);
    }

    public function show(string $id): string
    {
        $message = Message::find((int) $id);
        if ($message === null) {
            Flash::set('error', 'Mesaj bulunamadı.');
            redirect('/admin/messages');
        }
        Message::markRead((int) $id);
        return $this->render('admin/messages/show', [
            'title'   => 'Mesaj',
            'message' => $message,
        ]);
    }

    public function destroy(string $id): never
    {
        Message::delete((int) $id);
        Flash::set('success', 'Mesaj silindi.');
        redirect('/admin/messages');
    }
}
