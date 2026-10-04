<?php
/**
 * Socle de l'administration Foodmax : session, garde d'acces, jeton CSRF,
 * messages flash et petites aides d'affichage.
 *
 * Toutes les pages admin passent par ce fichier. Il demarre la session,
 * verifie que l'utilisateur est identifie, expose un jeton CSRF a controler
 * sur chaque POST, et garantit que la connexion est fermee en fin de script.
 *
 * Les anciens comptes stockent leur mot de passe en md5 (table members).
 * admin_verifier_mot_de_passe accepte bcrypt et, en repli, md5 ; le hash est
 * alors reecrit en bcrypt a la premiere connexion reussie.
 */

/* --------------------------------------------------------- configuration */

define('FM_ADMIN_TITRE', 'Administration Foodmax');

if (session_status() === PHP_SESSION_NONE) {
    /* httponly + samesite : le cookie de session ne part pas vers d'autres
       sites et n'est pas lisible par un script injecte. */
    session_set_cookie_params(array(
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ));
    session_start();
}

/* Connexion : l'include est fait une seule fois, le reste du site s'en sert. */
require_once(__DIR__ . '/connection.php');

/* ------------------------------------------------------------- identite */

function fm_admin_est_identifie() {
    return !empty($_SESSION['fm_admin_login']);
}

function fm_admin_login() {
    return isset($_SESSION['fm_admin_login']) ? $_SESSION['fm_admin_login'] : '';
}

/* ------------------------------------------------------------------- roles */

/** Les deux roles de l'administration. */
function fm_admin_roles() {
    return array(
        'admin'       => array('Administrateur', 'Acces complet, y compris la gestion des comptes.'),
        'moderateur'  => array('Moderateur',    'Catalogue, categories, galerie et messages. Pas de gestion des comptes.'),
    );
}

function fm_admin_role() {
    $r = isset($_SESSION['fm_admin_role']) ? (string) $_SESSION['fm_admin_role'] : 'moderateur';
    return $r === 'admin' ? 'admin' : 'moderateur';
}

/** Libelle lisible du role courant. */
function fm_admin_role_libelle($role = null) {
    $r = $role === null ? fm_admin_role() : (string) $role;
    $roles = fm_admin_roles();
    return isset($roles[$r]) ? $roles[$r][0] : $r;
}

/**
 * Droits par module. Seul 'admin' touche au module utilisateurs : un
 * moderateur gere le contenu mais pas les comptes.
 */
function fm_admin_droits($role = null) {
    $r = $role === null ? fm_admin_role() : (string) $role;
    if ($r === 'admin') {
        return array('produits', 'categories', 'galerie', 'contacts', 'utilisateurs');
    }
    return array('produits', 'categories', 'galerie', 'contacts');
}

/** Le role courant a-t-il le droit demande ? */
function fm_admin_peut($droit) {
    return in_array($droit, fm_admin_droits(), true);
}

/**
 * Bloque l'acces a toute page admin non identifiee : redirection vers la page
 * de connexion, en gardant la page demandee pour y revenir apres login.
 */
function fm_admin_exiger_login() {
    if (fm_admin_est_identifie()) {
        return;
    }
    header('Location: login.php?next=' . rawurlencode(fm_admin_cible()));
    exit;
}

/**
 * Idem pour un module reserve a l'administrateur : un moderateur qui y
 * arrive directement est renvoye vers le tableau de bord, pas vers le login
 * (il est bien identifie).
 */
function fm_admin_exiger_droit($droit) {
    fm_admin_exiger_login();
    if (fm_admin_peut($droit)) {
        return;
    }
    fm_admin_flash('err', 'Acces reserve a l\'administrateur.');
    header('Location: index.php');
    exit;
}

/** Page a rouvrir apres connexion, seulement si elle est interne. */
function fm_admin_cible() {
    $cible = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'index.php';
    /* On ne memorise que des chemins internes : une URL absolue ou un //
       allowait de renvoyer la session vers un site tiers. */
    if ($cible === '' || $cible[0] !== '/' || strpos($cible, '//') === 0) {
        $cible = 'index.php';
    }
    return $cible;
}

/** Surligne le module courant dans la barre laterale selon le role. */
function fm_admin_menu_visible() {
    $menu = array(
        'index'      => array('Tableau de bord', 'fa-dashboard', 'index.php'),
        'produits'   => array('Produits',        'fa-leaf',       'GestionProduits.php'),
        'categories' => array('Categories',      'fa-tags',       'GestionCategories.php'),
        'galerie'    => array('Galerie',         'fa-picture-o',  'GestionGaleries.php'),
        'contacts'   => array('Messages',        'fa-envelope',   'GestionContacts.php'),
        'utilisateurs' => array('Utilisateurs',   'fa-users',      'GestionUtilisateurs.php'),
    );
    $droits = fm_admin_droits();
    foreach ($menu as $cle => $item) {
        $besoin = $cle === 'utilisateurs' ? 'utilisateurs' : ($cle === 'index' ? 'produits' : $cle);
        if ($besoin === 'index' || in_array($besoin, $droits, true)) {
            $menu[$cle][] = $besoin;
        } else {
            $menu[$cle] = null;
        }
    }
    return array_filter($menu);
}

/**
 * Verifie un mot de passe stocke en bcrypt ou, pour les anciens comptes, en
 * md5. Retourne le hash a reecrire si un simple md5 a fait l'affaire.
 */
function fm_admin_verifier_mot_de_passe($saisi, $stocke) {
    if ($stocke === '' || $stocke === null) {
        return null;
    }
    /* bcrypt : longueur 60 et prefixe $2y$ / $2a$ / $2b$. */
    if (strlen($stocke) === 60 && strpos($stocke, '$2') === 0) {
        return password_verify($saisi, $stocke) ? null : false;
    }
    /* Ancien format md5 : accepte, puis renvoie le hash pour upgrade. */
    if (hash_equals(strtolower($stocke), md5($saisi))) {
        return password_hash($saisi, PASSWORD_DEFAULT);
    }
    return false;
}

/**
 * Jeton de la session, cree a la demande.
 */
function fm_admin_jeton() {
    if (empty($_SESSION['fm_admin_csrf'])) {
        $_SESSION['fm_admin_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['fm_admin_csrf'];
}

function fm_admin_champ_csrf() {
    return '<input type="hidden" name="csrf" value="' . htmlspecialchars(fm_admin_jeton(), ENT_QUOTES, 'UTF-8') . '">';
}

/** Refuse un POST dont le jeton ne correspond pas. */
function fm_admin_verifier_csrf() {
    $recu = isset($_POST['csrf']) ? (string) $_POST['csrf'] : '';
    if ($recu === '' || !hash_equals(fm_admin_jeton(), $recu)) {
        http_response_code(403);
        exit('Jeton de securite invalide ou expire. Rechargez la page et reessayez.');
    }
}

/* --------------------------------------------------------------- flash */

/** Message affiche une seule fois apres une redirection. */
function fm_admin_flash($type, $texte) {
    if (!isset($_SESSION['fm_admin_flash'])) {
        $_SESSION['fm_admin_flash'] = array();
    }
    $_SESSION['fm_admin_flash'][] = array('type' => $type, 'texte' => $texte);
}

function fm_admin_lire_flash() {
    $messages = isset($_SESSION['fm_admin_flash']) ? $_SESSION['fm_admin_flash'] : array();
    unset($_SESSION['fm_admin_flash']);
    return $messages;
}

/* ------------------------------------------------------------- affichage */

function fm_admin_echapper($valeur) {
    return htmlspecialchars((string) $valeur, ENT_QUOTES, 'UTF-8');
}

/** Construit une URL de l'admin en conservant la query courante. */
function fm_admin_url($page, $params = array()) {
    $qs = array_merge($_GET, $params);
    foreach ($qs as $k => $v) {
        if ($v === null || $v === '') {
            unset($qs[$k]);
        }
    }
    /* On repart d'un GET propre pour ne pas trainer d'anciens filtres. */
    $qs = array_merge(array('q' => isset($_GET['q']) ? $_GET['q'] : null), $params);
    $qs = array_filter($qs, function ($v) { return $v !== null && $v !== ''; });
    return $page . ($qs ? '?' . http_build_query($qs) : '');
}

/** « 2-10-2026 04:42:08 » (format pose par le formulaire public) -> date lisible. */
function fm_admin_date_contact($brut) {
    $brut = trim((string) $brut);
    $d = DateTime::createFromFormat('d-m-Y H:i:s', $brut);
    if (!$d) {
        $d = DateTime::createFromFormat('Y-m-d H:i:s', $brut);
    }
    return $d ? $d->format('d/m/Y à H:i') : $brut;
}

/* ------------------------------------------------------- fin de script */

register_shutdown_function(function () {
    global $conn;
    if ($conn instanceof mysqli) {
        @mysqli_close($conn);
    }
});