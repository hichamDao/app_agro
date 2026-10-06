/**
 * Creation de la table blog_posts.
 *
 * Table du blog Foodmax Group. Chaque article peut etre relie a une page
 * produit (product_link) pour guider les lecteurs vers le catalogue.
 *
 * Executer une fois sur le serveur de production (via phpMyAdmin ou mysql).
 */

CREATE TABLE IF NOT EXISTS `blog_posts` (
    `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title`            VARCHAR(250) NOT NULL,
    `slug`             VARCHAR(250) NOT NULL,
    `excerpt`          TEXT,
    `content`          LONGTEXT NOT NULL,
    `category`         VARCHAR(100) DEFAULT NULL,
    `product_link`     VARCHAR(250) DEFAULT NULL,
    `product_label`    VARCHAR(150) DEFAULT NULL,
    `image`            VARCHAR(250) DEFAULT NULL,
    `meta_title`       VARCHAR(250) DEFAULT NULL,
    `meta_description` VARCHAR(250) DEFAULT NULL,
    `status`           ENUM('published','draft') DEFAULT 'draft',
    `created_at`       DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`),
    KEY `status_created` (`status`, `created_at`),
    KEY `category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
