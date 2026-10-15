<?php
/**
 * Photos des produits : une seule regle pour tout le site.
 *
 * Les photos d'un produit sont rangees dans un dossier portant sa REFERENCE :
 *
 *     images/<Ref_prod>/photo1.jpg
 *     images/<Ref_prod>/photo2.png
 *
 * Quelles photos affiche-t-on ?
 *   1. Celles listees dans la colonne "Photo" de la base (separees par une
 *      virgule ou un retour a la ligne), si le fichier existe vraiment : on garde
 *      ainsi la main sur l'ordre et le choix.
 *   2. Sinon (colonne vide, ou fichiers absents), toutes les images trouvees dans
 *      le dossier images/<Ref_prod>/, classees par nom.
 *
 * Il suffit donc de deposer les photos dans le bon dossier pour qu'elles
 * apparaissent : plus besoin de remplir la colonne Photo.
 */

if (!function_exists('fm_thumb_cfg')) {
    /** Reglage de miniatures (voir thumb-config.php). */
    function fm_thumb_cfg($cle = null)
    {
        static $cfg = null;
        if ($cfg === null) {
            $defaut = array(
                'largeurs' => array(240, 320, 400, 480, 640, 800, 1000, 1200),
                'qualite_webp' => 80, 'qualite_jpeg' => 82,
                'carte' => array(320, 480, 640), 'grande' => array(640, 800, 1000),
            );
            $lu = is_file(__DIR__ . '/thumb-config.php') ? include __DIR__ . '/thumb-config.php' : array();
            $cfg = is_array($lu) ? array_merge($defaut, $lu) : $defaut;
        }
        return $cle === null ? $cfg : (isset($cfg[$cle]) ? $cfg[$cle] : null);
    }
}

if (!function_exists('fm_prod_fichiers')) {
    /**
     * Noms des fichiers photo d'un produit, tels qu'ils existent sur le disque.
     *
     * @param string $ref          Reference du produit (nom du dossier).
     * @param string $champ_photo  Colonne "Photo" de la base (peut etre vide).
     * @return string[]            Liste de noms de fichiers (vide : aucune photo).
     */
    function fm_prod_fichiers($ref, $champ_photo = '')
    {
        static $cache = array();
        $ref = trim((string) $ref);
        /* La reference vient de la base : on n'accepte qu'un nom de dossier simple. */
        if (!preg_match('/^[A-Za-z0-9][A-Za-z0-9_.-]{0,79}$/', $ref) || strpos($ref, '..') !== false) {
            return array();
        }
        $cle = $ref . "\0" . (string) $champ_photo;
        if (isset($cache[$cle])) { return $cache[$cle]; }

        $dossier = dirname(__DIR__) . '/images/' . $ref;
        $liste   = array();

        /* 1) photos listees dans la base et presentes sur le disque */
        foreach (preg_split('/[\r\n,;]+/', (string) $champ_photo) as $f) {
            $f = trim($f);
            if ($f === '' || strpos($f, '/') !== false || strpos($f, '\\') !== false || strpos($f, '..') !== false) { continue; }
            if (is_file($dossier . '/' . $f) && !in_array($f, $liste, true)) { $liste[] = $f; }
        }

        /* 2) sinon : les images du dossier de la reference */
        if (!$liste && is_dir($dossier)) {
            $tous = @scandir($dossier);
            if (is_array($tous)) {
                natcasesort($tous);
                foreach ($tous as $f) {
                    if ($f === '' || $f[0] === '.') { continue; }
                    if (!preg_match('/\.(jpe?g|png|webp|gif)$/i', $f)) { continue; }
                    if (is_file($dossier . '/' . $f)) { $liste[] = $f; }
                    if (count($liste) >= 12) { break; }
                }
            }
        }
        return $cache[$cle] = $liste;
    }
}

if (!function_exists('fm_prod_premiere_photo')) {
    /** Premiere photo d'un produit (nom de fichier) ou null. */
    function fm_prod_premiere_photo($ref, $champ_photo = '')
    {
        $l = fm_prod_fichiers($ref, $champ_photo);
        return $l ? $l[0] : null;
    }
}
