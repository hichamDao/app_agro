<?php
/**
 * Gestion de la galerie : les paires de photos affichees dans gallery.php.
 *
 * La table gallery ne contient que id_Gal, Photo et Photo2. Les deux colonnes
 * stockent un chemin RELATIF a la racine du site, ce qu'attend gallery.php
 * (fm_gal_local teste __DIR__ . '/' . chemin) :
 *
 *   Photo  -> images/photos/xxx.jpg     (vignette)
 *   Photo2 -> images/fullscreen/xxx.jpg (version plein ecran)
 *
 * Reecriture complete. L'ancien gestionnaire etait inutilisable :
 *  - addPhoto.php inserait une colonne « Titre » qui n'existe plus dans la
 *    table : l'INSERT echouait sur « Unknown column » ;
 *  - il ecrivait dans admin/images/... ( repertoire courant), hors du site
 *    public ;
 *  - aucune identification, requetes concatenees, aucune validation.
 */

require_once(__DIR__ . '/../includes/admin-auth.php');
require_once(__DIR__ . '/../includes/admin-layout.php');

fm_admin_exiger_droit('galerie');

define('FM_GAL_PAR_PAGE', 24);
define('FM_GAL_PAR_LIGNE', 6);

$extensionsOk = array('jpg', 'jpeg', 'png', 'webp', 'gif');

/* ------------------------------------------------------------ utilitaires */

/** Dossier de destination, resolu depuis la racine du site. */
function gal_dossier($sousDossier) {
    return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . $sousDossier;
}

/** Chemin relatif stocke en base, normalise en barres obliques. */
function gal_nettoyer_chemin($chemin) {
    return ltrim(trim(str_replace('\\', '/', (string) $chemin)), '/');
}

/** URL d'affichage, avec repli sur la vignette generique. */
function gal_url($chemin, $fm_app) {
    $c = gal_nettoyer_chemin($chemin);
    if ($c === '') {
        return '';
    }
    if (!is_file(dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $c))) {
        return $fm_app . 'img/product-placeholder.svg';
    }
    return $fm_app . $c;
}

/** Le fichier existe-t-il vraiment sur le disque ? */
function gal_fichier_present($chemin) {
    $c = gal_nettoyer_chemin($chemin);
    if ($c === '') {
        return false;
    }
    /* Refuse toute sortie du dossier images/. */
    if (strpos($c, '..') !== false) {
        return false;
    }
    return is_file(dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $c));
}

/**
 * Depose un fichier envoye et renvoie son chemin relatif, ou null.
 */
function gal_enregistrer($fichier, $sousDossier, $prefixe, $extensionsOk) {
    if (!isset($fichier['name']) || $fichier['name'] === '') {
        return null;
    }
    $erreur = isset($fichier['error']) ? (int) $fichier['error'] : UPLOAD_ERR_NO_FILE;
    if ($erreur !== UPLOAD_ERR_OK) {
        if ($erreur === UPLOAD_ERR_INI_SIZE || $erreur === UPLOAD_ERR_FORM_SIZE) {
            fm_admin_flash('err', 'Fichier trop volumineux pour la taille maximale autorisee par le serveur.');
        }
        return null;
    }

    $dossier = gal_dossier($sousDossier);
    if (!is_dir($dossier) && !@mkdir($dossier, 0755, true)) {
        fm_admin_flash('err', 'Dossier images/' . $sousDossier . '/ impossible a creer.');
        return null;
    }

    /* Le nom d'origine est jete : un nom de fichier envoye par un navigateur
       peut contenir une traversee de repertoire. */
    $ext = strtolower(pathinfo((string) $fichier['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $extensionsOk, true)) {
        fm_admin_flash('err', 'Format refuse (' . fm_admin_echapper($fichier['name']) . '). Attendu : ' . implode(', ', $extensionsOk) . '.');
        return null;
    }
    if ((int) $fichier['size'] > 12 * 1024 * 1024) {
        fm_admin_flash('err', 'Fichier de plus de 12 Mo refuse.');
        return null;
    }
    if (!is_uploaded_file($fichier['tmp_name'])) {
        return null;
    }

    $nom = $prefixe . '-' . date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
    if (!move_uploaded_file($fichier['tmp_name'], $dossier . DIRECTORY_SEPARATOR . $nom)) {
        fm_admin_flash('err', 'Enregistrement du fichier impossible.');
        return null;
    }
    return 'images/' . $sousDossier . '/' . $nom;
}

/** Supprime un fichier de la base, s'il est confine a images/. */
function gal_supprimer_fichier($chemin) {
    $c = gal_nettoyer_chemin($chemin);
    if ($c === '' || strpos($c, '..') !== false || strpos($c, "\0") !== false) {
        return false;
    }
    $absolu = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $c);
    /* On ne touche qu'aux deux dossiers de la galerie. */
    $attendu = array(
        realpath(gal_dossier('photos')),
        realpath(gal_dossier('fullscreen')),
    );
    $reel = realpath(dirname($absolu));
    if ($reel === false || !in_array($reel, $attendu, true)) {
        return false;
    }
    return is_file($absolu) ? @unlink($absolu) : true;
}

/* --------------------------------------------------------- traitements POST */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    fm_admin_verifier_csrf();
    $operation = isset($_POST['operation']) ? (string) $_POST['operation'] : '';

    /* -------------------------------------------------------------ajout */
    if ($operation === 'ajouter') {
        $vignette = isset($_FILES['photo'])  ? $_FILES['photo']  : null;
        $plein    = isset($_FILES['photo2']) ? $_FILES['photo2'] : null;

        $chemin1 = gal_enregistrer($vignette, 'photos', 'g', $extensionsOk);
        $chemin2 = gal_enregistrer($plein, 'fullscreen', 'f', $extensionsOk);

        if ($chemin1 === null && $chemin2 === null) {
            fm_admin_flash('err', 'Aucune photo recevable : rien n\'a ete enregistre.');
            header('Location: GestionGaleries.php?action=ajouter');
            exit;
        }

        $stmt = mysqli_prepare($conn, 'INSERT INTO gallery (Photo, Photo2) VALUES (?, ?)');
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ss', $chemin1, $chemin2);
            $ok = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            fm_admin_flash($ok ? 'ok' : 'err', $ok ? 'Photo ajoutee a la galerie.' : 'Enregistrement en base impossible.');
        }
        header('Location: GestionGaleries.php');
        exit;
    }

    /* ----------------------------------------------------------remplacement */
    if ($operation === 'remplacer') {
        $id = isset($_POST['id_gal']) ? (int) $_POST['id_gal'] : 0;
        if ($id > 0) {
            $stmt = mysqli_prepare($conn, 'SELECT Photo, Photo2 FROM gallery WHERE id_Gal = ?');
            $ligne = null;
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'i', $id);
                mysqli_stmt_execute($stmt);
                $res = mysqli_stmt_get_result($stmt);
                if ($res) { $ligne = mysqli_fetch_assoc($res); }
                mysqli_stmt_close($stmt);
            }
            if ($ligne) {
                $photos = array('Photo' => $ligne['Photo'], 'Photo2' => $ligne['Photo2']);
                foreach (array('Photo' => 'photos', 'Photo2' => 'fullscreen') as $col => $sousDossier) {
                    if (isset($_FILES[$col]) && $_FILES[$col]['name'] !== '') {
                        $prefixe = ($col === 'Photo') ? 'g' : 'f';
                        $nouveau = gal_enregistrer($_FILES[$col], $sousDossier, $prefixe, $extensionsOk);
                        if ($nouveau !== null) {
                            gal_supprimer_fichier($photos[$col]);
                            $photos[$col] = $nouveau;
                        }
                    }
                }
                $maj = mysqli_prepare($conn, 'UPDATE gallery SET Photo = ?, Photo2 = ? WHERE id_Gal = ?');
                if ($maj) {
                    mysqli_stmt_bind_param($maj, 'ssi', $photos['Photo'], $photos['Photo2'], $id);
                    $ok = mysqli_stmt_execute($maj);
                    mysqli_stmt_close($maj);
                    fm_admin_flash($ok ? 'ok' : 'err', $ok ? 'Photo mise a jour.' : 'Mise a jour impossible.');
                }
            } else {
                fm_admin_flash('err', 'Photo introuvable.');
            }
        }
        header('Location: GestionGaleries.php');
        exit;
    }

    /* --------------------------------------------------------- suppression */
    if ($operation === 'supprimer') {
        $id = isset($_POST['id_gal']) ? (int) $_POST['id_gal'] : 0;
        $garderFichiers = isset($_POST['garder_fichiers']);
        if ($id > 0) {
            $stmt = mysqli_prepare($conn, 'SELECT Photo, Photo2 FROM gallery WHERE id_Gal = ?');
            $ligne = null;
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'i', $id);
                mysqli_stmt_execute($stmt);
                $res = mysqli_stmt_get_result($stmt);
                if ($res) { $ligne = mysqli_fetch_assoc($res); }
                mysqli_stmt_close($stmt);
            }
            if ($ligne) {
                if (!$garderFichiers) {
                    gal_supprimer_fichier($ligne['Photo']);
                    gal_supprimer_fichier($ligne['Photo2']);
                }
                $suppr = mysqli_prepare($conn, 'DELETE FROM gallery WHERE id_Gal = ?');
                if ($suppr) {
                    mysqli_stmt_bind_param($suppr, 'i', $id);
                    $ok = mysqli_stmt_execute($suppr);
                    mysqli_stmt_close($suppr);
                    fm_admin_flash($ok ? 'ok' : 'err', $ok ? 'Photo retiree de la galerie.' : 'Suppression impossible.');
                }
            }
        }
        header('Location: GestionGaleries.php');
        exit;
    }
}

/* ------------------------------------------------------------- vues */

$action = isset($_GET['action']) ? (string) $_GET['action'] : '';

if ($action === 'ajouter') {
    admin_entete(
        'Ajouter une photo',
        'galerie',
        array('<a class="fm-adm-btn is-light is-sm" href="GestionGaleries.php"><i class="fa fa-arrow-left" aria-hidden="true"></i> Retour a la galerie</a>')
    );
    ?>

    <form method="post" enctype="multipart/form-data" action="GestionGaleries.php">
        <?php echo fm_admin_champ_csrf(); ?>
        <input type="hidden" name="operation" value="ajouter">

        <div class="fm-adm-card">
            <header><h2>Nouvelle photo</h2></header>
            <div class="fm-adm-pad">
                <div class="fm-adm-field">
                    <label for="photo">Vignette <span aria-hidden="true">*</span></label>
                    <input class="fm-adm-input" type="file" id="photo" name="photo"
                           accept=".jpg,.jpeg,.png,.webp,.gif" required>
                    <span class="fm-adm-help">
                        Deposee dans images/photos/. C'est l'image affichee dans la grille du site.
                    </span>
                </div>

                <div class="fm-adm-field">
                    <label for="photo2">Version plein ecran</label>
                    <input class="fm-adm-input" type="file" id="photo2" name="photo2"
                           accept=".jpg,.jpeg,.png,.webp,.gif">
                    <span class="fm-adm-help">
                        Facultative. Deposee dans images/fullscreen/. Sert a la visionneuse au clic.
                        Sans elle, la vignette est affichee en grand.
                    </span>
                </div>

                <p style="font-size:13px;color:var(--fm-muted);margin:14px 0 0;">
                    Formats acceptes : <?php echo fm_admin_echapper(implode(', ', $extensionsOk)); ?>, 12 Mo maximum par fichier.
                </p>
            </div>
            <footer>
                <button class="fm-adm-btn" type="submit"><i class="fa fa-upload" aria-hidden="true"></i> Ajouter a la galerie</button>
            </footer>
        </div>
    </form>

    <?php
    admin_pied();
    exit;
}

$remplacerId = isset($_GET['remplacer']) ? (int) $_GET['remplacer'] : 0;
if ($remplacerId > 0) {
    $stmt = mysqli_prepare($conn, 'SELECT id_Gal, Photo, Photo2 FROM gallery WHERE id_Gal = ?');
    $ligne = null;
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $remplacerId);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if ($res) { $ligne = mysqli_fetch_assoc($res); }
        mysqli_stmt_close($stmt);
    }
    if (!$ligne) {
        fm_admin_flash('err', 'Photo introuvable.');
        header('Location: GestionGaleries.php');
        exit;
    }

    admin_entete(
        'Remplacer une photo',
        'galerie',
        array('<a class="fm-adm-btn is-light is-sm" href="GestionGaleries.php"><i class="fa fa-arrow-left" aria-hidden="true"></i> Retour</a>')
    );
    ?>

    <form method="post" enctype="multipart/form-data" action="GestionGaleries.php">
        <?php echo fm_admin_champ_csrf(); ?>
        <input type="hidden" name="operation" value="remplacer">
        <input type="hidden" name="id_gal" value="<?php echo (int) $ligne['id_Gal']; ?>">

        <div class="fm-adm-card">
            <header><h2>Photo n&deg;<?php echo (int) $ligne['id_Gal']; ?></h2></header>
            <div class="fm-adm-pad">
                <div class="fm-adm-grid">
                    <div>
                        <div class="fm-adm-field">
                            <label>Vignette actuelle</label>
                            <?php if (gal_url($ligne['Photo'], $fm_app) !== '') { ?>
                            <img src="<?php echo fm_admin_echapper(gal_url($ligne['Photo'], $fm_app)); ?>"
                                 alt="Vignette actuelle" style="width:100%;height:170px;object-fit:cover;border:1px solid var(--fm-border);border-radius:var(--fm-radius-sm);background:var(--fm-sage);">
                            <?php } else { ?>
                            <div class="fm-adm-empty" style="padding:22px;">Fichier absent du disque</div>
                            <?php } ?>
                        </div>
                        <div class="fm-adm-field">
                            <label for="Photo">Remplacer la vignette</label>
                            <input class="fm-adm-input" type="file" id="Photo" name="Photo" accept=".jpg,.jpeg,.png,.webp,.gif">
                            <span class="fm-adm-help">Le fichier actuel sera supprime.</span>
                        </div>
                    </div>

                    <div>
                        <div class="fm-adm-field">
                            <label>Plein ecran actuel</label>
                            <?php if (gal_url($ligne['Photo2'], $fm_app) !== '') { ?>
                            <img src="<?php echo fm_admin_echapper(gal_url($ligne['Photo2'], $fm_app)); ?>"
                                 alt="Plein ecran actuel" style="width:100%;height:170px;object-fit:cover;border:1px solid var(--fm-border);border-radius:var(--fm-radius-sm);background:var(--fm-sage);">
                            <?php } else { ?>
                            <div class="fm-adm-empty" style="padding:22px;">Aucune version plein ecran</div>
                            <?php } ?>
                        </div>
                        <div class="fm-adm-field">
                            <label for="Photo2">Remplacer le plein ecran</label>
                            <input class="fm-adm-input" type="file" id="Photo2" name="Photo2" accept=".jpg,.jpeg,.png,.webp,.gif">
                            <span class="fm-adm-help">Le fichier actuel sera supprime.</span>
                        </div>
                    </div>
                </div>
            </div>
            <footer>
                <button class="fm-adm-btn" type="submit"><i class="fa fa-save" aria-hidden="true"></i> Enregistrer</button>
            </footer>
        </div>
    </form>

    <?php
    admin_pied();
    exit;
}

/* ----------------------------------------------------------------- liste */

$page   = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$offset = ($page - 1) * FM_GAL_PAR_PAGE;

/* Lignes dont aucun des deux fichiers n'est sur le disque : elles n'apparaissent
   pas sur le site, autant les signaler plutot que de les laisser croire
   actives. Le compteur de gallery.php les ignore deja. */
$rsAll = mysqli_query($conn, 'SELECT Photo, Photo2 FROM gallery');
$orphelines = array();
$totalAffiche = 0;
if ($rsAll) {
    while ($g = mysqli_fetch_assoc($rsAll)) {
        $a = gal_fichier_present($g['Photo']);
        $b = gal_fichier_present($g['Photo2']);
        if ($a || $b) {
            $totalAffiche++;
        } else {
            $orphelines[] = $g;
        }
    }
    mysqli_free_result($rsAll);
}
$nbOrphelines = count($orphelines);

$nombrePages = max(1, (int) ceil($totalAffiche / FM_GAL_PAR_PAGE));
if ($page > $nombrePages) { $page = $nombrePages; }

$photos = array();
$stmt = mysqli_prepare($conn, 'SELECT id_Gal, Photo, Photo2 FROM gallery ORDER BY id_Gal DESC LIMIT ? OFFSET ?');
if ($stmt) {
    $limit = FM_GAL_PAR_PAGE;
    $off = $offset;
    mysqli_stmt_bind_param($stmt, 'ii', $limit, $off);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res) {
        while ($g = mysqli_fetch_assoc($res)) { $photos[] = $g; }
    }
    mysqli_stmt_close($stmt);
}

admin_entete(
    'Galerie',
    'galerie',
    array('<a class="fm-adm-btn is-sm" href="GestionGaleries.php?action=ajouter"><i class="fa fa-plus" aria-hidden="true"></i> Ajouter une photo</a>')
);
?>

<?php if ($nbOrphelines > 0) { ?>
<div class="fm-adm-alert">
    <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
    <span>
        <b><?php echo (int) $nbOrphelines; ?></b> ligne(s) de la base ne pointent vers aucun fichier
        present sur le disque : le site les ignore et le compteur
        <?php echo (int) $totalAffiche; ?> les exclut. Vous pouvez les supprimer pour nettoyer la base.
    </span>
</div>
<?php } ?>

<div class="fm-adm-card">
    <header>
        <h2><?php echo (int) $totalAffiche; ?> photo<?php echo ((int) $totalAffiche > 1) ? 's' : ''; ?> affichee<?php echo ((int) $totalAffiche > 1) ? 's' : ''; ?> sur le site</h2>
        <span class="fm-adm-spacer"></span>
        <span style="font-size:13px;color:var(--fm-muted);">
            page <?php echo (int) $page; ?> sur <?php echo (int) $nombrePages; ?>
        </span>
    </header>

    <div class="fm-adm-pad">
        <?php if (!$photos) { ?>
        <div class="fm-adm-empty">
            <i class="fa fa-picture-o" aria-hidden="true"></i>
            Aucune photo sur cette page.
        </div>
        <?php } else { ?>
        <div class="fm-gal-grille">
            <?php foreach ($photos as $g) { ?>
            <?php
            $url1 = gal_url($g['Photo'], $fm_app);
            $url2 = gal_url($g['Photo2'], $fm_app);
            $a1 = gal_fichier_present($g['Photo']);
            $a2 = gal_fichier_present($g['Photo2']);
            $couverture = $url1 !== '' ? $url1 : ($url2 !== '' ? $url2 : $fm_app . 'img/product-placeholder.svg');
            ?>
            <div class="fm-gal-case">
                <div class="fm-gal-apercu">
                    <img src="<?php echo fm_admin_echapper($couverture); ?>"
                         alt="Photo n&deg;<?php echo (int) $g['id_Gal']; ?>" loading="lazy">
                    <?php if (!$a1 && !$a2) { ?>
                    <span class="fm-gal-orpheline">fichiers absents</span>
                    <?php } ?>
                    <span class="fm-gal-num">#<?php echo (int) $g['id_Gal']; ?></span>
                </div>

                <div class="fm-gal-etats">
                    <span class="fm-adm-badge <?php echo $a1 ? 'is-ok' : 'is-off'; ?>">vignette</span>
                    <span class="fm-adm-badge <?php echo $a2 ? 'is-ok' : 'is-off'; ?>">plein ecran</span>
                </div>

                <div class="fm-gal-actions">
                    <a class="fm-adm-btn is-light is-sm" href="GestionGaleries.php?remplacer=<?php echo (int) $g['id_Gal']; ?>">
                        <i class="fa fa-pencil" aria-hidden="true"></i> Remplacer
                    </a>
                    <form method="post" action="GestionGaleries.php"
                          onsubmit="return confirm('Retirer cette photo de la galerie ?');">
                        <?php echo fm_admin_champ_csrf(); ?>
                        <input type="hidden" name="operation" value="supprimer">
                        <input type="hidden" name="id_gal" value="<?php echo (int) $g['id_Gal']; ?>">
                        <label class="fm-adm-check" style="font-size:12px;margin:6px 0 8px;">
                            <input type="checkbox" name="garder_fichiers" value="1">
                            <span>Conserver les fichiers</span>
                        </label>
                        <button class="fm-adm-btn is-terra is-sm" type="submit">
                            <i class="fa fa-trash" aria-hidden="true"></i> Retirer
                        </button>
                    </form>
                </div>
            </div>
            <?php } ?>
        </div>
        <?php } ?>
    </div>

    <?php if ($nombrePages > 1) { ?>
    <footer>
        <div class="fm-adm-pager">
            <?php
            $lien = function ($p) { return 'GestionGaleries.php?page=' . (int) $p; };
            ?>
            <?php if ($page > 1) { ?>
            <a href="<?php echo $lien($page - 1); ?>"><i class="fa fa-chevron-left" aria-hidden="true"></i></a>
            <?php } else { ?>
            <span class="is-off"><i class="fa fa-chevron-left" aria-hidden="true"></i></span>
            <?php } ?>

            <?php for ($p = 1; $p <= $nombrePages; $p++) { ?>
                <?php if ($p === $page) { ?>
                <span class="is-on"><?php echo $p; ?></span>
                <?php } else { ?>
                <a href="<?php echo $lien($p); ?>"><?php echo $p; ?></a>
                <?php } ?>
            <?php } ?>

            <?php if ($page < $nombrePages) { ?>
            <a href="<?php echo $lien($page + 1); ?>"><i class="fa fa-chevron-right" aria-hidden="true"></i></a>
            <?php } else { ?>
            <span class="is-off"><i class="fa fa-chevron-right" aria-hidden="true"></i></span>
            <?php } ?>
        </div>
    </footer>
    <?php } ?>
</div>

<?php
admin_pied();