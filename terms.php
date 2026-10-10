<?php
/**
 * Conditions de vente.
 *
 * Ce site est un site de presentation B2B : aucune commande ni paiement n y est
 *possible. Les conditions commerciales sont etablies par devis et contrat. Le
 * texte ci-dessous ne fait que decrire ce fonctionnement ; il ne constitue pas
 * un contrat et doit etre relu par un juriste avant publication definitive.
 *
 * _header.php ouvre la session : il doit etre inclus AVANT tout affichage.
 */
require_once(__DIR__ . "/includes/paths.php");
require_once(__DIR__ . "/includes/_header.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<title>Terms of sale | FoodMax Group</title>
	<meta name="description" content="How orders and commercial relationships with FoodMax Group work.">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="stylesheet" type="text/css" href="<?php echo $fm_app; ?>css/phlox.css<?php echo fm_ver('css/phlox.css'); ?>">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,400i,600,600i,700,700i&display=swap">
	<link rel="stylesheet" type="text/css" href="<?php echo $fm_app; ?>css/font-awesome.min.css<?php echo fm_ver('css/font-awesome.min.css'); ?>">

	<script type="text/javascript" src="<?php echo $fm_app; ?>js/jquery.min.js<?php echo fm_ver('js/jquery.min.js'); ?>"></script>
	<script type="text/javascript" src="<?php echo $fm_app; ?>js/setting.js<?php echo fm_ver('js/setting.js'); ?>"></script>

	<script async src="https://www.googletagmanager.com/gtag/js?id=G-ZPRWMT85SP"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag() { dataLayer.push(arguments); }
		gtag('js', new Date());
		gtag('config', 'G-ZPRWMT85SP');
	</script>
</head>

<?php require_once((__DIR__ . "/includes/header-inc.php")); ?>

<section class="fm-pagehead">
	<div class="fm-pagehead-inner">
		<span class="fm-eyebrow">Legal</span>
		<h1>Terms of sale</h1>
		<p>
			How a purchase from FoodMax Group actually works, and what you can
			expect from us at each step.
		</p>

		<nav class="fm-crumbs" aria-label="Breadcrumb">
			<ol>
				<li><a href="<?php echo $fm_app; ?>">Home</a></li>
				<li>Terms of sale</li>
			</ol>
		</nav>
	</div>
</section>

<section class="fm-section">
	<div class="fm-section-head">
		<h2>How an order works</h2>
	</div>

	<div class="fm-legal">
		<p>
			This website is a presentation site. You can browse our products,
			look at the gallery and contact us, but no order is placed and no
			payment is taken online.
		</p>

		<h3>1. Your request</h3>
		<p>
			You tell us the products you are interested in, the quantities, the
			packing you need and the destination. The more precise the request, the
			more useful our answer will be.
		</p>

		<h3>2. Availability</h3>
		<p>
			Agricultural production follows the season. We answer honestly about
			whether a product is available, when it is at its best, and when we would
			advise against a shipment. If a variety is not suitable for its intended
			market, we say so before you commit.
		</p>

		<h3>3. Quotation</h3>
		<p>
			Prices, volumes and transport are established case by case, in a written
			quotation. They depend on the product, its grade, the quantity, the
			packing, the destination and the season. Nothing on this site constitutes
			a price.
		</p>

		<h3>4. Quality specification</h3>
		<p>
			Before a shipment is confirmed, we agree the specification in writing:
			variety, size, grade, maturity, packing and labelling. What is agreed in
			that document is what we select and ship.
		</p>

		<h3>5. Packing and documents</h3>
		<p>
			Packing is chosen for the product and the journey. Documentation depends
			on your market and is prepared in advance. We confirm with you which
			documents are required for your destination.
		</p>

		<h3>6. Transport</h3>
		<p>
			The cold chain is maintained from our packing facility to your delivery
			point. We plan the route and keep you informed during transit so that you
			can organise your side on arrival.
		</p>

		<h3>7. On arrival</h3>
		<p>
			Please check the goods against the agreed specification as soon as they
			arrive, and tell us straight away about any difference. We can only act
			on a discrepancy that is reported promptly and with photographs.
		</p>

		<h3>Payment and governing terms</h3>
		<p>
			Payment terms, incoterms and the governing law are specified in the
			quotation and the sales contract for each order. They are not defined by
			this page.
		</p>

		<h3>Content of this website</h3>
		<p>
			The information on this site is given for guidance. Availability and
			seasonality change, and we will always confirm the current situation when
			you contact us. Product photographs show the produce we handle, without
			guaranteeing a particular grade or presentation.
		</p>
	</div>

	<div class="fm-note">
		This page describes how we work, but it is not a contract. The commercial
		terms of each order are those of the written quotation and the sales
		contract signed with the customer. This text should be reviewed by a legal
		adviser before being treated as the official terms of sale.
	</div>
</section>

<?php require_once((__DIR__ . "/includes/footer.php")); ?>

</body>
</html>