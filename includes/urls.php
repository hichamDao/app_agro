<?php
/**
 * Adresses lisibles des familles et des produits.
 *
 *   famille :  products/Figs/            (et products/Figs/2/ pour la page 2)
 *   produit :  products_detail/Fresh-Figs/
 *   guide   :  guides/figs/              (guide acheteur, voir produit.php)
 *
 * Plus de numero dans l'adresse : le nom suffit. Les anciennes adresses avec
 * numero (products/16/Figs/, products_detail/46/) sont redirigees en 301 par
 * .htaccess et par products_detail.php, donc le referencement est conserve.
 *
 * Tout passe par ce fichier : pour changer la forme des adresses, c'est ici.
 */

if (!function_exists('fm_cle')) {
    /** Cle de comparaison : minuscules, lettres et chiffres seulement. */
    function fm_cle($s) { return strtolower(preg_replace('/[^a-z0-9]/i', '', (string) $s)); }
}

if (!function_exists('fm_slug_texte')) {
    /** "Crème brûlée & Co" -> "Creme-brulee-Co" (ASCII, tirets, majuscules conservees). */
    function fm_slug_texte($txt)
    {
        $t = strtr((string) $txt, array(
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'œ' => 'oe',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ý' => 'y', 'ÿ' => 'y', 'ß' => 'ss',
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C',
            'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Œ' => 'OE',
            'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y',
        ));
        $t = preg_replace('/[^A-Za-z0-9]+/', '-', $t);
        return trim($t, '-');
    }
}

/* ----------------------------------------------------------------- familles */

if (!function_exists('fm_slug_cat')) {
    /** Nom de famille -> adresse : "Dried_fruits" -> "Driedfruits" (comme les adresses deja connues de Google). */
    function fm_slug_cat($nom) { return preg_replace('/[^A-Za-z0-9-]/', '', str_replace('_', '', (string) $nom)); }
}

if (!function_exists('fm_cats_tous')) {
    /** Toutes les familles (lues une seule fois par page). */
    function fm_cats_tous($conn)
    {
        static $cats = null;
        if ($cats !== null) { return $cats; }
        $cats = array();
        if (!$conn) { return $cats; }
        try {
            $rs = mysqli_query($conn, 'SELECT Code_cat, Nom_cat FROM categories ORDER BY Code_cat ASC');
            if ($rs) {
                while ($r = mysqli_fetch_assoc($rs)) {
                    $cats[] = array('Code_cat' => (int) $r['Code_cat'], 'Nom_cat' => (string) $r['Nom_cat'], 'slug' => fm_slug_cat($r['Nom_cat']));
                }
            }
        } catch (Throwable $e) { $cats = array(); }
        return $cats;
    }
}

if (!function_exists('fm_cat_par_slug')) {
    /** Famille correspondant a une adresse (sans tenir compte des majuscules), ou null. */
    function fm_cat_par_slug($conn, $slug)
    {
        $k = fm_cle($slug);
        if ($k === '') { return null; }
        foreach (fm_cats_tous($conn) as $c) {
            if (fm_cle($c['slug']) === $k) { return $c; }
        }
        return null;
    }
}

if (!function_exists('fm_cat_chemin')) {
    /** Chemin (sans prefixe) d'une famille : products/Figs/ ou products/Figs/2/ */
    function fm_cat_chemin($nom, $page = 1)
    {
        $s = fm_slug_cat($nom);
        return 'products/' . $s . '/' . ($page > 1 ? (int) $page . '/' : '');
    }
}

if (!function_exists('fm_cat_url')) {
    function fm_cat_url($nom, $page = 1)
    {
        global $fm_app;
        return $fm_app . fm_cat_chemin($nom, $page);
    }
}

/* ----------------------------------------------------------------- produits */

if (!function_exists('fm_prod_index')) {
    /**
     * Adresse de chaque produit, construite a partir de son nom, une seule fois par page.
     * Deux produits de meme nom : le premier garde le nom seul, le suivant recoit
     * son numero ("Figs", "Figs-52"). Un nom qui ne contient que des chiffres est
     * precede de "p-" (pour ne pas etre pris pour un ancien numero).
     */
    function fm_prod_index($conn)
    {
        static $idx = null;
        if ($idx !== null) { return $idx; }
        $idx = array('id' => array(), 'slug' => array());
        if (!$conn) { return $idx; }
        try {
            $rs = mysqli_query($conn, 'SELECT id_prod, Designation FROM produits ORDER BY id_prod ASC');
            if ($rs) {
                while ($r = mysqli_fetch_assoc($rs)) {
                    $id = (int) $r['id_prod'];
                    $s  = fm_slug_texte($r['Designation']);
                    if ($s === '')        { $s = 'product-' . $id; }
                    if (ctype_digit($s))  { $s = 'p-' . $s; }
                    if (isset($idx['slug'][strtolower($s)])) { $s .= '-' . $id; }
                    $idx['id'][$id] = $s;
                    $idx['slug'][strtolower($s)] = $id;
                }
            }
        } catch (Throwable $e) { $idx = array('id' => array(), 'slug' => array()); }
        return $idx;
    }
}

if (!function_exists('fm_prod_slug')) {
    function fm_prod_slug($conn, $id)
    {
        $idx = fm_prod_index($conn);
        return isset($idx['id'][(int) $id]) ? $idx['id'][(int) $id] : '';
    }
}

if (!function_exists('fm_prod_par_slug')) {
    /** Numero du produit pour une adresse (sans tenir compte des majuscules), 0 si inconnu. */
    function fm_prod_par_slug($conn, $slug)
    {
        $idx = fm_prod_index($conn);
        $k = strtolower((string) $slug);
        return isset($idx['slug'][$k]) ? $idx['slug'][$k] : 0;
    }
}

if (!function_exists('fm_prod_chemin')) {
    /** Chemin (sans prefixe) d'un produit : products_detail/Fresh-Figs/ (ou, a defaut, avec le numero). */
    function fm_prod_chemin($conn, $id)
    {
        $s = fm_prod_slug($conn, $id);
        return 'products_detail/' . ($s !== '' ? $s : (int) $id) . '/';
    }
}

if (!function_exists('fm_prod_url')) {
    function fm_prod_url($id)
    {
        global $fm_app, $conn;
        return $fm_app . fm_prod_chemin(isset($conn) ? $conn : null, $id);
    }
}

/* ------------------------------------------------------------------- guides */

if (!function_exists('fm_guide_chemin')) {
    function fm_guide_chemin($slug) { return 'guides/' . rawurlencode((string) $slug) . '/'; }
}
if (!function_exists('fm_guide_url')) {
    function fm_guide_url($slug)
    {
        global $fm_app;
        return $fm_app . fm_guide_chemin($slug);
    }
}

/* --------------------------------------------------------------- redirection */

if (!function_exists('fm_redirige')) {
    /** Redirection definitive (301) vers un chemin du site, en gardant la requete utile. */
    function fm_redirige($chemin)
    {
        global $fm_app;
        header('Location: ' . $fm_app . ltrim($chemin, '/'), true, 301);
        exit;
    }
}
