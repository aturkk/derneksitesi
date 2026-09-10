<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\AdminController;

use App\Core\Database;
use App\Core\Flash;
use App\Core\Uploader;

final class SettingsController extends AdminController
{
    private const KEYS = [
        'site_name', 'site_tagline', 'site_description', 'logo',
        'hero_title', 'hero_text',
        'contact_phone', 'contact_email', 'contact_address', 'map_embed',
        'social_facebook', 'social_instagram', 'social_youtube', 'social_x',
        'footer_text',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->requireAdmin();
    }

    public function edit(): string
    {
        $settings = [];
        foreach (Database::fetchAll('SELECT setting_key, setting_value FROM settings') as $row) {
            $settings[$row['setting_key']] = (string) $row['setting_value'];
        }
        return $this->render('admin/settings/edit', [
            'title'    => 'Site Ayarları',
            'settings' => $settings,
        ]);
    }

    public function update(): never
    {
        foreach (self::KEYS as $key) {
            if ($key === 'logo') {
                continue; // logo aşağıda dosya olarak işlenir
            }
            $value = trim((string) ($_POST[$key] ?? ''));
            self::save($key, $value);
        }

        if (!empty($_POST['logo_sil'])) {
            self::save('logo', '');
        }
        $logo = Uploader::image($_FILES['logo'] ?? null);
        if ($logo !== null) {
            self::save('logo', $logo);
        }

        Flash::set('success', 'Ayarlar kaydedildi.');
        redirect('/admin/settings');
    }

    private static function save(string $key, string $value): void
    {
        Database::run(
            'INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)',
            [$key, $value]
        );
    }
}
