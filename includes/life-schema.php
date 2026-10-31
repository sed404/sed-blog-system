<?php

function life_ensure_personal_tools_schema(mysqli $conn): bool
{
    try {
        $linkTable = $conn->query("SHOW TABLES LIKE 'link'");
        if (!$linkTable || $linkTable->num_rows === 0) {
            $conn->query(
                "CREATE TABLE `link` (" .
                "`id` INT UNSIGNED NOT NULL AUTO_INCREMENT," .
                "`url` VARCHAR(1000) NOT NULL," .
                "`urls` VARCHAR(160) NOT NULL," .
                "`urlimg` VARCHAR(1000) NOT NULL DEFAULT ''," .
                "`description` VARCHAR(500) NOT NULL DEFAULT ''," .
                "`category` VARCHAR(80) NOT NULL DEFAULT '常用'," .
                "PRIMARY KEY (`id`), KEY `idx_link_category` (`category`, `id`)" .
                ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
            );
        } else {
            $columns = [];
            $result = $conn->query('SHOW COLUMNS FROM `link`');
            while ($result && $column = $result->fetch_assoc()) {
                $columns[(string) $column['Field']] = true;
            }
            if (!isset($columns['description'])) {
                $conn->query("ALTER TABLE `link` ADD COLUMN `description` VARCHAR(500) NOT NULL DEFAULT '' AFTER `urlimg`");
            }
            if (!isset($columns['category'])) {
                $conn->query("ALTER TABLE `link` ADD COLUMN `category` VARCHAR(80) NOT NULL DEFAULT '常用' AFTER `description`");
            }
        }

        $conn->query(
            "CREATE TABLE IF NOT EXISTS `life_planner_items` (" .
            "`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT," .
            "`item_type` ENUM('goal','memo') NOT NULL," .
            "`title` VARCHAR(200) NOT NULL," .
            "`details` TEXT NULL," .
            "`status` VARCHAR(20) NOT NULL," .
            "`created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP," .
            "`updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP," .
            "PRIMARY KEY (`id`), KEY `idx_life_planner_type_status` (`item_type`, `status`, `updated_at`)" .
            ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );

        $plannerColumns = [];
        $plannerResult = $conn->query('SHOW COLUMNS FROM life_planner_items');
        while ($plannerResult && $column = $plannerResult->fetch_assoc()) $plannerColumns[$column['Field']] = true;
        if (!isset($plannerColumns['progress_current'])) $conn->query('ALTER TABLE life_planner_items ADD COLUMN progress_current INT UNSIGNED NOT NULL DEFAULT 0');
        if (!isset($plannerColumns['progress_total'])) $conn->query('ALTER TABLE life_planner_items ADD COLUMN progress_total INT UNSIGNED NOT NULL DEFAULT 0');
        if (!isset($plannerColumns['template_key'])) $conn->query("ALTER TABLE life_planner_items ADD COLUMN template_key VARCHAR(32) NOT NULL DEFAULT 'standard'");

        $conn->query(
            "CREATE TABLE IF NOT EXISTS `life_goal_weight_records` (" .
            "`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT," .
            "`goal_id` BIGINT UNSIGNED NOT NULL," .
            "`weight_jin` DECIMAL(6,1) NOT NULL," .
            "`recorded_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP," .
            "PRIMARY KEY (`id`), KEY `idx_goal_weight_recent` (`goal_id`, `recorded_at`, `id`)," .
            "CONSTRAINT `fk_goal_weight_goal` FOREIGN KEY (`goal_id`) REFERENCES `life_planner_items` (`id`) ON DELETE CASCADE" .
            ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );

        $navigation = $conn->query("SHOW TABLES LIKE 'life_navigation'");
        $gallery = $conn->query("SHOW TABLES LIKE 'life_gallery_items'");
        if ($gallery && $gallery->num_rows > 0) {
            $columns = [];
            $result = $conn->query('SHOW COLUMNS FROM life_gallery_items');
            while ($result && $column = $result->fetch_assoc()) $columns[(string) $column['Field']] = true;
            if (!isset($columns['media_type'])) $conn->query("ALTER TABLE life_gallery_items ADD COLUMN media_type ENUM('image','video') NOT NULL DEFAULT 'image' AFTER image_source");
            if (!isset($columns['display_ratio'])) $conn->query("ALTER TABLE life_gallery_items ADD COLUMN display_ratio VARCHAR(20) NOT NULL DEFAULT 'auto' AFTER media_type");
        }
        if ($navigation && $navigation->num_rows > 0) {
            $conn->query("UPDATE life_navigation SET name='网站' WHERE url='./links.php'");
            $conn->query("UPDATE life_navigation SET name='关于', icon='mdi-account-outline' WHERE url='./about.php' AND name IN ('备忘录','关于我','关于')");
        }
        return true;
    } catch (mysqli_sql_exception $error) {
        error_log('personal tools schema upgrade failed: ' . $error->getMessage());
        return false;
    }
}
