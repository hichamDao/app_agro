<?php
/* _header.php ouvre la session : il doit etre inclus AVANT tout affichage,
   sinon les en-tetes HTTP sont deja partis et session_start() echoue. */
require_once(__DIR__ . "/includes/paths.php");
require_once(__DIR__ . "/includes/_header.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<title>Fresh Produce Export Services from Morocco | Foodmax</title>
	<meta name="description" content="FoodMax Group export services from Morocco: product sourcing, quality control, packing, cold chain management, export documentation, and shipping to international markets.">
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

<?php require_once((__DIR__ . "/includes/header-inc.php")); ?>

<!-- ============================================================ PAGE HEAD -->
<section class="fm-pagehead">
	<div class="fm-pagehead-inner">
		<span class="fm-eyebrow">Export services</span>
		<h1>Fresh Produce Export Services from Morocco</h1>
		<p>FoodMax Group handles every step from the Moroccan field to your warehouse sourcing quality produce controlling quality packing to your specs maintaining the cold chain preparing export documents and organizing shipment</p>

		<nav class="fm-crumbs" aria-label="Breadcrumb">
			<ol>
				<li><a href="<?php echo $fm_app; ?>">Home</a></li>
				<li>Export Services</li>
			</ol>
		</nav>
	</div>
</section>

<!-- ============================================================ PROCESS STEPS -->
<section class="fm-section">
	<div class="fm-section-head">
		<p class="fm-eyebrow">Our process</p>
		<h2>Six steps from field to destination</h2>
		<p class="fm-section-sub">
			We manage the full export chain so you receive fresh produce on time and in perfect condition
		</p>
	</div>

	<div class="fm-steps">
		<article class="fm-step">
			<span class="fm-step-num">01</span>
			<h3>Product sourcing</h3>
			<p>We select produce directly from growers across Morocco matching your variety size and quality requirements</p>
		</article>

		<article class="fm-step">
			<span class="fm-step-num">02</span>
			<h3>Quality control</h3>
			<p>Every lot is inspected at harvest and at the packhouse for size colour firmness brix level and phytosanitary compliance</p>
		</article>

		<article class="fm-step">
			<span class="fm-step-num">03</span>
			<h3>Packing</h3>
			<p>We pack to your specification retail clamshells flow wrap bulk bins private label everything prepared for your market</p>
		</article>

		<article class="fm-step">
			<span class="fm-step-num">04</span>
			<h3>Cold chain</h3>
			<p>Pre cooling hydrocooling refrigerated trucks and containers temperature monitored from field to your door</p>
		</article>

		<article class="fm-step">
			<span class="fm-step-num">05</span>
			<h3>Documentation</h3>
			<p>Phytosanitary certificates certificates of origin GlobalGAP BRC IFS customs paperwork we prepare it all</p>
		</article>

		<article class="fm-step">
			<span class="fm-step-num">06</span>
			<h3>Shipping</h3>
			<p>Sea freight air freight land transport we book the space coordinate loading and track the shipment until delivery</p>
		</article>
	</div>
</section>

<!-- ============================================================ WHY US -->
<section class="fm-section fm-section-alt">
	<div class="fm-section-head">
		<p class="fm-eyebrow">Why FoodMax</p>
		<h2>What you get with every shipment</h2>
	</div>

	<div class="fm-quality-grid">
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-leaf"></i></span>
			<h3>Fresh</h3>
			<p>Harvested at peak ripeness cooled fast shipped cold so it arrives in top condition</p>
		</div>
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-check-circle"></i></span>
			<h3>Reliable</h3>
			<p>We ship on time every time and communicate proactively if anything changes</p>
		</div>
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-file-text-o"></i></span>
			<h3>Compliant</h3>
			<p>All documentation ready for customs no surprises at the border</p>
		</div>
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-globe"></i></span>
			<h3>Global</h3>
			<p>Europe Middle East Africa Asia we know the routes the rules and the markets</p>
		</div>
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-handshake-o"></i></span>
			<h3>Flexible</h3>
			<p>Small trial orders or full container programmes we adapt to your volume</p>
		</div>
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-comments-o"></i></span>
			<h3>Transparent</h3>
			<p>One point of contact clear pricing and regular updates from loading to delivery</p>
		</div>
	</div>
</section>

<!-- ============================================================ PRODUCTS WE EXPORT -->
<section class="fm-section">
	<div class="fm-section-head">
		<p class="fm-eyebrow">Our range</p>
		<h2>Moroccan produce we export</h2>
		<p class="fm-section-sub">
			Twelve product families each with its own season we know them all
		</p>
	</div>

	<div class="fm-export-grid">
		<article class="fm-export">
			<span class="fm-export-icon"><i class="fa fa-fw fa-circle" aria-hidden="true"></i></span>
			<h3>Tomatoes</h3>
			<p>Cherry plum round coloured varieties October to May</p>
		</article>
		<article class="fm-export">
			<span class="fm-export-icon"><i class="fa fa-fw fa-circle" aria-hidden="true"></i></span>
			<h3>Citrus</h3>
			<p>Navel oranges blood oranges lemons mandarins November to April</p>
		</article>
		<article class="fm-export">
			<span class="fm-export-icon"><i class="fa fa-fw fa-circle" aria-hidden="true"></i></span>
			<h3>Melons</h3>
			<p>Watermelon seeded and seedless March to August</p>
		</article>
		<article class="fm-export">
			<span class="fm-export-icon"><i class="fa fa-fw fa-circle" aria-hidden="true"></i></span>
			<h3>Peppers</h3>
			<p>Red yellow orange bell peppers October to June</p>
		</article>
		<article class="fm-export">
			<span class="fm-export-icon"><i class="fa fa-fw fa-circle" aria-hidden="true"></i></span>
			<h3>Courgettes</h3>
			<p>Green yellow patty pan year round greenhouse grown</p>
		</article>
		<article class="fm-export">
			<span class="fm-export-icon"><i class="fa fa-fw fa-circle" aria-hidden="true"></i></span>
			<h3>Berries</h3>
			<p>Strawberries raspberries blueberries November to May</p>
		</article>
		<article class="fm-export">
			<span class="fm-export-icon"><i class="fa fa-fw fa-circle" aria-hidden="true"></i></span>
			<h3>Figs</h3>
			<p>Fresh and dried June to September</p>
		</article>
		<article class="fm-export">
			<span class="fm-export-icon"><i class="fa fa-fw fa-circle" aria-hidden="true"></i></span>
			<h3>Dried fruits</h3>
			<p>Apricots raisins dates almonds year round</p>
		</article>
		<article class="fm-export fm-export-more">
			<span class="fm-export-icon"><i class="fa fa-fw fa-ellipsis-h" aria-hidden="true"></i></span>
			<h3>And more</h3>
			<p>Eggplants green leaves pits ask us what is in season</p>
		</article>
	</div>
</section>

<!-- ============================================================ CTA -->
<section class="fm-cta">
	<div class="fm-cta-inner">
		<p class="fm-eyebrow">Next step</p>
		<h2>Ready to source from Morocco</h2>
		<p>
			Tell us what product you need the volume the destination and the timing
			and we will send you a proposal within one business day
		</p>
		<div class="fm-cta-actions">
			<a class="btn" href="<?php echo $fm_app; ?>contact/">Request a quote</a>
			<a class="btn btn-outline" href="<?php echo $fm_app; ?>products/">Browse the catalogue</a>
		</div>
	</div>
</section>

<?php require_once(__DIR__ . "/includes/page-close.php"); ?>