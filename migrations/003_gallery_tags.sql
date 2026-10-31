SET @life_gallery_tags_exists = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'life_gallery_items'
    AND COLUMN_NAME = 'tags'
);
SET @life_gallery_tags_sql = IF(
  @life_gallery_tags_exists = 0,
  'ALTER TABLE `life_gallery_items` ADD COLUMN `tags` VARCHAR(1000) NOT NULL DEFAULT '''' AFTER `description`',
  'SELECT 1'
);
PREPARE life_gallery_tags_stmt FROM @life_gallery_tags_sql;
EXECUTE life_gallery_tags_stmt;
DEALLOCATE PREPARE life_gallery_tags_stmt;

SET @life_gallery_media_sql = IF(
  (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'life_gallery_items' AND COLUMN_NAME = 'media_type') = 0,
  'ALTER TABLE `life_gallery_items` ADD COLUMN `media_type` ENUM(''image'',''video'') NOT NULL DEFAULT ''image'' AFTER `image_source`',
  'SELECT 1'
);
PREPARE life_gallery_media_stmt FROM @life_gallery_media_sql;
EXECUTE life_gallery_media_stmt;
DEALLOCATE PREPARE life_gallery_media_stmt;
SET @life_gallery_ratio_sql = IF(
  (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'life_gallery_items' AND COLUMN_NAME = 'display_ratio') = 0,
  'ALTER TABLE `life_gallery_items` ADD COLUMN `display_ratio` VARCHAR(20) NOT NULL DEFAULT ''auto'' AFTER `media_type`',
  'SELECT 1'
);
PREPARE life_gallery_ratio_stmt FROM @life_gallery_ratio_sql;
EXECUTE life_gallery_ratio_stmt;
DEALLOCATE PREPARE life_gallery_ratio_stmt;
