-- 仅在首次部署新代码前手动执行；具备 ALTER/CREATE 权限时页面也会自动升级。
ALTER TABLE `life_planner_items`
  ADD COLUMN `template_key` VARCHAR(32) NOT NULL DEFAULT 'standard';

CREATE TABLE IF NOT EXISTS `life_goal_weight_records` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `goal_id` BIGINT UNSIGNED NOT NULL,
  `weight_jin` DECIMAL(6,1) NOT NULL,
  `recorded_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_goal_weight_recent` (`goal_id`, `recorded_at`, `id`),
  CONSTRAINT `fk_goal_weight_goal` FOREIGN KEY (`goal_id`) REFERENCES `life_planner_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
