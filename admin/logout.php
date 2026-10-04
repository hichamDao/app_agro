<?php
/**
 * Deconnexion de l'administration.
 *
 * Depend de admin-auth.php : la session est ouverte puis vidée, et le cookie
 * de session est invalidé côté client. La redirection suit le paramètre next
 * s'il reste interne, sinon revient a la page de connexion.
 */

require_once(__DIR__ . '/../includes/admin-auth.php');

$next = isset($_GET['next']) ? (string) $_GET['next'] : '';
if ($next === '' || $next[0] !== '/' || strpos($next, '//') === 0 || strpos($next, "\n") !== false) {
    $next = 'login.php';
}

/* $_SESSION contient aussi ROLE_USER, lu par entete.php : on vide tout. */
$_SESSION = array();

if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $p['path'],
        $p['domain'],
        !empty($p['secure']),
        !empty($p['httponly'])
    );
}

session_destroy();

header('Location: ' . $next . (strpos($next, '?') === false ? '?deconnecte=1' : '&deconnecte=1'));
exit;