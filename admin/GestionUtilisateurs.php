<?php
/**
 * Gestion des comptes de l'administration, avec distinction de role.
 *
 * Deux roles seulement :
 *   admin      — acces complet, y compris ce module ;
 *   moderateur — catalogue, categories, galerie et messages, mais pas les
 *                comptes (voir fm_admin_droits dans includes/admin-auth.php).
 *
 * Le module est lui-meme reserve a 'admin' : un moderateur qui ouvre cette
 * page est renvoye vers le tableau de bord.
 *
 * Les mots de passe sont stockes en bcrypt (password_hash). Les comptes
 * crees avant la reprise de l'administration sont en md5 : ils ne sont
 * convertis qu'a leur premiere connexion reussie.
 */

require_once(__DIR__ . '/../includes/admin-auth.php');
require_once(__DIR__ . '/../includes/admin-layout.php');

fm_admin_exiger_droit('utilisateurs');

/* ------------------------------------------------------------ utilitaires */

function gu_role_valide($role) {
    return ($role === 'admin' || $role === 'moderateur');
}

/** Compte connecte, pour empecher de se verrouiller ou se supprimer. */
function gu_est_soi_meme($id) {
    return (int) $id === (int) $_SESSION['fm_admin_id'];
}

/** Nombre d'administrateurs restants : on refuse d.downgrader le dernier. */
function gu_nombre_admins($conn, $exclureId = 0) {
    $n = 0;
    $stmt = mysqli_prepare($conn, 'SELECT COUNT(*) FROM members WHERE role = ? AND id <> ?');
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'si', 'admin', $exclureId);
        mysqli_stmt_execute($stmt);
        $stmt->store_result();
        mysqli_stmt_bind_result($stmt, $n);
        $stmt->fetch();
        $stmt->close();
    }
    return (int) $n;
}

/* --------------------------------------------------------- traitements POST */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    fm_admin_verifier_csrf();
    $operation = isset($_POST['operation']) ? (string) $_POST['operation'] : '';

    /* ---------------------------------------------------------- creation */
    if ($operation === 'ajouter') {
        $login  = trim((string) (isset($_POST['username']) ? $_POST['username'] : ''));
        $email  = trim((string) (isset($_POST['email']) ? $_POST['email'] : ''));
        $tel    = trim((string) (isset($_POST['phone']) ? $_POST['phone'] : ''));
        $role   = (string) (isset($_POST['role']) ? $_POST['role'] : 'moderateur');
        $mdp    = (string) (isset($_POST['password']) ? $_POST['password'] : '');
        $mdp2   = (string) (isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '');

        $erreurs = array();
        if ($login === '') {
            $erreurs[] = 'L\'identifiant est obligatoire.';
        } elseif (!preg_match('/^[A-Za-z0-9._@-]{3,50}$/', $login)) {
            $erreurs[] = 'Identifiant invalide (3 a 50 caracteres : lettres, chiffres, point, tiret, @, souligné).';
        }
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erreurs[] = 'Adresse e-mail invalide.';
        }
        if (!gu_role_valide($role)) {
            $erreurs[] = 'Role inconnu.';
        }
        if (strlen($mdp) < 8) {
            $erreurs[] = 'Le mot de passe doit contenir au moins 8 caracteres.';
        } elseif ($mdp !== $mdp2) {
            $erreurs[] = 'Les deux mots de passe ne correspondent pas.';
        }

        /* Identifiant deja pris ? */
        if (!$erreurs) {
            $verif = mysqli_prepare($conn, 'SELECT id FROM members WHERE username = ?');
            if ($verif) {
                mysqli_stmt_bind_param($verif, 's', $login);
                mysqli_stmt_execute($verif);
                $res = mysqli_stmt_get_result($verif);
                if ($res && mysqli_fetch_row($res)) {
                    $erreurs[] = 'Cet identifiant est deja utilise.';
                }
                mysqli_stmt_close($verif);
            }
        }

        if ($erreurs) {
            foreach ($erreurs as $e) { fm_admin_flash('err', $e); }
            header('Location: GestionUtilisateurs.php?action=ajouter');
            exit;
        }

        $hash = password_hash($mdp, PASSWORD_DEFAULT);
        $dt = date('d-m-Y');
        $stmt = mysqli_prepare($conn, 'INSERT INTO members (username, role, password, email, phone, date) VALUES (?, ?, ?, ?, ?, ?)');
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ssssss', $login, $role, $hash, $email, $tel, $dt);
            $ok = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            fm_admin_flash($ok ? 'ok' : 'err', $ok
                ? 'Compte cree. Role : ' . fm_admin_role_libelle($role) . '.'
                : 'Creation impossible.');
        }
        header('Location: GestionUtilisateurs.php');
        exit;
    }

    /* ---------------------------------------------------------- edition */
    if ($operation === 'modifier') {
        $id    = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $email = trim((string) (isset($_POST['email']) ? $_POST['email'] : ''));
        $tel   = trim((string) (isset($_POST['phone']) ? $_POST['phone'] : ''));
        $role  = (string) (isset($_POST['role']) ? $_POST['role'] : '');

        $actuel = null;
        if ($id > 0) {
            $stmt = mysqli_prepare($conn, 'SELECT id, username, role FROM members WHERE id = ?');
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'i', $id);
                mysqli_stmt_execute($stmt);
                $res = mysqli_stmt_get_result($stmt);
                if ($res) { $actuel = mysqli_fetch_assoc($res); }
                mysqli_stmt_close($stmt);
            }
        }
        if (!$actuel) {
            fm_admin_flash('err', 'Compte introuvable.');
            header('Location: GestionUtilisateurs.php');
            exit;
        }

        $erreurs = array();
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erreurs[] = 'Adresse e-mail invalide.';
        }
        if (!gu_role_valide($role)) {
            $erreurs[] = 'Role inconnu.';
        }
        /* Ne pas retirer le dernier administrateur, ni se retirer soi-meme le
           droit : le site resterait sans personne pour gerer les comptes. */
        if ($actuel['role'] === 'admin' && $role !== 'admin') {
            if (gu_nombre_admins($conn, $id) === 0) {
                $erreurs[] = 'Impossible : ce compte est le dernier administrateur.';
            }
            if (gu_est_soi_meme($id)) {
                $erreurs[] = 'Vous ne pouvez pas retirer votre propre role administrateur.';
            }
        }

        if ($erreurs) {
            foreach ($erreurs as $e) { fm_admin_flash('err', $e); }
            header('Location: GestionUtilisateurs.php?action=modifier&id=' . $id);
            exit;
        }

        $stmt = mysqli_prepare($conn, 'UPDATE members SET email = ?, phone = ?, role = ? WHERE id = ?');
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'sssi', $email, $tel, $role, $id);
            $ok = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            fm_admin_flash($ok ? 'ok' : 'err', $ok ? 'Compte mis a jour.' : 'Mise a jour impossible.');
        }

        /* Le role du compte connecte vient de changer : la session doit
            suivre, sinon il garderait d'anciens droits jusqu'a reconnexion. */
        if ($ok && gu_est_soi_meme($id)) {
            $_SESSION['fm_admin_role'] = ($role === 'admin') ? 'admin' : 'moderateur';
        }
        header('Location: GestionUtilisateurs.php');
        exit;
    }

    /* ------------------------------------------------- reinitialisation mdp */
    if ($operation === 'mot_de_passe') {
        $id  = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $mdp = (string) (isset($_POST['password']) ? $_POST['password'] : '');
        $mdp2 = (string) (isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '');

        $erreurs = array();
        if ($id <= 0) { $erreurs[] = 'Compte invalide.'; }
        if (strlen($mdp) < 8) {
            $erreurs[] = 'Le mot de passe doit contenir au moins 8 caracteres.';
        } elseif ($mdp !== $mdp2) {
            $erreurs[] = 'Les deux mots de passe ne correspondent pas.';
        }
        if ($erreurs) {
            foreach ($erreurs as $e) { fm_admin_flash('err', $e); }
            header('Location: GestionUtilisateurs.php?action=mot_de_passe&id=' . $id);
            exit;
        }

        $hash = password_hash($mdp, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($conn, 'UPDATE members SET password = ? WHERE id = ?');
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'si', $hash, $id);
            $ok = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            fm_admin_flash($ok ? 'ok' : 'err', $ok ? 'Mot de passe reinitialise.' : 'Echec de la reinitialisation.');
        }
        header('Location: GestionUtilisateurs.php');
        exit;
    }

    /* ------------------------------------------------------- suppression */
    if ($operation === 'supprimer') {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($id <= 0) {
            fm_admin_flash('err', 'Compte invalide.');
            header('Location: GestionUtilisateurs.php');
            exit;
        }
        if (gu_est_soi_meme($id)) {
            fm_admin_flash('err', 'Vous ne pouvez pas supprimer votre propre compte.');
            header('Location: GestionUtilisateurs.php');
            exit;
        }

        /* Ne pas supprimer le dernier administrateur. */
        $verif = mysqli_prepare($conn, 'SELECT role FROM members WHERE id = ?');
        $roleCible = null;
        if ($verif) {
            mysqli_stmt_bind_param($verif, 'i', $id);
            mysqli_stmt_execute($verif);
            $res = mysqli_stmt_get_result($verif);
            if ($res && ($r = mysqli_fetch_assoc($res))) { $roleCible = $r['role']; }
            mysqli_stmt_close($verif);
        }
        if ($roleCible === null) {
            fm_admin_flash('err', 'Compte introuvable.');
        } elseif ($roleCible === 'admin' && gu_nombre_admins($conn, $id) === 0) {
            fm_admin_flash('err', 'Impossible : ce compte est le dernier administrateur.');
        } else {
            $suppr = mysqli_prepare($conn, 'DELETE FROM members WHERE id = ?');
            if ($suppr) {
                mysqli_stmt_bind_param($suppr, 'i', $id);
                $ok = mysqli_stmt_execute($suppr);
                mysqli_stmt_close($suppr);
                fm_admin_flash($ok ? 'ok' : 'err', $ok ? 'Compte supprime.' : 'Suppression impossible.');
            }
        }
        header('Location: GestionUtilisateurs.php');
        exit;
    }
}

/* --------------------------------------------------------------- lecture */

$action = isset($_GET['action']) ? (string) $_GET['action'] : '';
$idEd   = isset($_GET['id']) ? (int) $_GET['id'] : 0;

/* ------------------------------------------------------ vues formulaire */

if ($action === 'ajouter') {
    admin_entete(
        'Ajouter un compte',
        'utilisateurs',
        array('<a class="fm-adm-btn is-light is-sm" href="GestionUtilisateurs.php"><i class="fa fa-arrow-left" aria-hidden="true"></i> Retour</a>')
    );
    $retour = array('username' => '', 'email' => '', 'phone' => '', 'role' => 'moderateur');
    ?>

    <form method="post" action="GestionUtilisateurs.php">
        <?php echo fm_admin_champ_csrf(); ?>
        <input type="hidden" name="operation" value="ajouter">

        <div class="fm-adm-card">
            <header><h2>Nouveau compte</h2></header>
            <div class="fm-adm-pad">
                <div class="fm-adm-field">
                    <label for="username">Identifiant <span aria-hidden="true">*</span></label>
                    <input class="fm-adm-input" type="text" id="username" name="username" maxlength="50" required
                           value="<?php echo fm_admin_echapper($retour['username']); ?>">
                    <span class="fm-adm-help">Ce que la personne saisit sur la page de connexion.</span>
                </div>

                <div class="fm-adm-grid">
                    <div class="fm-adm-field">
                        <label for="email">E-mail</label>
                        <input class="fm-adm-input" type="email" id="email" name="email" maxlength="50"
                               value="<?php echo fm_admin_echapper($retour['email']); ?>">
                    </div>
                    <div class="fm-adm-field">
                        <label for="phone">Telephone</label>
                        <input class="fm-adm-input" type="text" id="phone" name="phone" maxlength="20"
                               value="<?php echo fm_admin_echapper($retour['phone']); ?>">
                    </div>
                </div>

                <div class="fm-adm-field">
                    <label for="role">Role <span aria-hidden="true">*</span></label>
                    <select class="fm-adm-select" id="role" name="role" required>
                        <?php foreach (fm_admin_roles() as $cle => $def) { ?>
                        <option value="<?php echo fm_admin_echapper($cle); ?>"
                            <?php echo ($retour['role'] === $cle) ? 'selected' : ''; ?>>
                            <?php echo fm_admin_echapper($def[0]); ?>
                        </option>
                        <?php } ?>
                    </select>
                    <span class="fm-adm-help">
                        <?php foreach (fm_admin_roles() as $cle => $def) { ?>
                        <b><?php echo fm_admin_echapper($def[0]); ?></b> : <?php echo fm_admin_echapper($def[1]); ?><br>
                        <?php } ?>
                    </span>
                </div>

                <div class="fm-adm-grid">
                    <div class="fm-adm-field">
                        <label for="password">Mot de passe <span aria-hidden="true">*</span></label>
                        <input class="fm-adm-input" type="password" id="password" name="password"
                               minlength="8" required autocomplete="new-password">
                        <span class="fm-adm-help">8 caracteres minimum, stocke en bcrypt.</span>
                    </div>
                    <div class="fm-adm-field">
                        <label for="password_confirm">Confirmation <span aria-hidden="true">*</span></label>
                        <input class="fm-adm-input" type="password" id="password_confirm" name="password_confirm"
                               minlength="8" required autocomplete="new-password">
                    </div>
                </div>
            </div>
            <footer>
                <button class="fm-adm-btn" type="submit"><i class="fa fa-user-plus" aria-hidden="true"></i> Creer le compte</button>
            </footer>
        </div>
    </form>

    <?php
    admin_pied();
    exit;
}

if (($action === 'modifier' || $action === 'mot_de_passe') && $idEd > 0) {
    $stmt = mysqli_prepare($conn, 'SELECT id, username, role, email, phone, date FROM members WHERE id = ?');
    $compte = null;
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $idEd);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if ($res) { $compte = mysqli_fetch_assoc($res); }
        mysqli_stmt_close($stmt);
    }
    if (!$compte) {
        fm_admin_flash('err', 'Compte introuvable.');
        header('Location: GestionUtilisateurs.php');
        exit;
    }
    $estMoi = gu_est_soi_meme($idEd);

    admin_entete(
        ($action === 'modifier' ? 'Modifier ' : 'Mot de passe de ') . $compte['username'],
        'utilisateurs',
        array('<a class="fm-adm-btn is-light is-sm" href="GestionUtilisateurs.php"><i class="fa fa-arrow-left" aria-hidden="true"></i> Retour</a>')
    );
    ?>

    <?php if ($action === 'modifier') { ?>
    <form method="post" action="GestionUtilisateurs.php">
        <?php echo fm_admin_champ_csrf(); ?>
        <input type="hidden" name="operation" value="modifier">
        <input type="hidden" name="id" value="<?php echo (int) $compte['id']; ?>">

        <div class="fm-adm-card">
            <header><h2>Compte</h2></header>
            <div class="fm-adm-pad">
                <div class="fm-adm-field">
                    <label>Identifiant</label>
                    <input class="fm-adm-input" type="text" value="<?php echo fm_admin_echapper($compte['username']); ?>" disabled>
                    <span class="fm-adm-help">Non modifiable : il sert de clef a la table members.</span>
                </div>

                <div class="fm-adm-grid">
                    <div class="fm-adm-field">
                        <label for="email">E-mail</label>
                        <input class="fm-adm-input" type="email" id="email" name="email" maxlength="50"
                               value="<?php echo fm_admin_echapper($compte['email']); ?>">
                    </div>
                    <div class="fm-adm-field">
                        <label for="phone">Telephone</label>
                        <input class="fm-adm-input" type="text" id="phone" name="phone" maxlength="20"
                               value="<?php echo fm_admin_echapper($compte['phone']); ?>">
                    </div>
                </div>

                <div class="fm-adm-field">
                    <label for="role">Role</label>
                    <select class="fm-adm-select" id="role" name="role">
                        <?php foreach (fm_admin_roles() as $cle => $def) { ?>
                        <option value="<?php echo fm_admin_echapper($cle); ?>"
                            <?php echo ($compte['role'] === $cle) ? 'selected' : ''; ?>>
                            <?php echo fm_admin_echapper($def[0]); ?>
                        </option>
                        <?php } ?>
                    </select>
                    <?php if ($estMoi) { ?>
                    <span class="fm-adm-help">
                        Vous modifiez votre propre compte : le role sera applique immediatement.
                    </span>
                    <?php } ?>
                </div>
            </div>
            <footer>
                <button class="fm-adm-btn" type="submit"><i class="fa fa-save" aria-hidden="true"></i> Enregistrer</button>
            </footer>
        </div>
    </form>
    <?php } else { ?>

    <form method="post" action="GestionUtilisateurs.php" autocomplete="off">
        <?php echo fm_admin_champ_csrf(); ?>
        <input type="hidden" name="operation" value="mot_de_passe">
        <input type="hidden" name="id" value="<?php echo (int) $compte['id']; ?>">

        <div class="fm-adm-card">
            <header><h2>Reinitialiser le mot de passe de <?php echo fm_admin_echapper($compte['username']); ?></h2></header>
            <div class="fm-adm-pad">
                <div class="fm-adm-grid">
                    <div class="fm-adm-field">
                        <label for="password">Nouveau mot de passe <span aria-hidden="true">*</span></label>
                        <input class="fm-adm-input" type="password" id="password" name="password"
                               minlength="8" required autocomplete="new-password">
                    </div>
                    <div class="fm-adm-field">
                        <label for="password_confirm">Confirmation <span aria-hidden="true">*</span></label>
                        <input class="fm-adm-input" type="password" id="password_confirm" name="password_confirm"
                               minlength="8" required autocomplete="new-password">
                    </div>
                </div>
                <p style="font-size:13px;color:var(--fm-muted);margin:0;">
                    Le nouveau mot de passe est stocke en bcrypt et remplace l'ancien definitivement.
                    <?php if (!$estMoi) { ?>
                    La personne ne peut pas changer elle-meme son mot de passe depuis l'interface.
                    <?php } ?>
                </p>
            </div>
            <footer>
                <button class="fm-adm-btn" type="submit"><i class="fa fa-key" aria-hidden="true"></i> Reinitialiser</button>
            </footer>
        </div>
    </form>
    <?php }

    admin_pied();
    exit;
}

/* ----------------------------------------------------------------- liste */

$filtreRole = isset($_GET['role']) ? (string) $_GET['role'] : '';
$motCle     = isset($_GET['q']) ? trim((string) $_GET['q']) : '';

if ($filtreRole !== '' && !gu_role_valide($filtreRole)) {
    $filtreRole = '';
}

$where  = array();
$params = array();
$types  = '';
if ($filtreRole !== '') {
    $where[]  = 'role = ?';
    $params[] = $filtreRole;
    $types   .= 's';
}
if ($motCle !== '') {
    $where[]  = '(username LIKE ? OR email LIKE ?)';
    $params[] = '%' . $motCle . '%';
    $params[] = '%' . $motCle . '%';
    $types   .= 'ss';
}
$conditions = $where ? (' WHERE ' . implode(' AND ', $where)) : '';
$sqlWhere   = $conditions . ' ORDER BY username ASC';

$membres = array();
$stmt = mysqli_prepare($conn, 'SELECT id, username, role, email, phone, date FROM members' . $sqlWhere);
if ($stmt) {
    if ($types !== '') { mysqli_stmt_bind_param($stmt, $types, ...$params); }
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res) {
        while ($m = mysqli_fetch_assoc($res)) { $membres[] = $m; }
    }
    mysqli_stmt_close($stmt);
}

$nbAdmins = 0;
$nbMods   = 0;
foreach ($membres as $m) {
    if ($m['role'] === 'admin') { $nbAdmins++; } else { $nbMods++; }
}

admin_entete(
    'Utilisateurs',
    'utilisateurs',
    array('<a class="fm-adm-btn is-sm" href="GestionUtilisateurs.php?action=ajouter"><i class="fa fa-user-plus" aria-hidden="true"></i> Ajouter un compte</a>')
);
?>

<div class="fm-adm-stats">
    <div class="fm-adm-stat">
        <span class="fm-adm-stat-n"><?php echo (int) $nbAdmins; ?></span>
        <span class="fm-adm-stat-l">Administrateur<?php echo ($nbAdmins > 1) ? 's' : ''; ?></span>
    </div>
    <div class="fm-adm-stat">
        <span class="fm-adm-stat-n"><?php echo (int) $nbMods; ?></span>
        <span class="fm-adm-stat-l">Moderateur<?php echo ($nbMods > 1) ? 's' : ''; ?></span>
    </div>
</div>

<div class="fm-adm-card">
    <header>
        <h2><?php echo count($membres); ?> compte<?php echo (count($membres) > 1) ? 's' : ''; ?></h2>
        <span class="fm-adm-spacer"></span>
    </header>

    <div class="fm-adm-pad" style="border-bottom:1px solid var(--fm-border);">
        <form class="fm-adm-filter" method="get" action="GestionUtilisateurs.php">
            <div class="fm-adm-field fm-adm-grow">
                <label for="q">Rechercher</label>
                <input class="fm-adm-input" type="search" id="q" name="q"
                       value="<?php echo fm_admin_echapper($motCle); ?>"
                       placeholder="Identifiant ou e-mail">
            </div>
            <div class="fm-adm-field">
                <label for="role">Role</label>
                <select class="fm-adm-select" id="role" name="role">
                    <option value="">Tous</option>
                    <?php foreach (fm_admin_roles() as $cle => $def) { ?>
                    <option value="<?php echo fm_admin_echapper($cle); ?>" <?php echo ($filtreRole === $cle) ? 'selected' : ''; ?>>
                        <?php echo fm_admin_echapper($def[0]); ?>
                    </option>
                    <?php } ?>
                </select>
            </div>
            <button class="fm-adm-btn" type="submit"><i class="fa fa-search" aria-hidden="true"></i> Filtrer</button>
            <?php if ($motCle !== '' || $filtreRole !== '') { ?>
            <a class="fm-adm-btn is-light" href="GestionUtilisateurs.php">Reinitialiser</a>
            <?php } ?>
        </form>
    </div>

    <?php if (!$membres) { ?>
    <div class="fm-adm-empty">
        <i class="fa fa-users" aria-hidden="true"></i>
        Aucun compte ne correspond a ces criteres.
    </div>
    <?php } else { ?>
    <div class="fm-adm-table-wrap">
        <table class="fm-adm-table">
            <thead>
                <tr>
                    <th>Identifiant</th>
                    <th>Role</th>
                    <th>Contact</th>
                    <th style="width:130px;">Compte cree</th>
                    <th class="fm-adm-actions" style="width:200px;">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($membres as $m) { ?>
            <?php $estMoi = gu_est_soi_meme($m['id']); ?>
                <tr<?php echo $estMoi ? ' style="background:#f4f9ee;"' : ''; ?>>
                    <td>
                        <strong><?php echo fm_admin_echapper($m['username']); ?></strong>
                        <?php if ($estMoi) { ?>
                        <br><span style="font-size:12px;color:var(--fm-primary);font-weight:600;">(vous)</span>
                        <?php } ?>
                    </td>
                    <td>
                        <span class="fm-adm-badge <?php echo $m['role'] === 'admin' ? 'is-ok' : ''; ?>">
                            <?php echo fm_admin_echapper(fm_admin_role_libelle($m['role'])); ?>
                        </span>
                    </td>
                    <td style="font-size:13px;">
                        <?php if (trim((string) $m['email']) !== '') { ?>
                        <a href="mailto:<?php echo fm_admin_echapper($m['email']); ?>"><?php echo fm_admin_echapper($m['email']); ?></a>
                        <?php } else { ?>
                        <span style="color:var(--fm-muted);">sans e-mail</span>
                        <?php } ?>
                        <?php if (trim((string) $m['phone']) !== '') { ?>
                        <br><span style="color:var(--fm-muted);"><?php echo fm_admin_echapper($m['phone']); ?></span>
                        <?php } ?>
                    </td>
                    <td style="font-size:13px;color:var(--fm-muted);"><?php echo fm_admin_echapper($m['date']); ?></td>
                    <td class="fm-adm-actions">
                        <a class="fm-adm-btn is-light is-sm"
                           href="GestionUtilisateurs.php?action=modifier&amp;id=<?php echo (int) $m['id']; ?>"
                           title="Modifier"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                        <a class="fm-adm-btn is-light is-sm"
                           href="GestionUtilisateurs.php?action=mot_de_passe&amp;id=<?php echo (int) $m['id']; ?>"
                           title="Reinitialiser le mot de passe"><i class="fa fa-key" aria-hidden="true"></i></a>
                        <?php if ($estMoi) { ?>
                        <button class="fm-adm-btn is-light is-sm" type="button" disabled title="Vous ne pouvez pas supprimer votre propre compte">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </button>
                        <?php } else { ?>
                        <form method="post" action="GestionUtilisateurs.php" style="display:inline;"
                              onsubmit="return confirm('Supprimer definitivement ce compte ?');">
                            <?php echo fm_admin_champ_csrf(); ?>
                            <input type="hidden" name="operation" value="supprimer">
                            <input type="hidden" name="id" value="<?php echo (int) $m['id']; ?>">
                            <button class="fm-adm-btn is-terra is-sm" type="submit" title="Supprimer">
                                <i class="fa fa-trash" aria-hidden="true"></i>
                            </button>
                        </form>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <?php } ?>
</div>

<?php
admin_pied();