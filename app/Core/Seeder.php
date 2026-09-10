<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Kurulum sihirbazı tarafından çağrılır: varsayılan ayarlar, örnek içerik
 * ve yer tutucu görselleri oluşturur.
 */
final class Seeder
{
    public static function run(string $siteName): void
    {
        self::settings($siteName);
        self::categories();
        self::pages();
        self::posts();
        self::albums();
        self::slides();
        self::sampleMessage();
        self::sampleImages();
    }

    private static function settings(string $siteName): void
    {
        $defaults = [
            'site_name'        => $siteName,
            'site_tagline'     => 'Topluluğumuza hizmet için kurulduk',
            'site_description' => $siteName . ' resmi internet sitesi: haberler, duyurular, faaliyetler ve iletişim bilgileri.',
            'logo'             => '',
            'hero_title'       => $siteName . "'a hoş geldiniz",
            'hero_text'        => 'Derneğimizin faaliyetleri, haberleri ve duyuruları bu sitede yayımlanır. Bize her zaman ulaşabilirsiniz.',
            'contact_phone'    => '',
            'contact_email'    => '',
            'contact_address'  => '',
            'map_embed'        => '',
            'social_facebook'  => '',
            'social_instagram' => '',
            'social_youtube'   => '',
            'social_x'         => '',
            'footer_text'      => 'Tüm hakları saklıdır.',
        ];
        foreach ($defaults as $key => $value) {
            Database::run('INSERT IGNORE INTO settings (setting_key, setting_value) VALUES (?, ?)', [$key, $value]);
        }
    }

    private static function categories(): void
    {
        foreach (['Haber', 'Duyuru', 'Proje'] as $name) {
            Database::run(
                'INSERT IGNORE INTO categories (name, slug) VALUES (?, ?)',
                [$name, slugify($name)]
            );
        }
    }

    private static function pages(): void
    {
        $pages = [
            [
                'title' => 'Hakkımızda', 'sort' => 1,
                'content' => '<p>Bu bölümde derneğinizin kuruluş hikâyesini, amacını ve çalışmalarını tanıtabilirsiniz. Ziyaretçilerinize kim olduğunuzu, neyi hedeflediğinizi ve hangi alanlarda faaliyet gösterdiğinizi anlatan kısa ve samimi bir metin hazırlamanız önerilir.</p><h3>Ne yapıyoruz?</h3><p>Dernek yönetimi giriş yaptıktan sonra <strong>Sayfalar</strong> bölümünden bu metni dilediğiniz gibi düzenleyebilir; başlıklar, listeler ve görseller ekleyebilirsiniz.</p>',
            ],
            [
                'title' => 'Misyon ve Vizyon', 'sort' => 2,
                'content' => '<h3>Misyonumuz</h3><p>Misyonunuzu birkaç cümleyle açıklayın: derneğinizin varoluş amacı, kime hizmet ettiği ve hangi değerlerle çalıştığı.</p><h3>Vizyonumuz</h3><p>Vizyonunuz, gelecekte ulaşmak istediğiniz noktayı anlatır. Uzun vadeli hedeflerinizi burada paylaşabilirsiniz.</p>',
            ],
            [
                'title' => 'Tüzük', 'sort' => 3,
                'content' => '<p>Dernek tüzüğünüzün tam metnini bu sayfaya yapıştırabilir veya tüzük dosyanızın (PDF) indirme bağlantısını buraya ekleyebilirsiniz.</p><p>Dernek yönetimi bu metni <strong>Sayfalar</strong> bölümünden kolayca güncelleyebilir.</p>',
            ],
            [
                'title' => 'Yönetim Kurulu', 'sort' => 4,
                'content' => '<p>Bu bölümde dernek organlarınızı tanıtabilirsiniz.</p><ul><li><strong>Başkan:</strong> Ad Soyad</li><li><strong>Başkan Yardımcısı:</strong> Ad Soyad</li><li><strong>Sayman:</strong> Ad Soyad</li><li><strong>Sekreter:</strong> Ad Soyad</li><li><strong>Üye:</strong> Ad Soyad</li></ul>',
            ],
        ];
        foreach ($pages as $page) {
            Database::run(
                'INSERT INTO pages (title, slug, content, meta_description, status, show_in_menu, sort_order)
                 VALUES (?, ?, ?, NULL, ?, 1, ?)',
                [$page['title'], slugify($page['title']), $page['content'], 'published', $page['sort']]
            );
        }
    }

    private static function posts(): void
    {
        $posts = [
            [
                'title' => 'Dernek Web Sitemiz Yayında', 'cat' => 'Haber', 'cover' => 'haber-1.svg',
                'excerpt' => 'Derneğimize ait internet sitemiz açıldı. Tüm haberler, duyurular ve faaliyetlerimiz artık tek yerde.',
                'content' => '<p>Değerli üyelerimiz ve dostlarımız, derneğimize ait internet sitemiz yayına girdi. Artık tüm haberlerimiz, duyurularımız ve faaliyetlerimiz bu siteden takip edilebilecek.</p><p>Sitede neler bulabilirsiniz?</p><ul><li>Güncel <strong>haber ve duyurularımız</strong>,</li><li>faaliyetlerimizden <strong>fotoğraf albümleri</strong>,</li><li>derneğimiz hakkında bilgiler ve <strong>iletişim kanallarımız</strong>.</li></ul><p>Görüş ve önerilerinizi iletişim formu üzerinden bize iletebilirsiniz.</p>',
            ],
            [
                'title' => 'Olağan Genel Kurul Toplantısı Duyurusu', 'cat' => 'Duyuru', 'cover' => 'haber-2.svg',
                'excerpt' => 'Derneğimizin olağan genel kurul toplantısı, tüzüğümüz gereği planlanan tarihte üyelerimizin katılımıyla yapılacaktır.',
                'content' => '<p>Saygıdeğer üyelerimiz, derneğimizin olağan genel kurul toplantısı tüzüğümüz uyarınca yapılacaktır. Toplantıya katılımınız üye sayısı açısından büyük önem taşımaktadır.</p><p>Ayrıntılı gündem, toplantı yeri ve saati gibi bilgiler yönetim kurulumuz tarafından üyelere ayrıca duyurulacaktır.</p>',
            ],
            [
                'title' => 'Yeni Dönem Faaliyet Planımız Açıklandı', 'cat' => 'Haber', 'cover' => 'haber-3.svg',
                'excerpt' => 'Yeni dönemde üyelerimize yönelik düzenleyeceğimiz etkinlik ve projelerimizi paylaşıyoruz.',
                'content' => '<p>Yeni dönem için hazırladığımız faaliyet planında üyelerimize ve topluluğumuza yönelik çeşitli etkinlikler yer alıyor.</p><h3>Öne çıkan başlıklar</h3><ul><li>Aylık bilgilendirme toplantıları,</li><li>sosyal dayanışma etkinlikleri,</li><li> eğitim ve seminer programları.</li></ul><p>Planın tamamına yönetim kurulumuzdan ulaşabilirsiniz.</p>',
            ],
        ];
        $catIds = [];
        foreach (Database::fetchAll('SELECT id, name FROM categories') as $c) {
            $catIds[$c['name']] = (int) $c['id'];
        }
        foreach ($posts as $post) {
            Database::run(
                'INSERT INTO posts (category_id, title, slug, excerpt, content, cover_image, status, published_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, NOW())',
                [
                    $catIds[$post['cat']] ?? null,
                    $post['title'],
                    slugify($post['title']),
                    $post['excerpt'],
                    $post['content'],
                    $post['cover'],
                    'published',
                ]
            );
        }
    }

    private static function albums(): void
    {
        Database::run('INSERT INTO albums (title, slug, sort_order) VALUES (?, ?, 1)', ['Dernek Faaliyetleri', 'dernek-faaliyetleri']);
        $album1 = (int) Database::pdo()->lastInsertId();
        Database::run('INSERT INTO albums (title, slug, sort_order) VALUES (?, ?, 2)', ['Genel Kurul Toplantısı', 'genel-kurul-toplantisi']);
        $album2 = (int) Database::pdo()->lastInsertId();

        for ($i = 1; $i <= 6; $i++) {
            Database::run(
                'INSERT INTO photos (album_id, filename, title, sort_order) VALUES (?, ?, ?, ?)',
                [$album1, "galeri-$i.svg", "Faaliyet fotoğrafı $i", $i]
            );
        }
        for ($i = 7; $i <= 9; $i++) {
            Database::run(
                'INSERT INTO photos (album_id, filename, title, sort_order) VALUES (?, ?, ?, ?)',
                [$album2, "galeri-$i.svg", 'Genel kurul fotoğrafı ' . ($i - 6), $i - 6]
            );
        }
    }

    private static function slides(): void
    {
        $slides = [
            [
                'title' => 'Derneğimize Hoş Geldiniz',
                'text'  => 'Topluluğumuz için birlikte çalışıyor, üretiyor ve paylaşıyoruz. Faaliyetlerimizi bu siteden takip edebilirsiniz.',
                'btext' => 'Hakkımızda', 'burl' => '/hakkimizda', 'image' => 'slider-1.svg', 'sort' => 1,
            ],
            [
                'title' => 'Faaliyetlerimizi Keşfedin',
                'text'  => 'Projelerimiz ve etkinliklerimizle topluluğumuza katkı sunmaya devam ediyoruz.',
                'btext' => 'Haberler', 'burl' => '/haberler', 'image' => 'slider-2.svg', 'sort' => 2,
            ],
            [
                'title' => 'Bize Katılın',
                'text'  => 'Derneğimize üye olarak ya da destek vererek aramıza katılabilirsiniz.',
                'btext' => 'İletişim', 'burl' => '/iletisim', 'image' => 'slider-3.svg', 'sort' => 3,
            ],
        ];
        foreach ($slides as $slide) {
            Database::run(
                'INSERT INTO slides (title, text, button_text, button_url, image, status, sort_order)
                 VALUES (?, ?, ?, ?, ?, ?, ?)',
                [$slide['title'], $slide['text'], $slide['btext'], $slide['burl'], $slide['image'], 'published', $slide['sort']]
            );
        }
    }

    private static function sampleMessage(): void
    {
        Database::run(
            'INSERT INTO messages (name, email, phone, subject, body, is_read) VALUES (?, ?, NULL, ?, ?, 0)',
            [
                'Ziyaretçi',
                'ziyaretci@ornek.com',
                'Hoş geldiniz',
                "Bu bir örnek mesajdır. İletişim formunuzdan gelen mesajlar bu listede görünecek.\n\nOkundu işareti vermek için mesajı açmanız yeterli.",
            ]
        );
    }

    /** Yer tutucu SVG görselleri üretir (haber kapakları ve galeri fotoğrafları). */
    private static function sampleImages(): void
    {
        $palettes = [
            ['#1d4ed8', '#38bdf8'], ['#0f766e', '#34d399'], ['#b45309', '#fbbf24'],
            ['#7c3aed', '#c084fc'], ['#be123c', '#fb7185'], ['#0f172a', '#64748b'],
            ['#065f46', '#6ee7b7'], ['#9a3412', '#fdba74'], ['#1e40af', '#93c5fd'],
            ['#4338ca', '#a5b4fc'], ['#166534', '#86efac'], ['#854d0e', '#fde047'],
        ];
        $make = static function (string $file, string $label, int $i, int $w = 1200, int $h = 800) use ($palettes): void {
            $path = BASE_PATH . '/public/uploads/' . $file;
            if (is_file($path)) {
                return;
            }
            [$c1, $c2] = $palettes[$i % count($palettes)];
            $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="' . $w . '" height="' . $h . '" viewBox="0 0 ' . $w . ' ' . $h . '">'
                 . '<defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1">'
                 . "<stop offset=\"0\" stop-color=\"$c1\"/><stop offset=\"1\" stop-color=\"$c2\"/>"
                 . '</linearGradient></defs>'
                 . "<rect width=\"$w\" height=\"$h\" fill=\"url(#g)\"/>"
                 . '<circle cx="' . (int) ($w * 0.88) . '" cy="' . (int) ($h * 0.15) . '" r="' . (int) ($h * 0.28) . '" fill="rgba(255,255,255,0.12)"/>'
                 . '<circle cx="' . (int) ($w * 0.1) . '" cy="' . (int) ($h * 0.88) . '" r="' . (int) ($h * 0.2) . '" fill="rgba(255,255,255,0.10)"/>'
                 . '<text x="' . (int) ($w / 2) . '" y="' . (int) ($h / 2 + 15) . '" font-family="Segoe UI, Arial, sans-serif" font-size="52" '
                 . 'fill="rgba(255,255,255,0.95)" text-anchor="middle">' . e($label) . '</text>'
                 . '</svg>';
            file_put_contents($path, $svg);
        };

        $make('haber-1.svg', 'Haber Kapak Görseli 1', 0);
        $make('haber-2.svg', 'Duyuru Kapak Görseli 2', 5);
        $make('haber-3.svg', 'Haber Kapak Görseli 3', 1);
        $make('slider-1.svg', 'Hoş Geldiniz', 0, 1600, 900);
        $make('slider-2.svg', 'Faaliyetler', 1, 1600, 900);
        $make('slider-3.svg', 'Bize Katılın', 3, 1600, 900);
        for ($i = 1; $i <= 9; $i++) {
            $make("galeri-$i.svg", "Fotoğraf $i", $i + 1);
        }
    }
}
