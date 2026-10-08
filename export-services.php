<?php
/* _header.php ouvre la session : il doit etre inclus AVANT tout affichage,
   sinon les en-tetes HTTP sont deja partis et session_start() echoue. */
require_once(__DIR__ . "/includes/paths.php");
require_once(__DIR__ . "/includes/explore.php");
require_once(__DIR__ . "/includes/seo.php");
require_once(__DIR__ . "/includes/_header.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<title>Fresh Produce Export Services from Morocco | FoodMax Group</title>
	<meta name="description" content="FoodMax Group export services from Morocco: sourcing, quality control, packing, cold chain, export documents and shipping to international markets.">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<?php fm_seo_head(array(
    'path'        => 'export-services/',
    'title'       => 'Fresh Produce Export Services from Morocco',
    'description' => 'FoodMax Group export services from Morocco: sourcing, quality control, packing, cold chain, export documents and shipping to international markets.',
    'jsonld'      => array(fm_seo_breadcrumb(array(array('Home', ''), array('Export services', 'export-services/')))),
)); ?>

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
		<p>FoodMax Group looks after every step between a Moroccan field and your warehouse: sourcing, quality control, packing to your specification, the cold chain, export documents and the organisation of the shipment, so that you deal with one team instead of several.</p>

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
			We look after the whole export chain, so that the produce reaches you on time and in the condition you expect.
		</p>
	</div>

	<div class="fm-steps">
		<article class="fm-step">
			<span class="fm-step-num">01</span>
			<h3>Product sourcing</h3>
			<p>We select produce from growers across Morocco, matching the variety, the size and the quality you ask for, and we tell you honestly what the season can and cannot offer.</p>
		</article>

		<article class="fm-step">
			<span class="fm-step-num">02</span>
			<h3>Quality control</h3>
			<p>Lots are checked when they are harvested and again when they are packed, looking at size, colour, firmness and general condition, and anything that does not match your specification is set aside.</p>
		</article>

		<article class="fm-step">
			<span class="fm-step-num">03</span>
			<h3>Packing</h3>
			<p>We pack to your specification, in cartons, trays, punnets, retail packs or bulk, with private label if you wish, so that the goods are ready for your market as soon as they arrive.</p>
		</article>

		<article class="fm-step">
			<span class="fm-step-num">04</span>
			<h3>Cold chain</h3>
			<p>Produce is cooled quickly after harvest and kept at the right temperature for each product, in refrigerated trucks and containers, until it reaches you. If you would like temperature records, just ask us.</p>
		</article>

		<article class="fm-step">
			<span class="fm-step-num">05</span>
			<h3>Documentation</h3>
			<p>Every shipment travels with the paperwork it needs: usually an invoice and a packing list and, depending on the product and the destination, a phytosanitary certificate, a certificate of origin and other customs documents. Tell us where the goods are going and we will explain what is required.</p>
		</article>

		<article class="fm-step">
			<span class="fm-step-num">06</span>
			<h3>Shipping</h3>
			<p>We organise the transport, by road or by sea depending on the product, the distance and how soon you need the goods, we coordinate the loading, and we keep you informed until delivery.</p>
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
			<p>Picked at the right moment, cooled quickly and kept cold, so that it arrives in the best possible condition.</p>
		</div>
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-check-circle"></i></span>
			<h3>Reliable</h3>
			<p>We aim to ship when we say we will, and if anything changes we tell you early instead of letting you find out.</p>
		</div>
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-file-text-o"></i></span>
			<h3>Compliant</h3>
			<p>We prepare the export documents for your market, so there are fewer surprises at the border.</p>
		</div>
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-globe"></i></span>
			<h3>International</h3>
			<p>We supply buyers in Europe and in other markets, and we are happy to look at a new destination together.</p>
		</div>
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-handshake-o"></i></span>
			<h3>Flexible</h3>
			<p>From a small trial shipment to a regular container programme, we adapt to your volume.</p>
		</div>
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-comments-o"></i></span>
			<h3>Transparent</h3>
			<p>One point of contact, clear prices and regular news from loading to delivery.</p>
		</div>
	</div>
</section>

<!-- ============================================================ PRODUCTS WE EXPORT -->
<section class="fm-section">
	<div class="fm-section-head">
		<p class="fm-eyebrow">Our range</p>
		<h2>Moroccan produce we export</h2>
		<p class="fm-section-sub">
			Each family has its own season and its own character. Click on a product to read our guide for buyers.
		</p>
	</div>

	<?php fm_export_grid(); ?>
</section>

<!-- ============================================================ CTA -->
<section class="fm-cta">
	<div class="fm-cta-inner">
		<p class="fm-eyebrow">Next step</p>
		<h2>Ready to source from Morocco?</h2>
		<p>
			Tell us the product, the quantity, the destination and the timing, and we will come back to you
			with a proposal within one business day.
		</p>
		<div class="fm-cta-actions">
			<a class="btn" href="<?php echo $fm_app; ?>contact/">Request a quote</a>
			<a class="btn btn-outline" href="<?php echo $fm_app; ?>products/">Browse the catalogue</a>
		</div>
	</div>
</section>

<?php require_once(__DIR__ . "/includes/page-close.php"); ?>