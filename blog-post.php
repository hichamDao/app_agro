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

/* ------------------------------------------------------------ slug + article */
$slug = isset($_GET['slug']) ? trim((string) $_GET['slug']) : '';
$slug = $slug !== '' ? substr($slug, 0, 250) : '';

if ($slug === '') {
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
$recents = fm_blog_recent($conn, 3);

/* ------------------------------------------------------------ produits connexes */
$relatedProducts = array();
if ($post['product_link'] !== '' && $post['product_link'] !== null) {
    /* On extrait la categorie du produit depuis product_link (ex: "products/3/Citrus/") */
    $linkParts = explode('/', trim($post['product_link'], '/'));
    $catId = isset($linkParts[1]) ? (int) $linkParts[1] : 0;
    if ($catId > 0) {
        $r = mysqli_query($conn, 'SELECT id_prod, Ref_prod, Designation, description FROM produits WHERE Code_cat = ' . $catId . ' ORDER BY Designation ASC LIMIT 4');
        if ($r) {
            while ($p = mysqli_fetch_assoc($r)) {
                $relatedProducts[] = $p;
            }
        }
    }
}

/* ------------------------------------------------------------ SEO */
$fm_titre       = $post['meta_title'] !== '' && $post['meta_title'] !== null
    ? $post['meta_title'] . ' | Foodmax'
    : $post['title'] . ' | Blog | Foodmax';
$fm_sousTitre = $post['meta_description'] !== '' && $post['meta_description'] !== null
    ? $post['meta_description']
    : fm_blog_resume(htmlspecialchars_decode($post['excerpt'], ENT_QUOTES), 160);
$fm_ogImage = fm_blog_image_url($fm_app, $post);
$dateArticle = fm_blog_date_fr($post['created_at']);
$catArticle = $post['category'] ? fm_blog_echapper($post['category']) : '';
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
	<meta property="og:image" content="<?php echo fm_blog_echapper($fm_ogImage); ?>">
	<meta property="og:type" content="article">
	<meta property="og:url" content="<?php echo $fm_app . 'blog/' . rawurlencode($post['slug']) . '/'; ?>">
	<meta property="article:published_time" content="<?php echo $post['created_at']; ?>">
	<meta property="article:section" content="<?php echo $catArticle; ?>">

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
				Related : <?php echo fm_blog_echapper($post['product_label']); ?>
			<?php } ?>
		</p>

		<nav class="fm-crumbs" aria-label="Fil d'Ariane">
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
			<?php echo $post['excerpt']; ?>
		</p>
		<?php } ?>

		<div class="fm-article-body">
			<?php echo $post['content']; ?>
		</div>

		<?php if ($post['product_link'] !== '' && $post['product_link'] !== null) { ?>
		<div class="fm-article-product-link">
			<div class="fm-card">
				<span class="fm-eyebrow">Related product</span>
				<h3><?php echo fm_blog_echapper($post['product_label'] !== '' && $post['product_label'] !== null ? $post['product_label'] : 'Our products'); ?></h3>
				<p>
					<?php echo fm_blog_echapper($post['excerpt'] !== '' && $post['excerpt'] !== null
                        ? fm_blog_resume($post['excerpt'])
                        : fm_blog_resume($post['content'])); ?>
				</p>
				<a class="btn btn-sm" href="<?php echo $fm_app . $post['product_link']; ?>">
					Discover this product <i class="fa fa-arrow-right" aria-hidden="true"></i>
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
		<span class="fm-eyebrow">More from this category</span>
		<h2>Other products you might like</h2>
	</div>

	<div class="fm-pgrid">
		<?php foreach ($relatedProducts as $prod):
			$lien = $fm_app . 'products_detail/' . (int) $prod['id_prod'] . '/';
			$resume = fm_blog_resume($prod['description'] ?? '', 110);
		?>
		<article class="fm-pcard">
			<a class="fm-pcard-media" href="<?php echo $lien; ?>" tabindex="-1" aria-hidden="true">
				<img src="<?php echo $fm_app; ?>img/product-placeholder.svg"
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
		<h2>Enjoyed this article?</h2>
		<p class="fm-section-sub">
			Share it with your network or contact our team to discuss
			your sourcing needs.
		</p>
	</div>
	<div class="fm-cta-inner">
		<div class="fm-cta-actions">
			<a class="btn" href="<?php echo $fm_app; ?>contact/">Contact our team</a>
			<a class="btn btn-outline" href="<?php echo $fm_app; ?>blog/">All articles</a>
		</div>
	</div>
</section>

<?php require_once((__DIR__ . "/includes/footer.php")); ?>
<?php require_once((__DIR__ . "/includes/analyticstracking.php")); ?>

</body>
</html>
