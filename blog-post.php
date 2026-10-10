<?php
/**
 * Article de blog individuel.
 *
 * Servi par la rewrite rule .htaccess : /blog/{slug}/ -> blog-post.php?slug={slug}
 * Affiche le contenu HTML de l'article, les liens produits connexes, et
 * une selection d'autres articles recents.
 *
 * 404 si l'article n'existe pas ou n'est pas publie.
 */
require_once(__DIR__ . "/includes/paths.php");
require_once(__DIR__ . "/includes/_header.php");
require_once(__DIR__ . "/includes/blog_functions.php");
require_once(__DIR__ . "/includes/seo.php");

/* ------------------------------------------------------------ slug + article */
$slug = isset($_GET['slug']) ? trim((string) $_GET['slug']) : '';
$slug = $slug !== '' ? substr($slug, 0, 250) : '';

if ($slug === '' || !preg_match('/^[A-Za-z0-9-]+$/', $slug)) {
    http_response_code(404);
    require_once(__DIR__ . "/includes/_404.php");
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    'SELECT id, title, slug, excerpt, content, category, product_link, product_label,
            image, meta_title, meta_description, status, created_at, updated_at
     FROM blog_posts WHERE slug = ? LIMIT 1'
);
if (!$stmt) {
    http_response_code(500);
    die('Database error.');
}

mysqli_stmt_bind_param($stmt, 's', $slug);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$post = $res ? mysqli_fetch_assoc($res) : null;
mysqli_stmt_close($stmt);

if (!$post || $post['status'] !== 'published') {
    http_response_code(404);
    require_once(__DIR__ . "/includes/_404.php");
    exit;
}

/* ------------------------------------------------------------ articles recents */
$recents = array();
foreach (fm_blog_recent($conn, 4) as $fm_r) {
    if ((int) $fm_r['id'] !== (int) $post['id'] && count($recents) < 3) { $recents[] = $fm_r; }
}

/* ------------------------------------------------------------ produits connexes */
$relatedProducts = array();
$lienProduit     = fm_blog_lien($fm_app, $post['product_link']);
if ($lienProduit !== '') {
    /* La categorie vient du lien saisi, par ex. "products/3/Citrus/". */
    $chemin    = preg_replace('~^https?://[^/]+/~i', '', trim((string) $post['product_link']));
    $linkParts = explode('/', trim($chemin, '/'));
    $catId = (isset($linkParts[0], $linkParts[1]) && $linkParts[0] === 'products') ? (int) $linkParts[1] : 0;
    if ($catId > 0) {
        $stmtP = mysqli_prepare(
            $conn,
            'SELECT id_prod, Ref_prod, Designation, description, Photo
               FROM produits WHERE Code_cat = ?
              ORDER BY Selectionne DESC, Designation ASC LIMIT 4'
        );
        if ($stmtP) {
            mysqli_stmt_bind_param($stmtP, 'i', $catId);
            mysqli_stmt_execute($stmtP);
            $rP = mysqli_stmt_get_result($stmtP);
            if ($rP) {
                while ($p = mysqli_fetch_assoc($rP)) { $relatedProducts[] = $p; }
            }
            mysqli_stmt_close($stmtP);
        }
    }
}

/* ------------------------------------------------------------ SEO */
$fm_titre = ($post['meta_title'] !== '' && $post['meta_title'] !== null ? $post['meta_title'] : $post['title'])
          . ' | ' . FM_SITE_NAME;
$fm_sousTitre = ($post['meta_description'] !== '' && $post['meta_description'] !== null)
    ? $post['meta_description']
    : fm_blog_texte($post['excerpt'] !== '' ? $post['excerpt'] : $post['content'], 158);
$fm_ogImage  = fm_blog_image_url($fm_app, $post);
$fm_imgAbs   = fm_img_absolue($fm_app, $fm_ogImage);
$dateArticle = fm_blog_date_fr($post['created_at']);
$catArticle  = $post['category'] ? fm_blog_echapper($post['category']) : '';
$fm_cheminArticle = 'blog/' . $post['slug'] . '/';

$fm_article = array(
    '@type'            => 'Article',
    'headline'         => fm_blog_texte($post['title'], 110),
    'description'      => $fm_sousTitre,
    'datePublished'    => fm_blog_date_iso($post['created_at']),
    'dateModified'     => fm_blog_date_iso($post['updated_at'] ? $post['updated_at'] : $post['created_at']),
    'author'           => fm_seo_org(),
    'publisher'        => fm_seo_org(),
    'mainEntityOfPage' => array('@type' => 'WebPage', '@id' => fm_seo_url($fm_cheminArticle)),
);
if ($fm_imgAbs !== '') { $fm_article['image'] = $fm_imgAbs; }
if ($post['category']) { $fm_article['articleSection'] = $post['category']; }

$fm_seo = array(
    'path'        => $fm_cheminArticle,
    'title'       => $post['title'],
    'description' => $fm_sousTitre,
    'image'       => $fm_imgAbs,
    'type'        => 'article',
    'published'   => fm_blog_date_iso($post['created_at']),
    'modified'    => fm_blog_date_iso($post['updated_at'] ? $post['updated_at'] : $post['created_at']),
    'section'     => (string) $post['category'],
    'jsonld'      => array(
        $fm_article,
        fm_seo_breadcrumb(array(array('Home', ''), array('Blog', 'blog/'), array($post['title'], $fm_cheminArticle))),
    ),
);
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<title><?php echo fm_blog_echapper(fm_seo_title($fm_titre)); ?></title>
	<meta name="description" content="<?php echo fm_blog_echapper($fm_sousTitre); ?>">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

<?php fm_seo_head($fm_seo); ?>

	<link rel="stylesheet" type="text/css" href="<?php echo $fm_app; ?>css/phlox.css<?php echo fm_ver('css/phlox.css'); ?>">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,400i,600,600i,700,700i&display=swap">
	<link rel="stylesheet" type="text/css" href="<?php echo $fm_app; ?>css/font-awesome.min.css<?php echo fm_ver('css/font-awesome.min.css'); ?>">

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

<?php require_once((__DIR__ . "/includes/header-inc.php")); ?>

<!-- ============================================================ EN-TETE PAGE -->
<section class="fm-pagehead fm-pagehead-blog">
	<div class="fm-pagehead-inner">
		<?php if ($catArticle !== '') { ?>
		<span class="fm-eyebrow"><?php echo $catArticle; ?></span>
		<?php } else { ?>
		<span class="fm-eyebrow">From the blog</span>
		<?php } ?>
		<h1><?php echo fm_blog_echapper($post['title']); ?></h1>
		<p>
			<i class="fa fa-calendar" aria-hidden="true"></i>
			<?php echo $dateArticle; ?>
			<?php if ($post['product_label'] !== '' && $post['product_label'] !== null) { ?>
				&middot; <i class="fa fa-cube" aria-hidden="true"></i>
				Related product: <?php echo fm_blog_echapper($post['product_label']); ?>
			<?php } ?>
		</p>

		<nav class="fm-crumbs" aria-label="Breadcrumb">
			<ol>
				<li><a href="<?php echo $fm_app; ?>">Home</a></li>
				<li><a href="<?php echo $fm_app; ?>blog/">Blog</a></li>
				<li><?php echo fm_blog_echapper($post['title']); ?></li>
			</ol>
		</nav>
	</div>
</section>

<!-- =============================================================== ARTICLE -->
<section class="fm-section">
	<div class="fm-article-inner">

		<?php if ($fm_ogImage !== $fm_app . 'img/blog-placeholder.svg') { ?>
		<div class="fm-article-hero">
			<img src="<?php echo fm_blog_echapper($fm_ogImage); ?>"
			     alt="<?php echo fm_blog_echapper($post['title']); ?>" loading="lazy">
		</div>
		<?php } ?>

		<?php if ($post['excerpt'] !== '' && $post['excerpt'] !== null) { ?>
		<p class="fm-article-lead">
			<?php echo fm_blog_echapper($post['excerpt']); ?>
		</p>
		<?php } ?>

		<div class="fm-article-body">
			<?php echo fm_blog_nettoyer($post['content'], $fm_app); ?>
		</div>

		<?php if ($lienProduit !== '') { ?>
		<div class="fm-article-product-link">
			<div class="fm-card">
				<span class="fm-eyebrow">Related product</span>
				<h3><?php echo fm_blog_echapper($post['product_label'] !== '' && $post['product_label'] !== null ? $post['product_label'] : 'Our products'); ?></h3>
				<p>
					If this article made you curious about the product itself, you can
					see our range and ask us what is available right now.
				</p>
				<a class="btn btn-sm" href="<?php echo $lienProduit; ?>">
					See the products <i class="fa fa-arrow-right" aria-hidden="true"></i>
				</a>
			</div>
		</div>
		<?php } ?>
	</div>
</section>

<!-- ============================================================ RELATED PRODUCTS -->
<?php if (!empty($relatedProducts)) { ?>
<section class="fm-section fm-section-alt">
	<div class="fm-section-head">
		<span class="fm-eyebrow">From the same family</span>
		<h2>Products you may want to look at next</h2>
	</div>

	<div class="fm-pgrid">
		<?php foreach ($relatedProducts as $prod):
			$lien = $fm_app . 'products_detail/' . (int) $prod['id_prod'] . '/';
			$resume = fm_blog_resume(isset($prod['description']) ? $prod['description'] : '', 110);
		?>
		<article class="fm-pcard">
			<a class="fm-pcard-media" href="<?php echo $lien; ?>" tabindex="-1" aria-hidden="true">
				<img<?php echo fm_img_attrs(fm_prod_photo($fm_app, $prod['Ref_prod'], $prod['Photo']), '(min-width: 1200px) 25vw, (min-width: 900px) 33vw, (min-width: 600px) 50vw, 100vw'); ?>
				     alt="<?php echo fm_blog_echapper($prod['Designation']); ?>" loading="lazy">
			</a>
			<div class="fm-pcard-body">
				<h3 class="fm-pcard-title">
					<a href="<?php echo $lien; ?>"><?php echo fm_blog_echapper($prod['Designation']); ?></a>
				</h3>
				<p class="fm-pcard-desc"><?php echo $resume; ?></p>
				<div class="fm-pcard-foot">
					<span class="fm-pcard-ref">Ref. <?php echo fm_blog_echapper($prod['Ref_prod']); ?></span>
				</div>
			</div>
		</article>
		<?php endforeach; ?>
	</div>
</section>
<?php } ?>

<!-- ============================================================ RECENT ARTICLES -->
<?php if (!empty($recents)) { ?>
<section class="fm-section">
	<div class="fm-section-head">
		<span class="fm-eyebrow">Keep reading</span>
		<h2>More from the blog</h2>
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
					<?php echo $r['excerpt'] !== '' && $r['excerpt'] !== null
                        ? fm_blog_resume($r['excerpt'])
                        : fm_blog_resume($r['content']); ?>
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
<?php } ?>

<!-- ================================================================ SHARE / CTA -->
<section class="fm-section fm-section-alt">
	<div class="fm-section-head">
		<h2>Did this help?</h2>
		<p class="fm-section-sub">
			If you would like to talk about your own sourcing needs, or if
			something in this article raised a question, write to us and a real
			person from our team will answer within one business day.
		</p>
	</div>
	<div class="fm-cta-inner">
		<div class="fm-cta-actions">
			<a class="btn" href="<?php echo $fm_app; ?>contact/">Write to our team</a>
			<a class="btn btn-outline" href="<?php echo $fm_app; ?>blog/">All articles</a>
		</div>
	</div>
</section>

<?php require_once((__DIR__ . "/includes/footer.php")); ?>
<?php require_once((__DIR__ . "/includes/analyticstracking.php")); ?>

</body>
</html>
