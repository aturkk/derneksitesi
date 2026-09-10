# Dernek Sitesi — Basit CMS

WordPress'in sadeleştirilmiş hali gibi çalışan, yönetim panelinden yönetilen bir dernek web sitesi.
Framework kullanmadan saf PHP 8 + MySQL ile yazılmıştır; hiçbir harici bağımlılık (Composer/npm) gerektirmez.

## Özellikler

- **Sayfalar** — Hakkımızda, Tüzük vb. sabit sayfalar; TinyMCE zengin metin editörü, taslak/yayın durumu,
  "menüde göster" seçeneği ve sıralama.
- **Slider (Ana Sayfa Hero)** — Panele eklenen slaytlar ana sayfanın üstünde otomatik geçişli büyük görsel
  olarak döner; başlık, metin ve buton desteği, sıralama ve taslak/yayın durumu. Slayt yoksa eski
  metin tabanlı tanıtım bölümü görünür.
- **Yazılar (Haber/Duyuru)** — Kategoriler, kapak görseli, özet, yayın tarihi, SEO açıklaması.
- **Fotoğraf Galerisi** — Albümler, çoklu yükleme, sıralama, lightbox görüntüleme.
- **İletişim Formu** — Ziyaretçi mesajları panele düşer; spam için honeypot koruması.
- **Ayarlar** — Site adı, slogan, logo, ana sayfa tanıtımı, iletişim bilgileri, harita, sosyal medya, footer.
- **Kullanıcılar** — `admin` (tüm yetkiler) ve `editor` (yalnızca içerik) rolleri.
- **Güvenlik** — PDO prepared statements, CSRF koruması, bcrypt şifreleme, giriş hız sınırı,
  yükleme beyaz listesi (JPG/PNG/WEBP/GIF, maks. 8 MB), uploads klasöründe PHP çalıştırma yasağı.

## Gereksinimler

- PHP 8.1+ (PDO MySQL eklentisiyla — XAMPP 8.2'de hepsi hazır)
- MySQL 5.7+ / MariaDB 10.3+

## Kurulum (XAMPP ile, sıfırdan)

1. XAMPP'ı kurun (varsayılan `C:\xampp`). MySQL'i başlatın
   (XAMPP Control Panel'den veya `baslat.bat` otomatik başlatır).
2. Bu klasörü istediğiniz yere koyun (ör. `C:\Users\user\Desktop\Dernek Sitesi`).
3. Siteyi çalıştırın:
   - **Kolay yol:** `baslat.bat` dosyasına çift tıklayın — MySQL'i kontrol eder, sunucuyu başlatır ve tarayıcıyı açar.
   - **Elle:** `php -S 127.0.0.1:8000 -t public router.php`
     (XAMPP PHP'si PATH'te değilse: `C:\xampp\php\php.exe -S 127.0.0.1:8000 -t public router.php`)
4. Tarayıcıda `http://127.0.0.1:8000` adresini açın; sizi **kurulum sihirbazı** karşılar.
   Veritabanı bilgilerini (XAMPP varsayılanı: sunucu `127.0.0.1`, kullanıcı `root`, şifre boş),
   dernek adını ve yönetici hesabını girin. Sihirbaz veritabanını kurar, örnek içerikleri ekler ve
   kendini kilitler (`storage/installed.lock`).

> Not: Bu kurulumda site ve yönetici hesabı zaten oluşturulmuş durumdadır
> (Yönetici: `admin@ornek.com` / şifre: `Dernek!2026` — **ilk girişten sonra Profilim'den değiştirin**).
> Sıfırdan kurmak isterseniz: veritabanını silin (`DROP DATABASE dernek_sitesi;`),
> `config/config.php` ve `storage/installed.lock` dosyalarını kaldırın, siteyi yeniden açın.

## Yönetim Paneli

- Adres: `http://127.0.0.1:8000/admin`
- **Genel Bakış** — içerik istatistikleri, son mesajlar ve yazılar.
- **Sayfalar / Yazılar / Kategoriler / Galeri** — içerik yönetimi (ekle-düzenle-sil-sırala).
- **Slider** — Ana sayfa hero bölümünün slaytları. Görsel (ideali 1600×900 px), başlık, metin ve
  opsiyonel buton (site içi adres `/hakkimizda` ya da dış bağlantı `https://…`) girilir.
  Slaytlar oklarla, noktalarla veya mobilde parmakla kaydırılarak gezilir; otomatik geçiş 6 saniyedir
  ve üzerine gelince durur.
- **Mesajlar** — iletişim formundan gelenler; okununca mavi vurgu kalkar, kenar çubuğunda okunmamış sayısı görünür.
- **Profilim** — ad, e-posta ve şifre değişikliği (editörler de erişebilir).
- **Site Ayarları / Kullanıcılar** — yalnızca `admin` rolü görür.

### Yazı/Sayfa yazarken

- Başlık yazdıkça adres (slug) önerisi otomatik güncellenir; el ile değiştirebilirsiniz.
- İçerik alanındaki editörde doğrudan görsel yapıştırabilir/yükleyebilirsiniz
  (TinyMCE, yüklenen görseli `public/uploads` altına kaydeder).
- Sayfalarda slug'u boş bırakırsanız mevcut adres korunur; yeni sayfada başlıktan üretilir.

## Klasör Yapısı

```
public/            Web kökü (yalnız bu klasör sunucuya gösterilir)
  index.php        Ön denetleyici
  install.php      Kurulum sihirbazı (kurulumdan sonra kilitlenir)
  assets/          CSS, JS, TinyMCE (yerel kopya)
  uploads/         Yüklenen görseller
app/
  Core/            Çekirdek: Router, Database, Auth, View, Csrf, Uploader, Seeder...
  Controllers/     Ön yüz ve panel kontrolörleri
  Models/          Page, Post, Album, Photo, Message, User
  Views/           Şablonlar (layouts, front, admin)
config/            config.php (sihirbaz üretir; örnek: config.sample.php)
database/          schema.sql
storage/           Loglar ve kurulum kilidi
baslat.bat         MySQL + geliştirme sunucusunu tek tıkla başlatır
router.php         `php -S` için yönlendirici
```

## Yedekleme / Geri Yükleme

- **Yedek:** `public/uploads` klasörünü kopyalayın + veritabanını dışa aktarın:
  `C:\xampp\mysql\bin\mysqldump -u root dernek_sitesi > yedek.sql`
- **Geri yükle:** `mysql -u root dernek_sitesi < yedek.sql` ve `uploads` klasörünü yerine koyun.

## Yayına Alma (paylaşımlı hosting / cPanel)

1. Dosyaları yükleyin; alan kökünü **`public`** klasörüne yönlendirin (cPanel'de document root ayarı)
   ya da tüm `public` içeriğini `public_html` içine kopyalayın.
2. cPanel'de bir MySQL veritabanı + kullanıcı oluşturun; `config/config.php` içine bu bilgileri yazın.
3. `database/schema.sql`'ı phpMyAdmin'den içe aktarın, ardından `http://siteniz.com/install.php`
   adresini açıp sihirbazı çalıştırın (sihirbaz yalnızca ayar ve örnek veriyi ekler).
4. `mod_rewrite` açıkken `.htaccess` adresleri otomatik yönlendirir.

## Sorun Giderme

- **"Veritabanına bağlanılamadı"** — MySQL çalışmıyor olabilir; `baslat.bat` yeniden çalıştırın
  veya XAMPP Control Panel'den MySQL'i başlatın. Hata ayrıntıları `storage/php-error.log` dosyasındadır.
- **419 (Güvenlik doğrulaması)** — Sekmeyi yenileyip tekrar deneyin; CSRF anahtarı eskimiştir.
- **Giriş sonrası tekrar giriş ekranı** — Çerezler engellenmiş olabilir; localhost'a izin verin.
- **Kurulumu yeniden çalıştırmak için** — `storage/installed.lock` dosyasını silin
  (veritabanındaki eski veriler korunmaz; şema `CREATE TABLE IF NOT EXISTS` kullanır).
