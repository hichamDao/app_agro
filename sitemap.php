<?php
/**
 * Plan du site pour Google : /sitemap.xml (reecrit vers ce fichier par .htaccess).
 *
 * Liste toutes les pages publiques utiles, mises a jour automatiquement depuis
 * la base : pages de presentation, familles de produits, produits, fiches
 * produit SEO et articles de blog. Google le lit pour decouvrir et reexplorer
 * les pages rapidement.
 *
 * Declare dans robots.txt. A soumettre une fois dans Google Search Console.
 */
require_once(__DIR__ . "/includes/paths.php");
require_once(__DIR__ . "/includes/connection.php");   /* pas de session : inutile ici */
require_once(__DIR__ . "/includes/seo.php");

header('Content-Type: application/xml; charset=utf-8');
header('X-Robots-Tag: noindex');

$urls = array();
$ajout = function ($chemin, $maj = '', $freq = 'monthly', $prio = '0.5') use (&$urls) {
    $urls[] = array(fm_seo_url($chemin), $maj ? date('Y-m-d', strtotime($maj)) : '', $freq, $prio);
};

/* ------------------------------------------------ pages de presentation */
$ajout('',               '', 'weekly',  '1.0');
$ajout('products/',      '', 'weekly',  '0.9');
$ajout('contact/',       '', 'yearly',  '0.8');
$ajout('fresh-produce-exporter-morocco/', '', 'monthly', '0.9');
$ajout('export-services/', '', 'monthly', '0.8');
$ajout('for-importers/',   '', 'monthly', '0.8');
$ajout('about-us/',      '', 'monthly', '0.7');
$ajout('how-we-work/',   '', 'monthly', '0.6');
$ajout('quality/',       '', 'monthly', '0.6');
$ajout('morocco/',       '', 'monthly', '0.6');
$ajout('why-foodmax/',   '', 'monthly', '0.6');
$ajout('blog/',          '', 'weekly',  '0.7');
$ajout('gallery/',       '', 'monthly', '0.4');
$ajout('privacy-policy/', '', 'yearly', '0.2');
$ajout('terms/',         '', 'yearly',  '0.2');

/* Chaque requete est isolee : une table absente ne doit pas vider le plan. */
$lire = function ($sql, $fn) use ($conn) {
    try {
        $rs = mysqli_query($conn, $sql);
        if ($rs) { while ($r = mysqli_fetch_assoc($rs)) { $fn($r); } }
    } catch (Throwable $e) { /* table absente : on passe */ }
};

/* ------------------------------------------------ familles de produits */
$lire('SELECT c.Code_cat, c.Nom_cat FROM categories c WHERE EXISTS (SELECT 1 FROM produits p WHERE p.Code_cat = c.Code_cat) ORDER BY c.Code_cat ASC', function ($r) use ($ajout) {
    $slug = preg_replace('/[^A-Za-z0-9-]/', '', str_replace('_', '', (string) $r['Nom_cat']));
    if ($slug !== '') { $ajout('products/' . (int) $r['Code_cat'] . '/' . $slug . '/', '', 'weekly', '0.8'); }
});

/* ------------------------------------------------ fiches produit SEO */
$lire("SELECT slug, updated_at FROM product_seo WHERE status = 'published' ORDER BY slug ASC", function ($r) use ($ajout) {
    $ajout('products/' . rawurlencode($r['slug']) . '/', $r['updated_at'], 'monthly', '0.8');
});

/* ------------------------------------------------ produits du catalogue */
$lire('SELECT id_prod FROM produits ORDER BY id_prod ASC LIMIT 5000', function ($r) use ($ajout) {
    $ajout('products_detail/' . (int) $r['id_prod'] . '/', '', 'monthly', '0.6');
});

/* ------------------------------------------------ articles de blog */
$lire("SELECT slug, updated_at FROM blog_posts WHERE status = 'published' ORDER BY created_at DESC", function ($r) use ($ajout) {
    $ajout('blog/' . rawurlencode($r['slug']) . '/', $r['updated_at'], 'monthly', '0.7');
});

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urls as $u) {
    echo "\t<url>\n\t\t<loc>" . htmlspecialchars($u[0], ENT_XML1 | ENT_QUOTES, 'UTF-8') . "</loc>\n";
    if ($u[1] !== '') { echo "\t\t<lastmod>" . $u[1] . "</lastmod>\n"; }
    echo "\t\t<changefreq>" . $u[2] . "</changefreq>\n\t\t<priority>" . $u[3] . "</priority>\n\t</url>\n";
}
echo "</urlset>\n";
