<?php
/**
 * Fiche produit SEO FoodMax Group.
 *
 * Servie par .htaccess : /products/{slug}/ -> products/produit.php?slug={slug}
 * Affiche la fiche d'une famille de produits (origine, varietes, saison, tailles,
 * conditionnement, disponibilite, transport, marches, qualite, certifications)
 * puis les produits du catalogue correspondants et les guides voisins.
 *
 * 404 si la fiche n'existe pas ou n'est pas publiee.
 */
require_once(__DIR__ . "/../includes/paths.php");
require_once(__DIR__ . "/../includes/_header.php");
require_once(__DIR__ . "/../includes/blog_functions.php");
require_once(__DIR__ . "/../includes/seo.php");
require_once(__DIR__ . "/../includes/guides.php");

/* ------------------------------------------------------------ slug + fiche */
$slug = isset($_GET['slug']) && is_string($_GET['slug']) ? trim($_GET['slug']) : '';
if ($slug === '' || strlen($slug) > 100 || !preg_match('/^[a-z0-9-]+$/', $slug)) {
    http_response_code(404);
    require_once(__DIR__ . "/../includes/_404.php");
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    'SELECT id, slug, title, subtitle, origin, varieties, season, sizes,
            packaging, availability, transportation, destinations, quality,
            certifications, meta_title, meta_description, status, created_at, updated_at
     FROM product_seo WHERE slug = ? LIMIT 1'
);
if (!$stmt) {
    http_response_code(500);
    die('Database error.');
}
mysqli_stmt_bind_param($stmt, 's', $slug);
mysqli_stmt_execute($stmt);
$res  = mysqli_stmt_get_result($stmt);
$post = $res ? mysqli_fetch_assoc($res) : null;
mysqli_stmt_close($stmt);

if (!$post || $post['status'] !== 'published') {
    http_response_code(404);
    require_once(__DIR__ . "/../includes/_404.php");
    exit;
}

/* ------------------------------------------------------------ famille du catalogue */
/* Noms de categories acceptes pour chaque fiche (compares sans majuscules,
   sans espaces ni "_"). Le premier sert de libelle. */
$categoryMap = array(
    'tomatoes'     => array('Tomatoes'),
    'oranges'      => array('Citrus'),
    'lemons'       => array('Citrus'),
    'watermelon'   => array('Watermelon', 'Melons', 'Melon'),
    'peppers'      => array('Peppers'),
    'courgettes'   => array('Courgettes'),
    'berries'      => array('Berries'),
    'dried-fruits' => array('Dried fruits', 'Driedfruits'),
    'figs'         => array('Figs'),
    'eggplants'    => array('Eggplants'),
    'green-leaves' => array('Green leaves', 'Greenleaves'),
    'pits'         => array('Pits'),
);
$norm = function ($v) { return strtolower(preg_replace('/[^a-z]/i', '', (string) $v)); };

$categoryName = isset($categoryMap[$slug]) ? $categoryMap[$slug][0] : '';
$categoryLink = '';
$catCode      = 0;
if (isset($categoryMap[$slug])) {
    $voulus = array_map($norm, $categoryMap[$slug]);
    $catRs  = mysqli_query($conn, 'SELECT Code_cat, Nom_cat FROM categories ORDER BY Nom_cat ASC');
    if ($catRs) {
        while ($c = mysqli_fetch_assoc($catRs)) {
            if (in_array($norm($c['Nom_cat']), $voulus, true)) {
                $catCode      = (int) $c['Code_cat'];
                $nomUrl       = preg_replace('/[^A-Za-z0-9-]/', '', str_replace('_', '', $c['Nom_cat']));
                $categoryLink = $fm_app . 'products/' . $catCode . '/' . rawurlencode($nomUrl) . '/';
                break;
            }
        }
    }
}

/* Produits du catalogue pour cette famille (cartes + image de la fiche). */
$produitsFamille = array();
$imageFiche      = '';
if ($catCode > 0) {
    $stmtP = mysqli_prepare(
        $conn,
        'SELECT id_prod, Ref_prod, Designation, description, Photo
           FROM produits WHERE Code_cat = ?
          ORDER BY Selectionne DESC, Designation ASC LIMIT 8'
    );
    if ($stmtP) {
        mysqli_stmt_bind_param($stmtP, 'i', $catCode);
        mysqli_stmt_execute($stmtP);
        $rP = mysqli_stmt_get_result($stmtP);
        if ($rP) { while ($p = mysqli_fetch_assoc($rP)) { $produitsFamille[] = $p; } }
        mysqli_stmt_close($stmtP);
    }
    foreach ($produitsFamille as $p) {
        $u = fm_prod_photo($fm_app, $p['Ref_prod'], $p['Photo']);
        if (substr($u, -4) !== '.svg') { $imageFiche = $u; break; }
    }
}
$produitsFamille = array_slice($produitsFamille, 0, 4);

/* ------------------------------------------------------------ SEO */
$fm_titre = ($post['meta_title'] !== '' && $post['meta_title'] !== null ? $post['meta_title'] : $post['title'])
          . ' | ' . FM_SITE_NAME;
$fm_desc  = ($post['meta_description'] !== '' && $post['meta_description'] !== null)
    ? $post['meta_description']
    : fm_blog_texte($post['subtitle'], 158);
$fm_imgAbs = fm_img_absolue($fm_app, $imageFiche);
$fm_chemin = 'products/' . $post['slug'] . '/';

$fm_seo = array(
    'path'        => $fm_chemin,
    'title'       => $post['title'],
    'description' => $fm_desc,
    'image'       => $fm_imgAbs,
    'type'        => 'website',
    'jsonld'      => array(
        array(
            '@type'         => 'WebPage',
            'name'          => $post['title'],
            'description'   => $fm_desc,
            'url'           => fm_seo_url($fm_chemin),
            'inLanguage'    => 'en',
            'dateModified'  => fm_blog_date_iso($post['updated_at'] ? $post['updated_at'] : $post['created_at']),
            'isPartOf'      => array('@type' => 'WebSite', 'name' => FM_SITE_NAME, 'url' => fm_seo_url('')),
            'publisher'     => fm_seo_org(),
        ),
        fm_seo_breadcrumb(array(array('Home', ''), array('Products', 'products/'), array($post['title'], $fm_chemin))),
    ),
);
if ($fm_imgAbs !== '') { $fm_seo['jsonld'][0]['primaryImageOfPage'] = array('@type' => 'ImageObject', 'url' => $fm_imgAbs); }

$recents = fm_blog_recent($conn, 3);
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<title><?php echo fm_blog_echapper(fm_seo_title($fm_titre)); ?></title>
	<meta name="description" content="<?php echo fm_blog_echapper($fm_desc); ?>">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<?php fm_seo_head($fm_seo); ?>

	<link rel="stylesheet" type="text/css" href="<?php echo $fm_app; ?>css/phlox.css">
	<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,400i,600,600i,700,700i">
	<link rel="stylesheet" type="text/css" href="<?php echo $fm_app; ?>css/font-awesome.min.css">

	<script type="text/javascript" src="<?php echo $fm_app; ?>js/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo $fm_app; ?>js/setting.js"></script>

	<!-- Google tag (gtag.js) -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=G-ZPRWMT85SP"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag() { dataLayer.push(arguments); }
		gtag('js', new Date());
		gtag('config', 'G-ZPRWMT85SP');
	</script>
</head>

<?php require_once((__DIR__ . "/../includes/header-inc.php")); ?>

<!-- ============================================================ EN-TETE PAGE -->
<section class="fm-pagehead fm-pagehead-product">
	<div class="fm-pagehead-inner">
		<span class="fm-eyebrow"><?php echo $categoryName !== '' ? fm_blog_echapper($categoryName) : 'From Morocco'; ?></span>

		<h1><?php echo fm_blog_echapper($post['title']); ?></h1>

		<?php if ($post['subtitle'] !== '' && $post['subtitle'] !== null) { ?>
		<p><?php echo fm_blog_echapper($post['subtitle']); ?></p>
		<?php } ?>

		<nav class="fm-crumbs" aria-label="Breadcrumb">
			<ol>
				<li><a href="<?php echo $fm_app; ?>">Home</a></li>
				<li><a href="<?php echo $fm_app; ?>products/">Products</a></li>
				<li><?php echo fm_blog_echapper($post['title']); ?></li>
			</ol>
		</nav>
	</div>
</section>

<!-- =============================================================== FICHE -->
<section class="fm-section">
	<div class="fm-article-inner">

		<?php if ($imageFiche !== '') { ?>
		<div class="fm-article-hero">
			<img src="<?php echo fm_blog_echapper($imageFiche); ?>"
			     alt="<?php echo fm_blog_echapper($post['title']); ?>" loading="lazy">
		</div>
		<?php } ?>

		<?php
		/* Chaque champ devient une section : titre + texte ou liste.
		   Icones de Font Awesome 4.7 (celle du site). */
		$champs = array(
			array('origin',         'Origin',              'fa-map-marker'),
			array('varieties',      'Varieties',           'fa-leaf'),
			array('season',         'Season',              'fa-calendar'),
			array('sizes',          'Sizes',               'fa-balance-scale'),
			array('packaging',      'Packaging',           'fa-cube'),
			array('availability',   'Availability',        'fa-check-circle'),
			array('transportation', 'Transport',           'fa-truck'),
			array('destinations',   'Destination markets', 'fa-globe'),
			array('quality',        'Quality',             'fa-check-circle-o'),
			array('certifications', 'Certificates',        'fa-certificate'),
		);

		foreach ($champs as $champ) {
			$cle   = $champ[0];
			$val   = isset($post[$cle]) ? trim((string) $post[$cle]) : '';
			if ($val === '') { continue; }
			$val         = str_replace("\r\n", "\n", $val);
			$paragraphes = preg_split('/\n\s*\n/', $val);
		?>
		<div class="fm-seo-section">
			<h2><i class="fa fa-fw <?php echo $champ[2]; ?>" aria-hidden="true"></i> <?php echo $champ[1]; ?></h2>
			<?php foreach ($paragraphes as $para) {
				$para = trim($para);
				if ($para === '') { continue; }
				/* Une ligne "a | b | c" (ou plusieurs lignes) devient une liste. */
				if (strpos($para, '|') !== false || strpos($para, "\n") !== false) {
					$items = preg_split('/\s*\|\s*|\n+/', $para);
			?>
			<ul class="fm-seo-list">
				<?php foreach ($items as $item) {
					$item = trim($item);
					if ($item === '') { continue; }
					$pos = strpos($item, ':');
					if ($pos !== false && $pos < 40) { ?>
				<li><strong><?php echo fm_blog_echapper(substr($item, 0, $pos)); ?>:</strong> <?php echo fm_blog_echapper(trim(substr($item, $pos + 1))); ?></li>
				<?php } else { ?>
				<li><?php echo fm_blog_echapper($item); ?></li>
				<?php } } ?>
			</ul>
			<?php } else { ?>
			<p><?php echo fm_blog_echapper($para); ?></p>
			<?php } } ?>
		</div>
		<?php } ?>

		<div class="fm-article-product-link">
			<div class="fm-card">
				<span class="fm-eyebrow">Request a quote</span>
				<h3>Interested in this product?</h3>
				<p>
					Tell us which product you have in mind, how much you need and where it
					should go, and a real person from our team will come back to you within
					one business day with what is available and at what price.
				</p>
				<a class="btn" href="<?php echo $fm_app; ?>contact/">
					Contact us <i class="fa fa-arrow-right" aria-hidden="true"></i>
				</a>
			</div>
		</div>
	</div>
</section>

<!-- ======================================================= PRODUITS DU CATALOGUE -->
<?php if (!empty($produitsFamille)) { ?>
<section class="fm-section fm-section-alt">
	<div class="fm-section-head">
		<span class="fm-eyebrow">From our catalogue</span>
		<h2><?php echo fm_blog_echapper($categoryName); ?> you can ask us about</h2>
		<?php if ($categoryLink !== '') { ?>
		<p class="fm-section-sub">
			A few of the products in this family. <a href="<?php echo $categoryLink; ?>">See the whole range</a>.
		</p>
		<?php } ?>
	</div>

	<div class="fm-pgrid">
		<?php foreach ($produitsFamille as $prod) {
			$lien = $fm_app . 'products_detail/' . (int) $prod['id_prod'] . '/';
		?>
		<article class="fm-pcard">
			<a class="fm-pcard-media" href="<?php echo $lien; ?>" tabindex="-1" aria-hidden="true">
				<img src="<?php echo fm_blog_echapper(fm_prod_photo($fm_app, $prod['Ref_prod'], $prod['Photo'])); ?>"
				     alt="<?php echo fm_blog_echapper($prod['Designation']); ?>" loading="lazy">
			</a>
			<div class="fm-pcard-body">
				<h3 class="fm-pcard-title"><a href="<?php echo $lien; ?>"><?php echo fm_blog_echapper($prod['Designation']); ?></a></h3>
				<p class="fm-pcard-desc"><?php echo fm_blog_resume(isset($prod['description']) ? $prod['description'] : '', 110); ?></p>
			</div>
		</article>
		<?php } ?>
	</div>

	<?php if ($categoryLink !== '') { ?>
	<p style="text-align:center;margin-top:28px;">
		<a class="btn" href="<?php echo $categoryLink; ?>">See all <?php echo fm_blog_echapper(strtolower($categoryName)); ?> <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
	</p>
	<?php } ?>
</section>
<?php } ?>

<!-- =========================================================== AUTRES GUIDES -->
<?php fm_guides_bloc($conn, $post['slug'], 'Other buyer\'s guides', 'The same kind of information for the other families we work with.'); ?>

<!-- ============================================================ BLOG -->
<?php if (!empty($recents)) { ?>
<section class="fm-section fm-section-alt">
	<div class="fm-section-head">
		<span class="fm-eyebrow">From the blog</span>
		<h2>Keep reading</h2>
	</div>

	<div class="fm-pgrid">
		<?php foreach ($recents as $r) {
			$rSlug = rawurlencode($r['slug']);
			$rImg  = fm_blog_image_url($fm_app, $r);
		?>
		<article class="fm-pcard">
			<a class="fm-pcard-media" href="<?php echo $fm_app; ?>blog/<?php echo $rSlug; ?>/" tabindex="-1" aria-hidden="true">
				<img src="<?php echo fm_blog_echapper($rImg); ?>"
				     alt="<?php echo fm_blog_echapper($r['title']); ?>" loading="lazy">
			</a>
			<div class="fm-pcard-body">
				<h3 class="fm-pcard-title">
					<a href="<?php echo $fm_app; ?>blog/<?php echo $rSlug; ?>/"><?php echo fm_blog_echapper($r['title']); ?></a>
				</h3>
				<p class="fm-pcard-desc"><?php echo $r['excerpt'] !== '' && $r['excerpt'] !== null ? fm_blog_resume($r['excerpt']) : ''; ?></p>
				<div class="fm-pcard-foot">
					<span class="fm-pcard-ref"><i class="fa fa-calendar" aria-hidden="true"></i> <?php echo fm_blog_date_fr($r['created_at']); ?></span>
				</div>
			</div>
		</article>
		<?php } ?>
	</div>
</section>
<?php } ?>

<!-- ==================================================================== CTA -->
<section class="fm-cta">
	<div class="fm-cta-inner">
		<p class="fm-eyebrow">Next step</p>
		<h2>Let's talk about your next order</h2>
		<p>
			Whether you already know exactly what you need or you are only starting to
			look around, we would love to hear from you.
		</p>
		<div class="fm-cta-actions">
			<a class="btn" href="<?php echo $fm_app; ?>contact/">Contact our team</a>
			<a class="btn btn-outline" href="<?php echo $fm_app; ?>products/">Browse all products</a>
		</div>
	</div>
</section>

<?php require_once((__DIR__ . "/../includes/footer.php")); ?>
<?php require_once((__DIR__ . "/../includes/analyticstracking.php")); ?>

</body>
</html>
