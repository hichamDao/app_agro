<?php
/**
 * Blog FoodMax Group.
 *
 * Liste les articles publies avec pagination et filtre par categorie.
 * Chaque article peut etre relie a une page produit (product_link) pour
 * guider les lecteurs vers le catalogue.
 *
 * Reecriture : /blog/ est servi par ce fichier, /blog/{slug}/ par
 * blog-post.php. Voir .htaccess.
 */
require_once(__DIR__ . "/includes/paths.php");
require_once(__DIR__ . "/includes/_header.php");
require_once(__DIR__ . "/includes/blog_functions.php");
require_once(__DIR__ . "/includes/seo.php");

/* ------------------------------------------------------------ parametres URL */
$categorie = isset($_GET['category']) ? trim((string) $_GET['category']) : '';
$page      = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

define('FM_BLOG_PAR_PAGE', 9);

$offset = ($page - 1) * FM_BLOG_PAR_PAGE;

/* ------------------------------------------------------------ construction WHERE */
$where  = array('status = \'published\'');
$params = array();
$types  = '';

if ($categorie !== '') {
    $where[]  = 'category = ?';
    $params[] = $categorie;
    $types   .= 's';
}

$sqlWhere = implode(' AND ', $where);

/* ------------------------------------------------------------ total + pagination */
$nombreTotal = 0;
$stmt = mysqli_prepare($conn, 'SELECT COUNT(*) FROM blog_posts WHERE ' . $sqlWhere);
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

/* ------------------------------------------------------------ articles */
$articles = array();
$stmt = mysqli_prepare(
    $conn,
    'SELECT id, title, slug, excerpt, content, category, product_link, product_label,
            image, meta_title, meta_description, status, created_at, updated_at
     FROM blog_posts WHERE ' . $sqlWhere . '
     ORDER BY created_at DESC LIMIT ? OFFSET ?'
);
if ($stmt) {
    $limit = FM_BLOG_PAR_PAGE;
    $bindVals = $params;
    $bindVals[] = $limit;
    $bindVals[] = $offset;
    mysqli_stmt_bind_param($stmt, $types . 'ii', ...$bindVals);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res) {
        while ($a = mysqli_fetch_assoc($res)) { $articles[] = $a; }
    }
    mysqli_stmt_close($stmt);
}

/* ------------------------------------------------------------ categories */
$categories = fm_blog_categories($conn);

/* ------------------------------------------------------------ chemin d'une page de la liste */
$fm_blog_chemin = function ($p) use ($categorie) {
    $q = array();
    if ($categorie !== '') { $q[] = 'category=' . rawurlencode($categorie); }
    if ($p > 1)            { $q[] = 'page=' . (int) $p; }
    return 'blog/' . ($q ? '?' . implode('&', $q) : '');
};

/* ------------------------------------------------------------ titres SEO */
$suffixePage = $page > 1 ? ' (page ' . $page . ')' : '';
if ($categorie !== '') {
    $fm_titre     = $categorie . ': articles and guides' . $suffixePage . ' | ' . FM_SITE_NAME;
    $fm_sousTitre = 'Our articles about ' . $categorie . ': what to know about the season, the growing and the shipping, written by the FoodMax Group team.';
} else {
    $fm_titre     = 'Moroccan fresh produce blog: guides and season notes' . $suffixePage . ' | ' . FM_SITE_NAME;
    $fm_sousTitre = 'Plain-language guides from FoodMax Group on Moroccan fruit and vegetables: seasons, packing, the cold chain and how exporting really works.';
}

$fm_seo = array(
    'path'        => $fm_blog_chemin($page),
    'title'       => $fm_titre,
    'description' => $fm_sousTitre,
    'type'        => 'website',
    'prev'        => $page > 1 ? $fm_blog_chemin($page - 1) : '',
    'next'        => $page < $nombrePages ? $fm_blog_chemin($page + 1) : '',
    'jsonld'      => array(
        array(
            '@type'       => 'Blog',
            'name'        => 'FoodMax Group blog',
            'url'         => fm_seo_url('blog/'),
            'description' => $fm_sousTitre,
            'publisher'   => fm_seo_org(),
        ),
        fm_seo_breadcrumb(array(array('Home', ''), array('Blog', 'blog/'))),
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
		<span class="fm-eyebrow">From the blog</span>
		<h1>Moroccan fresh produce: guides and field notes</h1>
		<p>
			Here we write about the things buyers ask us most, how the seasons run,
			what happens between the farm and your warehouse, and why the small
			details make such a difference, in plain words and without the jargon.
		</p>

		<nav class="fm-crumbs" aria-label="Breadcrumb">
			<ol>
				<li><a href="<?php echo $fm_app; ?>">Home</a></li>
				<li>Blog</li>
			</ol>
		</nav>
	</div>
</section>

<!-- =============================================================== BLOG GRID -->
<section class="fm-section">
	<div class="fm-section-head">
		<span class="fm-eyebrow">Latest articles</span>
		<h2><?php echo $categorie !== '' ? fm_blog_echapper($categorie) : 'All articles'; ?></h2>
		<p class="fm-section-sub">
			<?php echo $nombreTotal; ?> article<?php echo ($nombreTotal != 1) ? 's' : ''; ?>
			<?php echo $categorie !== '' ? 'about <strong>' . fm_blog_echapper($categorie) . '</strong>' : 'so far'; ?><?php if ($categorie !== '') { ?>,
			<a href="<?php echo $fm_app; ?>blog/">see all articles</a><?php } ?>.
		</p>
	</div>

	<?php if (!$articles) { ?>
		<div class="fm-empty">
			<span class="fm-empty-icon"><i class="fa fa-leaf"></i></span>
			<h2>Nothing here yet</h2>
			<p>We are still writing the next articles. In the meantime, you are very welcome to browse our <a href="<?php echo $fm_app; ?>products/">products</a> or to <a href="<?php echo $fm_app; ?>contact/">ask us a question</a> directly.</p>
		</div>
	<?php } else { ?>
	<div class="fm-blog-grid">
		<?php foreach ($articles as $post):
			$slug  = rawurlencode($post['slug']);
			$img   = fm_blog_image_url($fm_app, $post);
			$cat   = $post['category'] ? fm_blog_echapper($post['category']) : '';
			$date  = fm_blog_date_fr($post['created_at']);
		?>
		<article class="fm-bcard">
			<a class="fm-bcard-media" href="<?php echo $fm_app; ?>blog/<?php echo $slug; ?>/">
				<img src="<?php echo $img; ?>"
				     alt="<?php echo fm_blog_echapper($post['title']); ?>" loading="lazy">
			</a>
			<div class="fm-bcard-body">
				<?php if ($cat !== '') { ?>
				<span class="fm-bcard-cat"><?php echo $cat; ?></span>
				<?php } ?>

				<h3 class="fm-bcard-title">
					<a href="<?php echo $fm_app; ?>blog/<?php echo $slug; ?>/"><?php echo fm_blog_echapper($post['title']); ?></a>
				</h3>

				<p class="fm-bcard-desc">
					<?php echo $post['excerpt'] !== '' && $post['excerpt'] !== null
                        ? fm_blog_resume($post['excerpt'])
                        : fm_blog_resume($post['content']); ?>
				</p>

				<div class="fm-bcard-foot">
					<span class="fm-bcard-date"><i class="fa fa-calendar" aria-hidden="true"></i> <?php echo $date; ?></span>
					<?php $lienProd = fm_blog_lien($fm_app, $post['product_link']); if ($lienProd !== '') { ?>
					<a class="fm-bcard-link" href="<?php echo $lienProd; ?>">
						Products <i class="fa fa-angle-right" aria-hidden="true"></i>
					</a>
					<?php } ?>
				</div>
			</div>
		</article>
		<?php endforeach; ?>
	</div>

	<?php
	/* ------------------------------------------------------ pagination */
	$lienPage = function ($p) use ($fm_blog_chemin) {
		return fm_blog_echapper($GLOBALS['fm_app'] . $fm_blog_chemin($p));
	};
	?>
	<?php if ($nombrePages > 1) { ?>
	<nav class="fm-pager" aria-label="Blog pagination">
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

			<?php
			$debut = max(1, $page - 2);
			$fin   = min($nombrePages, $debut + 4);
			$debut = max(1, $fin - 4);
			for ($i = $debut; $i <= $fin; $i++): ?>
				<li>
					<?php if ($i === $page) { ?>
						<span class="fm-pager-item is-active" aria-current="page"><?php echo $i; ?></span>
					<?php } else { ?>
						<a class="fm-pager-item" href="<?php echo $lienPage($i); ?>"><?php echo $i; ?></a>
					<?php } ?>
				</li>
			<?php endfor; ?>

			<li>
				<?php if ($page < $nombrePages) { ?>
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
</section>

<!-- ============================================================ CATEGORIES -->
<?php if (!empty($categories)) { ?>
<section class="fm-section fm-section-alt">
	<div class="fm-section-head">
		<span class="fm-eyebrow">Browse by theme</span>
		<h2>Categories</h2>
	</div>

	<div class="fm-quality-grid">
		<?php foreach ($categories as $c): ?>
		<div class="fm-quality">
			<span class="fm-quality-icon">
				<?php
				$catIcon = array(
					'Morocco'    => 'fa-globe',
					'Tomatoes'   => 'fa-tint',
					'Citrus'     => 'fa-lemon-o',
					'Watermelon' => 'fa-sun-o',
					'Logistics'  => 'fa-truck',
					'Cold chain' => 'fa-snowflake-o',
				);
				$icon = isset($catIcon[$c['category']]) ? $catIcon[$c['category']] : 'fa-book';
				echo '<i class="fa ' . $icon . '"></i>';
				?>
			</span>
			<h3>
				<a href="<?php echo $fm_app; ?>blog/?category=<?php echo rawurlencode($c['category']); ?>">
					<?php echo fm_blog_echapper($c['category']); ?>
				</a>
			</h3>
			<p><?php echo $c['count']; ?> article<?php echo ($c['count'] > 1) ? 's' : ''; ?></p>
		</div>
		<?php endforeach; ?>
	</div>
</section>
<?php } ?>

<!-- ============================================================ RECENT / CTA -->
<section class="fm-cta">
	<div class="fm-cta-inner">
		<h2>Something you would like us to write about?</h2>
		<p>
			These articles come from real questions we get from buyers. If there is
			something you would like to understand better, a product, a season or
			the way a shipment travels, tell us and we will gladly explain.
		</p>
		<a class="btn" href="<?php echo $fm_app; ?>contact/">Ask us a question</a>
	</div>
</section>

<?php require_once((__DIR__ . "/includes/footer.php")); ?>
<?php require_once((__DIR__ . "/includes/analyticstracking.php")); ?>

</body>
</html>
