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
