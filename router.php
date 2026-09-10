<?php
// Geliştirme sunucusu yönlendiricisi:
//   php -S 127.0.0.1:8000 -t public router.php
$docRoot = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/\\');
$path    = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$file    = $docRoot . $path;

// Var olan statik dosyaları sunucunun kendisi verdikten sonra devam et
if ($path !== '/' && is_file($file) && !str_ends_with($path, '.php')) {
    return false;
}

// Kurulum sihirbazı doğrudan çalıştırılabilir
if (preg_match('~^/(index|install)\.php$~', $path) && is_file($file)) {
    chdir($docRoot);
    require $file;
    return true;
}

// Diğer tüm istekler ön denetleyiciye
$_SERVER['SCRIPT_NAME'] = '/index.php';
require $docRoot . '/index.php';
