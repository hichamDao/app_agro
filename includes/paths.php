<?php
/**
 * Chemin absolu de la racine du site, calcule une seule fois.
 *
 * Pourquoi : les URL publiques du site sont reecrites (.htaccess) vers des
 * fichiers situes ailleurs. Par exemple /foodmaxorg/about-us/ est servi par
 * /foodmaxorg/about-us.php, et /foodmaxorg/products/3/Citrus/ par
 * /foodmaxorg/products/products.php. Or le navigateur resout les chemins
 * relatifs par rapport a l'URL demandee, pas par rapport au fichier PHP :
 * un lien relatif "css/phlox.css" demande donc /foodmaxorg/about-us/css/phlox.css,
 * qui n'existe pas.
 *
 * On calcule donc $fm_app directement depuis le dossier racine de l'application
 * (le parent de ce fichier includes/), ce qui donne "/foodmaxorg/" quelle que
 * soit la page servie. Toutes les ressources doivent utiliser $fm_app.
 *
 * $fm_app : chaine finissant par "/", a concatener devant un chemin interne.
 */
if (!isset($fm_app) || $fm_app === '') {
    $fm_app = '/';

    if (!empty($_SERVER['DOCUMENT_ROOT']) && !empty($_SERVER['SCRIPT_FILENAME'])) {
        $docRoot = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/');
        $appDir  = rtrim(str_replace('\\', '/', dirname(__DIR__)), '/');

        /* Windows et insensibilite a la casse : on compare sans-slash et sans casse. */
        if (stripos($appDir . '/', $docRoot . '/') === 0) {
            $rel = substr($appDir, strlen($docRoot));
            $fm_app = rtrim(str_replace('\\', '/', $rel), '/') . '/';

            /* Hebergement mutualise type WEDOS : le site vit dans
               /www/domains/<nom-domaine>/ (ou /www/subdom/<sous-domaine>/) alors
               que DOCUMENT_ROOT peut rester /www. Dans ce cas le calcul brut
               donne "/domains/<nom-domaine>/", qui casserait toutes les URL
               publiques : on retire donc ce premier segment, le site etant
               servi a la racine du domaine. */
            $fm_app = preg_replace('#^/(domains|subdom)/[^/]+#', '', $fm_app);
            if ($fm_app === '' || $fm_app === null) {
                $fm_app = '/';
            }
        }
    }
}

/* Sur le site en ligne, les erreurs PHP ne doivent jamais s'afficher aux
   visiteurs ni a Google (elles revelent des chemins du serveur et abiment la
   page) : elles sont enregistrees dans le journal d'erreurs de l'hebergeur. En
   local (localhost), elles restent visibles pour travailler. */
if (!defined('FM_EST_LOCAL')) {
    $fm_hote = isset($_SERVER['HTTP_HOST']) ? strtolower(preg_replace('/:\d+$/', '', $_SERVER['HTTP_HOST'])) : '';
    define('FM_EST_LOCAL', in_array($fm_hote, array('localhost', '127.0.0.1', '::1'), true) || substr($fm_hote, -6) === '.local');
    if (!FM_EST_LOCAL) {
        @ini_set('display_errors', '0');
        @ini_set('log_errors', '1');
    }
}

if (!function_exists('fm_ver')) {
    /** "?v=1730000000" : date de modification du fichier. Le navigateur peut garder
     *  CSS et JS longtemps en cache, et recharge aussitot qu'un fichier change. */
    function fm_ver($rel)
    {
        $fichier = dirname(__DIR__) . '/' . ltrim((string) $rel, '/');
        return is_file($fichier) ? '?v=' . filemtime($fichier) : '';
    }
}

if (!function_exists('fm_thumb')) {
    /**
     * Adresse de la miniature d'une photo du dossier images/ (prete pour un
     * attribut HTML). Pour tout autre fichier (logo, .svg...), renvoie l'adresse
     * d'origine inchangee. Voir thumb.php.
     *
     * @param string $url  adresse publique de la photo, commencant par $fm_app
     * @param int    $w    largeur voulue en pixels
     */
    function fm_thumb($url, $w = 480)
    {
        global $fm_app;
        $url = (string) $url;
        $rel = (strpos($url, $fm_app) === 0) ? rawurldecode(substr($url, strlen($fm_app))) : '';
        if (!preg_match('~^images/[^/]+/[^/]+\.(jpe?g|png|webp)$~i', $rel)) {
            return htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
        }
        return $fm_app . 'thumb.php?p=' . rawurlencode($rel) . '&amp;w=' . (int) $w;
    }
}

if (!function_exists('fm_img_attrs')) {
    /**
     * Attributs src, srcset et sizes d'une photo : le navigateur choisit lui-meme
     * la miniature adaptee a son ecran. A utiliser ainsi :
     *   <img<?php echo fm_img_attrs($url, '(min-width: 900px) 33vw, 100vw'); ?> alt="...">
     */
    function fm_img_attrs($url, $sizes = '100vw', $largeurs = array(320, 480, 640))
    {
        $src = fm_thumb($url, $largeurs[(int) floor(count($largeurs) / 2)]);
        $attrs = ' src="' . $src . '"';
        if ($src === htmlspecialchars((string) $url, ENT_QUOTES, 'UTF-8')) {
            return $attrs;                                   /* pas une photo : rien a redimensionner */
        }
        $set = array();
        foreach ($largeurs as $l) { $set[] = fm_thumb($url, $l) . ' ' . (int) $l . 'w'; }
        return $attrs . ' srcset="' . implode(', ', $set) . '" sizes="' . htmlspecialchars($sizes, ENT_QUOTES, 'UTF-8') . '"';
    }
}
