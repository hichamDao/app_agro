<?php
/**
 * Liste des categories d'un produit, avec le nombre de produits associes.
 *
 * Cette inclusion etait en erreur fatale : elle appelait mysql_query() et
 * mysql_fetch_assoc(), supprimees en PHP 7. Les trois pages qui l'incluaient
 * (GestionProduits.php, GestionGaleries.php, addProduit.php) afficheaient
 * « Call to undefined function mysql_query() ».
 *
 * Le compteur de produits est desormais une seule requete pour toutes les
 * categories : l'ancienne version en lanait une par ligne, avec le nom de la
 * categorie concatene dans le SQL.
 *
 * $fm_app est attendu : fourni par les pages appelantes (voir
 * includes/paths.php).
 */

require_once(__DIR__ . '/connection.php');

if (!isset($fm_app)) {
    require_once(__DIR__ . '/paths.php');
}

/* Ces pages admin historiques n'incluent pas admin-auth.php, on fournit donc
   l'echappement. Declaration inconditionnelle en tete de fichier : placee
   dans un if, elle n'aurait pas ete definie au moment de l'appel. */
if (!function_exists('fm_admin_echapper_legacy')) {
    function fm_admin_echapper_legacy($valeur) {
        return htmlspecialchars((string) $valeur, ENT_QUOTES, 'UTF-8');
    }
}

$categoriesAdmin = array();
$rsCat = mysqli_query(
    $conn,
    'SELECT c.Code_cat, c.Nom_cat, COUNT(p.id_prod) AS nombre_prod
     FROM categories c
     LEFT JOIN produits p ON p.Code_cat = c.Code_cat
     GROUP BY c.Code_cat, c.Nom_cat
     ORDER BY c.Nom_cat ASC'
);
if ($rsCat) {
    while ($cat = mysqli_fetch_assoc($rsCat)) {
        $categoriesAdmin[] = $cat;
    }
}

if (!$categoriesAdmin) {
    echo '<p style="color:#888;font-size:13px;">Aucune categorie.</p>';
    return;
}

echo '<ul class="fm-adm-catlist">' . "\n";
foreach ($categoriesAdmin as $cat) {
    $lien = $fm_app . 'products/' . (int) $cat['Code_cat'] . '/' . rawurlencode($cat['Nom_cat']) . '/';
    echo '  <li>'
        . '<a href="' . fm_admin_echapper_legacy($lien) . '">' . htmlspecialchars($cat['Nom_cat'], ENT_QUOTES, 'UTF-8') . '</a>'
        . '<span>(' . (int) $cat['nombre_prod'] . ')</span>'
        . '</li>' . "\n";
}
echo '</ul>' . "\n";