/**
 * Table product_seo : contenu SEO pour les pages produit.
 *
 * Chaque produit categorise (Tomates, Agrumes, etc.) a sa propre page
 * SEO detaillee avec : origine, varietes, saison, tailles, conditionnement,
 * disponibilite, transport, marches de destination, qualite, certifications.
 *
 * La page est servie par products/produit.php?slug={slug}
 * ex: /products/tomatoes -> produits/produit.php?slug=tomatoes
 *
 * Executer APRES blog_posts.sql.
 */

CREATE TABLE IF NOT EXISTS `product_seo` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `slug`          VARCHAR(100) NOT NULL,
    `title`         VARCHAR(250) NOT NULL,
    `subtitle`      VARCHAR(500) DEFAULT NULL,
    `origin`        TEXT,
    `varieties`     TEXT,
    `season`        TEXT,
    `sizes`         TEXT,
    `packaging`     TEXT,
    `availability`  TEXT,
    `transportation` TEXT,
    `destinations`  TEXT,
    `quality`       TEXT,
    `certifications` TEXT,
    `meta_title`    VARCHAR(250) DEFAULT NULL,
    `meta_description` VARCHAR(250) DEFAULT NULL,
    `status`        ENUM('published','draft') DEFAULT 'draft',
    `created_at`    DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
