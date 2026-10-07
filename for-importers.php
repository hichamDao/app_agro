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
	<title>Fresh Produce Supply for Importers | FoodMax Group</title>
	<meta name="description" content="FoodMax Group supplies fresh Moroccan fruits and vegetables to international importers. Reliable sourcing, quality control, traceability, cold chain, custom packaging and export logistics.">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<?php fm_seo_head(array(
    'path'        => 'for-importers/',
    'title'       => 'Fresh Produce Supply for International Importers',
    'description' => 'FoodMax Group supplies fresh Moroccan fruits and vegetables to international importers. Reliable sourcing, quality control, traceability, cold chain, custom packaging and export logistics.',
    'jsonld'      => array(fm_seo_breadcrumb(array(array('Home', ''), array('For importers', 'for-importers/')))),
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
		<span class="fm-eyebrow">For importers</span>
		<h1>Fresh Produce Supply for International Importers</h1>
		<p>Are you looking for a reliable Moroccan supplier of fresh fruit and vegetables? Here is what an importer can expect from FoodMax Group, and how we like to start working together.</p>

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
			We take care of the whole chain, from the Moroccan grower to your warehouse, so that you can concentrate on your own business.
		</p>
	</div>

	<div class="fm-quality-grid">
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-check" aria-hidden="true"></i></span>
			<h3>Moroccan origin</h3>
			<p>Produce from growers across Morocco, from the regions where each product grows best.</p>
		</div>
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-check" aria-hidden="true"></i></span>
			<h3>Product sourcing</h3>
			<p>We look for the right variety, size and quality for your market.</p>
		</div>
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-check" aria-hidden="true"></i></span>
			<h3>Quality control</h3>
			<p>Lots are inspected at harvest and at packing, and what does not match your specification is set aside.</p>
		</div>
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-check" aria-hidden="true"></i></span>
			<h3>Traceability</h3>
			<p>We keep the link between the grower, the lot and your shipment as clear as we can, and we tell you honestly where our records stop.</p>
		</div>
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-check" aria-hidden="true"></i></span>
			<h3>Cold chain</h3>
			<p>Quick cooling after harvest and refrigerated transport at the right temperature for each product.</p>
		</div>
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-check" aria-hidden="true"></i></span>
			<h3>Custom packaging</h3>
			<p>Cartons, trays, punnets, retail packs, bulk and private label, all to your specification.</p>
		</div>
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-check" aria-hidden="true"></i></span>
			<h3>Export logistics</h3>
			<p>Export documents, including a phytosanitary certificate where your country asks for one, and transport by road or by sea.</p>
		</div>
	</div>
</section>

<!-- ============================================================ HOW TO START -->
<section class="fm-process">
	<div class="fm-section-head fm-section-head-center">
		<p class="fm-eyebrow">Getting started</p>
		<h2>How we like to begin</h2>
		<p class="fm-section-sub">
			There is no need to have everything worked out. We would much rather earn a second order than push for a large first one.
		</p>
	</div>

	<ol class="fm-steps">
		<li class="fm-step">
			<span class="fm-step-num">01</span>
			<h3>Tell us what you need</h3>
			<p>The product, the quantity, the destination and the timing. Even a rough idea is enough to begin the conversation.</p>
		</li>
		<li class="fm-step">
			<span class="fm-step-num">02</span>
			<h3>We send a proposal</h3>
			<p>We come back to you within one business day with what is available, how it can be packed and how it would travel.</p>
		</li>
		<li class="fm-step">
			<span class="fm-step-num">03</span>
			<h3>A trial shipment</h3>
			<p>A first, smaller shipment lets you judge the quality, the packing and the way we work with real goods in your hands.</p>
		</li>
		<li class="fm-step">
			<span class="fm-step-num">04</span>
			<h3>Regular supply</h3>
			<p>If you are happy, we plan the season together, with the volumes and the specification that suit you.</p>
		</li>
	</ol>
</section>

<!-- ============================================================ PRODUCTS -->
<section class="fm-section fm-section-alt">
	<div class="fm-section-head">
		<p class="fm-eyebrow">Our range</p>
		<h2>Moroccan fresh produce available</h2>
		<p class="fm-section-sub">
			Each family has its own season, and we will tell you plainly what is available when you ask. Click on a product to read our guide for buyers.
		</p>
	</div>

	<?php fm_export_grid(); ?>
</section>

<!-- ============================================================ CTA -->
<section class="fm-cta">
	<div class="fm-cta-inner">
		<p class="fm-eyebrow">Next step</p>
		<h2>Request an export quote</h2>
		<p>
			Tell us the product, the quantity, the destination and the timing, and we will come back to you
			with a proposal within one business day.
		</p>
		<div class="fm-cta-actions">
			<a class="btn" href="<?php echo $fm_app; ?>contact/">Request an export quote</a>
			<a class="btn btn-outline" href="<?php echo $fm_app; ?>products/">Browse the catalogue</a>
		</div>
	</div>
</section>

<?php require_once(__DIR__ . "/includes/page-close.php"); ?>