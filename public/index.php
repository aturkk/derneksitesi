<?php
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

ini_set('display_errors', '1');
error_reporting(E_ALL);
ini_set('log_errors', '1');
ini_set('error_log', BASE_PATH . '/storage/php-error.log');

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

// --- Güvenli oturum ---
if (session_status() === PHP_SESSION_NONE) {
    session_name('DERNEKSID');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => (base_url() === '' ? '/' : base_url()),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// --- Kurulmamışsa sihirbaza gönder ---
if (!is_file(BASE_PATH . '/config/config.php')) {
    redirect('/install.php');
}

$config = require BASE_PATH . '/config/config.php';

// --- Veritabanı ---
try {
    App\Core\Database::init($config['db']);
} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: text/html; charset=utf-8');
    echo '<h1>Veritabanına bağlanılamadı</h1>';
    echo '<p>config/config.php içindeki veritabanı ayarlarını ve MySQL servisinin çalıştığını kontrol edin.</p>';
    if (!empty($config['debug'])) {
        echo '<pre>' . e($e->getMessage()) . '</pre>';
    }
    exit;
}

// --- Temel güvenlik başlıkları ---
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');

// --- CSRF: tüm POST isteklerinde zorunlu ---
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    App\Core\Csrf::validateRequest();
}

$router = new App\Core\Router();
require BASE_PATH . '/app/routes.php';
$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
