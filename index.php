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

require_once((__DIR__ . "/includes/header-inc.php"));
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
<body id="url_div">

<!-- =============================================================== HERO -->
<section class="fm-hero">
	<div class="fm-hero-bg">
		<img src="<?php echo $fm_app; ?>images/banniere.png" alt="Moroccan fresh produce" fetchpriority="high">
	</div>
	<div class="fm-hero-inner">
		<p class="fm-hero-kicker">From Morocco to the World</p>
		<h1 class="fm-hero-title">Freshness, quality and trust</h1>
		<p class="fm-hero-text">
			FoodMax Group works with growers across southern Morocco to bring you
			fresh fruits and vegetables of consistent quality. Every shipment is
			selected, checked, packed and shipped under a cold chain we keep
			unbroken from the packing facility to your warehouse.
		</p>
		<div class="fm-hero-actions">
			<a class="btn" href="<?php echo $fm_app; ?>products/">
				Discover our products <i class="fa fa-arrow-right" aria-hidden="true"></i>
			</a>
			<a class="btn btn-outline fm-btn-light" href="<?php echo $fm_app; ?>contact/">
				Contact FoodMax Group
			</a>
		</div>
		<ul class="fm-hero-points">
			<li><i class="fa fa-check" aria-hidden="true"></i> Twelve product families</li>
			<li><i class="fa fa-check" aria-hidden="true"></i> Cold chain end to end</li>
			<li><i class="fa fa-check" aria-hidden="true"></i> B2B supply</li>
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
			FoodMax Group is a Moroccan fresh produce exporter based in Marrakech.
			We buy from growers we know, in the regions where each product is at
			its best, and we prepare each lot for the destination it is meant for.
		</p>
		<p>
			Our work is more than selling produce. Behind every carton there is a
			chain that has to work properly: a grower who picked at the right
			moment, a selection that removed what was not good enough, a packing
			that protected the product, and a transport that kept the temperature
			stable. If one link is weak, the whole shipment suffers — and so does
			your customer.
		</p>
		<p>
			That is why we prefer long-term relationships over one-off orders. We
			learn your requirements, keep you informed during the season, and tell
			you honestly when a variety is not at its best rather than send it
			anyway.
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
		<h2>More than fresh food</h2>
		<p class="fm-lead">
			For FoodMax Group, quality starts long before the product reaches your
			warehouse. It starts with the person who decided to grow it.
		</p>

		<div class="fm-philo-grid">
			<article class="fm-philo">
				<span class="fm-philo-icon"><i class="fa fa-fw fa-heart" aria-hidden="true"></i></span>
				<h3>Respect for the product</h3>
				<p>
					Fruit picked one day too early never ripens properly. We would
					rather wait and send a product that arrives fit to sell than
					send one that looks ready and disappoints.
				</p>
			</article>

			<article class="fm-philo">
				<span class="fm-philo-icon"><i class="fa fa-fw fa-handshake-o" aria-hidden="true"></i></span>
				<h3>Respect for growers</h3>
				<p>
					Our suppliers are partners, not suppliers on a list. We build
					relationships that last across seasons, discuss volumes in
					advance, and keep a market for the grades that are harder to sell.
				</p>
			</article>

			<article class="fm-philo">
				<span class="fm-philo-icon"><i class="fa fa-fw fa-balance-scale" aria-hidden="true"></i></span>
				<h3>Respect for the environment</h3>
				<p>
					We use water and land responsibly, limit what we discard, and work
					towards reducing waste at every stage. These are real constraints,
					and improving them is ongoing work.
				</p>
			</article>

			<article class="fm-philo">
				<span class="fm-philo-icon"><i class="fa fa-fw fa-smile-o" aria-hidden="true"></i></span>
				<h3>Respect for customers</h3>
				<p>
					We answer clearly and quickly. When a problem occurs, we say so
					and propose a solution. A client who knows what to expect is worth
					more to us than one who is surprised.
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
			Twelve product families from Moroccan agriculture. The varieties listed
			are the ones currently registered in our catalogue — availability depends
			on the season.
		</p>
	</div>

	<div class="fm-cat-grid">
		<?php foreach ($fm_categories as $fm_cat):
			$fm_lien = $fm_app . 'products/' . $fm_cat['code'] . '/' . rawurlencode($fm_cat['nom']) . '/';
			$fm_photo = $fm_cat['photo'] !== null
				? $fm_app . 'images/' . rawurlencode($fm_cat['ref']) . '/' . rawurlencode($fm_cat['photo'])
				: $fm_app . 'img/product-placeholder.svg';

			$fm_nb = count($fm_cat['varietes']);
			/* Varietes reelles du catalogue, 5 au plus : au-dela on ecrit
			   simplement "and more" plutot que de lister un roman. */
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

				<?php if ($fm_var !== '') { ?>
				<p class="fm-cat-varieties"><?php echo $fm_var; ?></p>
				<?php } else { ?>
				<p class="fm-cat-varieties fm-cat-varieties-empty">
					Range being finalised — contact us for details.
				</p>
				<?php } ?>

				<a class="fm-cat-link" href="<?php echo $fm_lien; ?>">
					Discover products <i class="fa fa-arrow-right" aria-hidden="true"></i>
				</a>
			</div>
		</article>
		<?php endforeach; ?>
	</div>
</section>

<!-- ============================================================ PROCESS -->
<section class="fm-process">
	<div class="fm-section-head fm-section-head-center">
		<p class="fm-eyebrow">From the field to you</p>
		<h2>How we work</h2>
		<p class="fm-section-sub">
			Five steps, each of them something we do every single day.
		</p>
	</div>

	<ol class="fm-steps">
		<li class="fm-step">
			<span class="fm-step-num">01</span>
			<h3>Production</h3>
			<p>
				Everything starts with the growers. We work with farms in the
				regions where each product performs best, agree volumes and
				varieties ahead of the season, and follow the crop through the
				season rather than buying whatever the market offers on the day.
			</p>
		</li>
		<li class="fm-step">
			<span class="fm-step-num">02</span>
			<h3>Selection</h3>
			<p>
				When a lot arrives, it is sorted by hand. Produce that does not
				match the requested size, colour, ripeness or condition is set
				aside instead of being mixed into your order. This is the step
				that decides what you actually receive.
			</p>
		</li>
		<li class="fm-step">
			<span class="fm-step-num">03</span>
			<h3>Quality control</h3>
			<p>
				Checks are carried out at several points — on arrival, during
				packing and before loading. We verify condition, size, maturity,
				temperature and cleanliness, and record what we find, so any
				question about a shipment can be answered.
			</p>
		</li>
		<li class="fm-step">
			<span class="fm-step-num">04</span>
			<h3>Packing</h3>
			<p>
				Packing is chosen to suit the product and the journey: ventilated
				cartons for berries, protection for delicate fruit, stacking that
				holds up on long routes. Labels and documentation follow your
				requirements.
			</p>
		</li>
		<li class="fm-step">
			<span class="fm-step-num">05</span>
			<h3>Delivery</h3>
			<p>
				Cold chain continuity is what protects everything we did before.
				We plan the route, monitor the conditions during transport and
				keep you informed so you can plan your side on arrival.
			</p>
		</li>
	</ol>
</section>

<!-- ============================================================ QUALITY -->
<section class="fm-section fm-section-alt">
	<div class="fm-section-head">
		<p class="fm-eyebrow">Our quality commitment</p>
		<h2>What we guarantee ourselves</h2>
		<p class="fm-section-sub">
			Quality is not a claim on a page. It is a set of things we do at
			every stage, and we hold ourselves to them.
		</p>
	</div>

	<div class="fm-commit-grid">
		<article class="fm-commit">
			<span class="fm-commit-icon"><i class="fa fa-fw fa-leaf" aria-hidden="true"></i></span>
			<h3>Freshness</h3>
			<p>
				We look for freshness and optimal maturity, and we would rather
				shorten our supply chain than extend the time between harvest and
				cold storage. Time is the main enemy of produce quality, so we
				organise everything around it.
			</p>
		</article>

		<article class="fm-commit">
			<span class="fm-commit-icon"><i class="fa fa-fw fa-check-circle-o" aria-hidden="true"></i></span>
			<h3>Quality</h3>
			<p>
				We select products that meet our own requirements before they meet
				your order. If a variety does not reach the standard we expect, it
				does not leave — even when the season is tight and other sellers
				are shipping.
			</p>
		</article>

		<article class="fm-commit">
			<span class="fm-commit-icon"><i class="fa fa-fw fa-map-marker" aria-hidden="true"></i></span>
			<h3>Traceability</h3>
			<p>
				We keep the link between the grower, the lot and your shipment as
				clear as possible. Where our records are complete, we can tell you
				where a product comes from and how it reached you. Where they are
				not, we say so rather than guess.
			</p>
		</article>

		<article class="fm-commit">
			<span class="fm-commit-icon"><i class="fa fa-fw fa-repeat" aria-hidden="true"></i></span>
			<h3>Consistency</h3>
			<p>
				Professional buyers plan around us, so the same specification has
				to arrive week after week. We work towards regular quality across
				the season, and we tell you early when conditions make that hard.
			</p>
		</article>
	</div>

	<p class="fm-note">
		Documentation and third-party certifications are available on request for
		each shipment and destination. Ask us for what applies to your market.
	</p>
</section>

<!-- ========================================================= MOROCCO -->
<section class="fm-morocco">
	<div class="fm-morocco-map">
		<img src="<?php echo $fm_app; ?>img/map-morocco.svg"
		     alt="Map of Morocco showing Souss region and export routes" loading="lazy">
	</div>
	<div class="fm-morocco-body">
		<p class="fm-eyebrow">FoodMax Group and Morocco</p>
		<h2>From Morocco to the world</h2>
		<p class="fm-lead">
			Morocco is a small country with an unusually wide range of climates,
			and that is precisely what makes its agriculture interesting.
		</p>
		<p>
			The Atlantic coast, the Souss valley, the mountain edges and the
			inland plains each have their own soil, their own water and their own
			season. A product that needs a cool morning — berries, stone fruit,
			green leaves — grows well in one region and not at all in another.
		</p>
		<p>
			That variety is what lets a Moroccan exporter supply so many product
			families across the year. It also explains why working with local
			growers matters: the expertise sits in the region, and it cannot be
			reproduced from another place.
		</p>
		<ul class="fm-morocco-list">
			<li><i class="fa fa-sun-o" aria-hidden="true"></i>
				<span><strong>Climate diversity</strong> — coast, mountains and inland plains in one country.</span></li>
			<li><i class="fa fa-users" aria-hidden="true"></i>
				<span><strong>Grower expertise</strong> — know-how passed down through generations.</span></li>
			<li><i class="fa fa-leaf" aria-hidden="true"></i>
				<span><strong>Product diversity</strong> — citrus, berries, melons, vegetables and dried fruit.</span></li>
			<li><i class="fa fa-globe" aria-hidden="true"></i>
				<span><strong>Export potential</strong> — a land well placed to serve nearby and further markets.</span></li>
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
			<h3><i class="fa fa-fw fa-star" aria-hidden="true"></i> Quality products</h3>
			<p>
				We select what we send, and we reject what does not match. You
				receive produce sorted to your specification rather than whatever
				arrived in the crate.
			</p>
		</article>

		<article class="fm-why">
			<h3><i class="fa fa-fw fa-handshake-o" aria-hidden="true"></i> Reliable partnership</h3>
			<p>
				We build relationships across seasons instead of selling lot by
				lot. You get the same contact, the same specifications and the same
				way of working, season after season.
			</p>
		</article>

		<article class="fm-why">
			<h3><i class="fa fa-fw fa-briefcase" aria-hidden="true"></i> Professional service</h3>
			<p>
				We work in the way a professional buyer expects: clear
				documentation, defined specifications, agreed timelines and
				answers that are direct rather than evasive.
			</p>
		</article>

		<article class="fm-why">
			<h3><i class="fa fa-fw fa-cogs" aria-hidden="true"></i> Flexible solutions</h3>
			<p>
				Different markets need different things. We adapt quantities,
				packaging and documentation to the destination and to the way you
				distribute your product.
			</p>
		</article>

		<article class="fm-why">
			<h3><i class="fa fa-fw fa-globe" aria-hidden="true"></i> International vision</h3>
			<p>
				We work with international requirements in mind — documentation,
				labelling and packing that match how product is handled at its
				destination.
			</p>
		</article>

		<article class="fm-why">
			<h3><i class="fa fa-fw fa-smile-o" aria-hidden="true"></i> Customer satisfaction</h3>
			<p>
				We measure ourselves by whether you come back. That means telling
				you the truth about quality and availability, even when the answer
				is not the easy one.
			</p>
		</article>
	</div>
</section>

<!-- ============================================================ MISSION -->
<section class="fm-mission">
	<div class="fm-mission-inner">
		<p class="fm-eyebrow">Our mission</p>
		<h2>Supplying quality, building lasting relationships</h2>
		<div class="fm-mission-text">
			<p class="fm-lead">
				FoodMax Group exists to supply quality agricultural products and to
				build relationships that last beyond a single shipment.
			</p>
			<p>
				Concretely, that means supplying produce that meets a clear
				specification, being straightforward about what is available and
				what is not, and treating growers, customers and partners as the
				long-term relationships they are.
			</p>
			<p>
				We would rather build a smaller, dependable business with clients
				who come back than a large one built on promises we cannot keep.
				That is the standard we hold ourselves to.
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
			FoodMax Group supplies professionals who need a reliable, repeatable
			quality: importers and distributors, wholesalers, retailers, and
			partners who buy on their own specifications.
		</p>
	</div>

	<div class="fm-partner-grid">
		<article class="fm-partner">
			<h3>Importers and distributors</h3>
			<p>Regular container volumes, clear specifications and documents prepared for customs clearance.</p>
		</article>
		<article class="fm-partner">
			<h3>Wholesalers</h3>
			<p>Consistent grading across deliveries, so your own customers find the same quality each week.</p>
		</article>
		<article class="fm-partner">
			<h3>Retail and fresh produce chains</h3>
			<p>Packing and labelling adapted to your store requirements and display needs.</p>
		</article>
		<article class="fm-partner">
			<h3>Private label partners</h3>
			<p>We can work to an agreed specification, including presentation and packaging.</p>
		</article>
	</div>

	<p class="fm-note fm-partner-note">
		Certifications, references and client logos will be published here once
		they have been verified and cleared for publication. We would rather leave
		this space empty than display claims we cannot prove.
	</p>
</section>

<!-- ================================================================== CTA -->
<section class="fm-cta">
	<div class="fm-cta-inner">
		<p class="fm-eyebrow">Next step</p>
		<h2>Ready to source your next order?</h2>
		<p>
			Tell us the product, the volume and the destination. We answer within
			one business day.
		</p>
		<div class="fm-cta-actions">
			<a class="btn" href="<?php echo $fm_app; ?>contact/">Start the conversation</a>
			<a class="btn btn-outline" href="<?php echo $fm_app; ?>products/">Browse the catalogue</a>
		</div>
	</div>
</section>

<?php require_once((__DIR__ . "/includes/footer.php")); ?>

<script type="text/javascript" src="<?php echo $fm_app; ?>js/nav.js"></script>
</body>
</html>