<?php
require_once((__DIR__ . "/../includes/_header.php"));
require_once((__DIR__ . "/../includes/paths.php"));

/* ============================================================ entrees */
$prod_id = isset($_GET['prod_id']) ? intval($_GET['prod_id']) : 0;

/* URL d'une photo de produit, avec repli sur une image generique.
   Plusieurs references de la table produits pointent vers un dossier images/
   inexistant (P9015, P69115, P72120, P75125, P78130, P81135, P87145, P90150,
   P93155, P96160, P99165...) : sans ce repli la page affiche des images
   cassees a la place des photos du produit. */
function fd_img($ref_prod, $photo)
{
    global $fm_app;
    $photo = trim((string) $photo);
    if ($photo !== '') {
        $disk = __DIR__ . '/../images/' . $ref_prod . '/' . $photo;
        if (is_file($disk)) {
            return $fm_app . 'images/' . $ref_prod . '/' . $photo;
        }
    }
    return $fm_app . 'img/product-placeholder.svg';
}

function fd_echapper($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); }

/* URL lisible d'une categorie : /products/8/Peppers/. Si le libelle ne contient
   aucun caractere utilisable dans une URL, on retombe sur la forme avec
   parametres. */
function fd_lien_categorie($code, $slug)
{
    global $fm_app;
    $slug = preg_replace('/[^A-Za-z0-9-]/', '', (string) $slug);
    if ($slug === '') {
        return $fm_app . 'products/?idCat=' . (int) $code;
    }
    return $fm_app . 'products/' . (int) $code . '/' . $slug . '/';
}

/* Toutes les photos du produit, en ne gardant que celles reellement
   presentes sur le disque. Si aucune ne l'est, on renverra le placeholder. */
function fd_photos($prod)
{
    $liste = array();
    foreach (explode(',', (string) $prod['Photo']) as $ph) {
        $ph = trim($ph);
        if ($ph === '') { continue; }
        $disk = __DIR__ . '/../images/' . $prod['Ref_prod'] . '/' . $ph;
        if (is_file($disk)) { $liste[] = $ph; }
    }
    return $liste;
}

/* ============================================================= produit */
$prod = null;
if ($prod_id > 0) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM produits WHERE id_prod = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $prod_id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if ($res && ($row = mysqli_fetch_assoc($res))) { $prod = $row; }
        mysqli_stmt_close($stmt);
    }
}

/* =========================================================== categories */
$categories = array();
$catLabels  = array();
$rsCat = mysqli_query($conn, "SELECT Code_cat, Nom_cat FROM categories ORDER BY Nom_cat ASC");
if ($rsCat) {
    while ($c = mysqli_fetch_assoc($rsCat)) {
        $c['Code_cat'] = (int) $c['Code_cat'];
        $c['label']    = str_replace('_', ' ', $c['Nom_cat']);
        $c['slug']     = str_replace('_', '', $c['Nom_cat']);
        $categories[]  = $c;
        $catLabels[$c['Code_cat']] = $c;
    }
}

/* Si l'identifiant est inconnu, on affiche la page 404 du site plutot qu'un
   produit vide. */
if ($prod === null) {
    http_response_code(404);
}
$famille = ($prod && isset($catLabels[(int) $prod['Code_cat']])) ? $catLabels[(int) $prod['Code_cat']] : null;
$photos  = $prod ? fd_photos($prod) : array();

/* ============================================================== tags */
$tags = array();
if ($prod) {
    foreach (explode(',', (string) $prod['tags']) as $t) {
        $t = trim($t);
        if ($t !== '') { $tags[] = $t; }
    }
}

/* ======================================================= nutrition */
$nutriments = array();
if ($prod && trim((string) $prod['composant']) !== '') {
    $composants = array_map('trim', explode(',', (string) $prod['composant']));
    $percents   = array_map('trim', explode(',', (string) $prod['percent']));
    foreach ($composants as $i => $libelle) {
        if ($libelle === '') { continue; }
        $val = isset($percents[$i]) ? $percents[$i] : '';
        $nutriments[] = array('label' => $libelle, 'value' => $val);
    }
}

/* ==================================================== produits lies */
$lies = array();
if ($prod) {
    $stmt = mysqli_prepare(
        $conn,
        "SELECT id_prod, Ref_prod, Designation, Photo
           FROM produits
          WHERE Code_cat = ? AND id_prod <> ?
          ORDER BY Selectionne DESC, Designation ASC
          LIMIT 4"
    );
    if ($stmt) {
        $code = (int) $prod['Code_cat'];
        mysqli_stmt_bind_param($stmt, 'ii', $code, $prod_id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if ($res) {
            while ($p = mysqli_fetch_assoc($res)) { $lies[] = $p; }
        }
        mysqli_stmt_close($stmt);
    }
}

/* Si la famille est trop petite, on complete avec d'autres produits. */
if (count($lies) < 4) {
    $stmt = mysqli_prepare(
        $conn,
        "SELECT id_prod, Ref_prod, Designation, Photo
           FROM produits
          WHERE id_prod <> ?
          ORDER BY Selectionne DESC, Designation ASC
          LIMIT 4"
    );
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $prod_id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if ($res) {
            while ($p = mysqli_fetch_assoc($res)) { $lies[] = $p; }
        }
        mysqli_stmt_close($stmt);
    }
}

/* Tags les plus utilises, pour le panneau lateral. */
$tagsPopulaires = array();
$rsT = mysqli_query($conn, "SELECT tags FROM produits WHERE tags <> '' LIMIT 40");
if ($rsT) {
    $compte = array();
    while ($r = mysqli_fetch_assoc($rsT)) {
        foreach (explode(',', (string) $r['tags']) as $t) {
            $t = trim($t);
            if ($t !== '') { $compte[$t] = isset($compte[$t]) ? $compte[$t] + 1 : 1; }
        }
    }
    arsort($compte);
    $tagsPopulaires = array_slice(array_keys($compte), 0, 14);
}

$fm_titre = $prod ? $prod['Designation'] . ' | Foodmax' : 'Product not found | Foodmax';
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<title><?php echo fd_echapper($fm_titre); ?></title>
	<meta name="description" content="<?php echo $prod
		? fd_echapper(mb_substr(strip_tags($prod['description']), 0, 155))
		: 'This product is no longer available in the Foodmax catalogue.'; ?>">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

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

<?php if ($prod === null) { ?>

	<!-- ==================================================== produit absent -->
	<section class="fm-pagehead">
		<div class="fm-pagehead-inner">
			<span class="fm-eyebrow">Catalogue</span>
			<h1>Product not found</h1>
			<nav class="fm-crumbs" aria-label="Fil d'Ariane">
				<ol>
					<li><a href="<?php echo $fm_app; ?>">Home</a></li>
					<li><a href="<?php echo $fm_app; ?>products/">Products</a></li>
					<li>Not found</li>
				</ol>
			</nav>
		</div>
	</section>

	<section class="fm-section">
		<div class="fm-empty">
			<span class="fm-empty-icon"><i class="fa fa-search"></i></span>
			<h2>This product is no longer in our catalogue</h2>
			<p>It may have been renamed or withdrawn. Browse the full range to find
			   an equivalent product.</p>
			<a class="btn" href="<?php echo $fm_app; ?>products/">Back to products</a>
		</div>
	</section>

<?php } else { ?>

	<!-- =========================================================== EN-TETE -->
	<section class="fm-pagehead">
		<div class="fm-pagehead-inner">
			<?php if ($famille) { ?>
				<span class="fm-eyebrow"><?php echo fd_echapper($famille['label']); ?></span>
			<?php } else { ?>
				<span class="fm-eyebrow">Our catalogue</span>
			<?php } ?>
			<h1><?php echo fd_echapper($prod['Designation']); ?></h1>
			<p><?php echo count($photos)
				? count($photos) . ' photo' . (count($photos) > 1 ? 's' : '') . ' available'
				: 'Photographs of this product are coming soon.'; ?></p>

			<nav class="fm-crumbs" aria-label="Fil d'Ariane">
				<ol>
					<li><a href="<?php echo $fm_app; ?>">Home</a></li>
					<li><a href="<?php echo $fm_app; ?>products/">Products</a></li>
					<?php if ($famille) { ?>
						<li><a href="<?php echo fd_lien_categorie($famille['Code_cat'], $famille['slug']); ?>">
							<?php echo fd_echapper($famille['label']); ?></a></li>
					<?php } ?>
					<li><?php echo fd_echapper($prod['Designation']); ?></li>
				</ol>
			</nav>
		</div>
	</section>

	<!-- ========================================================== PRODUIT -->
	<section class="fm-pdetail">
		<div class="fm-pdetail-inner">

			<!-- ------------------------------------------------------ galerie -->
			<div class="fm-pdetail-media">
				<?php if ($photos) { ?>
					<div class="fm-pdetail-main">
						<img id="fdMain" src="<?php echo $fm_app; ?>images/<?php echo $prod['Ref_prod'] . '/' . $photos[0]; ?>"
						     alt="<?php echo fd_echapper($prod['Designation']); ?>">
					</div>

					<?php if (count($photos) > 1) { ?>
						<div class="fm-pdetail-thumbs" role="group" aria-label="Other photos">
							<?php foreach ($photos as $i => $ph) { ?>
								<button type="button"
								        class="fm-thumb<?php echo $i === 0 ? ' is-active' : ''; ?>"
								        data-full="<?php echo $fm_app; ?>images/<?php echo $prod['Ref_prod'] . '/' . $ph; ?>"
								        aria-label="Photo <?php echo $i + 1; ?>">
									<img src="<?php echo $fm_app; ?>images/<?php echo $prod['Ref_prod'] . '/' . $ph; ?>"
									     alt="<?php echo fd_echapper($prod['Designation']); ?> photo <?php echo $i + 1; ?>"
									     loading="lazy">
								</button>
							<?php } ?>
						</div>
					<?php } ?>
				<?php } else { ?>
					<div class="fm-pdetail-main is-empty">
						<img src="<?php echo $fm_app; ?>img/product-placeholder.svg"
						     alt="<?php echo fd_echapper($prod['Designation']); ?>">
					</div>
					<p class="fm-pdetail-nophoto">
						<i class="fa fa-camera" aria-hidden="true"></i>
						No photograph available for this product yet.
					</p>
				<?php } ?>
			</div>

			<!-- ------------------------------------------------------ infos -->
			<div class="fm-pdetail-body">
				<h2 class="fm-pdetail-title"><?php echo fd_echapper($prod['Designation']); ?></h2>

				<div class="fm-pdetail-flags">
					<?php if ((int) $prod['Selectionne'] === 1) { ?>
						<span class="fm-flag fm-flag-star"><i class="fa fa-star" aria-hidden="true"></i> Featured</span>
					<?php } ?>
					<?php if ((int) $prod['Disponible'] === 1) { ?>
						<span class="fm-flag fm-flag-stock">In stock</span>
					<?php } ?>
					<?php if ((int) $prod['Promotion'] === 1) { ?>
						<span class="fm-flag fm-flag-promo">Promotion</span>
					<?php } ?>
				</div>

				<p class="fm-pdetail-desc">
					<?php echo nl2br(fd_echapper(trim(strip_tags($prod['description'])))); ?>
				</p>

				<?php if ($tags) { ?>
					<div class="fm-pdetail-tags">
						<span class="fm-pdetail-tagslabel">Tags</span>
						<ul>
							<?php foreach ($tags as $t) { ?>
								<li><a href="<?php echo $fm_app; ?>products/?tags=<?php echo rawurlencode($t); ?>">
									<?php echo fd_echapper($t); ?></a></li>
							<?php } ?>
						</ul>
					</div>
				<?php } ?>

				<?php if ($nutriments) { ?>
					<div class="fm-pdetail-nutri">
						<h3>Average content per 100 g</h3>
						<table>
							<tbody>
								<?php foreach ($nutriments as $n) { ?>
									<tr>
										<th scope="row"><?php echo fd_echapper($n['label']); ?></th>
										<td><?php echo $n['value'] === '' ? '&mdash;' : fd_echapper($n['value']) . '%'; ?></td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
				<?php } ?>

				<div class="fm-pdetail-meta">
					<span><i class="fa fa-barcode" aria-hidden="true"></i> Ref. <?php echo fd_echapper($prod['Ref_prod']); ?></span>
					<?php if ($famille) { ?>
						<span><i class="fa fa-folder-open-o" aria-hidden="true"></i>
							<a href="<?php echo fd_lien_categorie($famille['Code_cat'], $famille['slug']); ?>">
								<?php echo fd_echapper($famille['label']); ?></a></span>
					<?php } ?>
				</div>

				<div class="fm-pdetail-actions">
					<a class="btn" href="<?php echo $fm_app; ?>contact/">
						<i class="fa fa-envelope" aria-hidden="true"></i> Request a quote
					</a>
					<a class="btn btn-outline fm-btn-ghost" href="<?php echo $fm_app; ?>products/">
						Keep browsing
					</a>
				</div>
			</div>
		</div>
	</section>

	<!-- ========================================================= LATERAL -->
	<section class="fm-plateral">
		<div class="fm-plateral-inner">

			<aside class="fm-plateral-col" aria-label="Catalogue navigation">

				<div class="fm-side-box">
					<form class="fm-side-search" method="get" action="<?php echo $fm_app; ?>products/" role="search">
						<label class="sr-only" for="fdSearch">Search a product</label>
						<input type="search" id="fdSearch" name="motCle" placeholder="Search a product&hellip;" autocomplete="off">
						<button type="submit" aria-label="Search"><i class="fa fa-search" aria-hidden="true"></i></button>
					</form>
				</div>

				<div class="fm-side-box">
					<h3>Categories</h3>
					<ul class="fm-side-cats">
						<?php foreach ($categories as $c) { ?>
							<li>
								<a href="<?php echo fd_lien_categorie($c['Code_cat'], $c['slug']); ?>"
								   class="<?php echo ($famille && $famille['Code_cat'] === $c['Code_cat']) ? 'is-active' : ''; ?>">
									<?php echo fd_echapper($c['label']); ?>
								</a>
							</li>
						<?php } ?>
					</ul>
				</div>

				<?php if ($tagsPopulaires) { ?>
					<div class="fm-side-box">
						<h3>Popular tags</h3>
						<ul class="fm-side-tags">
							<?php foreach ($tagsPopulaires as $t) { ?>
								<li><a href="<?php echo $fm_app; ?>products/?tags=<?php echo rawurlencode($t); ?>"><?php echo fd_echapper($t); ?></a></li>
							<?php } ?>
						</ul>
					</div>
				<?php } ?>
			</aside>

			<div class="fm-plateral-main">
				<?php if ($lies) { ?>
					<div class="fm-section-head fm-section-head-left">
						<span class="fm-eyebrow">You may also like</span>
						<h2>Related products</h2>
					</div>

					<div class="fm-pgrid">
						<?php foreach ($lies as $p) {
							$lien = $fm_app . 'products_detail/' . (int) $p['id_prod'] . '/';
							$prem = trim(explode(',', (string) $p['Photo'])[0]);
						?>
						<article class="fm-pcard">
							<a class="fm-pcard-media" href="<?php echo $lien; ?>" tabindex="-1" aria-hidden="true">
								<img src="<?php echo fd_img($p['Ref_prod'], $prem); ?>"
								     alt="<?php echo fd_echapper($p['Designation']); ?>" loading="lazy">
							</a>
							<div class="fm-pcard-body">
								<h3 class="fm-pcard-title">
									<a href="<?php echo $lien; ?>"><?php echo fd_echapper($p['Designation']); ?></a>
								</h3>
								<div class="fm-pcard-foot">
									<span class="fm-pcard-ref">Ref. <?php echo fd_echapper($p['Ref_prod']); ?></span>
									<a class="fm-pcard-link" href="<?php echo $lien; ?>">
										Details <i class="fa fa-angle-right" aria-hidden="true"></i>
									</a>
								</div>
							</div>
						</article>
						<?php } ?>
					</div>
				<?php } ?>
			</div>

		</div>
	</section>

	<!-- ============================================================ CTA -->
	<section class="fm-cta" style="background-image:url('<?php echo $fm_app; ?>img/banner-cta.jpg');">
		<div class="fm-cta-inner">
			<h2>Interested in <?php echo fd_echapper($prod['Designation']); ?>?</h2>
			<p>Tell us the quantity and the destination &mdash; we reply within one business day
			   with grades, packing and shipping options.</p>
			<a class="btn" href="<?php echo $fm_app; ?>contact/">Request a quote</a>
		</div>
	</section>

<?php } ?>

<?php require_once((__DIR__ . "/../includes/footer.php")); ?>

<?php require_once((__DIR__ . "/../includes/analyticstracking.php")); ?>

<script type="text/javascript">
/* Galerie : le clic sur une vignette remplace la grande image.
   Demarrage immediat si le DOM est deja analyse, comme slider.js / nav.js :
   DOMContentLoaded peut ne pas se declencher si une feuille de style externe
   (police Google) ne repond pas. */
(function () {
	function init() {
		var $ = window.jQuery;
		if (!$ || !$.fn) { return; }

		var $main = $('#fdMain');
		var $thumbs = $('.fm-pdetail-thumbs');
		if (!$main.length || !$thumbs.length) { return; }

		$thumbs.on('click', '.fm-thumb', function () {
			var src = $(this).attr('data-full');
			if (!src) { return; }
			$main.attr('src', src);
			$thumbs.find('.fm-thumb').removeClass('is-active');
			$(this).addClass('is-active');
		});
	}

	function boot() {
		var $ = window.jQuery;
		if (!$ || !$.fn) { return false; }
		if ($('#fdMain').length) { init(); } else { $(init); }
		return true;
	}

	if (!boot()) {
		document.addEventListener('DOMContentLoaded', boot);
		window.addEventListener('load', boot);
	}
})();
</script>

</body>
</html>
