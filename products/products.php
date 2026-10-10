<?php
require_once((__DIR__ . "/../includes/_header.php"));
require_once((__DIR__ . "/../includes/paths.php"));
require_once((__DIR__ . "/../includes/seo.php"));
require_once((__DIR__ . "/../includes/guides.php"));

/* ------------------------------------------------------------------ entrees
   Les valeurs sont normalisees puis injectees via requetes preparees :
   aucune variable utilisateur n'est concatenee dans une chaine SQL. */
$idCat    = isset($_GET['idCat'])    ? intval($_GET['idCat'])                 : 0;
$motCle   = isset($_GET['motCle'])   ? trim(stripslashes((string) $_GET['motCle'])) : '';
$tag      = isset($_GET['tags'])     ? trim(stripslashes((string) $_GET['tags']))   : '';
$featured = isset($_GET['featured']) ? 1                                   : 0;
$page     = isset($_GET['page'])     ? max(1, intval($_GET['page']))         : 1;

$perPage = 12;
$offset  = ($page - 1) * $perPage;

/* -------------------------------------------------------------- categories */
$categories = array();
$rsCat = mysqli_query($conn, "SELECT Code_cat, Nom_cat FROM categories ORDER BY Nom_cat ASC");
if ($rsCat) {
    while ($c = mysqli_fetch_assoc($rsCat)) {
        $c['Code_cat'] = (int) $c['Code_cat'];
        $c['label']    = fm_cat_label($c['Nom_cat']);
        $c['slug']     = str_replace('_', '', $c['Nom_cat']);
        $categories[]  = $c;
    }
}

$catCourante = null;
if ($idCat > 0) {
    foreach ($categories as $c) {
        if ($c['Code_cat'] === $idCat) { $catCourante = $c; }
    }
    /* categorie inexistante : on retombe sur le catalogue complet */
    if ($catCourante === null) { $idCat = 0; }
}

/* ------------------------------------------------------- construction WHERE */
$where  = array();
$params = array();
$types  = '';

if ($idCat > 0) {
    $where[]  = 'Code_cat = ?';
    $params[] = $idCat;
    $types   .= 'i';
}
if ($motCle !== '') {
    $like     = '%' . addcslashes($motCle, '%_\\') . '%';
    $where[]  = 'Designation LIKE ?';
    $params[] = $like;
    $types   .= 's';
}
if ($tag !== '') {
    $where[]  = 'tags LIKE ?';
    $params[] = '%' . addcslashes($tag, '%_\\') . '%';
    $types   .= 's';
}
if ($featured) {
    $where[] = 'Selectionne = 1';
}

$sqlWhere = $where ? ' WHERE ' . implode(' AND ', $where) : '';
/* en recherche on trie par designation, sinon par famille puis par nom */
$sqlOrder = ($motCle !== '' || $tag !== '')
    ? ' ORDER BY Designation ASC'
    : ' ORDER BY Code_cat ASC, Designation ASC';

/* ------------------------------------------------------------------ total */
$stmt = mysqli_prepare($conn, 'SELECT COUNT(*) FROM produits' . $sqlWhere);
if ($stmt) {
    if ($types !== '') { mysqli_stmt_bind_param($stmt, $types, ...$params); }
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    mysqli_stmt_bind_result($stmt, $nombreTotal);
    mysqli_stmt_fetch($stmt);
    $nombreTotal = (int) $nombreTotal;
    mysqli_stmt_close($stmt);
} else {
    $nombreTotal = 0;
}

$nombreDePages = max(1, (int) ceil($nombreTotal / $perPage));
if ($page > $nombreDePages) { $page = $nombreDePages; }
$offset = ($page - 1) * $perPage;

/* --------------------------------------------------------------- produits */
$produits = array();
$stmt = mysqli_prepare(
    $conn,
    'SELECT id_prod, Ref_prod, Designation, description, Photo, Disponible, Promotion, Selectionne, Code_cat, tags'
        . ' FROM produits' . $sqlWhere . $sqlOrder . ' LIMIT ? OFFSET ?'
);
if ($stmt) {
    $limit = $perPage;
    $off   = $offset;
    $bindTypes = $types . 'ii';
    $bindVals  = $params;
    $bindVals[] = $limit;
    $bindVals[] = $off;

    mysqli_stmt_bind_param($stmt, $bindTypes, ...$bindVals);
    mysqli_stmt_execute($stmt);

    $res = mysqli_stmt_get_result($stmt);
    if ($res) {
        while ($p = mysqli_fetch_assoc($res)) { $produits[] = $p; }
    }
    mysqli_stmt_close($stmt);
}

/* Map Code_cat => label, pour afficher la famille sur chaque carte. */
$catLabels = array();
foreach ($categories as $c) { $catLabels[$c['Code_cat']] = $c['label']; }

/* Suggestions affichees quand une recherche ne renvoie rien. */
$suggestions = array();
if ($motCle !== '' && $nombreTotal === 0 && mb_strlen($motCle, 'UTF-8') >= 3) {
    $pref = '%' . addcslashes(mb_substr($motCle, 0, 3, 'UTF-8'), '%_\\') . '%';
    $stmtSug = mysqli_prepare(
        $conn,
        'SELECT id_prod, Designation FROM produits WHERE Designation LIKE ? ORDER BY Designation ASC LIMIT 4'
    );
    if ($stmtSug) {
        mysqli_stmt_bind_param($stmtSug, 's', $pref);
        mysqli_stmt_execute($stmtSug);
        $rsSug = mysqli_stmt_get_result($stmtSug);
        if ($rsSug) {
            while ($s = mysqli_fetch_assoc($rsSug)) { $suggestions[] = $s; }
        }
        mysqli_stmt_close($stmtSug);
    }
}

/* ------------------------------------------------------------------ titre */
if ($motCle !== '') {
    $fm_titre    = 'Search results';
    $fm_sousTitre = $nombreTotal . ' product' . ($nombreTotal > 1 ? 's' : '')
        . ' matching &laquo;&nbsp;' . htmlspecialchars($motCle, ENT_QUOTES, 'UTF-8') . '&nbsp;&raquo;';
} elseif ($tag !== '') {
    $fm_titre    = 'Products tagged &laquo;&nbsp;' . htmlspecialchars($tag, ENT_QUOTES, 'UTF-8') . '&nbsp;&raquo;';
    $fm_sousTitre = $nombreTotal . ' product' . ($nombreTotal > 1 ? 's' : '') . ' found.';
} elseif ($catCourante) {
    $fm_titre    = $catCourante['label'];
    $fm_sousTitre = (strtolower($catCourante['label']) === 'others')
        ? 'Other seasonal products from Morocco, packed and exported to professional buyers.'
        : 'Browse our ' . strtolower($catCourante['label']) . ' range, packed and exported from Morocco.';
} elseif ($featured) {
    $fm_titre     = 'Featured products';
    $fm_sousTitre = 'The selection our customers reorder most often.';
} else {
    $fm_titre     = 'Moroccan fruits and vegetables';
    $fm_sousTitre = 'Fresh fruits and vegetables grown in Morocco, packed for export.';
}

/* Mapping category name to SEO slug for product guide pages */
$seoSlugMap = array(
    'Tomatoes'    => 'tomatoes',
    'Citrus'      => 'oranges',
    'Melons'      => 'watermelon',
    'Peppers'     => 'peppers',
    'Courgettes'  => 'courgettes',
    'Berries'     => 'berries',
    'Figs'        => 'figs',
    'Dried_fruits'=> 'dried-fruits',
    'Dried fruits'=> 'dried-fruits',
    'Eggplants'   => 'eggplants',
    'Green_leaves'=> 'green-leaves',
    'Green leaves'=> 'green-leaves',
    'Pits'        => 'pits',
);
/* $catCourante vaut null sur /products/ (aucune famille choisie) : on teste avant de lire. */
/* Comparaison sans majuscules, sans espaces ni "_" : Dried_fruits, Driedfruits et
   "Dried fruits" designent la meme famille. */
$seoNorm = array();
foreach ($seoSlugMap as $k => $v) { $seoNorm[strtolower(preg_replace('/[^a-z]/i', '', $k))] = $v; }
$seoCle  = $catCourante ? strtolower(preg_replace('/[^a-z]/i', '', $catCourante['Nom_cat'])) : '';
$seoSlug = ($seoCle !== '' && isset($seoNorm[$seoCle])) ? $seoNorm[$seoCle] : '';
/* Le bouton n'apparait que si la fiche existe et est publiee : jamais de lien mort. */
if ($seoSlug !== '') {
    $fm_guideOk = false;
    try {
        $stG = mysqli_prepare($conn, "SELECT 1 FROM product_seo WHERE slug = ? AND status = 'published' LIMIT 1");
        if ($stG) {
            mysqli_stmt_bind_param($stG, 's', $seoSlug);
            mysqli_stmt_execute($stG);
            $rG = mysqli_stmt_get_result($stG);
            $fm_guideOk = ($rG && mysqli_fetch_assoc($rG)) ? true : false;
            mysqli_stmt_close($stG);
        }
    } catch (Throwable $fm_e) { $fm_guideOk = false; }
    if (!$fm_guideOk) { $seoSlug = ''; }
}
$seoLink = $seoSlug ? $fm_app . 'products/' . $seoSlug . '/' : '';

/* Filtre actif, pour surligner le bon lien dans la barre de navigation. */
$fm_lienActif = 'products/';
if ($catCourante) { $fm_lienActif = 'products/' . $catCourante['Code_cat'] . '/'; }
if ($motCle !== '' || $tag !== '' || $featured) { $fm_lienActif = 'products/'; }

/* ------------------------------------------------------------------ SEO
   Une seule adresse par contenu : la categorie utilise sa forme lisible
   /products/8/Peppers/, et les resultats de recherche, les tags et la
   selection ne sont pas indexes (ce sont des pages sans contenu propre qui
   dupliqueraient le catalogue). */
$fm_estFiltre = ($motCle !== '' || $tag !== '' || $featured);
$fm_suffixe   = $page > 1 ? ' (page ' . $page . ')' : '';
if ($catCourante && !$fm_estFiltre) {
    $fm_slugCat  = preg_replace('/[^A-Za-z0-9-]/', '', $catCourante['slug']);
    $fm_seoPath  = 'products/' . $catCourante['Code_cat'] . '/' . $fm_slugCat . '/' . ($page > 1 ? $page . '/' : '');
    $fm_seoBase  = 'products/' . $catCourante['Code_cat'] . '/' . $fm_slugCat . '/';
    $fm_seoTitle = 'Moroccan ' . $catCourante['label'] . ' for export: our range' . $fm_suffixe . ' | ' . FM_SITE_NAME;
    $fm_seoDesc  = 'Our ' . strtolower($catCourante['label']) . ' range from Morocco, packed for professional buyers. See the products and ask us for availability and a quote.';
} elseif (!$fm_estFiltre) {
    $fm_seoPath  = 'products/' . ($page > 1 ? '?page=' . $page : '');
    $fm_seoBase  = 'products/';
    $fm_seoTitle = 'Fresh fruits and vegetables from Morocco: product catalogue' . $fm_suffixe . ' | ' . FM_SITE_NAME;
    $fm_seoDesc  = 'Browse the FoodMax Group catalogue of Moroccan fruit and vegetables, packed for professional buyers. Ask us about availability and get a quote.';
} else {
    $fm_seoPath  = 'products/';
    $fm_seoBase  = 'products/';
    $fm_seoTitle = strip_tags(html_entity_decode($fm_titre, ENT_QUOTES, 'UTF-8')) . ' | ' . FM_SITE_NAME;
    $fm_seoDesc  = 'FoodMax Group product catalogue.';
}
$fm_pageSuiv = isset($nombreDePages) ? $nombreDePages : 1;
$fm_lienSeo  = function ($p) use ($catCourante, $fm_estFiltre) {
    if ($catCourante && !$fm_estFiltre) {
        return 'products/' . $catCourante['Code_cat'] . '/' . preg_replace('/[^A-Za-z0-9-]/', '', $catCourante['slug']) . '/' . ($p > 1 ? $p . '/' : '');
    }
    return 'products/' . ($p > 1 ? '?page=' . $p : '');
};
$fm_etapes = array(array('Home', ''), array('Products', 'products/'));
if ($catCourante && !$fm_estFiltre) { $fm_etapes[] = array($catCourante['label'], $fm_seoBase); }
$fm_seo = array(
    'path'        => $fm_seoPath,
    'title'       => $fm_seoTitle,
    'description' => $fm_seoDesc,
    'type'        => 'website',
    /* Une famille sans produit ne doit pas etre indexee : Google y verrait une page vide. */
    'noindex'     => ($fm_estFiltre || ($catCourante && $nombreTotal === 0)),
    'prev'        => (!$fm_estFiltre && $page > 1) ? $fm_lienSeo($page - 1) : '',
    'next'        => (!$fm_estFiltre && $page < $fm_pageSuiv) ? $fm_lienSeo($page + 1) : '',
    'jsonld'      => $fm_estFiltre ? array() : array(
        array(
            '@type'       => 'CollectionPage',
            'name'        => $fm_catName = ($catCourante ? $catCourante['label'] : 'Product catalogue'),
            'description' => $fm_seoDesc,
            'url'         => fm_seo_url($fm_seoPath),
            'isPartOf'    => array('@type' => 'WebSite', 'name' => FM_SITE_NAME, 'url' => fm_seo_url('')),
        ),
        fm_seo_breadcrumb($fm_etapes),
    ),
);
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<title><?php echo htmlspecialchars(fm_seo_title($fm_seoTitle), ENT_QUOTES, 'UTF-8'); ?></title>
	<meta name="description" content="<?php echo htmlspecialchars($fm_seoDesc, ENT_QUOTES, 'UTF-8'); ?>">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<?php fm_seo_head($fm_seo); ?>

	<link rel="stylesheet" type="text/css" href="<?php echo $fm_app; ?>css/phlox.css<?php echo fm_ver('css/phlox.css'); ?>">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,400i,600,600i,700,700i&display=swap">
	<link rel="stylesheet" type="text/css" href="<?php echo $fm_app; ?>css/font-awesome.min.css<?php echo fm_ver('css/font-awesome.min.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo $fm_app; ?>css/fancybox/jquery.fancybox.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo $fm_app; ?>css/fancybox/helpers/jquery.fancybox-thumbs.css" />

	<script type="text/javascript" src="<?php echo $fm_app; ?>js/jquery.min.js<?php echo fm_ver('js/jquery.min.js'); ?>"></script>
	<script type="text/javascript" src="<?php echo $fm_app; ?>js/setting.js<?php echo fm_ver('js/setting.js'); ?>"></script>

	<!-- Google tag (gtag.js) -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=G-ZPRWMT85SP"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag() { dataLayer.push(arguments); }
		gtag('js', new Date());
		gtag('config', 'G-ZPRWMT85SP');
	</script>
</head>

<?php
/* $fm_app est calcule par header-inc.php : toutes les URL ci-dessous l'utilise. */
require_once((__DIR__ . "/../includes/header-inc.php"));

/* Construit une URL de catalogue ; la page 1 reste sur /products/. */
function fm_lien_produit($qs, $page) {
    global $fm_app;
    $base = $fm_app . 'products/';
    if ($qs === '') {
        return $page > 1 ? $base . '?page=' . $page : $base;
    }
    return $base . '?' . $qs . ($page > 1 ? '&page=' . $page : '');
}

/* URL d'une categorie, sous forme lisible : /products/8/Peppers/ et
   /products/8/Peppers/2/ pour la page suivante (route ajoutee dans .htaccess).
   Si le libelle ne contient aucun caractere utilisable dans une URL, on
   retombe sur la forme avec parametres, qui fonctionne toujours. */
function fm_lien_categorie($code, $slug, $page) {
    global $fm_app;
    $code = (int) $code;
    $slug = preg_replace('/[^A-Za-z0-9-]/', '', (string) $slug);
    if ($slug === '') {
        return fm_lien_produit('idCat=' . $code, $page);
    }
    return $fm_app . 'products/' . $code . '/' . $slug . '/' . ($page > 1 ? $page . '/' : '');
}

function fm_echapper($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); }

/* Premiere photo du produit, avec repli sur une image generique. */
function fm_photo_produit($prod, $up) {
    $photos = array_filter(array_map('trim', explode(',', (string) $prod['Photo'])));
    if ($photos) {
        foreach ($photos as $ph) {
            $f = $up . 'images/' . $prod['Ref_prod'] . '/' . $ph;
	if (is_file(__DIR__ . '/../images/' . $prod['Ref_prod'] . '/' . $ph)) { return $f; }
        }
    }
    return $up . 'img/product-placeholder.svg';
}

/* Extrait lisible d'une description, sans casser les balises eventuelles. */
function fm_resume($texte, $long = 96) {
    $t = trim(preg_replace('/\s+/u', ' ', strip_tags((string) $texte)));
    if (function_exists('mb_strlen')) {
        if (mb_strlen($t, 'UTF-8') <= $long) { return $t; }
        return rtrim(mb_substr($t, 0, $long, 'UTF-8')) . '&hellip;';
    }
    if (strlen($t) <= $long) { return $t; }
    return rtrim(substr($t, 0, $long)) . '&hellip;';
}
?>

<!-- ============================================================ EN-TETE PAGE -->
<section class="fm-pagehead">
	<div class="fm-pagehead-inner">
		<span class="fm-eyebrow">Our catalogue</span>
		<h1><?php echo $fm_titre; ?></h1>
		<p><?php echo $fm_sousTitre; ?></p>

		<?php if ($seoLink !== ''): ?>
		<div class="fm-seo-link">
			<a href="<?php echo $seoLink; ?>" class="btn btn-outline">
				<i class="fa fa-book" aria-hidden="true"></i> Product Guide
			</a>
		</div>
		<?php endif; ?>

		<nav class="fm-crumbs" aria-label="Breadcrumb">
			<ol>
				<li><a href="<?php echo $fm_app; ?>">Home</a></li>
				<?php if ($catCourante) { ?>
				<li><a href="<?php echo $fm_app; ?>products/">Products</a></li>
				<li><?php echo fm_echapper($catCourante['label']); ?></li>
				<?php } else { ?>
				<li>Products</li>
				<?php } ?>
			</ol>
		</nav>
	</div>
</section>

<!-- =============================================================== CATALOGUE -->
<section class="fm-catalog">
	<div class="fm-catalog-inner">

		<!-- --------------------------------------------------------- filtres -->
		<aside class="fm-catalog-side" aria-label="Product categories">
			<h2 class="fm-side-title">Categories</h2>
			<ul class="fm-catside-list">
				<li>
					<a href="<?php echo fm_lien_produit('', 1); ?>"
					   class="<?php echo (!$idCat && !$motCle && !$tag && !$featured) ? 'is-active' : ''; ?>">
						All products <span class="fm-side-count"><?php echo (int) $nombreTotal; ?></span>
					</a>
				</li>
				<li>
					<a href="<?php echo fm_lien_produit('featured=1', 1); ?>"
					   class="<?php echo $featured ? 'is-active' : ''; ?>">
						<i class="fa fa-star" aria-hidden="true"></i> Featured
					</a>
				</li>
				<?php foreach ($categories as $c) { ?>
					<li>
						<a href="<?php echo fm_lien_categorie($c['Code_cat'], $c['slug'], 1); ?>"
						   class="<?php echo ($idCat === $c['Code_cat']) ? 'is-active' : ''; ?>">
							<?php echo fm_echapper($c['label']); ?>
						</a>
					</li>
				<?php } ?>
			</ul>

			<div class="fm-side-cta">
				<h3>Need a specific grade?</h3>
				<p>Our export team prepares custom specifications, packing and documentation.</p>
				<a class="btn btn-sm" href="<?php echo $fm_app; ?>contact/">Request a quote</a>
			</div>
		</aside>

		<!-- ------------------------------------------------------------ grille -->
		<div class="fm-catalog-main">

			<div class="fm-toolbar">
				<p class="fm-toolbar-count">
					<?php if ($nombreTotal > 0) { ?>
						Showing <strong><?php echo $offset + 1; ?>&ndash;<?php echo min($offset + $perPage, $nombreTotal); ?></strong>
						of <strong><?php echo $nombreTotal; ?></strong> product<?php echo $nombreTotal > 1 ? 's' : ''; ?>
					<?php } else { ?>
						<strong>No products</strong> found
					<?php } ?>
				</p>

				<?php if ($motCle !== '' || $tag !== '' || $idCat > 0 || $featured) { ?>
					<a class="fm-toolbar-reset" href="<?php echo fm_lien_produit('', 1); ?>">
						<i class="fa fa-times-circle" aria-hidden="true"></i> Clear filters
					</a>
				<?php } ?>
			</div>

			<?php if (!$produits) { ?>

				<div class="fm-empty">
					<span class="fm-empty-icon"><i class="fa fa-leaf"></i></span>
					<h2>Nothing to show here yet</h2>
					<?php if ($motCle !== '') { ?>
						<p>No product matches &laquo;&nbsp;<?php echo fm_echapper($motCle); ?>&nbsp;&raquo;.
						   Try a shorter keyword, or browse the full catalogue.</p>
					<?php } else { ?>
						<p>This selection is empty for now. Browse the other categories to find what you need.</p>
					<?php } ?>
					<a class="btn" href="<?php echo fm_lien_produit('', 1); ?>">View all products</a>
				</div>

				<?php if ($suggestions) { ?>
					<div class="fm-suggest">
						<h3>Did you mean</h3>
						<ul>
							<?php foreach ($suggestions as $s) { ?>
								<li>
									<a href="<?php echo $fm_app; ?>products_detail/<?php echo (int) $s['id_prod']; ?>/">
										<?php echo fm_echapper($s['Designation']); ?>
									</a>
								</li>
							<?php } ?>
						</ul>
					</div>
				<?php } ?>

			<?php } else { ?>

				<div class="fm-pgrid">
					<?php foreach ($produits as $prod) {
						$id       = (int) $prod['id_prod'];
						$lien     = $fm_app . 'products_detail/' . $id . '/';
						$famille  = isset($catLabels[(int) $prod['Code_cat']])
						          ? $catLabels[(int) $prod['Code_cat']] : '';
					?>
					<article class="fm-pcard">
						<a class="fm-pcard-media" href="<?php echo $lien; ?>" tabindex="-1" aria-hidden="true">
							<img<?php echo fm_img_attrs(fm_photo_produit($prod, $fm_app), '(min-width: 1200px) 25vw, (min-width: 900px) 33vw, (min-width: 600px) 50vw, 100vw'); ?>
							     alt="<?php echo fm_echapper($prod['Designation']); ?>" loading="lazy">
							<?php if ($famille !== '') { ?>
								<span class="fm-pcard-tag"><?php echo fm_echapper($famille); ?></span>
							<?php } ?>
						</a>

						<div class="fm-pcard-body">
							<div class="fm-pcard-flags">
								<?php if ((int) $prod['Selectionne'] === 1) { ?>
									<span class="fm-flag fm-flag-star"><i class="fa fa-star" aria-hidden="true"></i> Featured</span>
								<?php } ?>
								<?php if ((int) $prod['Promotion'] === 1) { ?>
									<span class="fm-flag fm-flag-promo">Promotion</span>
								<?php } ?>
								<?php if ((int) $prod['Disponible'] === 1) { ?>
									<span class="fm-flag fm-flag-stock">In stock</span>
								<?php } ?>
							</div>

							<h3 class="fm-pcard-title">
								<a href="<?php echo $lien; ?>"><?php echo fm_echapper($prod['Designation']); ?></a>
							</h3>

							<p class="fm-pcard-desc"><?php echo fm_resume($prod['description']); ?></p>

							<div class="fm-pcard-foot">
								<span class="fm-pcard-ref">Ref. <?php echo fm_echapper($prod['Ref_prod']); ?></span>
								<a class="fm-pcard-link" href="<?php echo $lien; ?>">
									Details <i class="fa fa-angle-right" aria-hidden="true"></i>
								</a>
							</div>
						</div>
					</article>
					<?php } ?>
				</div>

				<?php
				/* ------------------------------------------------------ pagination */
				$qs = array();
				if ($idCat > 0)   { $qs[] = 'idCat=' . $idCat; }
				if ($motCle !== '') { $qs[] = 'motCle=' . rawurlencode($motCle); }
				if ($tag !== '')   { $qs[] = 'tags=' . rawurlencode($tag); }
				if ($featured)     { $qs[] = 'featured=1'; }
				$qs = implode('&', $qs);

				/* Sur une categorie seule, on garde la forme lisible
				   /products/8/Peppers/2/ ; sinon on retombe sur les parametres. */
				$lienPage = function ($p) use ($qs, $idCat, $motCle, $tag, $featured) {
					if ($idCat > 0 && $motCle === '' && $tag === '' && !$featured && $catCourante) {
						return fm_lien_categorie($catCourante['Code_cat'], $catCourante['slug'], $p);
					}
					return fm_lien_produit($qs, $p);
				};

				if ($nombreDePages > 1) {
					/* fenetre glissante de 5 pages autour de la page courante */
					$debut = max(1, $page - 2);
					$fin   = min($nombreDePages, $debut + 4);
					$debut = max(1, $fin - 4);
				?>
				<nav class="fm-pager" aria-label="Product pagination">
					<ul class="fm-pager-list">
						<li>
							<?php if ($page > 1) { ?>
								<a class="fm-pager-item" href="<?php echo $lienPage($page - 1); ?>" rel="prev">
									<i class="fa fa-angle-left" aria-hidden="true"></i> Previous
								</a>
							<?php } else { ?>
								<span class="fm-pager-item is-disabled"><i class="fa fa-angle-left" aria-hidden="true"></i> Previous</span>
							<?php } ?>
						</li>

						<?php for ($i = $debut; $i <= $fin; $i++) { ?>
							<li>
								<?php if ($i === $page) { ?>
									<span class="fm-pager-item is-active" aria-current="page"><?php echo $i; ?></span>
								<?php } else { ?>
									<a class="fm-pager-item" href="<?php echo $lienPage($i); ?>"><?php echo $i; ?></a>
								<?php } ?>
							</li>
						<?php } ?>

						<li>
							<?php if ($page < $nombreDePages) { ?>
								<a class="fm-pager-item" href="<?php echo $lienPage($page + 1); ?>" rel="next">
									Next <i class="fa fa-angle-right" aria-hidden="true"></i>
								</a>
							<?php } else { ?>
								<span class="fm-pager-item is-disabled">Next <i class="fa fa-angle-right" aria-hidden="true"></i></span>
							<?php } ?>
						</li>
					</ul>
				</nav>
				<?php } ?>

			<?php } ?>

		</div>
	</div>
</section>

<!-- ============================================================ AUTRES PAGES -->
<section class="fm-section fm-section-alt">
	<div class="fm-section-head">
		<span class="fm-eyebrow">Why Foodmax</span>
		<h2>What you get with every order</h2>
	</div>

	<div class="fm-quality-grid">
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-leaf"></i></span>
			<h3>Fresh</h3>
			<p>Harvested and packed within hours of picking.</p>
		</div>
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-leaf"></i></span>
			<h3>Traceable</h3>
			<p>Every pallet is traced back to its field.</p>
		</div>
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-snowflake-o"></i></span>
			<h3>Cold chain</h3>
			<p>Refrigerated containers door to door.</p>
		</div>
		<div class="fm-quality">
<span class="fm-quality-icon"><i class="fa fa-check-circle-o"></i></span>
			<h3>Documented</h3>
			<p>Documentation is prepared for each shipment and destination.</p>
		</div>
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-cube"></i></span>
			<h3>Custom packing</h3>
			<p>Private label and retail-ready formats.</p>
		</div>
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-ship"></i></span>
			<h3>Worldwide</h3>
			<p>Distributors worldwide, and several European countries in particular.</p>
		</div>
	</div>
</section>

<?php if (!$fm_estFiltre) { fm_guides_bloc($conn, '', "Buyer's guides", 'Season, packing and transport for each family, written for people who buy fresh produce for a living.', true); } ?>

<section class="fm-cta" style="background-image:url('<?php echo $fm_app; ?>img/banner-cta.jpg');">
	<div class="fm-cta-inner">
		<h2>Looking for a specific variety?</h2>
		<p>Tell us the product, the quantity and where it should go, and we will come back to you within one business day.</p>
		<a class="btn" href="<?php echo $fm_app; ?>contact/">Talk to our team</a>
	</div>
</section>

<?php require_once((__DIR__ . "/../includes/footer.php")); ?>

<?php require_once((__DIR__ . "/../includes/analyticstracking.php")); ?>

</body>
</html>
