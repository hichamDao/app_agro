<?php
/**
 * Gestion des categories : liste avec nombre de produits, ajout, modification,
 * suppression.
 *
 * La page n'existait pas : le menu admin pointait vers GestionCategories.php,
 * lien mort. Le compte de produits est joint en base pour l'afficher
 * immediatement.
 *
 * Une categorie qui porte encore des produits n'est pas supprimable : la
 * supprimer laisserait des produits sans famille, ce que products.php affiche
 * comme « sans categorie » et que la barre laterale publique ne peut plus
 * joindre.
 */

require_once(__DIR__ . '/../includes/admin-auth.php');
require_once(__DIR__ . '/../includes/admin-layout.php');

fm_admin_exiger_login();

/* --------------------------------------------------------- traitements POST */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    fm_admin_verifier_csrf();
    $operation = isset($_POST['operation']) ? (string) $_POST['operation'] : '';

    /* ------------------------------------------------------------ suppression */
    if ($operation === 'supprimer') {
        $code = isset($_POST['code_cat']) ? (int) $_POST['code_cat'] : 0;
        if ($code <= 0) {
            fm_admin_flash('err', 'Categorie invalide.');
            header('Location: GestionCategories.php');
            exit;
        }

        $stmt = mysqli_prepare($conn, 'SELECT COUNT(*) AS n FROM produits WHERE Code_cat = ?');
        $nombre = 0;
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'i', $code);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            mysqli_stmt_bind_result($stmt, $nombre);
            mysqli_stmt_fetch($stmt);
            $nombre = (int) $nombre;
            mysqli_stmt_close($stmt);
        }

        if ($nombre > 0) {
            fm_admin_flash('err', 'Impossible de supprimer : ' . $nombre . ' produit(s) utilisent encore cette categorie. Reaffectez-les d\'abord.');
            header('Location: GestionCategories.php');
            exit;
        }

        $suppr = mysqli_prepare($conn, 'DELETE FROM categories WHERE Code_cat = ?');
        if ($suppr) {
            mysqli_stmt_bind_param($suppr, 'i', $code);
            mysqli_stmt_execute($suppr);
            $ok = mysqli_stmt_affected_rows($suppr) > 0;
            mysqli_stmt_close($suppr);
            fm_admin_flash($ok ? 'ok' : 'err', $ok ? 'Categorie supprimee.' : 'Categorie introuvable.');
        }
        header('Location: GestionCategories.php');
        exit;
    }

    /* -------------------------------------------------- creation / edition */
    if ($operation === 'enregistrer') {
        $code   = isset($_POST['code_cat']) ? (int) $_POST['code_cat'] : 0;
        $nom    = trim((string) (isset($_POST['nom_cat']) ? $_POST['nom_cat'] : ''));
        $desc   = trim((string) (isset($_POST['description']) ? $_POST['description'] : ''));
        $modification = ($code > 0);

        $erreurs = array();
        if ($nom === '') {
            $erreurs[] = 'Le nom de la categorie est obligatoire.';
        } elseif (mb_strlen($nom, 'UTF-8') > 255) {
            $erreurs[] = 'Le nom est trop long (255 caracteres maximum).';
        }

        /* Unicite du nom : deux familles avec le meme libelle rendraient la
           barre laterale publique ambigue. */
        if (!$erreurs) {
            $verif = mysqli_prepare(
                $conn,
                $modification
                    ? 'SELECT Code_cat FROM categories WHERE Nom_cat = ? AND Code_cat <> ?'
                    : 'SELECT Code_cat FROM categories WHERE Nom_cat = ?'
            );
            if ($verif) {
                if ($modification) {
                    mysqli_stmt_bind_param($verif, 'si', $nom, $code);
                } else {
                    mysqli_stmt_bind_param($verif, 's', $nom);
                }
                mysqli_stmt_execute($verif);
                $res = mysqli_stmt_get_result($verif);
                if ($res && mysqli_fetch_row($res)) {
                    $erreurs[] = 'Une categorie porte deja ce nom.';
                }
                mysqli_stmt_close($verif);
            }
        }

        if ($erreurs) {
            foreach ($erreurs as $e) { fm_admin_flash('err', $e); }
            header('Location: GestionCategories.php' . ($modification ? '?action=modifier&code=' . $code : '?action=nouveau'));
            exit;
        }

        if ($modification) {
            $stmt = mysqli_prepare($conn, 'UPDATE categories SET Nom_cat = ?, Description = ? WHERE Code_cat = ?');
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'ssi', $nom, $desc, $code);
                $ok = mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                fm_admin_flash($ok ? 'ok' : 'err', $ok ? 'Categorie modifiee.' : 'Echec de la modification.');
            }
            header('Location: GestionCategories.php');
            exit;
        }

        $stmt = mysqli_prepare($conn, 'INSERT INTO categories (Nom_cat, Description) VALUES (?, ?)');
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ss', $nom, $desc);
            $ok = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            fm_admin_flash($ok ? 'ok' : 'err', $ok ? 'Categorie ajoutee.' : 'Echec de l\'ajout.');
        }
        header('Location: GestionCategories.php');
        exit;
    }
}

/* ------------------------------------------------------------------ lecture */

$action = isset($_GET['action']) ? (string) $_GET['action'] : '';
$codeEd = isset($_GET['code']) ? (int) $_GET['code'] : 0;

/* ------------------------------------------------------ vues formulaire */

if ($action === 'nouveau' || ($action === 'modifier' && $codeEd > 0)) {
    $cat = array('Code_cat' => 0, 'Nom_cat' => '', 'Description' => '');
    $modification = ($codeEd > 0);

    if ($modification) {
        $stmt = mysqli_prepare($conn, 'SELECT Code_cat, Nom_cat, Description FROM categories WHERE Code_cat = ?');
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'i', $codeEd);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            $trouve = $res ? mysqli_fetch_assoc($res) : null;
            mysqli_stmt_close($stmt);
            if ($trouve) {
                $cat = $trouve;
            } else {
                fm_admin_flash('err', 'Categorie introuvable.');
                header('Location: GestionCategories.php');
                exit;
            }
        }
    }

    admin_entete(
        $modification ? 'Modifier une categorie' : 'Ajouter une categorie',
        'categories',
        array('<a class="fm-adm-btn is-light is-sm" href="GestionCategories.php"><i class="fa fa-arrow-left" aria-hidden="true"></i> Retour a la liste</a>')
    );
    ?>

    <form method="post" action="GestionCategories.php">
        <?php echo fm_admin_champ_csrf(); ?>
        <input type="hidden" name="operation" value="enregistrer">
        <?php if ($modification) { ?>
        <input type="hidden" name="code_cat" value="<?php echo (int) $cat['Code_cat']; ?>">
        <?php } ?>

        <div class="fm-adm-card">
            <header><h2>Categorie</h2></header>
            <div class="fm-adm-pad">
                <div class="fm-adm-field">
                    <label for="nom_cat">Nom <span aria-hidden="true">*</span></label>
                    <input class="fm-adm-input" type="text" id="nom_cat" name="nom_cat" maxlength="255" required
                           value="<?php echo fm_admin_echapper($cat['Nom_cat']); ?>">
                    <span class="fm-adm-help">Libelle affiche dans le menu public et dans le catalogue.</span>
                </div>

                <div class="fm-adm-field">
                    <label for="description">Description</label>
                    <textarea class="fm-adm-textarea" id="description" name="description"><?php echo fm_admin_echapper($cat['Description']); ?></textarea>
                    <span class="fm-adm-help">Texte facultatif, reserve a l'administration.</span>
                </div>
            </div>
            <footer>
                <button class="fm-adm-btn" type="submit">
                    <i class="fa fa-save" aria-hidden="true"></i>
                    <?php echo $modification ? 'Enregistrer' : 'Ajouter la categorie'; ?>
                </button>
            </footer>
        </div>
    </form>

    <?php
    admin_pied();
    exit;
}

/* ------------------------------------------------------------------- liste */

$motCle = isset($_GET['q']) ? trim((string) $_GET['q']) : '';

$where  = array();
$params = array();
$types  = '';
if ($motCle !== '') {
    $where[]  = 'Nom_cat LIKE ?';
    $params[] = '%' . $motCle . '%';
    $types   .= 's';
}
$sqlWhere = $where ? ' WHERE ' . implode(' AND ', $where) : '';

$categories = array();
$stmt = mysqli_prepare(
    $conn,
    'SELECT c.Code_cat, c.Nom_cat, c.Description, COUNT(p.id_prod) AS nombre_produits
     FROM categories c
     LEFT JOIN produits p ON p.Code_cat = c.Code_cat'
        . $sqlWhere
        . ' GROUP BY c.Code_cat, c.Nom_cat, c.Description
          ORDER BY c.Nom_cat ASC'
);
if ($stmt) {
    if ($types !== '') { mysqli_stmt_bind_param($stmt, $types, ...$params); }
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res) {
        while ($c = mysqli_fetch_assoc($res)) { $categories[] = $c; }
    }
    mysqli_stmt_close($stmt);
}

$total = count($categories);

admin_entete(
    'Categories',
    'categories',
    array('<a class="fm-adm-btn is-sm" href="GestionCategories.php?action=nouveau"><i class="fa fa-plus" aria-hidden="true"></i> Ajouter une categorie</a>')
);
?>

<div class="fm-adm-card">
    <header>
        <h2><?php echo (int) $total; ?> categorie<?php echo ((int) $total > 1) ? 's' : ''; ?></h2>
        <span class="fm-adm-spacer"></span>
        <?php if ($motCle !== '') { ?>
        <span style="font-size:13px;color:var(--fm-muted);">filtre : &laquo;&nbsp;<?php echo fm_admin_echapper($motCle); ?>&nbsp;&raquo;</span>
        <?php } ?>
    </header>

    <div class="fm-adm-pad" style="border-bottom:1px solid var(--fm-border);">
        <form class="fm-adm-filter" method="get" action="GestionCategories.php">
            <div class="fm-adm-field fm-adm-grow">
                <label for="q">Rechercher une categorie</label>
                <input class="fm-adm-input" type="search" id="q" name="q"
                       value="<?php echo fm_admin_echapper($motCle); ?>"
                       placeholder="Nom de la categorie">
            </div>
            <button class="fm-adm-btn" type="submit"><i class="fa fa-search" aria-hidden="true"></i> Filtrer</button>
            <?php if ($motCle !== '') { ?>
            <a class="fm-adm-btn is-light" href="GestionCategories.php">Reinitialiser</a>
            <?php } ?>
        </form>
    </div>

    <?php if (!$categories) { ?>
    <div class="fm-adm-empty">
        <i class="fa fa-tags" aria-hidden="true"></i>
        Aucune categorie<?php echo ($motCle !== '') ? ' ne correspond a «&nbsp;' . fm_admin_echapper($motCle) . '&nbsp;»' : ' pour le moment'; ?>.
    </div>
    <?php } else { ?>
    <div class="fm-adm-table-wrap">
        <table class="fm-adm-table">
            <thead>
                <tr>
                    <th class="fm-adm-num" style="width:70px;">Code</th>
                    <th>Nom</th>
                    <th>Description</th>
                    <th class="fm-adm-num" style="width:110px;">Produits</th>
                    <th class="fm-adm-actions" style="width:150px;">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($categories as $c) { ?>
                <tr>
                    <td class="fm-adm-num"><?php echo (int) $c['Code_cat']; ?></td>
                    <td><strong><?php echo fm_admin_echapper($c['Nom_cat']); ?></strong></td>
                    <td>
                        <?php
                        $d = trim((string) $c['Description']);
                        if ($d === '') {
                            echo '<span style="color:var(--fm-muted);">&mdash;</span>';
                        } else {
                            echo fm_admin_echapper(mb_strimwidth($d, 0, 90, '...', 'UTF-8'));
                        }
                        ?>
                    </td>
                    <td class="fm-adm-num">
                        <?php $n = (int) $c['nombre_produits']; ?>
                        <?php if ($n > 0) { ?>
                        <a href="GestionProduits.php?cat=<?php echo (int) $c['Code_cat']; ?>"><?php echo $n; ?></a>
                        <?php } else { ?>
                        <span style="color:var(--fm-muted);">0</span>
                        <?php } ?>
                    </td>
                    <td class="fm-adm-actions">
                        <a class="fm-adm-btn is-light is-sm"
                           href="GestionCategories.php?action=modifier&amp;code=<?php echo (int) $c['Code_cat']; ?>"
                           title="Modifier"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                        <?php if ((int) $c['nombre_produits'] > 0) { ?>
                        <button class="fm-adm-btn is-light is-sm" type="button" disabled
                                title="<?php echo (int) $c['nombre_produits']; ?> produit(s) utilisent cette categorie">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </button>
                        <?php } else { ?>
                        <form method="post" action="GestionCategories.php" style="display:inline;"
                              onsubmit="return confirm('Supprimer cette categorie ?');">
                            <?php echo fm_admin_champ_csrf(); ?>
                            <input type="hidden" name="operation" value="supprimer">
                            <input type="hidden" name="code_cat" value="<?php echo (int) $c['Code_cat']; ?>">
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