<?php
/**
 * Gestion du blog : liste, ajout, modification, suppression.
 *
 * Suive le même modèle que GestionProduits.php :
 *  - authentification admin (admin-auth.php) ;
 *  - gabarit commun (admin-layout.php) ;
 *  - jeton CSRF sur chaque POST ;
 *  - messages flash après redirection ;
 *  - requêtes preparees mysqli.
 *
 * Les images d'articles sont déposées dans img/blog/ à la racine du site.
 */
require_once(__DIR__ . '/../includes/admin-auth.php');
require_once(__DIR__ . '/../includes/admin-layout.php');
require_once(__DIR__ . '/../includes/blog_functions.php');

fm_admin_exiger_login();
fm_admin_exiger_droit('produits');

define('FM_BLOG_PAR_PAGE', 15);

/* ------------------------------------------------------------ slug -> article */
function gb_trouver_par_slug($conn, $slug)
{
    $stmt = mysqli_prepare(
        $conn,
        'SELECT id, title, slug, excerpt, content, category, product_link, product_label,
                image, meta_title, meta_description, status, created_at, updated_at
         FROM blog_posts WHERE slug = ? LIMIT 1'
    );
    if (!$stmt) { return null; }
    mysqli_stmt_bind_param($stmt, 's', $slug);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $p = $res ? mysqli_fetch_assoc($res) : null;
    mysqli_stmt_close($stmt);
    return $p;
}

/* ------------------------------------------------------------ POST handling */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    fm_admin_verifier_csrf();
    $operation = isset($_POST['operation']) ? (string) $_POST['operation'] : '';

    /* ------------------------------------------------------------- suppression */
    if ($operation === 'supprimer') {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($id > 0) {
            $stmt = mysqli_prepare($conn, 'DELETE FROM blog_posts WHERE id = ?');
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'i', $id);
                mysqli_stmt_execute($stmt);
                $ok = mysqli_stmt_affected_rows($stmt) > 0;
                mysqli_stmt_close($stmt);
                fm_admin_flash($ok ? 'ok' : 'err', $ok ? 'Article supprimé.' : 'Article introuvable.');
            }
        }
        header('Location: GestionBlog.php');
        exit;
    }

    /* ----------------------------------------------------- enregistrement (ajout/modif) */
    if ($operation === 'enregistrer') {
        $id          = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $title       = trim((string) (isset($_POST['title']) ? $_POST['title'] : ''));
        $slug        = trim((string) (isset($_POST['slug']) ? $_POST['slug'] : ''));
        $excerpt     = trim((string) (isset($_POST['excerpt']) ? $_POST['excerpt'] : ''));
        $content     = trim((string) (isset($_POST['content']) ? $_POST['content'] : ''));
        $category    = trim((string) (isset($_POST['category']) ? $_POST['category'] : ''));
        $productLink = trim((string) (isset($_POST['product_link']) ? $_POST['product_link'] : ''));
        $productLbl  = trim((string) (isset($_POST['product_label']) ? $_POST['product_label'] : ''));
        $image       = trim((string) (isset($_POST['image']) ? $_POST['image'] : ''));
        $metaTitle   = trim((string) (isset($_POST['meta_title']) ? $_POST['meta_title'] : ''));
        $metaDesc    = trim((string) (isset($_POST['meta_description']) ? $_POST['meta_description'] : ''));
        $status      = isset($_POST['status']) && $_POST['status'] === 'published' ? 'published' : 'draft';

        $erreurs = array();
        if ($title === '')  { $erreurs[] = 'Le titre est obligatoire.'; }
        if ($slug === '')  { $erreurs[] = 'Le slug est obligatoire.'; }
        if ($content === '') { $erreurs[] = 'Le contenu est obligatoire.'; }

        /* Validation du slug : caractères alphanumériques et tirets. */
        if ($slug !== '' && !preg_match('/^[a-z0-9-]+$/', $slug)) {
            $erreurs[] = 'Le slug ne peut contenir que des lettres minuscules, des chiffres et des tirets.';
        }

        /* Generation automatique du slug si vide mais titre renseigné. */
        if ($slug === '' && $title !== '') {
            $slug = fm_blog_slugify($title);
            if ($slug === '') { $slug = 'article'; }
        }

        /* Verification d'unicite du slug (sauf pour l'article courant). */
        if ($slug !== '') {
            $stmtUnique = mysqli_prepare($conn, 'SELECT id FROM blog_posts WHERE slug = ? AND id != ? LIMIT 1');
            if ($stmtUnique) {
                mysqli_stmt_bind_param($stmtUnique, 'si', $slug, $id);
                mysqli_stmt_execute($stmtUnique);
                $resUnique = mysqli_stmt_get_result($stmtUnique);
                if ($resUnique && mysqli_fetch_assoc($resUnique)) {
                    $erreurs[] = 'Un autre article utilise déjà ce slug.';
                }
                mysqli_stmt_close($stmtUnique);
            }
        }

        /* Validation de l'image : si un nom de fichier est fourni, verifier
           qu'il n'existe pas sur le serveur via un chemin relatif (pas de ../). */
        if ($image !== '' && (strpos($image, '../') !== false || strpos($image, '..\\') !== false || strpos($image, '/') === 0)) {
            $erreurs[] = 'Le nom de fichier image est invalide.';
            $image = '';
        }

        if ($erreurs) {
            foreach ($erreurs as $e) { fm_admin_flash('err', $e); }
            header('Location: GestionBlog.php?action=' . ($id > 0 ? 'modifier&id=' . $id : 'nouveau'));
            exit;
        }

        if ($id > 0) {
            $sql = 'UPDATE blog_posts SET title = ?, slug = ?, excerpt = ?, content = ?, category = ?,
                    product_link = ?, product_label = ?, image = ?, meta_title = ?, meta_description = ?, status = ?
                    WHERE id = ?';
            $stmt = mysqli_prepare($conn, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'ssssssssssi',
                    $title, $slug, $excerpt, $content, $category,
                    $productLink, $productLbl, $image, $metaTitle, $metaDesc, $status, $id);
                $ok = mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                fm_admin_flash($ok ? 'ok' : 'err', $ok ? 'Article modifié.' : 'Échec de la modification.');
                header('Location: GestionBlog.php');
                exit;
            }
        } else {
            $sql = 'INSERT INTO blog_posts (title, slug, excerpt, content, category, product_link,
                    product_label, image, meta_title, meta_description, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
            $stmt = mysqli_prepare($conn, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'sssssssssss',
                    $title, $slug, $excerpt, $content, $category,
                    $productLink, $productLbl, $image, $metaTitle, $metaDesc, $status);
                $ok = mysqli_stmt_execute($stmt);
                $nouveauId = $ok ? mysqli_insert_id($conn) : 0;
                mysqli_stmt_close($stmt);
                fm_admin_flash($ok ? 'ok' : 'err', $ok ? 'Article ajouté.' : 'Échec de l\'ajout.');
                header('Location: GestionBlog.php' . ($nouveauId ? '?action=modifier&id=' . $nouveauId : ''));
                exit;
            }
        }
        fm_admin_flash('err', 'Requête impossible.');
        header('Location: GestionBlog.php?action=' . ($id > 0 ? 'modifier&id=' . $id : 'nouveau'));
        exit;
    }

    /* ----------------------------------------------------- publication / dépublication */
    if ($operation === 'changer_statut') {
        $id     = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $status = isset($_POST['status']) ? (string) $_POST['status'] : 'draft';
        $status = ($status === 'published') ? 'published' : 'draft';

        if ($id > 0) {
            $stmt = mysqli_prepare($conn, 'UPDATE blog_posts SET status = ? WHERE id = ?');
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'si', $status, $id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                fm_admin_flash('ok', 'Statut mis à jour.');
            }
        }
        header('Location: GestionBlog.php');
        exit;
    }
}

/* ------------------------------------------------------------ lecture */

$action = isset($_GET['action']) ? (string) $_GET['action'] : '';
$idEdit = isset($_GET['id']) ? (int) $_GET['id'] : 0;

/* Vue formulaire : ajout ou modification */
if ($action === 'nouveau' || ($action === 'modifier' && $idEdit > 0)) {
    $post = array(
        'id'              => 0,
        'title'           => '',
        'slug'            => '',
        'excerpt'         => '',
        'content'         => '',
        'category'        => '',
        'product_link'    => '',
        'product_label'   => '',
        'image'           => '',
        'meta_title'      => '',
        'meta_description'=> '',
        'status'          => 'draft',
        'created_at'      => '',
        'updated_at'      => '',
    );
    $modification = ($idEdit > 0);

    if ($modification) {
        $stmt = mysqli_prepare($conn, 'SELECT * FROM blog_posts WHERE id = ? LIMIT 1');
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'i', $idEdit);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if ($res) {
                $trouve = mysqli_fetch_assoc($res);
                if ($trouve) { $post = $trouve; }
            }
            mysqli_stmt_close($stmt);
        }
        if (empty($trouve)) {
            fm_admin_flash('err', 'Article introuvable.');
            header('Location: GestionBlog.php');
            exit;
        }
    }

    admin_entete(
        $modification ? 'Modifier un article' : 'Ajouter un article',
        'blog',
        array('<a class="fm-adm-btn is-light is-sm" href="GestionBlog.php"><i class="fa fa-arrow-left" aria-hidden="true"></i> Retour à la liste</a>')
    );
    ?>

    <form method="post" action="GestionBlog.php" class="fm-adm-form">
        <?php echo fm_admin_champ_csrf(); ?>
        <input type="hidden" name="operation" value="enregistrer">
        <?php if ($modification) { ?>
        <input type="hidden" name="id" value="<?php echo (int) $post['id']; ?>">
        <?php } ?>

        <div class="fm-adm-card">
            <header><h2>Informations</h2></header>
            <div class="fm-adm-pad">
                <div class="fm-adm-field">
                    <label for="title">Titre <span aria-hidden="true">*</span></label>
                    <input class="fm-adm-input" type="text" id="title" name="title" maxlength="250" required
                           value="<?php echo fm_admin_echapper($post['title']); ?>">
                </div>

                <div class="fm-adm-field">
                    <label for="slug">Slug <span aria-hidden="true">*</span></label>
                    <input class="fm-adm-input" type="text" id="slug" name="slug" maxlength="250" required
                           value="<?php echo fm_admin_echapper($post['slug']); ?>">
                    <span class="fm-adm-help">L'URL de l'article sera : /blog/{slug}/</span>
                </div>

                <div class="fm-adm-grid">
                    <div class="fm-adm-field">
                        <label for="category">Catégorie</label>
                        <select class="fm-adm-select" id="category" name="category">
                            <option value="">— Aucune —</option>
                            <?php
                            $cats = array('Morocco', 'Tomatoes', 'Citrus', 'Watermelon', 'Logistics', 'Cold chain');
                            foreach ($cats as $c) {
                                echo '<option value="' . fm_admin_echapper($c) . '"'
                                   . ((string) $post['category'] === $c ? ' selected' : '')
                                   . '>' . fm_admin_echapper($c) . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="fm-adm-field">
                        <label for="status">Statut</label>
                        <select class="fm-adm-select" id="status" name="status">
                            <option value="draft" <?php echo ($post['status'] === 'draft') ? 'selected' : ''; ?>>Brouillon</option>
                            <option value="published" <?php echo ($post['status'] === 'published') ? 'selected' : ''; ?>>Publié</option>
                        </select>
                    </div>
                </div>

                <div class="fm-adm-field">
                    <label for="excerpt">Extrait (excerpt)</label>
                    <textarea class="fm-adm-textarea" id="excerpt" name="excerpt" rows="3"
                              placeholder="Résumé affiché dans la liste du blog..."><?php echo fm_admin_echapper($post['excerpt']); ?></textarea>
                    <span class="fm-adm-help">Texte court qui résume l'article. Si vide, un extrait est généré automatiquement.</span>
                </div>

                <div class="fm-adm-field">
                    <label for="content">Contenu HTML <span aria-hidden="true">*</span></label>
                    <textarea class="fm-adm-textarea fm-adm-textarea-large" id="content" name="content" rows="20"
                              placeholder="<h2>Titre</h2><p>Votre texte...</p>"><?php echo fm_admin_echapper($post['content']); ?></textarea>
                    <span class="fm-adm-help">Contenu HTML brut. Utilisez les balises &lt;h2&gt;, &lt;p&gt;, &lt;ul&gt;&lt;li&gt;, &lt;img&gt;, &lt;strong&gt;, &lt;em&gt;.</span>
                </div>
            </div>
        </div>

        <div class="fm-adm-card">
            <header><h2>Liens et médias</h2></header>
            <div class="fm-adm-pad">
                <div class="fm-adm-field">
                    <label for="image">Image (fichier dans img/blog/)</label>
                    <input class="fm-adm-input" type="text" id="image" name="image" maxlength="250"
                           value="<?php echo fm_admin_echapper($post['image']); ?>">
                    <span class="fm-adm-help">Nom du fichier (ex: blog-tomatoes.jpg), sans chemin.</span>
                </div>

                <div class="fm-adm-grid">
                    <div class="fm-adm-field">
                        <label for="product_link">Lien produit</label>
                        <input class="fm-adm-input" type="text" id="product_link" name="product_link" maxlength="250"
                               value="<?php echo fm_admin_echapper($post['product_link']); ?>">
                        <span class="fm-adm-help">Ex: products/3/Citrus/</span>
                    </div>

                    <div class="fm-adm-field">
                        <label for="product_label">Libellé produit lié</label>
                        <input class="fm-adm-input" type="text" id="product_label" name="product_label" maxlength="150"
                               value="<?php echo fm_admin_echapper($post['product_label']); ?>">
                    </div>
                </div>

                <div class="fm-adm-field">
                    <label for="meta_title">Titre SEO (meta)</label>
                    <input class="fm-adm-input" type="text" id="meta_title" name="meta_title" maxlength="250"
                           value="<?php echo fm_admin_echapper($post['meta_title']); ?>">
                    <span class="fm-adm-help">Si vide, le titre de l'article est utilisé.</span>
                </div>

                <div class="fm-adm-field">
                    <label for="meta_description">Description SEO (meta)</label>
                    <input class="fm-adm-input" type="text" id="meta_description" name="meta_description" maxlength="250"
                           value="<?php echo fm_admin_echapper($post['meta_description']); ?>">
                    <span class="fm-adm-help">Si vide, l'extrait est utilisé.</span>
                </div>
            </div>

            <footer>
                <button class="fm-adm-btn" type="submit">
                    <i class="fa fa-save" aria-hidden="true"></i>
                    <?php echo $modification ? 'Enregistrer les modifications' : 'Créer l’article'; ?>
                </button>
                <?php if ($modification) { ?>
                <a class="fm-adm-btn is-light" href="GestionBlog.php">Annuler</a>
                <?php } ?>
            </footer>
        </div>
    </form>

    <?php
    admin_pied();
    exit;
}

/* ------------------------------------------------------------ liste principale */

$motCle    = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
$page      = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

$where  = array();
$params = array();
$types  = '';

if ($motCle !== '') {
    $where[]  = '(title LIKE ? OR slug LIKE ? OR category LIKE ?)';
    $params[] = '%' . $motCle . '%';
    $params[] = '%' . $motCle . '%';
    $params[] = '%' . $motCle . '%';
    $types   .= 'sss';
}
$sqlWhere = $where ? ' WHERE ' . implode(' AND ', $where) : '';

$nombreTotal = 0;
$stmt = mysqli_prepare($conn, 'SELECT COUNT(*) FROM blog_posts' . $sqlWhere);
if ($stmt) {
    if ($types !== '') { mysqli_stmt_bind_param($stmt, $types, ...$params); }
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    mysqli_stmt_bind_result($stmt, $nombreTotal);
    mysqli_stmt_fetch($stmt);
    $nombreTotal = (int) $nombreTotal;
    mysqli_stmt_close($stmt);
}

$nombrePages = max(1, (int) ceil($nombreTotal / FM_BLOG_PAR_PAGE));
if ($page > $nombrePages) { $page = $nombrePages; }
$offset = ($page - 1) * FM_BLOG_PAR_PAGE;

$posts = array();
$stmt = mysqli_prepare(
    $conn,
    'SELECT id, title, slug, category, status, created_at, updated_at
     FROM blog_posts' . $sqlWhere . ' ORDER BY created_at DESC LIMIT ? OFFSET ?'
);
if ($stmt) {
    $limit = FM_BLOG_PAR_PAGE;
    $off = $offset;
    $bindVals = $params;
    $bindVals[] = $limit;
    $bindVals[] = $off;
    mysqli_stmt_bind_param($stmt, $types . 'ii', ...$bindVals);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res) {
        while ($p = mysqli_fetch_assoc($res)) { $posts[] = $p; }
    }
    mysqli_stmt_close($stmt);
}

admin_entete(
    'Blog',
    'blog',
    array('<a class="fm-adm-btn is-sm" href="GestionBlog.php?action=nouveau"><i class="fa fa-plus" aria-hidden="true"></i> Nouvel article</a>')
);
?>

<div class="fm-adm-card">
    <header>
        <h2><?php echo (int) $nombreTotal; ?> article<?php echo ((int) $nombreTotal > 1) ? 's' : ''; ?></h2>
        <span class="fm-adm-spacer"></span>
    </header>

    <div class="fm-adm-pad" style="border-bottom:1px solid var(--fm-border);">
        <form class="fm-adm-filter" method="get" action="GestionBlog.php">
            <div class="fm-adm-field fm-adm-grow">
                <label for="q">Rechercher</label>
                <input class="fm-adm-input" type="search" id="q" name="q"
                       value="<?php echo fm_admin_echapper($motCle); ?>"
                       placeholder="Titre, slug ou catégorie">
            </div>
            <button class="fm-adm-btn" type="submit"><i class="fa fa-search" aria-hidden="true"></i> Filtrer</button>
            <?php if ($motCle !== '') { ?>
            <a class="fm-adm-btn is-light" href="GestionBlog.php">Réinitialiser</a>
            <?php } ?>
        </form>
    </div>

    <?php if (!$posts) { ?>
    <div class="fm-adm-empty">
        <i class="fa fa-book" aria-hidden="true"></i>
        Aucun article<?php echo ($motCle !== '') ? ' ne correspond à «&nbsp;' . fm_admin_echapper($motCle) . '&nbsp;»' : ' pour le moment'; ?>.
        <a href="GestionBlog.php?action=nouveau">Créer le premier article</a>
    </div>
    <?php } else { ?>

    <div class="fm-adm-table-wrap">
        <table class="fm-adm-table">
            <thead>
                <tr>
                    <th class="fm-adm-num" style="width:50px;">N&deg;</th>
                    <th>Titre</th>
                    <th style="width:120px;">Catégorie</th>
                    <th style="width:100px;">Statut</th>
                    <th style="width:150px;">Date</th>
                    <th class="fm-adm-actions" style="width:225px;">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($posts as $p) { ?>
                <tr>
                    <td class="fm-adm-num"><?php echo (int) $p['id']; ?></td>
                    <td>
                        <strong><?php echo fm_admin_echapper($p['title']); ?></strong><br>
                        <span style="font-size:12.5px;color:var(--fm-muted);">
                            /<?php echo fm_admin_echapper($p['slug']); ?>/
                        </span>
                    </td>
                    <td>
                        <?php echo $p['category'] !== '' && $p['category'] !== null
                            ? fm_admin_echapper($p['category'])
                            : '<span style="color:var(--fm-muted);">(sans)</span>'; ?>
                    </td>
                    <td>
                        <?php if ($p['status'] === 'published') { ?>
                        <span class="fm-adm-badge is-ok">Publié</span>
                        <?php } else { ?>
                        <span class="fm-adm-badge is-off">Brouillon</span>
                        <?php } ?>
                    </td>
                    <td style="font-size:12.5px;color:var(--fm-muted);">
                        <?php echo fm_admin_echapper(fm_blog_date_fr($p['created_at'])); ?>
                    </td>
                    <td class="fm-adm-actions">
                        <a class="fm-adm-btn is-light is-sm"
                           href="GestionBlog.php?action=modifier&id=<?php echo (int) $p['id']; ?>"
                           title="Modifier"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                        <a class="fm-adm-btn is-light is-sm"
                           href="<?php echo $GLOBALS['fm_app']; ?>blog/<?php echo rawurlencode($p['slug']); ?>/"
                           target="_blank" rel="noopener"
                           title="Voir l'article"><i class="fa fa-eye" aria-hidden="true"></i></a>

                        <?php
                        /* Toggle statut : publier ou dépublier */
                        $nouveauStatut = ($p['status'] === 'published') ? 'draft' : 'published';
                        $actionLibelle = ($p['status'] === 'published') ? 'Dépublier' : 'Publier';
                        ?>
                        <form method="post" action="GestionBlog.php" style="display:inline;"
                              onsubmit="return confirm('<?php echo addslashes($actionLibelle); ?> cet article ?');">
                            <?php echo fm_admin_champ_csrf(); ?>
                            <input type="hidden" name="operation" value="changer_statut">
                            <input type="hidden" name="id" value="<?php echo (int) $p['id']; ?>">
                            <input type="hidden" name="status" value="<?php echo $nouveauStatut; ?>">
                            <button class="fm-adm-btn is-light is-sm" type="submit"
                                    title="<?php echo $actionLibelle; ?>">
                                <?php echo $p['status'] === 'published' ? '<i class="fa fa-eye-slash" aria-hidden="true"></i>' : '<i class="fa fa-eye" aria-hidden="true"></i>'; ?>
                            </button>
                        </form>

                        <form method="post" action="GestionBlog.php" style="display:inline;"
                              onsubmit="return confirm('Supprimer définitivement cet article ?');">
                            <?php echo fm_admin_champ_csrf(); ?>
                            <input type="hidden" name="operation" value="supprimer">
                            <input type="hidden" name="id" value="<?php echo (int) $p['id']; ?>">
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
            $lien = function ($p) use ($motCle) {
                $qs = array('page' => $p);
                if ($motCle !== '') { $qs['q'] = $motCle; }
                return 'GestionBlog.php?' . http_build_query($qs);
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
