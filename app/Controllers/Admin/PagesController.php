<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\AdminController;

use App\Core\Database;
use App\Core\Flash;
use App\Models\Page;

final class PagesController extends AdminController
{
    public function index(): string
    {
        return $this->render('admin/pages/index', [
            'title' => 'Sayfalar',
            'pages' => Page::all(),
        ]);
    }

    public function create(): string
    {
        return $this->render('admin/pages/form', [
            'title'  => 'Yeni Sayfa',
            'page'   => null,
            'editor' => true,
        ]);
    }

    public function store(): never
    {
        $errors = $this->validate($_POST);
        if ($errors !== []) {
            keep_old($_POST);
            Flash::set('error', implode(' ', $errors));
            redirect('/admin/pages/new');
        }

        Page::create([
            'title'            => trim((string) $_POST['title']),
            'slug'             => unique_slug('pages', (string) ($_POST['slug'] ?: $_POST['title'])),
            'content'          => (string) ($_POST['content'] ?? ''),
            'meta_description' => trim((string) ($_POST['meta_description'] ?? '')) ?: null,
            'status'           => ($_POST['status'] ?? '') === 'published' ? 'published' : 'draft',
            'show_in_menu'     => !empty($_POST['show_in_menu']) ? 1 : 0,
        ]);
        clear_old();
        Flash::set('success', 'Sayfa oluşturuldu.');
        redirect('/admin/pages');
    }

    public function edit(string $id): string
    {
        $page = Page::find((int) $id);
        if ($page === null) {
            Flash::set('error', 'Sayfa bulunamadı.');
            redirect('/admin/pages');
        }
        return $this->render('admin/pages/form', [
            'title'  => 'Sayfayı Düzenle',
            'page'   => $page,
            'editor' => true,
        ]);
    }

    public function update(string $id): never
    {
        $page = Page::find((int) $id);
        if ($page === null) {
            redirect('/admin/pages');
        }
        $errors = $this->validate($_POST);
        if ($errors !== []) {
            keep_old($_POST);
            Flash::set('error', implode(' ', $errors));
            redirect('/admin/pages/' . (int) $id . '/edit');
        }

        $newSlugSource = trim((string) ($_POST['slug'] ?? ''));
        $slug = $newSlugSource !== ''
            ? unique_slug('pages', $newSlugSource, (int) $id)
            : $page['slug']; // boş bırakılırsa mevcut slug korunur (bağlantılar kırılmaz)

        Page::update((int) $id, [
            'title'            => trim((string) $_POST['title']),
            'slug'             => $slug,
            'content'          => (string) ($_POST['content'] ?? ''),
            'meta_description' => trim((string) ($_POST['meta_description'] ?? '')) ?: null,
            'status'           => ($_POST['status'] ?? '') === 'published' ? 'published' : 'draft',
            'show_in_menu'     => !empty($_POST['show_in_menu']) ? 1 : 0,
        ]);
        clear_old();
        Flash::set('success', 'Sayfa güncellendi.');
        redirect('/admin/pages');
    }

    public function destroy(string $id): never
    {
        Page::delete((int) $id);
        Flash::set('success', 'Sayfa silindi.');
        redirect('/admin/pages');
    }

    public function move(string $id, string $dir): never
    {
        Page::move((int) $id, $dir === 'up' ? 'up' : 'down');
        redirect('/admin/pages');
    }

    /** @return string[] */
    private function validate(array $input): array
    {
        $errors = [];
        if (mb_strlen(trim((string) ($input['title'] ?? ''))) < 2) {
            $errors[] = 'Sayfa başlığı en az 2 karakter olmalı.';
        }
        if (!in_array($input['status'] ?? '', ['draft', 'published'], true)) {
            $errors[] = 'Geçersiz durum seçimi.';
        }
        return $errors;
    }
}
