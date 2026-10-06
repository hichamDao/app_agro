<?php
/**
 * Page produit SEO Foodmax Group.
 *
 * Servi par la rewrite rule .htaccess : /products/{slug}/ -> produits/produit.php?slug={slug}
 * Affiche une fiche produit complete : origine, varietes, saison, tailles,
 * conditionnement, disponibilite, transport, marches, qualite, certifications.
 *
 * 404 si l'article n'existe pas ou n'est pas publie.
 */
require_once(__DIR__ . "/../includes/paths.php");
require_once(__DIR__ . "/../includes/_header.php");
require_once(__DIR__ . "/../includes/blog_functions.php");

/* ------------------------------------------------------------ slug + produit */
$slug = isset($_GET['slug']) ? trim((string) $_GET['slug']) : '';
$slug = $slug !== '' ? substr($slug, 0, 100) : '';

/* Normalisation : on retire les caracteres non-alphanumeriques (securite). */
$cleanSlug = preg_replace('/[^a-z0-9-]/', '', $slug);

if ($cleanSlug === '' || $cleanSlug !== $slug) {
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

mysqli_stmt_bind_param($stmt, 's', $cleanSlug);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$post = $res ? mysqli_fetch_assoc($res) : null;
mysqli_stmt_close($stmt);

if (!$post || $post['status'] !== 'published') {
    http_response_code(404);
    require_once(__DIR__ . "/../includes/_404.php");
    exit;
}

/* ------------------------------------------------------------ categories associees (pour le lien catalogue) */
$categories = array();
$catSql = "SELECT Code_cat, Nom_cat FROM categories ORDER BY Nom_cat ASC";
$catRs = mysqli_query($conn, $catSql);
if ($catRs) {
    while ($c = mysqli_fetch_assoc($catRs)) {
        $categories[] = $c;
    }
}

/* Recherche la categorie produit correspondant au slug. */
$categoryMap = array(
    'tomatoes'    => 'Tomatoes',
    'oranges'     => 'Citrus',
    'lemons'      => 'Citrus',
    'watermelon'  => 'Watermelon',
    'peppers'     => 'Peppers',
    'courgettes'  => 'Courgettes',
    'berries'     => 'Berries',
    'dried-fruits'=> 'Dried fruits',
    'figs'        => 'Figs',
);
$categoryName = isset($categoryMap[$cleanSlug]) ? $categoryMap[$cleanSlug] : '';
$categoryLink = '';
foreach ($categories as $c) {
    $nomCat = str_replace('_', ' ', $c['Nom_cat']);
    if (strtolower($nomCat) === strtolower($categoryName) || strtolower($c['Nom_cat']) === strtolower(str_replace(' ', '', $nomCat))) {
        $categoryLink = $fm_app . 'products/' . (int) $c['Code_cat'] . '/' . rawurlencode(str_replace('_', '', $c['Nom_cat'])) . '/';
        break;
    }
}

/* ------------------------------------------------------------ donnees produit */
$fm_titre       = $post['meta_title'] !== '' && $post['meta_title'] !== null
    ? $post['meta_title'] . ' | Foodmax'
    : $post['title'];
$fm_sousTitre   = $post['meta_description'] !== '' && $post['meta_description'] !== null
    ? $post['meta_description']
    : fm_blog_resume($post['subtitle'], 160);
$fm_image       = fm_blog_image_url($fm_app, $post);
$catArticle     = $post['title'];
$dateArticle    = fm_blog_date_fr($post['created_at']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<title><?php echo fm_blog_echapper($fm_titre); ?></title>
	<meta name="description" content="<?php echo fm_blog_echapper($fm_sousTitre); ?>">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<meta property="og:title" content="<?php echo fm_blog_echapper($post['title']); ?>">
	<meta property="og:description" content="<?php echo fm_blog_echapper($fm_sousTitre); ?>">
	<meta property="og:image" content="<?php echo fm_blog_echapper($fm_image); ?>">
	<meta property="og:type" content="article">
	<meta property="og:url" content="<?php echo $fm_app . 'products/' . rawurlencode($post['slug']) . '/'; ?>">

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
		<?php if ($categoryName !== ''): ?>
		<span class="fm-eyebrow"><?php echo fm_blog_echapper($categoryName); ?></span>
		<?php else: ?>
		<span class="fm-eyebrow">From Morocco</span>
		<?php endif; ?>

		<h1><?php echo fm_blog_echapper($post['title']); ?></h1>

		<?php if ($post['subtitle'] !== '' && $post['subtitle'] !== null): ?>
		<p><?php echo fm_blog_echapper($post['subtitle']); ?></p>
		<?php endif; ?>

		<nav class="fm-crumbs" aria-label="Fil d'Ariane">
			<ol>
				<li><a href="<?php echo $fm_app; ?>">Home</a></li>
				<li><a href="<?php echo $fm_app; ?>products/">Products</a></li>
				<li><?php echo fm_blog_echapper($post['title']); ?></li>
			</ol>
		</nav>
	</div>
</section>

<!-- =============================================================== PRODUIT -->
<section class="fm-section">
	<div class="fm-article-inner">

		<?php if ($fm_image !== $fm_app . 'img/blog-placeholder.svg'): ?>
		<div class="fm-article-hero">
			<img src="<?php echo $fm_blog_echapper($fm_image); ?>"
			     alt="<?php echo fm_blog_echapper($post['title']); ?>" loading="lazy">
		</div>
		<?php endif; ?>

		<?php if ($post['subtitle'] !== '' && $post['subtitle'] !== null): ?>
		<p class="fm-article-lead">
			<?php echo fm_blog_echapper($post['subtitle']); ?>
		</p>
		<?php endif; ?>

		<?php
		/* ---------------------------------------------------------- champs SEO */
		/* Chaque champ est rendu comme une section avec titre + contenu. */
		$champs = array(
			array('origin',          'Origin',             'fa-map-marker'),
			array('varieties',       'Varieties',          'fa-seedling'),
			array('season',          'Season',             'fa-calendar'),
			array('sizes',           'Sizes',              'fa-balance-scale'),
			array('packaging',       'Packaging',          'fa-box'),
			array('availability',    'Availability',       'fa-check-circle'),
			array('transportation',  'Transportation',     'fa-shipping-fast'),
			array('destinations',    'Destination markets', 'fa-globe'),
			array('quality',         'Quality',            'fa-check-circle-o'),
			array('certifications',  'Certifications',     'fa-award'),
		);
		?>

		<?php foreach ($champs as $champ):
			$cle   = $champ[0];
			$label = $champ[1];
			$icone = $champ[2];
			$val = isset($post[$cle]) ? trim((string) $post[$cle]) : '';
			if ($val === '') continue;
			/* Si la valeur contient des sauts de ligne, on les transforme en paragraphs. */
			$val = str_replace("\r\n", "\n", $val);
			$paragraphes = preg_split('/\n\s*\n/', $val);
		?>
		<div class="fm-seo-section">
			<h2><i class="fa fa-fw <?php echo $icone; ?>" aria-hidden="true"></i> <?php echo $label; ?></h2>
			<?php foreach ($paragraphes as $p):
				$p = trim($p);
				if ($p === '') continue;
				/* Si le paragraphe ressemble a une liste à puces (lignes avec |), on le formate en liste. */
				if (preg_match('/^[A-Za-z].*\|.*\n/m', $p)) {
					$lignes = array_filter(array_map('trim', explode("\n", $p)));
				?>
				<ul class="fm-seo-list">
					<?php foreach ($lignes as $ligne):
						/* On split sur le premier | pour séparer le label du contenu. */
						$parts = preg_split('/\s*\|\s*/', $ligne, 2);
					?>
					<li>
						<?php if (isset($parts[1]) && $parts[1] !== ''): ?>
						<strong><?php echo fm_blog_echapper($parts[0]); ?>:</strong> <?php echo fm_blog_echapper($parts[1]); ?>
						<?php else: ?>
						<?php echo fm_blog_echapper($ligne); ?>
						<?php endif; ?>
					</li>
					<?php endforeach; ?>
				</ul>
				<?php } else { ?>
				<p><?php echo fm_blog_echapper($p); ?></p>
				<?php } ?>
			<?php endforeach; ?>
		</div>
		<?php endforeach; ?>

		<?php if ($categoryLink !== ''): ?>
		<div class="fm-article-product-link">
			<div class="fm-card">
				<span class="fm-eyebrow">See the full catalogue</span>
				<h3><?php echo fm_blog_echapper($categoryName); ?> products</h3>
				<p>
					Browse our complete selection of <?php echo strtolower(fm_blog_echapper($categoryName)); ?>,
					check availability and request a quote for your next order.
				</p>
				<a class="btn" href="<?php echo $categoryLink; ?>">
					View products <i class="fa fa-arrow-right" aria-hidden="true"></i>
				</a>
			</div>
		</div>
		<?php endif; ?>

		<div class="fm-article-product-link">
			<div class="fm-card">
				<span class="fm-eyebrow">Request a quote</span>
				<h3>Need this product?</h3>
				<p>
					Tell us the product, the volume and the destination &mdash; and we will reply
					within one business day with availability and pricing.
				</p>
				<a class="btn" href="<?php echo $fm_app; ?>contact/">
					Request a quote <i class="fa fa-arrow-right" aria-hidden="true"></i>
				</a>
			</div>
		</div>
	</div>
</section>

<!-- ============================================================ RECENT ARTICLES -->
<?php $recents = fm_blog_recent($conn, 3); ?>
<?php if (!empty($recents)): ?>
<section class="fm-section fm-section-alt">
	<div class="fm-section-head">
		<span class="fm-eyebrow">From the blog</span>
		<h2>Related articles</h2>
	</div>

	<div class="fm-pgrid">
		<?php foreach ($recents as $r):
			$rSlug = rawurlencode($r['slug']);
			$rImg  = fm_blog_image_url($fm_app, $r);
		?>
		<article class="fm-pcard">
			<a class="fm-pcard-media" href="<?php echo $fm_app; ?>blog/<?php echo $rSlug; ?>/" aria-hidden="true">
				<img src="<?php echo $rImg; ?>"
				     alt="<?php echo fm_blog_echapper($r['title']); ?>" loading="lazy">
			</a>
			<div class="fm-pcard-body">
				<h3 class="fm-pcard-title">
					<a href="<?php echo $fm_app; ?>blog/<?php echo $rSlug; ?>/"><?php echo fm_blog_echapper($r['title']); ?></a>
				</h3>
				<p class="fm-pcard-desc">
					<?php echo $r['excerpt'] !== '' ? fm_blog_resume($r['excerpt']) : fm_blog_resume($r['content']); ?>
				</p>
				<div class="fm-pcard-foot">
					<span class="fm-pcard-ref">
						<i class="fa fa-calendar" aria-hidden="true"></i>
						<?php echo fm_blog_date_fr($r['created_at']); ?>
					</span>
				</div>
			</div>
		</article>
		<?php endforeach; ?>
	</div>
</section>
<?php endif; ?>

<!-- ================================================================ SHARE / CTA -->
<section class="fm-section fm-section-alt">
	<div class="fm-section-head">
		<span class="fm-eyebrow">Ready to source?</span>
		<h2>Let's talk about your next order</h2>
		<p class="fm-section-sub">
			Whether you already know exactly what you need or are just starting
			to look around, we would love to hear from you.
		</p>
	</div>
	<div class="fm-cta-inner">
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
