ALTER TABLE `link`
  ADD COLUMN IF NOT EXISTS `description` VARCHAR(500) NOT NULL DEFAULT '' AFTER `urlimg`,
  ADD COLUMN IF NOT EXISTS `category` VARCHAR(80) NOT NULL DEFAULT '常用' AFTER `description`;

CREATE TABLE IF NOT EXISTS `life_planner_items` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `item_type` ENUM('goal','memo') NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `details` TEXT NULL,
  `status` VARCHAR(20) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_life_planner_type_status` (`item_type`, `status`, `updated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

UPDATE `life_navigation` SET `name`='网站' WHERE `url`='./links.php';
UPDATE `life_navigation` SET `name`='目标', `icon`='✓' WHERE `url`='./about.php';
