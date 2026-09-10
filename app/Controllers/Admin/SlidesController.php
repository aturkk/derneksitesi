<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Core\Flash;
use App\Core\Uploader;
use App\Models\Slide;

final class SlidesController extends AdminController
{
    public function index(): string
    {
        return $this->render('admin/slides/index', [
            'title'  => 'Slider',
            'slides' => Slide::all(),
        ]);
    }

    public function create(): string
    {
        return $this->render('admin/slides/form', [
            'title' => 'Yeni Slayt',
            'slide' => null,
        ]);
    }

    public function store(): never
    {
        $errors = $this->validate($_POST);
        [$image, $imageError] = $this->uploadedImage(required: true);
        if ($imageError !== null) {
            $errors[] = $imageError;
        }
        if ($errors !== []) {
            keep_old($_POST);
            Flash::set('error', implode(' ', $errors));
            redirect('/admin/slides/new');
        }

        Slide::create([
            'title'       => trim((string) $_POST['title']),
            'text'        => trim((string) ($_POST['text'] ?? '')) ?: null,
            'button_text' => trim((string) ($_POST['button_text'] ?? '')) ?: null,
            'button_url'  => trim((string) ($_POST['button_url'] ?? '')) ?: null,
            'image'       => $image,
            'status'      => ($_POST['status'] ?? '') === 'draft' ? 'draft' : 'published',
        ]);
        clear_old();
        Flash::set('success', 'Slayt oluşturuldu.');
        redirect('/admin/slides');
    }

    public function edit(string $id): string
    {
        $slide = Slide::find((int) $id);
        if ($slide === null) {
            Flash::set('error', 'Slayt bulunamadı.');
            redirect('/admin/slides');
        }
        return $this->render('admin/slides/form', [
            'title' => 'Slaytı Düzenle',
            'slide' => $slide,
        ]);
    }

    public function update(string $id): never
    {
        $slide = Slide::find((int) $id);
        if ($slide === null) {
            redirect('/admin/slides');
        }
        $errors = $this->validate($_POST);
        [$image, $imageError] = $this->uploadedImage(required: false);
        if ($imageError !== null) {
            $errors[] = $imageError;
        }
        if ($errors !== []) {
            keep_old($_POST);
            Flash::set('error', implode(' ', $errors));
            redirect('/admin/slides/' . (int) $id . '/edit');
        }

        $data = [
            'title'       => trim((string) $_POST['title']),
            'text'        => trim((string) ($_POST['text'] ?? '')) ?: null,
            'button_text' => trim((string) ($_POST['button_text'] ?? '')) ?: null,
            'button_url'  => trim((string) ($_POST['button_url'] ?? '')) ?: null,
            'status'      => ($_POST['status'] ?? '') === 'draft' ? 'draft' : 'published',
        ];
        if ($image !== null) {
            $data['image'] = $image;
        }

        Slide::update((int) $id, $data);
        if ($image !== null && !empty($slide['image']) && $slide['image'] !== $image) {
            // Kayıt güncellendikten sonra eski görseli temizle (başka slaytta kullanılıyorsa korunur).
            Slide::deleteFile((string) $slide['image'], (int) $id);
        }
        clear_old();
        Flash::set('success', 'Slayt güncellendi.');
        redirect('/admin/slides');
    }

    public function destroy(string $id): never
    {
        Slide::delete((int) $id);
        Flash::set('success', 'Slayt silindi.');
        redirect('/admin/slides');
    }

    public function move(string $id, string $dir): never
    {
        Slide::move((int) $id, $dir === 'up' ? 'up' : 'down');
        redirect('/admin/slides');
    }

    /** @return string[] */
    private function validate(array $input): array
    {
        $errors = [];
        if (mb_strlen(trim((string) ($input['title'] ?? ''))) < 2) {
            $errors[] = 'Slayt başlığı en az 2 karakter olmalı.';
        }
        if (!in_array($input['status'] ?? '', ['published', 'draft'], true)) {
            $errors[] = 'Geçersiz durum seçimi.';
        }
        return $errors;
    }

    /** @return array{0: ?string, 1: ?string} [dosya adı, hata mesajı] */
    private function uploadedImage(bool $required): array
    {
        $file = $_FILES['image'] ?? null;
        $hasFile = is_array($file) && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
        if (!$hasFile) {
            return $required ? [null, 'Slider görseli gerekli (JPG, PNG, WEBP veya GIF; en fazla 8 MB).'] : [null, null];
        }
        $name = Uploader::image($file);
        if ($name === null) {
            return [null, 'Görsel yüklenemedi — yalnızca JPG, PNG, WEBP ve GIF kabul edilir (en fazla 8 MB).'];
        }
        return [$name, null];
    }
}
