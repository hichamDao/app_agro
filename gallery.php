<?php
require_once((__DIR__ . "/includes/_header.php"));
require_once((__DIR__ . "/includes/paths.php"));

/* ---------------------------------------------------------------- entrees
   Seule la pagination provient de l'URL ; elle est castee en entier avant
   d'atteindre la requete preparee, donc jamais concatenee dans le SQL. */
$page    = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 16;

/* ------------------------------------------------------------------ total */
$total = 0;
$rs = mysqli_query($conn, "SELECT COUNT(*) AS n FROM gallery");
if ($rs && ($ligne = mysqli_fetch_assoc($rs))) {
    $total = (int) $ligne['n'];
}

$nbPages = max(1, (int) ceil($total / $perPage));
if ($page > $nbPages) { $page = $nbPages; }
$offset = ($page - 1) * $perPage;

/* ---------------------------------------------------------------- photos */
$photos = array();
$stmt = mysqli_prepare($conn, "SELECT Photo, Photo2 FROM gallery ORDER BY id_Gal DESC LIMIT ? OFFSET ?");
if ($stmt) {
    /* L'ordre suit la requete : le premier ? est LIMIT, le second OFFSET. */
    mysqli_stmt_bind_param($stmt, 'ii', $perPage, $offset);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res) {
        while ($g = mysqli_fetch_assoc($res)) { $photos[] = $g; }
    }
    mysqli_stmt_close($stmt);
}

/* ------------------------------------------------- total des photos utiles
   Le compteur de l'en-tete annonce le nombre de photos reellement
   affichables : certaines lignes ne pointent vers aucun fichier. */
$totalAffiche = 0;
$rsAll = mysqli_query($conn, "SELECT Photo, Photo2 FROM gallery");
if ($rsAll) {
    while ($gAll = mysqli_fetch_assoc($rsAll)) {
        if (fm_gal_local($gAll['Photo']) !== '' || fm_gal_local($gAll['Photo2']) !== '') {
            $totalAffiche++;
        }
    }
    mysqli_free_result($rsAll);
}

function fm_gal_esc($s) {
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

/* Une image absente du disque ne doit pas casser la page : on tente le
   chemin demande, puis l'autre version de la meme photo, et seulement
   en dernier recours une vignette de substitution. */
function fm_gal_local($chemin) {
    $c = ltrim(trim(str_replace('\\', '/', (string) $chemin)), '/');
    if ($c !== '' && is_file(__DIR__ . '/' . $c)) { return $c; }
    return '';
}

function fm_gal_url($local, $fm_app) {
    return ($local === '') ? ($fm_app . 'img/product-placeholder.svg') : ($fm_app . $local);
}

$lienPage = function ($n) use ($fm_app) {
    return ($n <= 1) ? ($fm_app . 'gallery/') : ($fm_app . 'gallery/' . $n . '/');
};

$fm_titre       = 'Gallery';
$fm_sousTitre  = 'Fresh produce, packing and export moments from our partner farms and facilities in Morocco.';
?>
<!DOCTYPE html>
<html lang="en">
<?php require_once((__DIR__ . "/includes/paths.php")); ?>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Gallery <?php echo $page > 1 ? '&mdash; page ' . $page . ' &mdash; ' : '&mdash; ' ?>Foodmax</title>
	<meta name="description" content="Foodmax gallery: photographs of Moroccan fresh fruit and vegetables, packing and cold chain facilities.">
	<link rel="stylesheet" href="<?php echo $fm_app; ?>css/font-awesome.min.css">
	<link rel="stylesheet" href="<?php echo $fm_app; ?>css/fancybox/jquery.fancybox.css">
	<link rel="stylesheet" href="<?php echo $fm_app; ?>css/fancybox/helpers/jquery.fancybox-thumbs.css">
	<link rel="stylesheet" href="<?php echo $fm_app; ?>css/phlox.css">
</head>
<?php require_once((__DIR__ . "/includes/header-inc.php")); ?>

<!-- ============================================================ EN-TETE PAGE -->
<section class="fm-pagehead">
	<div class="fm-pagehead-inner">
		<span class="fm-eyebrow">In pictures</span>
		<h1><?php echo $fm_titre; ?></h1>
		<p><?php echo $fm_sousTitre; ?></p>

		<nav class="fm-crumbs" aria-label="Fil d'Ariane">
			<ol>
				<li><a href="<?php echo $fm_app; ?>">Home</a></li>
				<li>Gallery<?php echo $page > 1 ? ' &mdash; page ' . $page : ''; ?></li>
			</ol>
		</nav>
	</div>
</section>

<!-- ============================================================== GALERIE -->
<section class="fm-section">
	<div class="fm-section-head">
		<h2>Our farms, our packhouse</h2>
		<ul class="fm-gallery-stats">
			<li><strong><?php echo $totalAffiche; ?></strong> photographs</li>
			<li>Page <?php echo $page; ?> of <?php echo $nbPages; ?></li>
		</ul>
	</div>

	<?php if ($photos) { ?>
		<ul class="fm-gallery">
			<?php
			$rang = ($offset);
			foreach ($photos as $g) {
				$miniLocal  = fm_gal_local($g['Photo']);
				$grandLocal = fm_gal_local($g['Photo2']);

				/* Une ligne dont aucune version n'est sur le disque est
				   ignorede plutot que d'afficher un cadre vide. */
				if ($miniLocal === '' && $grandLocal === '') { continue; }
				if ($grandLocal === '') { $grandLocal = $miniLocal; }
				if ($miniLocal === '')  { $miniLocal  = $grandLocal; }

				$mini  = fm_gal_url($miniLocal, $fm_app);
				$grand = fm_gal_url($grandLocal, $fm_app);

				/* Taille intrinsèque : reserve la place avant chargement
				   et supprime le saut de mise en page de la grille. */
				$d = @getimagesize(__DIR__ . '/' . $miniLocal);
				$w = $d ? (int) $d[0] : 0;
				$h = $d ? (int) $d[1] : 0;
				$rang++;
			?>
				<li class="fm-gallery-item">
					<a class="fm-gallery-link fancybox-thumbs"
					   href="<?php echo $grand; ?>"
					   data-fancybox-group="thumbs"
					   aria-label="View photograph <?php echo $rang; ?> full size">
						<img src="<?php echo $mini; ?>" alt=""
						     <?php echo $w ? 'width="' . $w . '" height="' . $h . '"' : ''; ?>
						     loading="lazy" decoding="async">
						<span class="fm-gallery-zoom" aria-hidden="true">
							<i class="fa fa-search-plus"></i>
						</span>
					</a>
				</li>
			<?php } ?>
		</ul>
	<?php } else { ?>
		<div class="fm-empty">
			<span class="fm-empty-icon"><i class="fa fa-picture-o" aria-hidden="true"></i></span>
			<h2>No photographs here yet</h2>
			<p>Our packhouse and partner farms are photographed regularly.
			   Write to us and we will send you the current catalogue by email.</p>
			<a class="btn" href="<?php echo $fm_app; ?>contact/">Contact Foodmax</a>
		</div>
	<?php } ?>

	<?php if ($nbPages > 1) { ?>
		<nav class="fm-pager" aria-label="Gallery pagination">
			<ul class="fm-pager-list">
				<li>
					<?php if ($page > 1) { ?>
					<a class="fm-pager-item" href="<?php echo $lienPage($page - 1); ?>" rel="prev">
						<i class="fa fa-angle-left" aria-hidden="true"></i> Previous
					</a>
					<?php } else { ?>
					<span class="fm-pager-item is-disabled">
						<i class="fa fa-angle-left" aria-hidden="true"></i> Previous
					</span>
					<?php } ?>
				</li>

				<?php for ($i = 1; $i <= $nbPages; $i++) { ?>
				<li>
					<?php if ($i === $page) { ?>
					<span class="fm-pager-item is-active" aria-current="page"><?php echo $i; ?></span>
					<?php } else { ?>
					<a class="fm-pager-item" href="<?php echo $lienPage($i); ?>"><?php echo $i; ?></a>
					<?php } ?>
				</li>
				<?php } ?>

				<li>
					<?php if ($page < $nbPages) { ?>
					<a class="fm-pager-item" href="<?php echo $lienPage($page + 1); ?>" rel="next">
						Next <i class="fa fa-angle-right" aria-hidden="true"></i>
					</a>
					<?php } else { ?>
					<span class="fm-pager-item is-disabled">
						Next <i class="fa fa-angle-right" aria-hidden="true"></i>
					</span>
					<?php } ?>
				</li>
			</ul>
		</nav>
	<?php } ?>
</section>

<section class="fm-cta" style="background-image:url('<?php echo $fm_app; ?>img/banner-cta.jpg');">
	<div class="fm-cta-inner">
		<h2>Want the same produce delivered?</h2>
		<p>Tell us the product, the volume and the destination &mdash; we reply within one business day.</p>
		<a class="btn" href="<?php echo $fm_app; ?>contact/">Talk to our team</a>
	</div>
</section>

<?php require_once((__DIR__ . "/includes/footer.php")); ?>
<?php require_once((__DIR__ . "/includes/analyticstracking.php")); ?>

<script type="text/javascript" src="<?php echo $fm_app; ?>js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $fm_app; ?>css/fancybox/jquery.fancybox.pack.js"></script>
<script type="text/javascript" src="<?php echo $fm_app; ?>css/fancybox/helpers/jquery.fancybox-thumbs.js"></script>
<script type="text/javascript" src="<?php echo $fm_app; ?>js/setting.js"></script>

</body>
</html>