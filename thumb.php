<?php
/**
 * Miniatures d'images a la demande.
 *
 *   /thumb.php?p=images/P102170/carottes.jpg&w=480
 *
 * Pourquoi : les photos de produits font souvent 1 a 3 Mo alors qu'une carte du
 * site les affiche a 300 pixels de large. Cette page renvoie la meme photo
 * redimensionnee (quelques dizaines de Ko), en WebP quand le navigateur le
 * supporte, et la garde en cache sur le disque (cache/thumbs/) : elle n'est
 * calculee qu'une fois.
 *
 * Securite : seuls les fichiers image situes sous images/ sont acceptes ; un
 * chemin avec "..", un autre dossier ou une autre extension est refuse. La
 * largeur est limitee a une liste fixe, ce qui borne la taille du cache.
 *
 * Si la bibliotheque GD manque ou si le cache ne peut pas etre ecrit, la page
 * renvoie vers la photo d'origine : le site continue de fonctionner.
 */

$racine = __DIR__;
$rel    = isset($_GET['p']) && is_string($_GET['p']) ? $_GET['p'] : '';
$w      = isset($_GET['w']) ? (int) $_GET['w'] : 480;

/* ---- validation du chemin */
if ($rel === '' || strpos($rel, '..') !== false || strpos($rel, "\0") !== false
    || !preg_match('~^images/[^/\\\\\x00-\x1f]{1,120}/[^/\\\\\x00-\x1f]{1,160}\.(jpe?g|png|webp)$~iu', $rel)) {
    http_response_code(400);
    exit;
}
$source = $racine . '/' . $rel;
if (!is_file($source)) {
    http_response_code(404);
    exit;
}

/* ---- largeur : la plus proche valeur autorisee, vers le haut */
$permises = array(240, 320, 480, 640, 800, 1000, 1200);
$choisie  = end($permises);
foreach ($permises as $p) { if ($w <= $p) { $choisie = $p; break; } }
$w = $choisie;

/* ---- adresse de la photo d'origine (repli) */
$base    = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
$segments = array_map('rawurlencode', explode('/', $rel));
$origine = $base . '/' . implode('/', $segments);
$repli = function () use ($origine) {
    header('Location: ' . $origine, true, 302);
    exit;
};

if (!function_exists('imagecreatefromstring') || !function_exists('imagecopyresampled')) { $repli(); }

/* ---- format de sortie : WebP si le navigateur l'accepte */
$accepte = isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : '';
$webp    = function_exists('imagewebp') && stripos($accepte, 'image/webp') !== false;
$ext     = $webp ? 'webp' : 'jpg';
$mime    = $webp ? 'image/webp' : 'image/jpeg';

/* ---- cache disque */
$dossier = $racine . '/cache/thumbs';
$cle     = sha1($rel) . '-' . $w . '.' . $ext;
$fichier = $dossier . '/' . $cle;
$mtimeSrc = filemtime($source);

$octets = null;
$date   = null;
if (is_file($fichier) && filemtime($fichier) >= $mtimeSrc) {
    $date = filemtime($fichier);
} else {
    @ini_set('memory_limit', '256M');
    $donnees = @file_get_contents($source);
    $img = $donnees !== false ? @imagecreatefromstring($donnees) : false;
    if (!$img) { $repli(); }

    $sw = imagesx($img);
    $sh = imagesy($img);
    $tw = min($w, $sw);                         /* jamais d'agrandissement */
    $th = max(1, (int) round($sh * $tw / $sw));
    $dst = imagecreatetruecolor($tw, $th);

    if ($webp) {                                /* garde la transparence (PNG) */
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagefill($dst, 0, 0, imagecolorallocatealpha($dst, 255, 255, 255, 127));
    } else {                                    /* JPEG : fond blanc */
        imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));
    }
    imagecopyresampled($dst, $img, 0, 0, 0, 0, $tw, $th, $sw, $sh);
    imagedestroy($img);

    ob_start();
    if ($webp) { imagewebp($dst, null, 80); } else { imageinterlace($dst, true); imagejpeg($dst, null, 82); }
    $octets = ob_get_clean();
    imagedestroy($dst);
    if ($octets === false || $octets === '') { $repli(); }

    /* ecriture dans le cache (si impossible, on sert quand meme l'image calculee) */
    if (!is_dir($dossier)) { @mkdir($dossier, 0775, true); }
    if (is_dir($dossier) && is_writable($dossier)) {
        $tmp = $fichier . '.' . getmypid() . '.tmp';
        if (@file_put_contents($tmp, $octets) !== false) { @rename($tmp, $fichier); }
    }
    $date = time();
}

/* ---- reponse + cache navigateur (304 si inchange) */
$etag = '"' . substr(md5($cle . $date), 0, 20) . '"';
header('Content-Type: ' . $mime);
header('Cache-Control: public, max-age=2592000');
header('Vary: Accept');
header('ETag: ' . $etag);
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $date) . ' GMT');

$inm = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH']) : '';
$ims = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) : false;
if ($inm === $etag || ($inm === '' && $ims !== false && $ims >= $date)) {
    http_response_code(304);
    exit;
}

if ($octets === null) { $octets = file_get_contents($fichier); }
header('Content-Length: ' . strlen($octets));
echo $octets;
