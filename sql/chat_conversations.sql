/**
 * Table chat_conversations : stockage des conversations du chatbot FoodMax.
 *
 * Une conversation = une session de chat complète (de l'ouverture à la qualification ou abandon).
 * Les messages sont stockés en JSON pour flexibilité.
 *
 * Executer une fois sur la base de production (via phpMyAdmin ou mysql).
 */

CREATE TABLE IF NOT EXISTS `chat_conversations` (
    `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `session_id`        VARCHAR(64) NOT NULL,
    `ip_address`        VARCHAR(45) DEFAULT NULL,
    `city`              VARCHAR(100) DEFAULT NULL,
    `country`           VARCHAR(100) DEFAULT NULL,
    `user_agent`        TEXT DEFAULT NULL,
    `referrer`          VARCHAR(500) DEFAULT NULL,
    `page_url`          VARCHAR(500) DEFAULT NULL,
    `status`            ENUM('active','qualified','abandoned') DEFAULT 'active',
    `lead_data`         JSON DEFAULT NULL,
    `messages`          JSON DEFAULT NULL,
    `message_count`     INT UNSIGNED DEFAULT 0,
    `step`              VARCHAR(32) DEFAULT 'welcome',
    `started_at`        DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `qualified_at`      DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `session_id` (`session_id`),
    KEY `status_started` (`status`, `started_at`),
    KEY `ip_started` (`ip_address`, `started_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;