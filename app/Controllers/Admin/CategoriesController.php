<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\AdminController;

use App\Core\Database;
use App\Core\Flash;
use App\Models\Photo;

final class CategoriesController extends AdminController
{
    public function index(): string
    {
        return $this->render('admin/categories/index', [
            'title'      => 'Kategoriler',
            'categories' => Database::fetchAll(
                'SELECT c.*, (SELECT COUNT(*) FROM posts p WHERE p.category_id = c.id) AS post_count
                 FROM categories c ORDER BY c.name ASC'
            ),
        ]);
    }

    public function store(): never
    {
        $name = trim((string) ($_POST['name'] ?? ''));
        if (mb_strlen($name) < 2) {
            Flash::set('error', 'Kategori adı en az 2 karakter olmalı.');
            redirect('/admin/categories');
        }
        try {
            Database::run('INSERT INTO categories (name, slug) VALUES (?, ?)', [$name, unique_slug('categories', $name)]);
            Flash::set('success', 'Kategori eklendi.');
        } catch (\Throwable $e) {
            Flash::set('error', 'Kategori eklenemedi.');
        }
        redirect('/admin/categories');
    }

    public function destroy(string $id): never
    {
        Database::delete('categories', ['id' => (int) $id]);
        Flash::set('success', 'Kategori silindi. Bu kategorideki yazılar kategorisiz kaldı.');
        redirect('/admin/categories');
    }
}
