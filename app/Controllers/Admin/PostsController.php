<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\AdminController;

use App\Core\Database;
use App\Core\Flash;
use App\Core\Uploader;
use App\Models\Post;

final class PostsController extends AdminController
{
    public function index(): string
    {
        $total = Post::countQuery(['q' => trim((string) ($_GET['q'] ?? ''))]);
        $pager = paginate($total, 15);
        $posts = Post::query(['q' => trim((string) ($_GET['q'] ?? ''))], $pager['perPage'], $pager['offset']);

        return $this->render('admin/posts/index', [
            'title' => 'Yazılar',
            'posts' => $posts,
            'pager' => $pager,
            'q'     => trim((string) ($_GET['q'] ?? '')),
        ]);
    }

    public function create(): string
    {
        return $this->render('admin/posts/form', [
            'title'      => 'Yeni Yazı',
            'post'       => null,
            'categories' => Database::fetchAll('SELECT id, name FROM categories ORDER BY name'),
            'editor'     => true,
        ]);
    }

    public function store(): never
    {
        $errors = $this->validate($_POST);
        if ($errors !== []) {
            keep_old($_POST);
            Flash::set('error', implode(' ', $errors));
            redirect('/admin/posts/new');
        }

        $cover = Uploader::image($_FILES['cover'] ?? null);

        Post::create([
            'category_id'      => $this->categoryId($_POST['category_id'] ?? ''),
            'title'            => trim((string) $_POST['title']),
            'slug'             => unique_slug('posts', (string) $_POST['title']),
            'excerpt'          => $this->excerpt($_POST),
            'content'          => (string) ($_POST['content'] ?? ''),
            'cover_image'      => $cover,
            'status'           => ($_POST['status'] ?? '') === 'published' ? 'published' : 'draft',
            'published_at'     => $this->publishedAt($_POST['published_at'] ?? '', ($_POST['status'] ?? '') === 'published'),
            'meta_description' => trim((string) ($_POST['meta_description'] ?? '')) ?: null,
        ]);
        clear_old();
        Flash::set('success', 'Yazı oluşturuldu.');
        redirect('/admin/posts');
    }

    public function edit(string $id): string
    {
        $post = Post::find((int) $id);
        if ($post === null) {
            Flash::set('error', 'Yazı bulunamadı.');
            redirect('/admin/posts');
        }
        return $this->render('admin/posts/form', [
            'title'      => 'Yazıyı Düzenle',
            'post'       => $post,
            'categories' => Database::fetchAll('SELECT id, name FROM categories ORDER BY name'),
            'editor'     => true,
        ]);
    }

    public function update(string $id): never
    {
        $post = Post::find((int) $id);
        if ($post === null) {
            redirect('/admin/posts');
        }
        $errors = $this->validate($_POST);
        if ($errors !== []) {
            keep_old($_POST);
            Flash::set('error', implode(' ', $errors));
            redirect('/admin/posts/' . (int) $id . '/edit');
        }

        $data = [
            'category_id'      => $this->categoryId($_POST['category_id'] ?? ''),
            'title'            => trim((string) $_POST['title']),
            'excerpt'          => $this->excerpt($_POST),
            'content'          => (string) ($_POST['content'] ?? ''),
            'status'           => ($_POST['status'] ?? '') === 'published' ? 'published' : 'draft',
            'published_at'     => $this->publishedAt($_POST['published_at'] ?? '', ($_POST['status'] ?? '') === 'published'),
            'meta_description' => trim((string) ($_POST['meta_description'] ?? '')) ?: null,
        ];

        $cover = Uploader::image($_FILES['cover'] ?? null);
        $oldCover = (string) ($post['cover_image'] ?? '');
        if ($cover !== null) {
            $data['cover_image'] = $cover;
        }

        Post::update((int) $id, $data);
        if ($cover !== null && $oldCover !== '') {
            // Kayıt güncellendikten sonra eski kapak dosyasını temizle
            // (deleteFile, dosyanın başka kayıtta kullanımda olup olmadığını DB'den kontrol eder).
            \App\Models\Photo::deleteFile($oldCover);
        }
        clear_old();
        Flash::set('success', 'Yazı güncellendi.');
        redirect('/admin/posts');
    }

    public function destroy(string $id): never
    {
        $post = Post::find((int) $id);
        if ($post === null) {
            redirect('/admin/posts');
        }
        $cover = (string) ($post['cover_image'] ?? '');
        Post::delete((int) $id);
        if ($cover !== '') {
            \App\Models\Photo::deleteFile($cover);
        }
        Flash::set('success', 'Yazı silindi.');
        redirect('/admin/posts');
    }

    /** @return string[] */
    private function validate(array $input): array
    {
        $errors = [];
        if (mb_strlen(trim((string) ($input['title'] ?? ''))) < 2) {
            $errors[] = 'Yazı başlığı en az 2 karakter olmalı.';
        }
        if (!in_array($input['status'] ?? '', ['draft', 'published'], true)) {
            $errors[] = 'Geçersiz durum seçimi.';
        }
        return $errors;
    }

    private function categoryId(mixed $value): ?int
    {
        $id = (int) $value;
        if ($id <= 0) {
            return null;
        }
        return Database::fetchColumn('SELECT COUNT(*) FROM categories WHERE id = ?', [$id]) > 0 ? $id : null;
    }

    private function excerpt(array $input): ?string
    {
        $excerpt = trim((string) ($input['excerpt'] ?? ''));
        if ($excerpt === '' && !empty($input['content'])) {
            $excerpt = make_excerpt((string) $input['content'], 240);
        }
        return $excerpt !== '' ? $excerpt : null;
    }

    private function publishedAt(string $value, bool $publishing): ?string
    {
        $value = trim($value);
        if ($value !== '') {
            $ts = strtotime(str_replace('T', ' ', $value));
            return $ts !== false ? date('Y-m-d H:i:s', $ts) : date('Y-m-d H:i:s');
        }
        return $publishing ? date('Y-m-d H:i:s') : null;
    }
}
