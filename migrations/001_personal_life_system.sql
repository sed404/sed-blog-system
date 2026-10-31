CREATE TABLE IF NOT EXISTS `life_navigation` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `icon` VARCHAR(100) NOT NULL DEFAULT '',
  `url` VARCHAR(500) NOT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_enabled` TINYINT(1) NOT NULL DEFAULT 1,
  `target` ENUM('_self','_blank') NOT NULL DEFAULT '_self',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_life_navigation_enabled_sort` (`is_enabled`, `sort_order`, `id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `life_navigation` (`name`, `icon`, `url`, `sort_order`, `is_enabled`, `target`)
SELECT '首页', '🏠', './index.php', 10, 1, '_self'
WHERE NOT EXISTS (SELECT 1 FROM `life_navigation` WHERE `url` = './index.php');

INSERT INTO `life_navigation` (`name`, `icon`, `url`, `sort_order`, `is_enabled`, `target`)
SELECT '画廊', '🖼️', './gallery.php', 30, 1, '_self'
WHERE NOT EXISTS (SELECT 1 FROM `life_navigation` WHERE `url` = './gallery.php');
INSERT INTO `life_navigation` (`name`, `icon`, `url`, `sort_order`, `is_enabled`, `target`)
SELECT '友链', '🔗', './links.php', 40, 1, '_self'
WHERE NOT EXISTS (SELECT 1 FROM `life_navigation` WHERE `url` = './links.php');
INSERT INTO `life_navigation` (`name`, `icon`, `url`, `sort_order`, `is_enabled`, `target`)
SELECT '关于', '👤', './about.php', 50, 1, '_self'
WHERE NOT EXISTS (SELECT 1 FROM `life_navigation` WHERE `url` = './about.php');



CREATE TABLE IF NOT EXISTS `life_gallery_albums` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(120) NOT NULL,
  `description` TEXT,
  `cover_url` VARCHAR(1000) DEFAULT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_visible` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_life_gallery_albums_visible_sort` (`is_visible`, `sort_order`, `id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `life_gallery_items` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `album_id` BIGINT UNSIGNED DEFAULT NULL,
  `title` VARCHAR(255) NOT NULL DEFAULT '',
  `description` TEXT,
  `tags` VARCHAR(1000) NOT NULL DEFAULT '',
  `image_url` VARCHAR(1000) NOT NULL,
  `image_source` ENUM('external','local') NOT NULL DEFAULT 'external',
  `media_type` ENUM('image','video') NOT NULL DEFAULT 'image',
  `display_ratio` VARCHAR(20) NOT NULL DEFAULT 'auto',
  `taken_at` DATETIME DEFAULT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_visible` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_life_gallery_items_album` (`album_id`, `is_visible`, `sort_order`),
  KEY `idx_life_gallery_items_taken` (`taken_at`, `id`),
  CONSTRAINT `fk_life_gallery_items_album` FOREIGN KEY (`album_id`) REFERENCES `life_gallery_albums` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
