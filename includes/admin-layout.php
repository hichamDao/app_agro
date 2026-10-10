<?php
/**
 * Gabarit commun de l'administration : barre laterale, bandeau, flash.
 *
 * Les pages admin appellent admin_entete() en ouverture et admin_pied() en
 * fermeture ; le reste du gabarit (ouverture du <body>, Fermeture) est ici.
 *
 * $fm_app est calcule dans includes/paths.php ; on l'exige ici parce que les
 * pages admin vivent dans /admin/ et que leurs URL doivent remonter d'un
 * niveau pour les ressources partagees (css, images).
 */

require_once(__DIR__ . '/paths.php');

/**
 * Ouvre la page : <head>, barre laterale de navigation, bandeau de titre.
 *
 * @param string $titre   titre affiche dans le bandeau
 * @param string $actif   cle de la page courante pour la mettre en avant
 * @param array  $actions boutons html a poser a droite du bandeau
 */
function admin_entete($titre, $actif = '', array $actions = array()) {
    /* $fm_app vit dans le scope global ; sans ce global les URL de ressources
       partiraient d'un chemin vide et casseraient logo, feuille de style et
       lien "Voir le site" depuis /admin/. */
    global $fm_app;

    /* Les entrees hors droit pour le role courant sont retirees du menu. */
    $menu = fm_admin_menu_visible();
    ?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?php echo fm_admin_echapper($titre . ' - ' . FM_ADMIN_TITRE); ?></title>
<link rel="stylesheet" href="<?php echo $fm_app; ?>css/font-awesome.min.css<?php echo fm_ver('css/font-awesome.min.css'); ?>">
<link rel="stylesheet" href="admin-style.css">
</head>
<body class="fm-admin">
<div class="fm-adm-wrap">

    <aside class="fm-adm-side">
        <a class="fm-adm-brand" href="index.php">
            <img src="<?php echo $fm_app; ?>images/logo.png" alt="Foodmax">
            <span><strong>Foodmax</strong><span>Administration</span></span>
        </a>

        <nav class="fm-adm-nav">
            <?php foreach ($menu as $cle => $item) { ?>
            <a href="<?php echo $item[2]; ?>"<?php echo ($actif === $cle) ? ' class="is-active"' : ''; ?>>
                <i class="fa <?php echo $item[1]; ?>" aria-hidden="true"></i><?php echo $item[0]; ?>
            </a>
            <?php } ?>
        </nav>

        <div class="fm-adm-side-foot">
            Connecte en tant que<br>
            <b><?php echo fm_admin_echapper(fm_admin_login()); ?></b><br>
            <span class="fm-adm-role"><?php echo fm_admin_echapper(fm_admin_role_libelle()); ?></span><br>
            <a href="<?php echo $fm_app; ?>" target="_blank" rel="noopener">Voir le site</a>
        </div>
    </aside>

    <div class="fm-adm-main">

        <header class="fm-adm-top">
            <h1><?php echo fm_admin_echapper($titre); ?></h1>
            <div class="fm-adm-top-actions">
                <?php foreach ($actions as $a) { echo $a . "\n"; } ?>
                <span class="fm-adm-who">Session : <b><?php echo fm_admin_echapper(fm_admin_login()); ?></b></span>
                <span class="fm-adm-badge <?php echo fm_admin_role() === 'admin' ? '' : 'is-off'; ?>"><?php echo fm_admin_echapper(fm_admin_role_libelle()); ?></span>
                <a class="fm-adm-btn is-light is-sm" href="logout.php">Deconnexion</a>
            </div>
        </header>

        <main class="fm-adm-body">
            <?php
            foreach (fm_admin_lire_flash() as $msg) {
                $cls = ($msg['type'] === 'ok') ? ' is-ok' : ' is-err';
                $ico = ($msg['type'] === 'ok') ? 'fa-check-circle' : 'fa-exclamation-circle';
                echo '<div class="fm-adm-alert' . $cls . '"><i class="fa ' . $ico . '" aria-hidden="true"></i>'
                    . '<span>' . fm_admin_echapper($msg['texte']) . '</span></div>' . "\n";
            }
}

/**
 * Ferme la page ouverte par admin_entete().
 *
 * @param string $scripts balises <script> additionnelles (facultatif)
 */
function admin_pied($scripts = '') {
    if ($scripts !== '') {
        echo "\n" . $scripts . "\n";
    }
    ?>
        </main>
    </div>
</div>
</body>
</html>
<?php
}