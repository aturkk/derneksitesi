<?php
declare(strict_types=1);

/*
 * Kurulum sihirbazı.
 * Site ilk açıldığında çalışır; veritabanını oluşturur, şemayı kurar,
 * varsayılan içerik ve yönetici hesabını yaratır, config/config.php yazar
 * ve kendini storage/installed.lock ile kilitler.
 */

define('BASE_PATH', dirname(__DIR__));

ini_set('display_errors', '1');
error_reporting(E_ALL);

require BASE_PATH . '/app/Core/helpers.php';

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $file = BASE_PATH . '/app/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

if (session_status() === PHP_SESSION_NONE) {
    session_name('DERNEKSID');
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

header('Content-Type: text/html; charset=utf-8');

$lockFile = BASE_PATH . '/storage/installed.lock';
$configFile = BASE_PATH . '/config/config.php';

function install_page(string $title, string $body): never
{
    echo '<!DOCTYPE html><html lang="tr"><head><meta charset="utf-8">'
        . '<meta name="viewport" content="width=device-width, initial-scale=1">'
        . '<title>' . e($title) . '</title><style>'
        . 'body{font-family:"Segoe UI",Arial,sans-serif;background:#f1f5f9;margin:0;padding:24px;color:#0f172a}'
        . '.card{max-width:720px;margin:24px auto;background:#fff;border-radius:14px;box-shadow:0 8px 30px rgba(15,23,42,.08);padding:32px}'
        . 'h1{margin-top:0;font-size:22px}label{display:block;font-weight:600;font-size:14px;margin:14px 0 4px}'
        . 'input,select{width:100%;box-sizing:border-box;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:14px}'
        . 'fieldset{border:1px solid #e2e8f0;border-radius:10px;margin:18px 0;padding:8px 18px 18px}'
        . 'legend{font-weight:700;font-size:14px;padding:0 6px}'
        . 'button{margin-top:20px;background:#1d4ed8;color:#fff;border:0;border-radius:8px;padding:12px 26px;font-size:15px;font-weight:600;cursor:pointer}'
        . 'button:hover{background:#1e40af}'
        . '.error{background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;padding:10px 14px;border-radius:8px;font-size:14px}'
        . '.ok{color:#15803d}.fail{color:#b91c1c}ul.req{list-style:none;padding:0;font-size:14px}ul.req li{padding:3px 0}'
        . '.grid{display:grid;grid-template-columns:1fr 1fr;gap:0 18px}.hint{font-size:12px;color:#64748b;margin-top:3px}'
        . '</style></head><body><div class="card">' . $body . '</div></body></html>';
    exit;
}

if (is_file($lockFile)) {
    install_page('Kurulum kilitli', '<h1>Kurulum daha önce tamamlanmış</h1>'
        . '<p>Bu sihirbaz güvenlik nedeniyle kilitlendi. Tekrar kurulum yapmak için '
        . '<code>storage/installed.lock</code> dosyasını silin (veritabanı verileri korunmaz).</p>'
        . '<p><a href="' . e(url('/')) . '">Siteye dön</a></p>');
}

$errors = [];
$old = [
    'db_host' => '127.0.0.1', 'db_port' => '3306', 'db_name' => 'dernek_sitesi',
    'db_user' => 'root', 'db_pass' => '', 'site_name' => '', 'admin_name' => '', 'admin_email' => '',
];

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    foreach ($old as $k => $v) {
        $old[$k] = trim((string) ($_POST[$k] ?? ''));
    }
    $adminPass = (string) ($_POST['admin_pass'] ?? '');
    $adminPass2 = (string) ($_POST['admin_pass2'] ?? '');

    if ($old['site_name'] === '') {
        $errors[] = 'Dernek (site) adı gerekli.';
    }
    if ($old['admin_name'] === '') {
        $errors[] = 'Yönetici adı gerekli.';
    }
    if (!filter_var($old['admin_email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Geçerli bir yönetici e-postası girin.';
    }
    if (strlen($adminPass) < 8) {
        $errors[] = 'Yönetici şifresi en az 8 karakter olmalı.';
    }
    if ($adminPass !== $adminPass2) {
        $errors[] = 'Şifreler birbiriyle uyuşmuyor.';
    }
    if ($old['db_host'] === '' || $old['db_user'] === '') {
        $errors[] = 'Veritabanı sunucusu ve kullanıcı adı gerekli.';
    }

    if ($errors === []) {
        try {
            $dbName = preg_replace('~[^a-zA-Z0-9_]~', '', $old['db_name']);
            if ($dbName === '') {
                throw new RuntimeException('Geçersiz veritabanı adı.');
            }

            // 1) Sunucuya bağlan ve veritabanını oluştur
            $server = new PDO(
                sprintf('mysql:host=%s;port=%d;charset=utf8mb4', $old['db_host'], (int) $old['db_port']),
                $old['db_user'],
                $old['db_pass'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            $server->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            unset($server);

            // 2) Şemayı çalıştır (çok ifadeli bağlantı)
            $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $old['db_host'], (int) $old['db_port'], $dbName);
            $pdo = new PDO($dsn, $old['db_user'], $old['db_pass'], [
                PDO::ATTR_ERRMODE              => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_MULTI_STATEMENTS => true,
            ]);
            $schema = (string) file_get_contents(BASE_PATH . '/database/schema.sql');
            $pdo->exec($schema);
            unset($pdo);

            // 3) Yapılandırmayı yaz
            $config = [
                'debug' => true,
                'db' => [
                    'host'     => $old['db_host'],
                    'port'     => (int) $old['db_port'],
                    'database' => $dbName,
                    'username' => $old['db_user'],
                    'password' => $old['db_pass'],
                    'charset'  => 'utf8mb4',
                ],
            ];
            $export = "<?php\n\ndeclare(strict_types=1);\n\n/* Kurulum sihirbazı tarafından oluşturuldu. */\n\nreturn "
                . var_export($config, true) . ";\n";
            if (@file_put_contents($configFile, $export) === false) {
                throw new RuntimeException('config/config.php yazılamadı; klasör izinlerini kontrol edin.');
            }

            // 4) Bağlantıyı çekirdekle kur, tohum verisini ve yöneticiyi oluştur
            App\Core\Database::init($config['db']);
            App\Core\Seeder::run($old['site_name']);
            App\Core\Database::insert('users', [
                'name'          => $old['admin_name'],
                'email'         => $old['admin_email'],
                'password_hash' => password_hash($adminPass, PASSWORD_DEFAULT),
                'role'          => 'admin',
            ]);

            // 5) Çalışma klasörlerini hazırla ve kilitle
            foreach ([BASE_PATH . '/storage', BASE_PATH . '/public/uploads'] as $dir) {
                if (!is_dir($dir)) {
                    mkdir($dir, 0775, true);
                }
            }
            file_put_contents($lockFile, 'Kurulum: ' . date('Y-m-d H:i:s') . "\n");

            redirect('/admin/login?installed=1');
        } catch (Throwable $e) {
            $errors[] = 'Kurulum sırasında hata: ' . $e->getMessage();
        }
    }
}

// --- Gereksinim kontrolü ---
$requirements = [
    ['PHP 8.1+', version_compare(PHP_VERSION, '8.1.0', '>='), PHP_VERSION],
    ['PDO MySQL eklentisi', extension_loaded('pdo_mysql'), extension_loaded('pdo_mysql') ? 'var' : 'YOK'],
    ['config/ klasörü yazılabilir', is_writable(BASE_PATH . '/config'), ''],
    ['public/uploads yazılabilir', is_writable(BASE_PATH . '/public/uploads'), ''],
    ['storage/ klasörü yazılabilir', is_writable(BASE_PATH . '/storage') || @mkdir(BASE_PATH . '/storage', 0775, true), ''],
];

$body = '<h1>Dernek Sitesi Kurulumu</h1><p>Sihirbaz veritabanını oluşturacak, temel içerikleri ekleyecek ve yönetici hesabınızı yaratacaktır.</p>';

foreach ($errors as $err) {
    $body .= '<p class="error">' . e($err) . '</p>';
}

$body .= '<fieldset><legend>Sunucu gereksinimleri</legend><ul class="req">';
foreach ($requirements as [$label, $ok, $extra]) {
    $body .= '<li class="' . ($ok ? 'ok' : 'fail') . '">' . ($ok ? '✓' : '✗') . ' ' . e($label . ($extra !== '' ? " — $extra" : '')) . '</li>';
}
$body .= '</ul></fieldset>';

$body .= '<form method="post">'
    . '<fieldset><legend>Veritabanı (XAMPP varsayılanları)</legend><div class="grid">'
    . '<div><label>Sunucu</label><input name="db_host" value="' . e($old['db_host']) . '"></div>'
    . '<div><label>Port</label><input name="db_port" value="' . e($old['db_port']) . '"></div>'
    . '<div><label>Veritabanı adı</label><input name="db_name" value="' . e($old['db_name']) . '"></div>'
    . '<div><label>Kullanıcı adı</label><input name="db_user" value="' . e($old['db_user']) . '"></div>'
    . '<div><label>Şifre</label><input type="password" name="db_pass" value=""><p class="hint">XAMPP kurulumunda şifre boş bırakılır.</p></div>'
    . '</div></fieldset>'
    . '<fieldset><legend>Site ve yönetici hesabı</legend><div class="grid">'
    . '<div><label>Dernek (site) adı</label><input name="site_name" value="' . e($old['site_name']) . '" required></div>'
    . '<div><label>Yönetici adı</label><input name="admin_name" value="' . e($old['admin_name']) . '" required></div>'
    . '<div><label>Yönetici e-postası</label><input type="email" name="admin_email" value="' . e($old['admin_email']) . '" required></div>'
    . '<div><label>Yönetici şifresi (en az 8 karakter)</label><input type="password" name="admin_pass" required></div>'
    . '<div><label>Şifre (tekrar)</label><input type="password" name="admin_pass2" required></div>'
    . '</div></fieldset>'
    . '<button type="submit">Kurulumu Tamamla</button></form>';

install_page('Kurulum — Dernek Sitesi', $body);
