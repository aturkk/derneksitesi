<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\AdminController;

use App\Core\Flash;
use App\Core\Uploader;
use App\Models\Album;
use App\Models\Photo;

final class AlbumsController extends AdminController
{
    public function index(): string
    {
        return $this->render('admin/albums/index', [
            'title'  => 'Galeri',
            'albums' => Album::all(),
        ]);
    }

    public function create(): string
    {
        return $this->render('admin/albums/form', [
            'title' => 'Yeni Albüm',
            'album' => null,
        ]);
    }

    public function store(): never
    {
        $name = trim((string) ($_POST['title'] ?? ''));
        if (mb_strlen($name) < 2) {
            keep_old($_POST);
            Flash::set('error', 'Albüm adı en az 2 karakter olmalı.');
            redirect('/admin/albums/new');
        }
        $id = Album::create(['title' => $name, 'slug' => unique_slug('albums', $name)]);
        clear_old();
        Flash::set('success', 'Albüm oluşturuldu. Şimdi fotoğraf yükleyebilirsiniz.');
        redirect('/admin/albums/' . $id);
    }

    public function show(string $id): string
    {
        $album = Album::find((int) $id);
        if ($album === null) {
            Flash::set('error', 'Albüm bulunamadı.');
            redirect('/admin/albums');
        }
        return $this->render('admin/albums/show', [
            'title'  => $album['title'],
            'album'  => $album,
            'photos' => Album::photos((int) $album['id']),
        ]);
    }

    /** Çoklu fotoğraf yükleme (input name="photos[]"). */
    public function addPhotos(string $id): never
    {
        $album = Album::find((int) $id);
        if ($album === null) {
            redirect('/admin/albums');
        }

        $files = $_FILES['photos'] ?? null;
        $ok = 0;
        $fail = 0;
        if (is_array($files) && is_array($files['name'] ?? null)) {
            $count = count($files['name']);
            for ($i = 0; $i < $count; $i++) {
                $single = [
                    'name'     => $files['name'][$i],
                    'type'     => $files['type'][$i] ?? '',
                    'tmp_name' => $files['tmp_name'][$i] ?? '',
                    'error'    => $files['error'][$i] ?? UPLOAD_ERR_NO_FILE,
                    'size'     => $files['size'][$i] ?? 0,
                ];
                $filename = Uploader::image($single);
                if ($filename !== null) {
                    Photo::add((int) $album['id'], $filename, null);
                    $ok++;
                } elseif (($single['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                    $fail++;
                }
            }
        }

        if ($ok > 0) {
            Flash::set('success', $ok . ' fotoğraf yüklendi.' . ($fail > 0 ? " ($fail dosya yüklenemedi — yalnızca JPG, PNG, WEBP ve GIF kabul edilir, en fazla 8 MB.)" : ''));
        } else {
            Flash::set('error', 'Yüklenecek geçerli fotoğraf bulunamadı. (Yalnızca JPG, PNG, WEBP ve GIF, en fazla 8 MB)');
        }
        redirect('/admin/albums/' . (int) $id);
    }

    public function update(string $id): never
    {
        $album = Album::find((int) $id);
        if ($album === null) {
            redirect('/admin/albums');
        }
        $name = trim((string) ($_POST['title'] ?? ''));
        if (mb_strlen($name) >= 2) {
            Album::update((int) $id, ['title' => $name]);
            Flash::set('success', 'Albüm adı güncellendi.');
        }
        redirect('/admin/albums/' . (int) $id);
    }

    public function destroy(string $id): never
    {
        Album::delete((int) $id);
        Flash::set('success', 'Albüm ve içindeki fotoğraflar silindi.');
        redirect('/admin/albums');
    }

    public function destroyPhoto(string $id): never
    {
        $photo = Photo::find((int) $id);
        $albumId = $photo['album_id'] ?? null;
        Photo::delete((int) $id);
        Flash::set('success', 'Fotoğraf silindi.');
        redirect($albumId !== null ? '/admin/albums/' . (int) $albumId : '/admin/albums');
    }

    public function movePhoto(string $id, string $dir): never
    {
        $photo = Photo::find((int) $id);
        $albumId = $photo['album_id'] ?? null;
        Photo::move((int) $id, $dir === 'up' ? 'up' : 'down');
        redirect($albumId !== null ? '/admin/albums/' . (int) $albumId : '/admin/albums');
    }
}
