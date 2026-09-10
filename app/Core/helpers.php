<?php
declare(strict_types=1);

/*
 * Global yardımcı fonksiyonlar.
 * BASE_PATH sabiti giriş dosyalarında (public/index.php, public/install.php) tanımlanır.
 */

function e(mixed $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function config(string $key, mixed $default = null): mixed
{
    static $config = null;
    if ($config === null) {
        $file = BASE_PATH . '/config/config.php';
        $config = is_file($file) ? (require $file) : [];
    }
    return $config[$key] ?? $default;
}

/** Uygulamanın taban adresi. Kökte boş, alt klasörde '/dernek' gibi döner. */
function base_url(): string
{
    static $base = null;
    if ($base === null) {
        $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
        $dir = str_replace('\\', '/', dirname($script));
        $dir = rtrim($dir, '/');
        $base = in_array($dir, ['', '.', '/'], true) ? '' : $dir;
    }
    return $base;
}

function url(string $path = '/'): string
{
    if ($path === '') {
        $path = '/';
    }
    return base_url() . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return url($path);
}

function redirect(string $path): never
{
    header('Location: ' . (preg_match('~^https?://~i', $path) ? $path : url($path)));
    exit;
}

/** Türkçe karakter dostu URL temizleyici. */
function slugify(string $text): string
{
    $tr = ['ç' => 'c', 'Ç' => 'c', 'ğ' => 'g', 'Ğ' => 'g', 'ı' => 'i', 'I' => 'i', 'İ' => 'i',
           'ö' => 'o', 'Ö' => 'o', 'ş' => 's', 'Ş' => 's', 'ü' => 'u', 'Ü' => 'u'];
    $text = strtr(trim($text), $tr);
    $text = mb_strtolower($text, 'UTF-8');
    $text = preg_replace('~[^a-z0-9]+~u', '-', $text) ?? '';
    $text = trim($text, '-');
    return $text !== '' ? $text : 'icerik-' . time();
}

/** Tabloda benzersiz bir slug üretir (varsa -2, -3... ekler). */
function unique_slug(string $table, string $title, int $ignoreId = 0): string
{
    $base = slugify($title);
    $slug = $base;
    $i = 2;
    while (true) {
        $stmt = App\Core\Database::run(
            "SELECT COUNT(*) FROM `$table` WHERE slug = ? AND id != ?",
            [$slug, $ignoreId]
        );
        if ((int) $stmt->fetchColumn() === 0) {
            return $slug;
        }
        $slug = $base . '-' . $i++;
    }
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(App\Core\Csrf::token()) . '">';
}

/** Türkçe tarih: tr_date('2026-09-09') → "9 Eylül 2026" */
function tr_date(mixed $date, bool $withWeekday = false): string
{
    if (empty($date)) {
        return '';
    }
    $ts = (is_int($date) || is_numeric($date)) ? (int) $date : strtotime((string) $date);
    if ($ts === false) {
        return '';
    }
    static $months = [1 => 'Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran',
                      'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'];
    static $days = ['Pazar', 'Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi'];
    $out = (int) date('j', $ts) . ' ' . $months[(int) date('n', $ts)] . ' ' . date('Y', $ts);
    if ($withWeekday) {
        $out = $days[(int) date('w', $ts)] . ', ' . $out;
    }
    return $out;
}

function tr_datetime(mixed $date): string
{
    if (empty($date)) {
        return '';
    }
    return tr_date($date) . ' ' . date('H:i', strtotime((string) $date));
}

/** HTML'den sade özet metni üretir. */
function make_excerpt(string $html, int $length = 180): string
{
    $text = trim(preg_replace('~\s+~u', ' ', strip_tags($html)) ?? '');
    if (mb_strlen($text, 'UTF-8') <= $length) {
        return $text;
    }
    return rtrim(mb_substr($text, 0, $length, 'UTF-8')) . '…';
}

/** Yüklenmiş dosyanın genel adresi. Boşsa yer tutucu görsel döner. */
function upload_url(?string $filename): string
{
    if ($filename === null || $filename === '') {
        return asset('assets/img/placeholder.svg');
    }
    if (preg_match('~^https?://~i', $filename)) {
        return $filename;
    }
    return asset('uploads/' . $filename);
}

function flash_get(string $key): ?string
{
    return App\Core\Flash::pull($key);
}

/** Form hatasında önceki değerleri korumak için. */
function old(string $key, mixed $default = ''): mixed
{
    return $_SESSION['_old'][$key] ?? $default;
}

function keep_old(array $data): void
{
    $_SESSION['_old'] = $data;
}

function clear_old(): void
{
    unset($_SESSION['_old']);
}

/** settings tablosundan değer okur (istek başına önbellekli). */
function setting(string $key, string $default = ''): string
{
    static $settings = null;
    if ($settings === null) {
        $settings = [];
        try {
            foreach (App\Core\Database::fetchAll('SELECT setting_key, setting_value FROM settings') as $row) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
        } catch (Throwable) {
            $settings = [];
        }
    }
    return (isset($settings[$key]) && $settings[$key] !== '') ? $settings[$key] : $default;
}

/**
 * Ana menüyü kurar: sabit bağlantılar + "menüde göster" işaretli yayınlanmış sayfalar.
 * Dönüş: [[title, path, is_page], ...]
 */
function main_menu(): array
{
    static $menu = null;
    if ($menu !== null) {
        return $menu;
    }
    $menu = [
        ['title' => 'Ana Sayfa', 'path' => '/'],
        ['title' => 'Haberler', 'path' => '/haberler'],
        ['title' => 'Galeri', 'path' => '/galeri'],
    ];
    try {
        $pages = App\Core\Database::fetchAll(
            "SELECT title, slug FROM pages
             WHERE status = 'published' AND show_in_menu = 1
             ORDER BY sort_order ASC, id ASC"
        );
    } catch (Throwable) {
        $pages = [];
    }
    foreach ($pages as $page) {
        $menu[] = ['title' => $page['title'], 'path' => '/' . $page['slug']];
    }
    $menu[] = ['title' => 'İletişim', 'path' => '/iletisim'];
    return $menu;
}

/** Aktif sayfanın yolunu döndürür (menü vurgulama için). */
function current_path(): string
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $base = base_url();
    if ($base !== '' && str_starts_with($path, $base)) {
        $path = substr($path, strlen($base));
    }
    return $path === '' ? '/' : $path;
}

function is_active(string $path): bool
{
    $current = current_path();
    if ($path === '/') {
        return $current === '/';
    }
    return $current === $path || str_starts_with($current, rtrim($path, '/') . '/');
}

/** Basit sayfalama bilgisi: [page, perPage, offset, total] */
function paginate(int $total, int $perPage = 10): array
{
    $pages = max(1, (int) ceil($total / $perPage));
    $page = min($pages, max(1, (int) ($_GET['sayfa'] ?? 1)));
    return [
        'page'    => $page,
        'pages'   => $pages,
        'perPage' => $perPage,
        'offset'  => ($page - 1) * $perPage,
        'total'   => $total,
    ];
}

/** GET sorgu dizesini koruyarak sayfa bağlantısı üretir. */
function page_url(int $page): string
{
    $query = $_GET;
    $query['sayfa'] = $page;
    return '?' . http_build_query($query);
}
