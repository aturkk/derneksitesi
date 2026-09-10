# Dernek Sitesi — Basit CMS Planı

## 1. Amaç ve Yaklaşım

WordPress'in sadeleştirilmiş hali gibi çalışan, yönetim panelinden yönetilen bir dernek web sitesi.
Framework kullanmadan, saf PHP 8 + MySQL ile yazılacak: bağımlılık olmaz, her hosting ortamında
(cPanel dâhil) sorunsuz taşınır ve yapıyı anlamak/kendine göre geliştirmek kolaydır.

**Temel modüller:** Sayfalar, Yazılar (haber/duyuru), Foto galerisi, İletişim formu, Ayarlar, Yönetim paneli.

**Teknoloji kararları:**
| Konu | Karar |
|---|---|
| Dil / çalışma ortamı | PHP 8.x, MySQL (MariaDB) — localhost, XAMPP ile |
| Mimari | Saf PHP, mini özel çekirdek (router + PDO + basit şablon) |
| Zengin metin editörü | TinyMCE (yerel dosya, CDN'e bağımlı değil) |
| Ön yüz tasarımı | Özel, sade ve responsive kurumsal tema (vanilya CSS) |
| Menü yönetimi | Sabit bağlantılar (Ana Sayfa, Haberler, Galeri, İletişim) + "menüde göster" işaretli sayfalar |
| Dil | Tek dil (Türkçe); çoklu dil ihtiyacı doğarsa şema genişletilir |

## 2. Faz 0 — Ortam Kurulumu

1. **XAMPP kurulumu** (php.net yerine apachefriends.org): PHP 8.x, MySQL/MariaDB ve phpMyAdmin gelir.
   - Geliştirme sırasında site, PHP'nin kendi sunucusuyla çalışacak: `php -S localhost:8000 -t public`
     (Apache ayarıyla uğraşmaya gerek yok; MySQL için XAMPP Control Panel'den "Start" yeterli.)
   - İstenirse aynı klasör Apache'ye vhost olarak da bağlanabilir.
2. **Kurulum sihirbazı:** Site ilk açılışta `install.php` akışıyla DB bağlantısını sorar,
   `database/schema.sql` + `seed.sql` çalıştırır, varsayılan admin hesabını oluşturur ve kendini kilitler.

## 3. Klasör Yapısı

```
Dernek Sitesi/
├─ public/               # tek web kökü (sunucu burayı gösterir)
│  ├─ index.php          # front controller (tüm istekler buraya gelir)
│  ├─ .htaccess          # istekleri index.php'ye yönlendirme
│  ├─ assets/            # css, js, img, tinymce/
│  └─ uploads/           # yüklenen görseller (listeleme kapalı)
├─ app/
│  ├─ Core/              # Router, Database (PDO), Auth, View, Csrf, Flash, Validator
│  ├─ Controllers/       # Ön yüz: Home, PageController, PostController, Gallery, Contact
│  │                     # Panel: Admin\Dashboard, Admin\Pages, Admin\Posts, Admin\Gallery,
│  │                     #        Admin\Messages, Admin\Settings, Admin\Users, Admin\Auth
│  ├─ Models/            # User, Page, Post, Category, Album, Photo, Message, Setting
│  └─ Views/             # layouts/, front/, admin/, errors/ (404 vb.)
├─ config/config.php     # DB bilgileri (config.sample.php örnek olarak repo'da kalır)
├─ database/schema.sql   # tablolar
├─ database/seed.sql     # varsayılan ayarlar, admin, örnek sayfa/haberler
├─ storage/              # loglar
└─ README.md             # kurulum ve kullanım dokümanı
```

## 4. Veritabanı Şeması

- **users** — id, name, email (unique), password_hash, role (`admin`|`editor`), created_at
- **settings** — setting_key (PK), setting_value → site adı, açıklama, logo, telefon, e-posta,
  adres, sosyal medya bağlantıları, footer metni, iletişim haritası koordinatları
- **pages** — id, title, slug (unique), content, meta_description, status (`draft`|`published`),
  show_in_menu, sort_order, created_at, updated_at
- **posts** — id, title, slug (unique), excerpt, content, cover_image, category_id,
  status, published_at, meta_description, created_at, updated_at
- **categories** — id, name, slug (yazı kategorileri: Haber, Duyuru, Proje…)
- **albums** — id, title, slug, sort_order, created_at
- **photos** — id, album_id (FK), filename, title, sort_order, created_at
- **messages** — id, name, email, phone, subject, body, is_read, created_at

Varsayılan veri: admin hesabı, temel ayar satırları, örnek "Hakkımızda" ve "İletişim" sayfaları,
örnek haberler, örnek albüm.

## 5. Güvenlik

- Tüm sorgularda PDO prepared statements (SQL enjeksiyonuna kapalı).
- Şifreler `password_hash` / `password_verify` (bcrypt).
- Tüm formlarda CSRF tokeni; oturum çerezleri httponly + samesite.
- Girişte basit hız sınırlama (5 hatalı denemeden sonra 10 dk bekleme).
- Yükleme güvenliği: uzantı + MIME beyaz listesi (jpg/png/webp), rastgele dosya adı, boyut limiti.
- XSS: şablonlarda tüm çıktı `htmlspecialchars` ile escape; yönetici girişindeki içerik
  (TinyMCE çıktısı) güvenilir kabul edilir — WordPress'in klasik yaklaşımı.
- `install.php` kurulum sonrası tamamen devre dışı kalır.
- Yetki modeli: admin her şeye erişir; editor yalnız içerik (sayfa/yazı/galeri) yönetir.

## 6. Yönetim Paneli (Faz 1) — `/admin`

1. **Giriş** ve dashboard (sayı özetleri + son mesajlar).
2. **Sayfalar:** liste, ekle/düzenle/sil, yukarı-aşağı sıralama, "menüde göster" işareti, taslak/yayın.
3. **Yazılar:** liste + CRUD, kategori, kapak görseli, kısa özet, yayın tarihi.
4. **Galeri:** albüm yönetimi, çoklu fotoğraf yükleme, sıralama, albüm kapağı.
5. **Mesajlar:** iletişim formundan gelenler; okundu işaretle, sil.
6. **Ayarlar:** site kimliği, logo yükleme, iletişim bilgileri, sosyal medya, footer.
7. **Kullanıcılar:** admin/editor ekleme-düzenleme (yalnız admin).
8. Zengin metin alanlarında **TinyMCE** (yerel kopya; görsel ekleme uploads ile entegre).

## 7. Ön Yüz (Faz 2)

- **Ana sayfa:** hero (logo + dernek adı + kısa tanıtım), son haberler, galeriden seçki, iletişim şeridi.
- **Haberler:** `/haberler` liste sayfası + `/haber/{slug}` detay; kategoriye göre filtre.
- **Galeri:** `/galeri` albüm listesi + albüm detay (lightbox ile büyütme).
- **İletişim:** form (CSRF + doğrulama + spam için basit honeypot), adres/telefon/e-posta, harita gömülümü.
- **Dinamik sayfalar:** `/{slug}` (Hakkımızda, Tüzük, Misyon-Vizyon…) menüden erişilir.
- Arama (yazı + sayfa başlığında LIKE) ve özel 404 sayfası.
- Tek responsive tema; menü ve footer tamamen veritabanından beslenir.

## 8. Faz 3 — Cilalama ve Yayına Hazırlık

- SEO: sayfa başına title/description, Open Graph etiketleri, `sitemap.xml`, `robots.txt`.
- Yedekleme dokümanı (localhost'ta phpMyAdmin export; ileride cPanel'de aynı yöntem).
- Basit dosya önbelleği (gerekirse) ve resim küçültme (yüklerken max genişlik).
- İleride yayınlanacaksa: cPanel paylaşımlı hostinge dosya + DB taşıma rehberi (README'ye eklenir).

## 9. Geliştirme Sırası (Kilometre Taşları)

| # | İş | Çıktı |
|---|---|---|
| 1 | XAMPP kurulumu + proje iskeleti + config | `localhost` boş iskelet çalışıyor |
| 2 | Şema + seed + install sihirbazı | DB hazır, admin girişi oluşuyor |
| 3 | Çekirdek: router, PDO, auth, layout, CSRF | Giriş yapılabilen panel kabuğu |
| 4 | Panel: sayfa CRUD + TinyMCE | Sayfalar yönetiliyor |
| 5 | Panel: yazılar + kategoriler | Haberler yönetiliyor |
| 6 | Panel: galeri + mesajlar + ayarlar + kullanıcılar | Panel tamam |
| 7 | Ön yüz: ana sayfa + dinamik sayfalar + haberler | Site gezilebilir |
| 8 | Ön yüz: galeri + iletişim + arama + 404 | Tüm sayfalar çalışıyor |
| 9 | Güvenlik turu, SEO, README, son testler | Teslime hazır |

## 10. Varsayımlar ve Sonraki Adımlar

- Site şimdilik yalnızca localhost'ta çalışacak; yayına alma kararı esnek tutuluyor
  (bu plan PHP+MySQL olduğu için cPanel hostinge taşımak çok kolay olacak).
- Etkinlik modülü ve çoklu dil istenmedi; gerektiğinde eklenebilir (şema buna uygun tasarlanır).
- Hazır olunduğunda ilk adım: XAMPP kurulumunun yapılması (indirme linki + adımları ile yönlendiririm),
  ardından 1. kilometre taşındaki iskeletin kurulması.
