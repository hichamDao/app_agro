<?php
/**
 * Ancien point d'entree de l'ajout de photos. Conserve en redirection.
 *
 * Ce script inserait une colonne « Titre » qui n'existe plus dans la table
 * gallery (id_Gal, Photo, Photo2), ecrivait dans admin/images/... plutot que
 * dans images/ du site public, concateneait les requetes et n'exigeait aucune
 * identification. L'echec etait donc garanti et le script exposait une
 * injection SQL ; voir l'en-tete de GestionGaleries.php.
 *
 * L'ajout de photos est desormais traite par GestionGaleries.php, qui
 * verifie le role, le jeton CSRF, la taille (12 Mo), l'extension et confine
 * la suppression aux dossiers images/photos et images/fullscreen. On redirige
 * donc ici pour que les anciennes adresses continuent de mener a la bonne page.
 */

require_once(__DIR__ . '/../includes/admin-auth.php');

fm_admin_exiger_login();
fm_admin_exiger_droit('galerie');
fm_admin_flash('ok', 'L’ajout de photos se fait desormais depuis cette page.');

header('Location: GestionGaleries.php');
exit;
