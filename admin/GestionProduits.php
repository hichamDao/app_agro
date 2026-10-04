<?php
/**
 * Gestion des produits : liste filtrable, ajout, modification, suppression.
 *
 * Reecriture complete. La version precedente :
 *  - ne verifiait pas l'identification (le formulaire de connexion etait
 *    affiche dans la page, la verification elle-meme etait dans
 *    authentifier.php qui n'existe pas) ;
 *  - concaténait $id et $motCle dans le SQL ;
 *  - pointait vers bootstrap-3.3.6-dist, absent du projet (feuille en 404) ;
 *  - includait includes/categories.php, qui appelait mysql_query(), fonction
 *    supprimee en PHP 7 : la pagelevait une erreur fatale.
 *
 * Les photos sont deposees dans images/{Ref_prod}/ a la racine du site, seul
 * emplacement ou products/products.php sait les relire (fm_photo_produit).
 */

require_once(__DIR__ . '/../includes/admin-auth.php');
require_once(__DIR__ . '/../includes/admin-layout.php');

fm_admin_exiger_login();

/* Dossier des photos, resolu depuis la racine du site et non depuis le
   repertoire courant, qui est admin/ pendant l'execution. */
define('FM_PHOTOS_DIR', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'images');
define('FM_PHOTOS_URL', $fm_app . 'images/');
define('FM_PAR_PAGE', 15);

$extensionsOk = array('jpg', 'jpeg', 'png', 'webp', 'gif');

/* ------------------------------------------------------------ utilitaires */

/** Liste des categories, pour le select et le filtre. */
function gp_categories($conn) {
    $r = mysqli_query($conn, 'SELECT Code_cat, Nom_cat FROM categories ORDER BY Nom_cat ASC');
    $out = array();
    if ($r) {
        while ($c = mysqli_fetch_assoc($r)) { $out[] = $c; }
    }
    return $out;
}

function gp_photos_existantes($ref) {
    return array_filter(array_map('trim', explode(',', (string) $ref)), 'strlen');
}

/**
 * Premiere photo lisible d'un produit, comme le fait le site public, avec
 * repli sur la vignette generique.
 */
function gp_vignette($fm_app, $ref_prod, $photo) {
    foreach (gp_photos_existantes($photo) as $nom) {
        if (is_file(FM_PHOTOS_DIR . DIRECTORY_SEPARATOR . $ref_prod . DIRECTORY_SEPARATOR . $nom)) {
            return FM_PHOTOS_URL . rawurlencode($ref_prod) . '/' . rawurlencode($nom);
        }
    }
    return $fm_app . 'img/product-placeholder.svg';
}

/** Page courante, bornee a un entier positif. */
function gp_page() {
    $p = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    return $p > 0 ? $p : 1;
}

/** Enregistre les photos envoyees et renvoie la liste complete (noms). */
function gp_enregistrer_photos($ref_prod, $nomsExistants, $fichiers, $extensionsOk) {
    $noms = $nomsExistants;
    if (!isset($fichiers['name']) || !is_array($fichiers['name'])) {
        return $noms;
    }

    $dossier = FM_PHOTOS_DIR . DIRECTORY_SEPARATOR . $ref_prod;
    if (!is_dir($dossier) && !@mkdir($dossier, 0755, true)) {
        fm_admin_flash('err', 'Dossier de photos impossible a creer : ' . $ref_prod);
        return $noms;
    }
    /* Le dossier peut deja exister avec des droits trop restrictifs (import
       d'un ancien serveur, copie FTP) : on les renormalise a chaque envoi,
       sinon le navigateur recoit une erreur de permission sur les photos. */
    @chmod($dossier, 0755);

    $total = count($fichiers['name']);
    for ($i = 0; $i < $total; $i++) {
        $erreur = isset($fichiers['error'][$i]) ? (int) $fichiers['error'][$i] : UPLOAD_ERR_NO_FILE;
        if ($erreur === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        if ($erreur !== UPLOAD_ERR_OK) {
            fm_admin_flash('err', 'Envoi de photo refuse par le serveur (code ' . $erreur . ').');
            continue;
        }

        $nom      = (string) $fichiers['name'][$i];
        $tmp      = (string) $fichiers['tmp_name'][$i];
        $taille   = isset($fichiers['size'][$i]) ? (int) $fichiers['size'][$i] : 0;

        /* Le nom d'origine n'est pas utilise : on recompose un nom sur, a
           partir de la reference, ce qui evite les traversees de repertoire
           et les collisions. */
        $ext = strtolower(pathinfo($nom, PATHINFO_EXTENSION));
        if (!in_array($ext, $extensionsOk, true)) {
            fm_admin_flash('err', 'Format refuse pour "' . $nom . '" (autorise : ' . implode(', ', $extensionsOk) . ').');
            continue;
        }
        if ($taille > 5 * 1024 * 1024) {
            fm_admin_flash('err', '"' . $nom . '" depasse la limite de 5 Mo.');
            continue;
        }
        if (!is_uploaded_file($tmp)) {
            fm_admin_flash('err', 'Fichier temporaire invalide pour "' . $nom . '".');
            continue;
        }

        $base = preg_replace('/[^A-Za-z0-9]+/', '-', $ref_prod);
        $nouveau = $base . '-' . date('YmdHis') . '-' . $i . '.' . $ext;
        $cible = $dossier . DIRECTORY_SEPARATOR . $nouveau;

        if (move_uploaded_file($tmp, $cible)) {
            /* move_uploaded_file() laisse les droits par defaut du serveur
               (souvent 600 sur certains hebergeurs) : le fichier existe mais le
               navigateur ne peut pas le lire, ce qui affiche des images
               cassee. On impose 755, comme pour le reste du site. */
            @chmod($cible, 0755);
            $noms[] = $nouveau;
        } else {
            fm_admin_flash('err', 'Enregistrement impossible pour "' . $nom . '".');
        }
    }
    return $noms;
}

/* --------------------------------------------------------- traitements POST */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    fm_admin_verifier_csrf();
    $operation = isset($_POST['operation']) ? (string) $_POST['operation'] : '';

    /* --------------------------------------------------------- suppression */
    if ($operation === 'supprimer') {
        $id = isset($_POST['id_prod']) ? (int) $_POST['id_prod'] : 0;
        if ($id > 0) {
            $stmt = mysqli_prepare($conn, 'DELETE FROM produits WHERE id_prod = ?');
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'i', $id);
                mysqli_stmt_execute($stmt);
                $ok = mysqli_stmt_affected_rows($stmt) > 0;
                mysqli_stmt_close($stmt);
                fm_admin_flash($ok ? 'ok' : 'err', $ok ? 'Produit supprime.' : 'Produit introuvable.');
            }
        }
        header('Location: GestionProduits.php');
        exit;
    }

    /* ------------------------------------------------- photos d'un produit */
    if ($operation === 'photos') {
        $id = isset($_POST['id_prod']) ? (int) $_POST['id_prod'] : 0;
        if ($id > 0) {
            $stmt = mysqli_prepare($conn, 'SELECT Ref_prod, Photo FROM produits WHERE id_prod = ?');
            $prod = null;
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'i', $id);
                mysqli_stmt_execute($stmt);
                $res = mysqli_stmt_get_result($stmt);
                if ($res) { $prod = mysqli_fetch_assoc($res); }
                mysqli_stmt_close($stmt);
            }
            if ($prod) {
                $noms = gp_photos_existantes($prod['Photo']);

                /* Retrait d'une photo cochee */
                if (!empty($_POST['supprimer_photo']) && is_array($_POST['supprimer_photo'])) {
                    foreach ($_POST['supprimer_photo'] as $aSupprimer) {
                        $aSupprimer = (string) $aSupprimer;
                        if ($aSupprimer === '' || strpos($aSupprimer, '..') !== false || strpos($aSupprimer, '/') !== false) {
                            continue;
                        }
                        $fichier = FM_PHOTOS_DIR . DIRECTORY_SEPARATOR . $prod['Ref_prod'] . DIRECTORY_SEPARATOR . $aSupprimer;
                        if (is_file($fichier)) { @unlink($fichier); }
                        $noms = array_values(array_diff($noms, array($aSupprimer)));
                    }
                }

                $noms = gp_enregistrer_photos($prod['Ref_prod'], $noms, isset($_FILES['photo']) ? $_FILES['photo'] : null, $extensionsOk);

                $liste = implode(',', $noms);
                $maj = mysqli_prepare($conn, 'UPDATE produits SET Photo = ? WHERE id_prod = ?');
                if ($maj) {
                    mysqli_stmt_bind_param($maj, 'si', $liste, $id);
                    mysqli_stmt_execute($maj);
                    mysqli_stmt_close($maj);
                }
                fm_admin_flash('ok', 'Photos mises a jour.');
            } else {
                fm_admin_flash('err', 'Produit introuvable.');
            }
        }
        header('Location: GestionProduits.php?action=photos&id=' . $id);
        exit;
    }

    /* ------------------------------------------------------------- creation */
    if ($operation === 'enregistrer') {
        $id      = isset($_POST['id_prod']) ? (int) $_POST['id_prod'] : 0;
        $ref     = trim((string) (isset($_POST['ref_prod']) ? $_POST['ref_prod'] : ''));
        $design  = trim((string) (isset($_POST['designation']) ? $_POST['designation'] : ''));
        $desc    = trim((string) (isset($_POST['description']) ? $_POST['description'] : ''));
        $codeCat = (int) (isset($_POST['code_cat']) ? $_POST['code_cat'] : 0);
        $quantite= (int) (isset($_POST['quantite']) ? $_POST['quantite'] : 0);
        $prix    = (float) (isset($_POST['prix']) ? $_POST['prix'] : 0);
        $tags    = trim((string) (isset($_POST['tags']) ? $_POST['tags'] : ''));
        $composant = trim((string) (isset($_POST['composant']) ? $_POST['composant'] : ''));
        $percent = trim((string) (isset($_POST['percent']) ? $_POST['percent'] : ''));
        $dispo   = isset($_POST['disponible']) ? 1 : 0;
        $promo   = isset($_POST['promotion']) ? 1 : 0;
        $sel     = isset($_POST['selectionne']) ? 1 : 0;

        $erreurs = array();
        if ($design === '') { $erreurs[] = 'La designation est obligatoire.'; }
        if ($ref === '') {
            $erreurs[] = 'La reference est obligatoire.';
        } elseif (!preg_match('/^[A-Za-z0-9._-]{2,20}$/', $ref)) {
            /* La reference nomme le dossier de photos : on la limite a ce qui
               peut servir de nom de dossier sans risque. */
            $erreurs[] = 'Reference invalide (2 a 20 caracteres : lettres, chiffres, point, tiret, souligne).';
        }
        if ($codeCat <= 0) { $erreurs[] = 'Choisissez une categorie.'; }

        if ($erreurs) {
            foreach ($erreurs as $e) { fm_admin_flash('err', $e); }
            header('Location: GestionProduits.php?action=' . ($id > 0 ? 'modifier&id=' . $id : 'nouveau'));
            exit;
        }

        /* Photos existantes en modification, nouvelles en creation. */
        $photos = array();
        if ($id > 0) {
            $sel_photos = mysqli_prepare($conn, 'SELECT Photo FROM produits WHERE id_prod = ?');
            if ($sel_photos) {
                mysqli_stmt_bind_param($sel_photos, 'i', $id);
                mysqli_stmt_execute($sel_photos);
                $res = mysqli_stmt_get_result($sel_photos);
                if ($res) {
                    $ligne = mysqli_fetch_assoc($res);
                    if ($ligne) { $photos = gp_photos_existantes($ligne['Photo']); }
                }
                mysqli_stmt_close($sel_photos);
            }
        }
        $photos = gp_enregistrer_photos($ref, $photos, isset($_FILES['photo']) ? $_FILES['photo'] : null, $extensionsOk);
        $listePhotos = implode(',', $photos);

        if ($id > 0) {
            $sql = 'UPDATE produits SET Ref_prod = ?, Designation = ?, description = ?, Quantite = ?,
                    Prix = ?, Photo = ?, Disponible = ?, Promotion = ?, Selectionne = ?, Code_cat = ?,
                    tags = ?, composant = ?, percent = ?
                    WHERE id_prod = ?';
            $stmt = mysqli_prepare($conn, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'sssiissiiiiisssi',
                    $ref, $design, $desc, $quantite, $prix, $listePhotos,
                    $dispo, $promo, $sel, $codeCat, $tags, $composant, $percent, $id);
                $ok = mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                fm_admin_flash($ok ? 'ok' : 'err', $ok ? 'Produit modifie.' : 'Echec de la modification.');
            }
            header('Location: GestionProduits.php?action=modifier&id=' . $id);
            exit;
        }

        $sql = 'INSERT INTO produits (Ref_prod, Designation, description, Quantite, Prix, Photo,
                Disponible, Promotion, Selectionne, Code_cat, tags, composant, percent)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'sssiissiiiiiss',
                $ref, $design, $desc, $quantite, $prix, $listePhotos,
                $dispo, $promo, $sel, $codeCat, $tags, $composant, $percent);
            $ok = mysqli_stmt_execute($stmt);
            $nouveauId = $ok ? mysqli_insert_id($conn) : 0;
            mysqli_stmt_close($stmt);
            fm_admin_flash($ok ? 'ok' : 'err', $ok ? 'Produit ajoute.' : 'Echec de l\'ajout.');
            header('Location: GestionProduits.php' . ($nouveauId ? '?action=modifier&id=' . $nouveauId : ''));
            exit;
        }
        fm_admin_flash('err', 'Requete preparee impossible.');
        header('Location: GestionProduits.php?action=nouveau');
        exit;
    }
}

/* --------------------------------------------------------------- lecture */

$action = isset($_GET['action']) ? (string) $_GET['action'] : '';
$idEdit = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$categories = gp_categories($conn);

/* -------------------------------------------------------- vues formulaire */

if ($action === 'nouveau' || ($action === 'modifier' && $idEdit > 0)) {
    $prod = array('Ref_prod' => '', 'Designation' => '', 'description' => '',
                  'Quantite' => 0, 'Prix' => 0, 'Photo' => '', 'Disponible' => 1,
                  'Promotion' => 0, 'Selectionne' => 0, 'Code_cat' => 0,
                  'tags' => '', 'composant' => '', 'percent' => '');
    $modification = ($idEdit > 0);

    if ($modification) {
        $stmt = mysqli_prepare($conn, 'SELECT * FROM produits WHERE id_prod = ?');
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'i', $idEdit);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            $trouve = $res ? mysqli_fetch_assoc($res) : null;
            mysqli_stmt_close($stmt);
            if ($trouve) {
                $prod = $trouve;
            } else {
                fm_admin_flash('err', 'Produit introuvable.');
                header('Location: GestionProduits.php');
                exit;
            }
        }
    }

    admin_entete(
        $modification ? 'Modifier un produit' : 'Ajouter un produit',
        'produits',
        array('<a class="fm-adm-btn is-light is-sm" href="GestionProduits.php"><i class="fa fa-arrow-left" aria-hidden="true"></i> Retour a la liste</a>')
    );
    ?>

    <form method="post" enctype="multipart/form-data" action="GestionProduits.php">
        <?php echo fm_admin_champ_csrf(); ?>
        <input type="hidden" name="operation" value="enregistrer">
        <?php if ($modification) { ?>
        <input type="hidden" name="id_prod" value="<?php echo (int) $prod['id_prod']; ?>">
        <?php } ?>

        <div class="fm-adm-card">
            <header><h2>Informations</h2></header>
            <div class="fm-adm-pad">
                <div class="fm-adm-grid">
                    <div class="fm-adm-field">
                        <label for="ref_prod">Reference <span aria-hidden="true">*</span></label>
                        <input class="fm-adm-input" type="text" id="ref_prod" name="ref_prod"
                               maxlength="20" required
                               value="<?php echo fm_admin_echapper($prod['Ref_prod']); ?>">
                        <span class="fm-adm-help">Nom du dossier de photos : images/<?php echo fm_admin_echapper($prod['Ref_prod'] !== '' ? $prod['Ref_prod'] : 'REF'); ?>/</span>
                    </div>

                    <div class="fm-adm-field">
                        <label for="designation">Designation <span aria-hidden="true">*</span></label>
                        <input class="fm-adm-input" type="text" id="designation" name="designation"
                               maxlength="50" required
                               value="<?php echo fm_admin_echapper($prod['Designation']); ?>">
                        <span class="fm-adm-help">C'est le titre affiche, et le seul champ recherche par le catalogue.</span>
                    </div>
                </div>

                <div class="fm-adm-field">
                    <label for="description">Description</label>
                    <textarea class="fm-adm-textarea" id="description" name="description"><?php echo fm_admin_echapper($prod['description']); ?></textarea>
                    <span class="fm-adm-help">Texte long de la fiche produit. N'est pas utilise pour la recherche.</span>
                </div>

                <div class="fm-adm-grid">
                    <div class="fm-adm-field">
                        <label for="code_cat">Categorie <span aria-hidden="true">*</span></label>
                        <select class="fm-adm-select" id="code_cat" name="code_cat" required>
                            <option value="0">&mdash; Choisir &mdash;</option>
                            <?php foreach ($categories as $c) { ?>
                            <option value="<?php echo (int) $c['Code_cat']; ?>"
                                <?php echo ((int) $prod['Code_cat'] === (int) $c['Code_cat']) ? 'selected' : ''; ?>>
                                <?php echo fm_admin_echapper($c['Nom_cat']); ?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="fm-adm-field">
                        <label for="quantite">Quantite</label>
                        <input class="fm-adm-input" type="number" id="quantite" name="quantite" min="0" step="1"
                               value="<?php echo (int) $prod['Quantite']; ?>">
                    </div>

                    <div class="fm-adm-field">
                        <label for="prix">Prix</label>
                        <input class="fm-adm-input" type="number" id="prix" name="prix" min="0" step="0.01"
                               value="<?php echo (float) $prod['Prix']; ?>">
                    </div>
                </div>

                <div class="fm-adm-grid">
                    <div class="fm-adm-field">
                        <label for="tags">Tags</label>
                        <input class="fm-adm-input" type="text" id="tags" name="tags"
                               value="<?php echo fm_admin_echapper($prod['tags']); ?>">
                        <span class="fm-adm-help">Separes par des virgules.</span>
                    </div>

                    <div class="fm-adm-field">
                        <label for="composant">Composants</label>
                        <input class="fm-adm-input" type="text" id="composant" name="composant"
                               value="<?php echo fm_admin_echapper($prod['composant']); ?>">
                    </div>

                    <div class="fm-adm-field">
                        <label for="percent">Percent</label>
                        <input class="fm-adm-input" type="text" id="percent" name="percent"
                               value="<?php echo fm_admin_echapper($prod['percent']); ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="fm-adm-card">
            <header><h2>Photos</h2></header>
            <div class="fm-adm-pad">
                <?php $existantes = gp_photos_existantes($prod['Photo']); ?>
                <?php if ($existantes) { ?>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:12px;margin-bottom:16px;">
                    <?php foreach ($existantes as $nom) { ?>
                    <div style="text-align:center;">
                        <img src="<?php echo FM_PHOTOS_URL . rawurlencode($prod['Ref_prod']) . '/' . rawurlencode($nom); ?>"
                             alt="<?php echo fm_admin_echapper($nom); ?>"
                             style="width:100%;height:104px;object-fit:cover;border:1px solid var(--fm-border);border-radius:var(--fm-radius-sm);background:var(--fm-sage);">
                        <label class="fm-adm-check" style="margin-top:6px;justify-content:center;font-size:12.5px;">
                            <input type="checkbox" name="supprimer_photo[]" value="<?php echo fm_admin_echapper($nom); ?>">
                            <span>Supprimer</span>
                        </label>
                    </div>
                    <?php } ?>
                </div>
                <?php } else { ?>
                <p style="color:var(--fm-muted);font-size:13.5px;margin:0 0 14px;">Aucune photo enregistree.</p>
                <?php } ?>

                <div class="fm-adm-field">
                    <label for="photo">Ajouter des photos</label>
                    <input class="fm-adm-input" type="file" id="photo" name="photo[]" multiple
                           accept=".jpg,.jpeg,.png,.webp,.gif">
                    <span class="fm-adm-help">
                        Formats acceptes : <?php echo fm_admin_echapper(implode(', ', $extensionsOk)); ?>, 5 Mo par fichier.
                        Les cases « Supprimer » ne sont prise en compte qu'apres enregistrement.
                    </span>
                </div>
            </div>
        </div>

        <div class="fm-adm-card">
            <header><h2>Visibilite</h2></header>
            <div class="fm-adm-pad">
                <div class="fm-adm-check">
                    <input type="checkbox" id="disponible" name="disponible" value="1" <?php echo ((int) $prod['Disponible'] === 1) ? 'checked' : ''; ?>>
                    <label for="disponible">Disponible</label>
                </div>
                <div class="fm-adm-check">
                    <input type="checkbox" id="promotion" name="promotion" value="1" <?php echo ((int) $prod['Promotion'] === 1) ? 'checked' : ''; ?>>
                    <label for="promotion">En promotion</label>
                </div>
                <div class="fm-adm-check">
                    <input type="checkbox" id="selectionne" name="selectionne" value="1" <?php echo ((int) $prod['Selectionne'] === 1) ? 'checked' : ''; ?>>
                    <label for="selectionne">Mis en avant</label>
                </div>
            </div>
            <footer>
                <button class="fm-adm-btn" type="submit">
                    <i class="fa fa-save" aria-hidden="true"></i>
                    <?php echo $modification ? 'Enregistrer les modifications' : 'Ajouter le produit'; ?>
                </button>
            </footer>
        </div>
    </form>

    <?php
    admin_pied();
    exit;
}

/* --------------------------------------------------------- vue photos seule */

if ($action === 'photos' && $idEdit > 0) {
    $stmt = mysqli_prepare($conn, 'SELECT * FROM produits WHERE id_prod = ?');
    $prod = null;
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $idEdit);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if ($res) { $prod = mysqli_fetch_assoc($res); }
        mysqli_stmt_close($stmt);
    }
    if (!$prod) {
        fm_admin_flash('err', 'Produit introuvable.');
        header('Location: GestionProduits.php');
        exit;
    }
    $existantes = gp_photos_existantes($prod['Photo']);

    admin_entete(
        'Photos - ' . $prod['Designation'],
        'produits',
        array('<a class="fm-adm-btn is-light is-sm" href="GestionProduits.php"><i class="fa fa-arrow-left" aria-hidden="true"></i> Liste</a>')
    );
    ?>

    <form method="post" enctype="multipart/form-data" action="GestionProduits.php">
        <?php echo fm_admin_champ_csrf(); ?>
        <input type="hidden" name="operation" value="photos">
        <input type="hidden" name="id_prod" value="<?php echo (int) $prod['id_prod']; ?>">

        <div class="fm-adm-card">
            <header><h2>Photos de <?php echo fm_admin_echapper($prod['Designation']); ?></h2></header>
            <div class="fm-adm-pad">
                <?php if ($existantes) { ?>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:14px;margin-bottom:18px;">
                    <?php foreach ($existantes as $nom) { ?>
                    <div style="text-align:center;">
                        <img src="<?php echo FM_PHOTOS_URL . rawurlencode($prod['Ref_prod']) . '/' . rawurlencode($nom); ?>"
                             alt="<?php echo fm_admin_echapper($nom); ?>"
                             style="width:100%;height:130px;object-fit:cover;border:1px solid var(--fm-border);border-radius:var(--fm-radius-sm);background:var(--fm-sage);">
                        <label class="fm-adm-check" style="margin-top:6px;justify-content:center;font-size:12.5px;">
                            <input type="checkbox" name="supprimer_photo[]" value="<?php echo fm_admin_echapper($nom); ?>">
                            <span>Supprimer</span>
                        </label>
                    </div>
                    <?php } ?>
                </div>
                <?php } else { ?>
                <div class="fm-adm-empty"><i class="fa fa-picture-o" aria-hidden="true"></i>Aucune photo pour ce produit.</div>
                <?php } ?>

                <div class="fm-adm-field">
                    <label for="photo">Ajouter des photos</label>
                    <input class="fm-adm-input" type="file" id="photo" name="photo[]" multiple
                           accept=".jpg,.jpeg,.png,.webp,.gif">
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

/* ------------------------------------------------------------ liste + filtre */

$motCle = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
$filtreCat = isset($_GET['cat']) ? (int) $_GET['cat'] : 0;
$page = gp_page();
$offset = ($page - 1) * FM_PAR_PAGE;

$where  = array();
$params = array();
$types  = '';

if ($motCle !== '') {
    /* Recherche sur le titre uniquement : meme comportement que le catalogue
       public (products/products.php), qui ne cherche plus dans la description. */
    $where[]  = 'Designation LIKE ?';
    $params[] = '%' . $motCle . '%';
    $types   .= 's';
}
if ($filtreCat > 0) {
    $where[]  = 'Code_cat = ?';
    $params[] = $filtreCat;
    $types   .= 'i';
}
$sqlWhere = $where ? ' WHERE ' . implode(' AND ', $where) : '';

/* Total, pour la pagination. */
$nombreTotal = 0;
$stmt = mysqli_prepare($conn, 'SELECT COUNT(*) FROM produits' . $sqlWhere);
if ($stmt) {
    if ($types !== '') { mysqli_stmt_bind_param($stmt, $types, ...$params); }
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    mysqli_stmt_bind_result($stmt, $nombreTotal);
    mysqli_stmt_fetch($stmt);
    $nombreTotal = (int) $nombreTotal;
    mysqli_stmt_close($stmt);
}

$nombrePages = max(1, (int) ceil($nombreTotal / FM_PAR_PAGE));
if ($page > $nombrePages) { $page = $nombrePages; }
$offset = ($page - 1) * FM_PAR_PAGE;

/* La jointure impose de prefixer la clause WHERE par p. : on reconstruit les
   memes conditions avec le prefixe plutot que de chaîner du SQL. */
$whereJ  = array();
$paramsJ = array();
$typesJ  = '';
if ($motCle !== '') {
    $whereJ[]  = 'p.Designation LIKE ?';
    $paramsJ[] = '%' . $motCle . '%';
    $typesJ   .= 's';
}
if ($filtreCat > 0) {
    $whereJ[]  = 'p.Code_cat = ?';
    $paramsJ[] = $filtreCat;
    $typesJ   .= 'i';
}
$sqlWhereJ = $whereJ ? ' WHERE ' . implode(' AND ', $whereJ) : '';

$stmt = mysqli_prepare(
    $conn,
    'SELECT p.id_prod, p.Ref_prod, p.Designation, p.description, p.Quantite, p.Prix, p.Photo,
            p.Disponible, p.Promotion, p.Selectionne, p.Code_cat, p.tags, c.Nom_cat
     FROM produits p
     LEFT JOIN categories c ON c.Code_cat = p.Code_cat'
        . $sqlWhereJ
        . ' ORDER BY p.Designation ASC LIMIT ? OFFSET ?'
);
if ($stmt) {
    $limit = FM_PAR_PAGE;
    $off = $offset;
    $bindVals = $paramsJ;
    $bindVals[] = $limit;
    $bindVals[] = $off;
    mysqli_stmt_bind_param($stmt, $typesJ . 'ii', ...$bindVals);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res) {
        while ($ligne = mysqli_fetch_assoc($res)) { $produits[] = $ligne; }
    }
    mysqli_stmt_close($stmt);
}

admin_entete(
    'Produits',
    'produits',
    array('<a class="fm-adm-btn is-sm" href="GestionProduits.php?action=nouveau"><i class="fa fa-plus" aria-hidden="true"></i> Ajouter un produit</a>')
);
?>

<div class="fm-adm-card">
    <header>
        <h2><?php echo (int) $nombreTotal; ?> produit<?php echo ((int) $nombreTotal > 1) ? 's' : ''; ?></h2>
        <span class="fm-adm-spacer"></span>
    </header>

    <div class="fm-adm-pad" style="border-bottom:1px solid var(--fm-border);">
        <form class="fm-adm-filter" method="get" action="GestionProduits.php">
            <div class="fm-adm-field fm-adm-grow">
                <label for="q">Rechercher un titre</label>
                <input class="fm-adm-input" type="search" id="q" name="q"
                       value="<?php echo fm_admin_echapper($motCle); ?>"
                       placeholder="Designation du produit">
            </div>
            <div class="fm-adm-field">
                <label for="cat">Categorie</label>
                <select class="fm-adm-select" id="cat" name="cat">
                    <option value="0">Toutes</option>
                    <?php foreach ($categories as $c) { ?>
                    <option value="<?php echo (int) $c['Code_cat']; ?>" <?php echo ($filtreCat === (int) $c['Code_cat']) ? 'selected' : ''; ?>>
                        <?php echo fm_admin_echapper($c['Nom_cat']); ?>
                    </option>
                    <?php } ?>
                </select>
            </div>
            <button class="fm-adm-btn" type="submit"><i class="fa fa-search" aria-hidden="true"></i> Filtrer</button>
            <?php if ($motCle !== '' || $filtreCat > 0) { ?>
            <a class="fm-adm-btn is-light" href="GestionProduits.php">Reinitialiser</a>
            <?php } ?>
        </form>
    </div>

    <?php if (!$produits) { ?>
    <div class="fm-adm-empty">
        <i class="fa fa-leaf" aria-hidden="true"></i>
        Aucun produit ne correspond a ces criteres.
    </div>
    <?php } else { ?>
    <div class="fm-adm-table-wrap">
        <table class="fm-adm-table">
            <thead>
                <tr>
                    <th style="width:62px;">Photo</th>
                    <th>Designation</th>
                    <th>Categorie</th>
                    <th class="fm-adm-num">Quantite</th>
                    <th class="fm-adm-num">Prix</th>
                    <th>Etat</th>
                    <th class="fm-adm-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($produits as $p) { ?>
                <tr>
                    <td>
                        <img class="fm-adm-thumb"
                             src="<?php echo fm_admin_echapper(gp_vignette($fm_app, $p['Ref_prod'], $p['Photo'])); ?>"
                             alt="<?php echo fm_admin_echapper($p['Designation']); ?>">
                    </td>
                    <td>
                        <strong><?php echo fm_admin_echapper($p['Designation']); ?></strong><br>
                        <span style="color:var(--fm-muted);font-size:12.5px;"><?php echo fm_admin_echapper($p['Ref_prod']); ?></span>
                    </td>
                    <td><?php echo $p['Nom_cat'] ? fm_admin_echapper($p['Nom_cat']) : '<span style="color:var(--fm-muted);">(sans)</span>'; ?></td>
                    <td class="fm-adm-num"><?php echo (int) $p['Quantite']; ?></td>
                    <td class="fm-adm-num"><?php echo (float) $p['Prix']; ?></td>
                    <td>
                        <?php if ((int) $p['Disponible'] === 1) { ?>
                        <span class="fm-adm-badge is-ok">Disponible</span>
                        <?php } else { ?>
                        <span class="fm-adm-badge is-off">Indisponible</span>
                        <?php } ?>
                        <?php if ((int) $p['Promotion'] === 1) { ?>
                        <span class="fm-adm-badge is-promo">Promo</span>
                        <?php } ?>
                        <?php if ((int) $p['Selectionne'] === 1) { ?>
                        <span class="fm-adm-badge">En avant</span>
                        <?php } ?>
                    </td>
                    <td class="fm-adm-actions">
                        <a class="fm-adm-btn is-light is-sm" href="GestionProduits.php?action=modifier&amp;id=<?php echo (int) $p['id_prod']; ?>"
                           title="Modifier"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                        <a class="fm-adm-btn is-light is-sm" href="GestionProduits.php?action=photos&amp;id=<?php echo (int) $p['id_prod']; ?>"
                           title="Photos"><i class="fa fa-camera" aria-hidden="true"></i></a>
                        <form method="post" action="GestionProduits.php"
                              onsubmit="return confirm('Supprimer definitivement ce produit ?');"
                              style="display:inline;">
                            <?php echo fm_admin_champ_csrf(); ?>
                            <input type="hidden" name="operation" value="supprimer">
                            <input type="hidden" name="id_prod" value="<?php echo (int) $p['id_prod']; ?>">
                            <button class="fm-adm-btn is-terra is-sm" type="submit" title="Supprimer">
                                <i class="fa fa-trash" aria-hidden="true"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

    <?php if ($nombrePages > 1) { ?>
    <footer>
        <div class="fm-adm-pager">
            <?php
            $lien = function ($p) use ($motCle, $filtreCat) {
                $qs = array('page' => $p);
                if ($motCle !== '') { $qs['q'] = $motCle; }
                if ($filtreCat > 0) { $qs['cat'] = $filtreCat; }
                return 'GestionProduits.php?' . http_build_query($qs);
            };
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
    <?php } ?>
</div>

<?php
admin_pied();