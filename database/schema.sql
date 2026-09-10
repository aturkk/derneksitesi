-- Dernek Sitesi veritabanı şeması (kurulum sihirbazı çalıştırır)

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS users (
    id            INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    name          VARCHAR(100)     NOT NULL,
    email         VARCHAR(190)     NOT NULL,
    password_hash VARCHAR(255)     NOT NULL,
    role          ENUM('admin','editor') NOT NULL DEFAULT 'editor',
    created_at    TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS settings (
    setting_key   VARCHAR(100)     NOT NULL,
    setting_value TEXT             NULL,
    PRIMARY KEY (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pages (
    id               INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    title            VARCHAR(190)  NOT NULL,
    slug             VARCHAR(190)  NOT NULL,
    content          LONGTEXT      NULL,
    meta_description VARCHAR(300)  NULL,
    status           ENUM('draft','published') NOT NULL DEFAULT 'draft',
    show_in_menu     TINYINT(1)    NOT NULL DEFAULT 0,
    sort_order       INT           NOT NULL DEFAULT 0,
    created_at       TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_pages_slug (slug),
    KEY idx_pages_status (status, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS categories (
    id   INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    name VARCHAR(100)    NOT NULL,
    slug VARCHAR(120)    NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_categories_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS posts (
    id               INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    category_id      INT UNSIGNED  NULL,
    title            VARCHAR(190)  NOT NULL,
    slug             VARCHAR(190)  NOT NULL,
    excerpt          VARCHAR(500)  NULL,
    content          LONGTEXT      NULL,
    cover_image      VARCHAR(255)  NULL,
    status           ENUM('draft','published') NOT NULL DEFAULT 'draft',
    published_at     DATETIME      NULL,
    meta_description VARCHAR(300)  NULL,
    created_at       TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_posts_slug (slug),
    KEY idx_posts_status (status, published_at),
    KEY idx_posts_category (category_id),
    CONSTRAINT fk_posts_category FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS albums (
    id         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    title      VARCHAR(190)  NOT NULL,
    slug       VARCHAR(190)  NOT NULL,
    sort_order INT           NOT NULL DEFAULT 0,
    created_at TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_albums_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS photos (
    id         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    album_id   INT UNSIGNED  NOT NULL,
    filename   VARCHAR(255)  NOT NULL,
    title      VARCHAR(190)  NULL,
    sort_order INT           NOT NULL DEFAULT 0,
    created_at TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_photos_album (album_id, sort_order),
    CONSTRAINT fk_photos_album FOREIGN KEY (album_id) REFERENCES albums (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS slides (
    id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    title       VARCHAR(190)  NOT NULL,
    text        TEXT          NULL,
    button_text VARCHAR(100)  NULL,
    button_url  VARCHAR(255)  NULL,
    image       VARCHAR(255)  NOT NULL,
    status      ENUM('draft','published') NOT NULL DEFAULT 'published',
    sort_order  INT           NOT NULL DEFAULT 0,
    created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_slides_status (status, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS messages (
    id         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    name       VARCHAR(120)  NOT NULL,
    email      VARCHAR(190)  NOT NULL,
    phone      VARCHAR(50)   NULL,
    subject    VARCHAR(190)  NULL,
    body       TEXT          NOT NULL,
    is_read    TINYINT(1)    NOT NULL DEFAULT 0,
    created_at TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_messages_read (is_read, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
