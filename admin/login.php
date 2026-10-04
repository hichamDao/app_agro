<?php
/**
 * Page d'identification de l'administration.
 *
 * Verifie le couple identifiant / mot de passe contre la table members avec
 * une requete preparee, ouvre la session, puis renvoie vers la page demandee.
 * Un ancien hash md5 est accepte puis reecrit en bcrypt : les comptes deja en
 * base ne sont pas condamnes, mais ne restent pas en md5.
 */

require_once(__DIR__ . '/../includes/admin-auth.php');
/* $fm_app (chemin absolu de la racine du site) sert aux URL du logo et des
   feuilles de style. Sans cet include, $fm_app restait indefini et les
   ressources ne se chargeaient pas. admin-layout.php ne le fait pas pour
   cette page : elle n'utilise pas le gabarit. */
require_once(__DIR__ . '/../includes/paths.php');

/* Deja identifie : on ne montre pas le formulaire. */
if (fm_admin_est_identifie()) {
    header('Location: index.php');
    exit;
}

$erreur = '';
$info   = '';

/* Message de confirmation apres une deconnexion (logout.php renvoie ici). */
if (isset($_GET['deconnecte'])) {
    $info = 'Vous avez ete deconnecte.';
}

/* Destination apres connexion, uniquement une page interne de l'admin. */
$next = isset($_GET['next']) ? (string) $_GET['next'] : 'index.php';
if ($next === '' || $next[0] !== '/' || strpos($next, '//') === 0 || strpos($next, "\n") !== false) {
    $next = 'index.php';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    fm_admin_verifier_csrf();

    $login     = trim((string) (isset($_POST['login']) ? $_POST['login'] : ''));
    $motdepasse = (string) (isset($_POST['pass']) ? $_POST['pass'] : '');

    if ($login === '' || $motdepasse === '') {
        $erreur = 'Renseignez votre identifiant et votre mot de passe.';
    } else {
        $stmt = mysqli_prepare($conn, 'SELECT id, username, password, role FROM members WHERE username = ? LIMIT 1');
        $membre = null;
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 's', $login);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if ($res) {
                $membre = mysqli_fetch_assoc($res);
            }
            mysqli_stmt_close($stmt);
        }

        $hash = $membre ? fm_admin_verifier_mot_de_passe($motdepasse, $membre['password']) : false;

        if ($membre && $hash !== false) {
            /* Upgrade md5 -> bcrypt a la premiere reussite. */
            if (is_string($hash)) {
                $maj = mysqli_prepare($conn, 'UPDATE members SET password = ? WHERE id = ?');
                if ($maj) {
                    mysqli_stmt_bind_param($maj, 'si', $hash, $membre['id']);
                    mysqli_stmt_execute($maj);
                    mysqli_stmt_close($maj);
                }
            }

            /* Renouveler l'identifiant de session : evite la fixation de
               session si le cookie existait avant la connexion. */
            session_regenerate_id(true);

            $_SESSION['fm_admin_login']  = $membre['username'];
            $_SESSION['fm_admin_id']     = (int) $membre['id'];
            $_SESSION['fm_admin_debut']  = time();
            /* Un role inconnu ou vide ne donne jamais les droits d'admin :
               on retombe sur 'moderateur', le role le plus restrictif. */
            $_SESSION['fm_admin_role']   = (isset($membre['role']) && $membre['role'] === 'admin') ? 'admin' : 'moderateur';
            /* Conserve la session existante : entete.php (pages admin
               historiques) teste encore ROLE_USER. */
            $_SESSION['ROLE_USER']       = '0';

            header('Location: ' . $next);
            exit;
        }

        /* Meme message dans les deux cas : ne pas reveler quel identifiant
           existe. */
        $erreur = 'Identifiant ou mot de passe incorrect.';
    }
}

fm_admin_jeton();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Connexion - <?php echo fm_admin_echapper(FM_ADMIN_TITRE); ?></title>
<link rel="stylesheet" href="<?php echo $fm_app; ?>css/font-awesome.min.css">
<link rel="stylesheet" href="admin-style.css">
</head>
<body class="fm-admin">
<div class="fm-login-page">
    <div class="fm-login-box">
        <img class="fm-login-logo" src="<?php echo $fm_app; ?>images/logo.png" alt="Foodmax">
        <h1>Espace administration</h1>
        <p class="fm-login-sub">Foodmax Group &mdash; Acces reserve</p>

        <?php if ($erreur !== '') { ?>
        <div class="fm-adm-alert is-err">
            <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
            <span><?php echo fm_admin_echapper($erreur); ?></span>
        </div>
        <?php } ?>

        <?php if ($info !== '') { ?>
        <div class="fm-adm-alert is-ok">
            <i class="fa fa-check-circle" aria-hidden="true"></i>
            <span><?php echo fm_admin_echapper($info); ?></span>
        </div>
        <?php } ?>

        <form method="post" action="login.php" autocomplete="off">
            <?php echo fm_admin_champ_csrf(); ?>
            <input type="hidden" name="next" value="<?php echo fm_admin_echapper($next); ?>">

            <div class="fm-adm-field">
                <label for="login">Identifiant</label>
                <input class="fm-adm-input" type="text" id="login" name="login"
                       value="<?php echo fm_admin_echapper(isset($_POST['login']) ? $_POST['login'] : ''); ?>"
                       required autofocus>
            </div>

            <div class="fm-adm-field">
                <label for="pass">Mot de passe</label>
                <input class="fm-adm-input" type="password" id="pass" name="pass" required>
            </div>

            <button class="fm-adm-btn" type="submit">
                <i class="fa fa-sign-in" aria-hidden="true"></i> Se connecter
            </button>
        </form>

        <p class="fm-login-foot">
            <a href="<?php echo $fm_app; ?>">Retour au site</a>
        </p>
    </div>
</div>
</body>
</html>