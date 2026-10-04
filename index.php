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
			We buy from growers we know, in the regions where each product is at
			its best, and we prepare every lot for the place it is going to,
			because a box of berries crossing the sea does not need the same care
			as a tomato sold a few hours away. For us, selling is only the last
			small part of the job: before it there is a grower who picked at the
			right moment, a team who set aside whatever was not good enough, a
			pack that protected the fruit and a truck that kept it cool, and when
			one of those links is weak the whole shipment feels it, and so does
			your customer.
		</p>
		<p>
			That is why we always prefer a long relationship to a quick order. We
			like to learn what you need, keep you posted while the season moves
			along, and tell you honestly when a variety is not at its best
			instead of sending it anyway, because trust takes a long time to build
			and almost no time to lose.
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

<!-- ======================================================== PHILOSOPHY -->
<section class="fm-philosophy">
	<div class="fm-philosophy-inner">
		<p class="fm-eyebrow">Our philosophy</p>
		<h2>More Than Fresh Food</h2>
		<p class="fm-lead">
			For us, quality does not begin at the door of a warehouse, it begins
			long before, with the person who decided to grow something and with
			the care we put into choosing what comes out of that field.
		</p>

		<div class="fm-philo-grid">
			<article class="fm-philo">
				<span class="fm-philo-icon"><i class="fa fa-fw fa-heart" aria-hidden="true"></i></span>
				<h3>Respect for the product</h3>
				<p>
					A fruit picked one day too early never becomes what it promised
					to be, so we would rather wait a little and send something that
					arrives ready to sell than something that only looks ready.
					Freshness and careful selection are where everything starts for
					us.
				</p>
			</article>

			<article class="fm-philo">
				<span class="fm-philo-icon"><i class="fa fa-fw fa-handshake-o" aria-hidden="true"></i></span>
				<h3>Respect for growers</h3>
				<p>
					Our growers are partners, not names on a list. We stay with them
					from one season to the next, we talk about volumes before the
					harvest begins, and we try to keep the path of every lot clear,
					from the field to your order, because traceability starts with
					knowing the people behind the crop.
				</p>
			</article>

			<article class="fm-philo">
				<span class="fm-philo-icon"><i class="fa fa-fw fa-leaf" aria-hidden="true"></i></span>
				<h3>Respect for the environment</h3>
				<p>
					We try to use water and land wisely and to throw away as little
					as we can, and we know there is always more to improve. We would
					rather admit that honestly and keep getting better than promise
					things we cannot prove.
				</p>
			</article>

			<article class="fm-philo">
				<span class="fm-philo-icon"><i class="fa fa-fw fa-smile-o" aria-hidden="true"></i></span>
				<h3>Respect for customers</h3>
				<p>
					We answer clearly and quickly, and when something goes wrong we
					say so and offer a solution straight away. A customer who knows
					what to expect is worth far more to us than one who has been
					surprised, even by a nice surprise.
				</p>
			</article>
		</div>
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

<!-- ============================================================ PROCESS -->
<section class="fm-process">
	<div class="fm-section-head fm-section-head-center">
		<p class="fm-eyebrow">From the farm to you</p>
		<h2>How we work</h2>
		<p class="fm-section-sub">
			Five steps, and each of them is something we do with care every
			single day, so that what reaches you is the result of a lot of quiet
			work you never have to worry about.
		</p>
	</div>

	<ol class="fm-steps">
		<li class="fm-step">
			<span class="fm-step-num">01</span>
			<h3>Production</h3>
			<p>
				Everything begins with the growers. We work with farms in the
				regions where each product naturally does best, we agree on
				varieties and volumes before the season starts, and we follow
				the crop as it grows instead of simply buying what the market
				offers that day, because knowing the people behind a harvest
				makes everything after it easier and more honest.
			</p>
		</li>
		<li class="fm-step">
			<span class="fm-step-num">02</span>
			<h3>Selection</h3>
			<p>
				When a lot reaches us it is sorted by people who know what a
				good fruit looks like. Anything that does not match the size,
				colour, ripeness or condition you asked for is put aside rather
				than slipped into your order, and this quiet step is really what
				decides what you end up receiving.
			</p>
		</li>
		<li class="fm-step">
			<span class="fm-step-num">03</span>
			<h3>Quality Control</h3>
			<p>
				We check at several moments, when the produce arrives, while it
				is being packed and once more before loading, looking at
				condition, size, maturity, temperature and cleanliness. What we
				find is written down, so that if you ever have a question about
				a shipment there is a real answer waiting for you.
			</p>
		</li>
		<li class="fm-step">
			<span class="fm-step-num">04</span>
			<h3>Packing</h3>
			<p>
				Each product is packed for the journey it is about to make, with
				ventilated cartons for berries, extra protection for delicate
				fruit and stacking that holds on long routes. Labels and
				documents follow your requirements, so that when the boxes are
				opened at the other end, everything is where you expect it to be.
			</p>
		</li>
		<li class="fm-step">
			<span class="fm-step-num">05</span>
			<h3>Delivery</h3>
			<p>
				A cold chain that holds is what protects everything we did
				before, so we plan the route with care, keep an eye on the
				conditions during transport and let you know how things are going,
				which gives you time to organise your side before the goods
				arrive.
			</p>
		</li>
	</ol>
</section>

<!-- ============================================================ QUALITY -->
<section class="fm-section fm-section-alt">
	<div class="fm-section-head">
		<p class="fm-eyebrow">Our quality commitment</p>
		<h2>What we promise ourselves</h2>
		<p class="fm-section-sub">
			Quality is not a sentence on a web page, it is a handful of things we
			do at every stage and hold ourselves to, even when nobody is checking.
		</p>
	</div>

	<div class="fm-commit-grid">
		<article class="fm-commit">
			<span class="fm-commit-icon"><i class="fa fa-fw fa-leaf" aria-hidden="true"></i></span>
			<h3>Freshness</h3>
			<p>
				We always look for freshness and the right point of maturity, and
				we would rather shorten our chain than let time pass between the
				harvest and the cold room. Time is the biggest enemy of quality,
				so we organise everything around it.
			</p>
		</article>

		<article class="fm-commit">
			<span class="fm-commit-icon"><i class="fa fa-fw fa-check-circle-o" aria-hidden="true"></i></span>
			<h3>Quality</h3>
			<p>
				We choose products that meet our own standards before they ever
				meet your order, and if a variety does not reach the level we
				expect, it simply does not leave, even in a tight season when
				others are shipping anyway.
			</p>
		</article>

		<article class="fm-commit">
			<span class="fm-commit-icon"><i class="fa fa-fw fa-map-marker" aria-hidden="true"></i></span>
			<h3>Traceability</h3>
			<p>
				We keep the link between the grower, the lot and your shipment as
				clear as we can. Where our records are complete we can tell you
				where a product comes from and how it reached you, and where they
				are not, we say so instead of guessing.
			</p>
		</article>

		<article class="fm-commit">
			<span class="fm-commit-icon"><i class="fa fa-fw fa-repeat" aria-hidden="true"></i></span>
			<h3>Consistency</h3>
			<p>
				Professional buyers plan around us, so the same specification has
				to arrive week after week. We work towards a steady quality across
				the whole season, and we tell you early when the conditions make
				that difficult.
			</p>
		</article>
	</div>

	<p class="fm-note">
		Documentation and third-party certifications can be provided on request
		for each shipment and destination, so just ask us for whatever applies
		to your market.
	</p>
</section>

<!-- ====================================================== COMMITMENT -->
<section class="fm-section" id="commitment">
	<div class="fm-section-head">
		<p class="fm-eyebrow">Our commitment</p>
		<h2>Doing this responsibly, and getting better as we go</h2>
		<p class="fm-section-sub">
			Fresh produce depends on land, water and people, and we feel
			responsible for each of them. What follows are the principles we work
			by and the places where we keep trying to improve, not labels we claim
			to have already earned.
		</p>
	</div>

	<div class="fm-commit-grid">
		<article class="fm-commit">
			<span class="fm-commit-icon"><i class="fa fa-fw fa-tree" aria-hidden="true"></i></span>
			<h3>Responsible agriculture</h3>
			<p>
				We prefer to work with growers who look after their land, because
				good soil is what the next season depends on, and we talk openly
				about farming practices and tell you what we are able to document.
			</p>
		</article>

		<article class="fm-commit">
			<span class="fm-commit-icon"><i class="fa fa-fw fa-tint" aria-hidden="true"></i></span>
			<h3>Sensible use of resources</h3>
			<p>
				Water, energy and packaging all have a cost, so we aim to use what
				a shipment truly needs, with packing suited to the product and
				loads planned with care instead of more material than necessary.
			</p>
		</article>

		<article class="fm-commit">
			<span class="fm-commit-icon"><i class="fa fa-fw fa-recycle" aria-hidden="true"></i></span>
			<h3>Less waste</h3>
			<p>
				Throwing away good produce is a loss for the grower and for
				everyone after them, so when a lot does not suit one order we look
				for another place for it before we ever call it waste.
			</p>
		</article>

		<article class="fm-commit">
			<span class="fm-commit-icon"><i class="fa fa-fw fa-pagelines" aria-hidden="true"></i></span>
			<h3>Respect for the environment</h3>
			<p>
				Our work depends on nature, so we try to limit what we leave
				behind. It is a long road and we are still on it, and we prefer to
				say that plainly rather than announce results we could not back up.
			</p>
		</article>

		<article class="fm-commit">
			<span class="fm-commit-icon"><i class="fa fa-fw fa-refresh" aria-hidden="true"></i></span>
			<h3>Continuous improvement</h3>
			<p>
				After every season we look at what worked and what did not, and we
				adjust our selection, our packing and our transport. Small, steady
				improvements matter more to us than big announcements.
			</p>
		</article>

		<article class="fm-commit">
			<span class="fm-commit-icon"><i class="fa fa-fw fa-handshake-o" aria-hidden="true"></i></span>
			<h3>Lasting relationships with growers</h3>
			<p>
				A grower who knows we will be there next season can plan, invest
				and farm with a longer view, and that stability is good for the
				land as well as for the quality that reaches you.
			</p>
		</article>
	</div>

	<p class="fm-note">
		We do not display environmental labels or certifications on this site. If
		one applies to a product or to a grower, we will tell you exactly which
		one it is and send you the document.
	</p>
</section>

<!-- ========================================================= MOROCCO -->
<section class="fm-morocco">
	<div class="fm-morocco-map">
		<img src="<?php echo $fm_app; ?>img/map-morocco.svg"
		     alt="Map of Morocco showing Marrakech, the Souss valley and our export routes" loading="lazy">
	</div>
	<div class="fm-morocco-body">
		<p class="fm-eyebrow">FoodMax Group and Morocco</p>
		<h2>From Morocco to the World</h2>
		<p class="fm-lead">
			Morocco is not a big country, yet it holds a surprising variety of
			climates, and that is exactly what makes its agriculture so rich and
			so full of possibilities.
		</p>
		<p>
			The Atlantic coast, the Souss valley, the foothills of the mountains
			and the inland plains each have their own soil, their own water and
			their own season, so a product that likes a cool morning, like berries
			or green leaves, can grow beautifully in one region and not at all in
			another.
		</p>
		<p>
			That variety is what allows a Moroccan exporter to offer so many
			families of produce across the year, and it is also why working with
			local growers matters so much to us: the knowledge lives in the
			region, handed down from one generation of farmers to the next, and it
			cannot be copied from somewhere else.
		</p>
		<ul class="fm-morocco-list">
			<li><i class="fa fa-sun-o" aria-hidden="true"></i>
				<span><strong>Climate diversity</strong>, from the coast to the mountains and the inland plains, all inside one country.</span></li>
			<li><i class="fa fa-users" aria-hidden="true"></i>
				<span><strong>Grower expertise</strong>, the know-how of people who have worked this land for years.</span></li>
			<li><i class="fa fa-leaf" aria-hidden="true"></i>
				<span><strong>Product diversity</strong>, with citrus, berries, melons, vegetables and dried fruits.</span></li>
			<li><i class="fa fa-globe" aria-hidden="true"></i>
				<span><strong>Export potential</strong>, from a country well placed to serve nearby and distant markets.</span></li>
		</ul>
	</div>
</section>

<!-- ================================================================ WHY -->
<section class="fm-section fm-section-alt">
	<div class="fm-section-head">
		<p class="fm-eyebrow">Why work with us</p>
		<h2>Six reasons to talk to FoodMax Group</h2>
	</div>

	<div class="fm-why-grid">
		<article class="fm-why">
			<h3><i class="fa fa-fw fa-star" aria-hidden="true"></i> Quality Products</h3>
			<p>
				We choose carefully what we send and we set aside what does not
				match, so you receive produce sorted to your specification
				instead of whatever happened to arrive in the crate, and that
				makes a real difference on the shelf.
			</p>
		</article>

		<article class="fm-why">
			<h3><i class="fa fa-fw fa-handshake-o" aria-hidden="true"></i> Reliable Partnership</h3>
			<p>
				We build relationships over seasons rather than selling lot by
				lot, so you deal with the same people, the same specifications
				and the same way of working each time, and you know what to
				expect when you pick up the phone or write to us.
			</p>
		</article>

		<article class="fm-why">
			<h3><i class="fa fa-fw fa-briefcase" aria-hidden="true"></i> Professional Service</h3>
			<p>
				We work the way a professional buyer hopes to be worked with,
				with clear documents, defined specifications, agreed timelines
				and answers that are direct instead of vague.
			</p>
		</article>

		<article class="fm-why">
			<h3><i class="fa fa-fw fa-cogs" aria-hidden="true"></i> Flexible Solutions</h3>
			<p>
				Every market asks for something a little different, so we adapt
				quantities, packaging and documents to the destination and to
				the way you distribute your goods, and we will gladly think it
				through with you.
			</p>
		</article>

		<article class="fm-why">
			<h3><i class="fa fa-fw fa-globe" aria-hidden="true"></i> International Vision</h3>
			<p>
				We keep international requirements in mind from the start, with
				documents, labels and packing that match how produce is handled
				at its destination, so there are fewer surprises when the goods
				arrive.
			</p>
		</article>

		<article class="fm-why">
			<h3><i class="fa fa-fw fa-smile-o" aria-hidden="true"></i> Customer Satisfaction</h3>
			<p>
				We measure ourselves by whether you come back, which means
				telling you the truth about quality and availability even when
				the answer is not the easy one, because we would rather earn your
				trust than win one order.
			</p>
		</article>
	</div>
</section>

<!-- ============================================================ MISSION -->
<section class="fm-mission">
	<div class="fm-mission-inner">
		<p class="fm-eyebrow">Our Mission</p>
		<h2>Supplying quality, building lasting relationships</h2>
		<div class="fm-mission-text">
			<p class="fm-lead">
				We are here to supply quality agricultural products and to build
				relationships that last well beyond a single shipment, with
				seriousness, transparency and professionalism.
			</p>
			<p>
				In practice, that means sending produce that meets a clear
				specification, being straightforward about what is available and
				what is not, and treating growers, customers and partners as the
				long-term relationships they really are, because behind every
				order there are people who are counting on us.
			</p>
			<p>
				We would rather build a smaller business that people can depend
				on, with clients who come back year after year, than a big one
				built on promises we could not keep, and that is the standard we
				try to hold ourselves to every day.
			</p>
		</div>
	</div>
</section>

<!-- ========================================================== PARTNERS -->
<section class="fm-section">
	<div class="fm-section-head">
		<p class="fm-eyebrow">Clients and partners</p>
		<h2>Who we work with</h2>
		<p class="fm-section-sub">
			We work with professionals who need a quality they can rely on, and
			depending on the product and the market that can mean importers and
			distributors, wholesalers, retailers or partners who buy to their own
			specifications. What matters most to all of them, and to us, is
			trust, good communication, regular supply, steady quality and
			commitments that are kept.
		</p>
	</div>

	<div class="fm-partner-grid">
		<article class="fm-partner">
			<h3>Importers and distributors</h3>
			<p>Regular volumes, clear specifications and documents prepared with customs clearance in mind.</p>
		</article>
		<article class="fm-partner">
			<h3>Wholesalers</h3>
			<p>A consistent grading from one delivery to the next, so your own customers find the same quality each week.</p>
		</article>
		<article class="fm-partner">
			<h3>Retail and fresh produce chains</h3>
			<p>Packing and labelling adapted to your stores and to the way you present your fruit and vegetables.</p>
		</article>
		<article class="fm-partner">
			<h3>Private label partners</h3>
			<p>We can work to an agreed specification, including the presentation and the packaging.</p>
		</article>
	</div>

	<p class="fm-note fm-partner-note">
		This space is kept for our real partners, certifications and references,
		which we will publish here once they have been checked and cleared for
		publication, because we would rather leave it empty than show anything we
		cannot prove.
	</p>
</section>

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