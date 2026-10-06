<?php
/* _header.php ouvre la session : il doit etre inclus AVANT tout affichage,
   sinon les en-tetes HTTP sont deja partis et session_start() echoue. */
require_once(__DIR__ . "/includes/paths.php");
require_once(__DIR__ . "/includes/_header.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<title>Fresh Produce Supply for International Importers | Foodmax</title>
	<meta name="description" content="FoodMax Group supplies fresh Moroccan fruits and vegetables to international importers. Reliable sourcing, quality control, traceability, cold chain, custom packaging and export logistics.">
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
		<span class="fm-eyebrow">For importers</span>
		<h1>Fresh Produce Supply for International Importers</h1>
		<p>Are you looking for a reliable Moroccan supplier for fresh fruits and vegetables</p>

		<nav class="fm-crumbs" aria-label="Breadcrumb">
			<ol>
				<li><a href="<?php echo $fm_app; ?>">Home</a></li>
				<li>For Importers</li>
			</ol>
		</nav>
	</div>
</section>

<!-- ============================================================ WHAT WE OFFER -->
<section class="fm-section">
	<div class="fm-section-head">
		<p class="fm-eyebrow">What we offer</p>
		<h2>Your supply partner in Morocco</h2>
		<p class="fm-section-sub">
			We handle the full chain from Moroccan growers to your warehouse so you can focus on your business
		</p>
	</div>

	<div class="fm-quality-grid">
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-check" aria-hidden="true"></i></span>
			<h3>Moroccan origin</h3>
			<p>Direct from growers across Moroccos best production regions</p>
		</div>
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-check" aria-hidden="true"></i></span>
			<h3>Product sourcing</h3>
			<p>We find the right variety size and quality for your market</p>
		</div>
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-check" aria-hidden="true"></i></span>
			<h3>Quality control</h3>
			<p>Inspection at harvest and packhouse for size colour firmness brix and compliance</p>
		</div>
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-check" aria-hidden="true"></i></span>
			<h3>Traceability</h3>
			<p>Field to shipment traceability with documentation for every lot</p>
		</div>
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-check" aria-hidden="true"></i></span>
			<h3>Cold chain</h3>
			<p>Pre cooling refrigerated transport temperature monitored door to door</p>
		</div>
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-check" aria-hidden="true"></i></span>
			<h3>Custom packaging</h3>
			<p>Retail clamshells flow wrap bulk bins private label your specs</p>
		</div>
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-check" aria-hidden="true"></i></span>
			<h3>Export logistics</h3>
			<p>Phytosanitary certificates customs paperwork sea air and land freight</p>
		</div>
	</div>
</section>

<!-- ============================================================ PRODUCTS -->
<section class="fm-section fm-section-alt">
	<div class="fm-section-head">
		<p class="fm-eyebrow">Our range</p>
		<h2>Moroccan fresh produce available</h2>
		<p class="fm-section-sub">
			Twelve product families each in season we supply what you need when you need it
		</p>
	</div>

	<div class="fm-export-grid">
		<article class="fm-export">
			<span class="fm-export-icon"><i class="fa fa-fw fa-circle" aria-hidden="true"></i></span>
			<h3>Tomatoes</h3>
			<p>Cherry plum round coloured October to May</p>
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
			<p>Green yellow patty pan year round greenhouse</p>
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
		<h2>Request an Export Quote</h2>
		<p>
			Tell us the product volume destination and timing and we will send a proposal within one business day
		</p>
		<div class="fm-cta-actions">
			<a class="btn" href="<?php echo $fm_app; ?>contact/">Request an Export Quote</a>
			<a class="btn btn-outline" href="<?php echo $fm_app; ?>products/">Browse the catalogue</a>
		</div>
	</div>
</section>

<?php require_once(__DIR__ . "/includes/page-close.php"); ?>