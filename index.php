<?php
/**
 * Accueil FoodMax Group.
 *
 * Presentation de l entreprise, de sa philosophie, de ses produits, de son
 * processus et de ses engagements.
 *
 * Regle de contenu appliquee a cette page : aucune certification, aucun client,
 * aucun chiffre, aucun label ni certification n est affirme sans source. Les
 * categories et les varietes proviennent de la base (categories, produits), les
 * photos des fichiers reels du dossier images/. Les informations qui n existent
 * pas encore en base — saisonnalite, origine par variete, conditionnement —
 * sont explicitement signalees comme a completer plutot que d etre inventees.
 */

/* _header.php ouvre la session : il doit etre inclus AVANT tout affichage,
   sinon les en-tetes HTTP sont deja partis et session_start() echoue. */
require_once(__DIR__ . "/includes/paths.php");
require_once(__DIR__ . "/includes/_header.php");
require_once(__DIR__ . "/includes/explore.php");

/* -------------------------------------------------------------------------
   Donnees reelles : categories et produits.
   Les varietes affichees sont les designations reelles de la table produits.
   Aucun contenu n est invente.
   ------------------------------------------------------------------------- */
define('FM_IMG_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'images');

function fm_echapper($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); }

/* Premiere photo reellement presente sur le disque pour un produit donne.
   La colonne Photo liste des noms de fichiers separes par virgule ou retour
   a la ligne ; certains fichiers disparu, on les ignore donc. */
function fm_premiere_photo($ref_prod, $champ_photo)
{
    $fichiers = preg_split('/[\r\n,]+/', (string) $champ_photo);
    foreach ($fichiers as $f) {
        $f = trim($f);
        if ($f === '') { continue; }
        $chemin = FM_IMG_DIR . DIRECTORY_SEPARATOR . $ref_prod . DIRECTORY_SEPARATOR . $f;
        if (is_file($chemin)) { return $f; }
    }
    return null;
}

/* Libelle lisible a partir du nom de la colonne : Green_leaves -> Green leaves */
function fm_libelle_categorie($nom)
{
    $nom = str_replace('_', ' ', (string) $nom);
    $nom = preg_replace('/(?<=\s)([a-z])/u', ' $1', $nom);
    return trim($nom);
}


/* Courte presentation humaine de chaque famille. Texte volontairement general :
   aucune variete, aucun chiffre, aucune saison n'y est affirme (ces donnees
   viennent de la base ou sont donnees sur demande). */
function fm_presentation_categorie($nom)
{
    $cle = strtolower(preg_replace('/[^a-z]/i', '', (string) $nom));
    $textes = array(
        'citrus'      => 'Bright, juicy citrus from Moroccan orchards, chosen for colour, size and that fresh taste people remember.',
        'berries'     => 'Delicate and quick to bruise, which is exactly why we give berries extra attention from the picking all the way to the packing.',
        'melons'      => 'Sweet, refreshing melons, because a melon is only ever as good as the moment it was picked.',
        'melon'       => 'Sweet, refreshing melons, because a melon is only ever as good as the moment it was picked.',
        'tomatoes'    => 'Tomatoes sorted by size, colour and firmness, so that they arrive looking as good as they taste.',
        'peppers'     => 'Crisp, colourful peppers, graded with care so every box is even and ready to sell.',
        'courgettes'  => 'Fresh courgettes chosen for firmness and a smooth, clean skin, and handled gently on their way to you.',
        'eggplants'   => 'Glossy eggplants selected for firm flesh and a clean skin, for kitchens that care about the details.',
        'greenleaves' => 'Tender green leaves that need a cool and quick journey, so we plan their route with special attention.',
        'driedfruits' => 'Dried fruits for customers who need a longer shelf life, selected and packed with the same care as our fresh range.',
        'figs'        => 'A soft and delicate fruit that rewards patience and a gentle hand, from the tree to the box.',
    );
    return isset($textes[$cle])
        ? $textes[$cle]
        : 'A selection we source according to the season and to demand, so ask us what is available right now.';
}

$fm_categories = array();
$fm_sql = 'SELECT c.Code_cat, c.Nom_cat, p.Ref_prod, p.Designation, p.Photo
          FROM categories c
          LEFT JOIN produits p ON p.Code_cat = c.Code_cat
          ORDER BY c.Code_cat, p.Designation';
if ($fm_res = mysqli_query($conn, $fm_sql)) {
    while ($fm_l = mysqli_fetch_assoc($fm_res)) {
        $code = (int) $fm_l['Code_cat'];
        if (!isset($fm_categories[$code])) {
            $fm_categories[$code] = array(
                'code' => $code,
                'nom'  => $fm_l['Nom_cat'],
                'photo' => null,
                'ref'  => '',
                'varietes' => array(),
            );
        }
        $designation = trim((string) $fm_l['Designation']);
        if ($designation !== '' && !in_array($designation, $fm_categories[$code]['varietes'], true)) {
            $fm_categories[$code]['varietes'][] = $designation;
        }
        /* Une seule photo de representative par categorie : la premiere trouvee. */
        if ($fm_categories[$code]['photo'] === null && $designation !== '') {
            $p = fm_premiere_photo($fm_l['Ref_prod'], $fm_l['Photo']);
            if ($p !== null) {
                $fm_categories[$code]['photo'] = $p;
                $fm_categories[$code]['ref'] = $fm_l['Ref_prod'];
            }
        }
    }
}
$fm_categories = array_values($fm_categories);

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<title>FoodMax Group | Freshness, quality and trust from Morocco</title>
	<meta name="description" content="FoodMax Group grows, selects, packs and exports quality Moroccan fruits and vegetables: citrus, berries, melons, tomatoes, peppers, green leaves and dried fruits.">
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

<?php /* header-inc.php ouvre <body id="url_div"> et affiche le menu. */
require_once(__DIR__ . "/includes/header-inc.php"); ?>

<!-- =============================================================== HERO -->
<section class="fm-hero">
	<div class="fm-hero-bg">
		<img src="<?php echo $fm_app; ?>images/banniere.png" alt="Fresh fruits and vegetables from Morocco" fetchpriority="high">
	</div>
	<div class="fm-hero-inner">
		<p class="fm-hero-kicker">From Morocco to the World</p>
		<h1 class="fm-hero-title">Freshness, quality and trust</h1>
		<p class="fm-hero-text">
			Every box we send starts in a Moroccan field, in the hands of people
			who care about what they grow, and it stays with us all the way to
			your door. At FoodMax Group we select, check, pack and ship fresh
			fruits and vegetables for markets close to home and far away, because
			we believe a good product deserves to arrive just as it left the
			farm, and a good customer deserves to know exactly what they are
			getting.
		</p>
		<div class="fm-hero-actions">
			<a class="btn" href="<?php echo $fm_app; ?>products/">
				Discover Our Products <i class="fa fa-arrow-right" aria-hidden="true"></i>
			</a>
			<a class="btn btn-outline fm-btn-light" href="<?php echo $fm_app; ?>contact/">
				Contact FoodMax Group
			</a>
		</div>
		<ul class="fm-hero-points">
			<li><i class="fa fa-check" aria-hidden="true"></i> Twelve product families</li>
			<li><i class="fa fa-check" aria-hidden="true"></i> Cold chain from start to finish</li>
			<li><i class="fa fa-check" aria-hidden="true"></i> Supply for professionals</li>
		</ul>
	</div>
</section>

<!-- ============================================================== ABOUT -->
<section class="fm-about" id="about">
	<div class="fm-about-media">
		<img src="<?php echo $fm_app; ?>images/grow-food.png"
		     alt="Fresh produce at FoodMax Group" loading="lazy" width="1672" height="941">
	</div>
	<div class="fm-about-body">
		<p class="fm-eyebrow">About FoodMax Group</p>
		<h2>A partner, not just a supplier</h2>
		<p class="fm-lead">
			FoodMax Group is a Moroccan fresh produce exporter based in Marrakech,
			and behind the name there is a real team of people who take this work
			personally and like to know that what they send will be enjoyed.
		</p>
		<p>
			We buy from growers we know and we prepare every lot for the place it
			is going to, because for us selling is only the last small part of the
			job, and what really counts is everything that happens before it, from
			the field to the truck to your warehouse.
		</p>

		<ol class="fm-chain" aria-label="From grower to customer">
			<li><span>Growers</span></li>
			<li><span>Farming</span></li>
			<li><span>Selection</span></li>
			<li><span>Quality control</span></li>
			<li><span>Packing</span></li>
			<li><span>Logistics</span></li>
			<li><span>You</span></li>
		</ol>

		<a class="btn" href="<?php echo $fm_app; ?>about-us/">Discover our story</a>
	</div>
</section>

<!-- ============================================================ PRODUCTS -->
<section class="fm-section" id="products">
	<div class="fm-section-head">
		<p class="fm-eyebrow">Our products</p>
		<h2>What we export</h2>
		<p class="fm-section-sub">
			Twelve families of Moroccan produce, each with its own season and its
			own character. The varieties you see below are the ones currently in
			our catalogue, and what is really available depends on the time of
			year, so if you are looking for something in particular, just ask us.
		</p>
	</div>

	<div class="fm-cat-grid">
		<?php foreach ($fm_categories as $fm_cat):
			$fm_lien = $fm_app . 'products/' . $fm_cat['code'] . '/' . rawurlencode($fm_cat['nom']) . '/';
			$fm_photo = $fm_cat['photo'] !== null
				? $fm_app . 'images/' . rawurlencode($fm_cat['ref']) . '/' . rawurlencode($fm_cat['photo'])
				: $fm_app . 'img/product-placeholder.svg';

			$fm_nb = count($fm_cat['varietes']);
			/* Varietes reelles du catalogue, 5 au plus. */
			$fm_var = $fm_nb > 0
				? fm_echapper(implode(', ', array_slice($fm_cat['varietes'], 0, 5))) . ($fm_nb > 5 ? ' and more' : '')
				: '';
		?>
		<article class="fm-cat">
			<a class="fm-cat-media" href="<?php echo $fm_lien; ?>">
				<img src="<?php echo $fm_photo; ?>"
				     alt="<?php echo fm_echapper(fm_libelle_categorie($fm_cat['nom'])); ?>"
				     loading="lazy">
				<?php if ($fm_nb > 0) { ?>
				<span class="fm-cat-count">
					<?php echo $fm_nb; ?> <?php echo $fm_nb > 1 ? 'products' : 'product'; ?>
				</span>
				<?php } ?>
			</a>
			<div class="fm-cat-body">
				<h3><a href="<?php echo $fm_lien; ?>"><?php echo fm_echapper(fm_libelle_categorie($fm_cat['nom'])); ?></a></h3>

				<p class="fm-cat-intro"><?php echo fm_echapper(fm_presentation_categorie($fm_cat['nom'])); ?></p>

				<?php if ($fm_var !== '') { ?>
				<p class="fm-cat-varieties"><strong>Varieties:</strong> <?php echo $fm_var; ?></p>
				<?php } else { ?>
				<p class="fm-cat-varieties fm-cat-varieties-empty">
					The range is being finalised, so contact us for details.
				</p>
				<?php } ?>

				<p class="fm-cat-meta">
					<strong>Origin:</strong> Morocco &nbsp;&middot;&nbsp;
					<strong>Season and packing:</strong> ask us for the current details
				</p>

				<a class="fm-cat-link" href="<?php echo $fm_lien; ?>">
					Discover Products <i class="fa fa-arrow-right" aria-hidden="true"></i>
				</a>
			</div>
		</article>
		<?php endforeach; ?>
	</div>
</section>

<!-- ============================================================ DISCOVER -->
<?php fm_explore(array('about-us'), 'Discover FoodMax Group', 'Get to know us at your own pace',
	'Each page is short and covers a single subject, so you can read whatever interests you most, in the order you prefer.'); ?>

<!-- ================================================================== CTA -->
<section class="fm-cta">
	<div class="fm-cta-inner">
		<p class="fm-eyebrow">Next step</p>
		<h2>Ready to source your next order?</h2>
		<p>
			Tell us which product you have in mind, how much you need and where it
			should go, and we will come back to you within one business day.
		</p>
		<div class="fm-cta-actions">
			<a class="btn" href="<?php echo $fm_app; ?>contact/">Start the conversation</a>
			<a class="btn btn-outline" href="<?php echo $fm_app; ?>products/">Browse the catalogue</a>
		</div>
	</div>
</section>

<?php require_once((__DIR__ . "/includes/footer.php")); ?>

</body>
</html>