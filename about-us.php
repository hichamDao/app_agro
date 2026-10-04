<?php
/* _header.php ouvre la session : il doit etre inclus AVANT tout affichage,
   sinon les en-tetes HTTP sont deja partis et session_start() echoue. */
require_once(__DIR__ . "/includes/paths.php");
require_once(__DIR__ . "/includes/_header.php"); ?>
<!DOCTYPE html>
<html lang="en">

<head>
	<title>About us | Foodmax</title>
	<meta name="description" content="Foodmax Group is a Moroccan company specialised in the export and distribution of fresh food. Our mission, our quality approach and our strategy.">
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

<!-- ============================================================ EN-TETE PAGE -->
<section class="fm-pagehead">
	<div class="fm-pagehead-inner">
		<span class="fm-eyebrow">Our story</span>
		<h1>About us</h1>
		<p>Foodmax Group is a Moroccan company specialising in the export and
		   distribution of food, recognised for premium quality.</p>

		<nav class="fm-crumbs" aria-label="Fil d'Ariane">
			<ol>
				<li><a href="<?php echo $fm_app; ?>">Home</a></li>
				<li>About us</li>
			</ol>
		</nav>
	</div>
</section>

<!-- ================================================================== STORY -->
<section class="fm-about fm-about-page">
	<div class="fm-about-media">
		<img src="<?php echo $fm_app; ?>img/photo-about.png" width="1441" height="1092" alt="Foodmax production site" loading="lazy">
	</div>
	<div class="fm-about-body">
		<span class="fm-eyebrow">Who we are</span>
		<h2>A Moroccan exporter, built on quality</h2>
		<p>Foodmax Group is a Moroccan company specialising in the export and
		   distribution of food, recognised for its premium quality. We grow,
		   pack and ship our produce to distributors all over the world, and to
		   several European countries in particular.</p>
		<p>Our strategy and our management rest on one principle: a trustworthy
		   collaboration with our partners, built on contracts honoured and
		   shipments that arrive exactly as they left us.</p>
		<a class="btn" href="<?php echo $fm_app; ?>products/">See our products</a>
	</div>
</section>

<!-- =============================================================== MISSION -->
<section class="fm-section">
	<div class="fm-mission">
		<div class="fm-mission-body">
			<span class="fm-eyebrow">Our mission</span>
			<h2>Healthy products, premium quality</h2>
			<p>Providing healthy products and premium quality food is the priority
			   of our company. We rely on established measures of quality control
			   at every stage of the distribution process, designed to have a
			   positive impact on consumers&rsquo; health, safety and environment.</p>
		</div>

		<div class="fm-mission-list">
			<h3>Quality control, step by step</h3>
			<ul class="fm-checks">
				<li>Controlled at the field, from harvest</li>
				<li>Graded and sorted before packing</li>
				<li>Cold chain never broken</li>
				<li>Checks recorded at every stage</li>
			</ul>
		</div>
	</div>
</section>

<!-- ============================================================== STRATEGY -->
<section class="fm-section fm-section-alt">
	<div class="fm-section-head">
		<span class="fm-eyebrow">Our strategy</span>
		<h2>Three commitments</h2>
		<p>What we put behind every container we ship.</p>
	</div>

	<div class="fm-strategy">
		<div class="fm-strategy-item">
			<span class="fm-strategy-icon"><i class="fa fa-question" aria-hidden="true"></i></span>
			<h3>Solutions for growers</h3>
			<p>We support farmers with practical agricultural solutions, from soil
			   preparation to harvest scheduling.</p>
		</div>

		<div class="fm-strategy-item">
			<span class="fm-strategy-icon"><i class="fa fa-cogs" aria-hidden="true"></i></span>
			<h3>Progressive technology</h3>
			<p>We invest in equipment and processes that keep pace with the
			   standards our customers require.</p>
		</div>

		<div class="fm-strategy-item">
			<span class="fm-strategy-icon"><i class="fa fa-lightbulb-o" aria-hidden="true"></i></span>
			<h3>Smart cultivation</h3>
			<p>Better growing conditions mean better fruit: we work on it season
			   after season.</p>
		</div>
	</div>
</section>

<!-- ============================================================== QUALITIES -->
<section class="fm-section">
	<div class="fm-section-head">
		<span class="fm-eyebrow">Why Foodmax</span>
		<h2>What we stand for</h2>
	</div>

	<div class="fm-quality-grid">
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-leaf"></i></span>
			<h3>Fresh</h3>
			<p>Harvested and shipped within hours.</p>
		</div>
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-heartbeat"></i></span>
			<h3>Healthy</h3>
			<p>Grown to keep your family healthy.</p>
		</div>
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-pagelines"></i></span>
			<h3>Eco</h3>
			<p>Selected with care, and packed to protect the product.</p>
		</div>
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-heart"></i></span>
			<h3>Tasty</h3>
			<p>Easy to turn into delicious meals.</p>
		</div>
	</div>
</section>

<!-- ================================================================== CTA -->
<section class="fm-cta" style="background-image:url('<?php echo $fm_app; ?>img/banner-cta.jpg');">
	<div class="fm-cta-inner">
		<h2>Work with us</h2>
		<p>Tell us the product, the volume and the destination &mdash; we reply within one business day.</p>
		<a class="btn" href="<?php echo $fm_app; ?>contact/">Contact our team</a>
	</div>
</section>

<?php require_once((__DIR__ . "/includes/footer.php")); ?>

<?php require_once((__DIR__ . "/includes/analyticstracking.php")); ?>

</body>
</html>
