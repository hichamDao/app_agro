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
		<p>FoodMax Group is a Moroccan company specialised in the export and
		   distribution of food, recognised for its premium quality, and this
		   page is our way of introducing ourselves to you properly.</p>

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
		<img src="<?php echo $fm_app; ?>img/photo-about.png" width="1441" height="1092" alt="FoodMax Group production site" loading="lazy">
	</div>
	<div class="fm-about-body">
		<span class="fm-eyebrow">Who we are</span>
		<h2>A Moroccan exporter, built on quality and on people</h2>
		<p class="fm-lead">FoodMax Group is a Moroccan company that exports and
		   distributes food, and behind it there is a team that cares about what
		   it sends, about the growers it works with and about the customers who
		   receive it.</p>
		<p>We grow, pack and ship our produce to distributors all over the world,
		   and to several European countries in particular, but we have never
		   seen our job as simply moving boxes from one place to another. What we
		   want is for a buyer who opens a shipment to feel that someone looked
		   after it on the way, from the field to the truck to the cold room.</p>
		<p>Our strategy and the way we manage the company rest on one simple
		   idea, a collaboration that can be trusted, built on contracts that are
		   honoured and on shipments that arrive exactly as they left us. We think
		   of ourselves as a partner you can count on rather than a supplier you
		   have to keep an eye on.</p>

		<ol class="fm-chain" aria-label="From grower to customer">
			<li><span>Growers</span></li>
			<li><span>Farming</span></li>
			<li><span>Selection</span></li>
			<li><span>Quality control</span></li>
			<li><span>Packing</span></li>
			<li><span>Logistics</span></li>
			<li><span>You</span></li>
		</ol>

		<a class="btn" href="<?php echo $fm_app; ?>products/">See our products</a>
	</div>
</section>

<!-- ============================================================ PHILOSOPHY -->
<section class="fm-philosophy">
	<div class="fm-philosophy-inner">
		<p class="fm-eyebrow">Our philosophy</p>
		<h2>More Than Fresh Food</h2>
		<p class="fm-lead">
			For us, quality begins long before a product reaches your warehouse,
			in the choices we make about who we work with, what we pick up and
			how we look after it on the way.
		</p>

		<div class="fm-philo-grid">
			<article class="fm-philo">
				<span class="fm-philo-icon"><i class="fa fa-fw fa-heart" aria-hidden="true"></i></span>
				<h3>Respect for the product</h3>
				<p>
					We would rather wait for the right moment than send something
					that only looks ready, because a fruit picked too early never
					becomes what it promised to be.
				</p>
			</article>
			<article class="fm-philo">
				<span class="fm-philo-icon"><i class="fa fa-fw fa-handshake-o" aria-hidden="true"></i></span>
				<h3>Respect for growers</h3>
				<p>
					Our growers are partners, and we stay with them from one season
					to the next, because the quality you receive begins with their
					work.
				</p>
			</article>
			<article class="fm-philo">
				<span class="fm-philo-icon"><i class="fa fa-fw fa-leaf" aria-hidden="true"></i></span>
				<h3>Respect for the environment</h3>
				<p>
					We try to use resources wisely and to waste as little as we
					can, and we keep looking for ways to do it better.
				</p>
			</article>
			<article class="fm-philo">
				<span class="fm-philo-icon"><i class="fa fa-fw fa-smile-o" aria-hidden="true"></i></span>
				<h3>Respect for customers</h3>
				<p>
					We tell you clearly what to expect, and when something does
					not go as planned we say so and look for a solution with you.
				</p>
			</article>
		</div>
	</div>
</section>

<!-- =============================================================== MISSION -->
<section class="fm-section">
	<div class="fm-mission">
		<div class="fm-mission-body">
			<span class="fm-eyebrow">Our mission</span>
			<h2>Healthy products, premium quality</h2>
			<p>Providing healthy products and premium quality food is the first
			   priority of our company, and we do not leave it to chance. We rely
			   on established quality control measures at every stage of the
			   distribution process, designed to have a positive impact on the
			   health and safety of consumers and on the environment.</p>
			<p>Behind that sentence there is a very simple wish, that the person
			   who finally eats what we sent enjoys it, and that the customer who
			   trusted us with the order never regrets it.</p>
		</div>

		<div class="fm-mission-list">
			<h3>Quality control, step by step</h3>
			<ul class="fm-checks">
				<li>Controlled at the field, from the harvest</li>
				<li>Graded and sorted before packing</li>
				<li>Cold chain kept unbroken</li>
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
		<p>What we put behind every container we ship, and what we hope you can feel when it arrives.</p>
	</div>

	<div class="fm-strategy">
		<div class="fm-strategy-item">
			<span class="fm-strategy-icon"><i class="fa fa-users" aria-hidden="true"></i></span>
			<h3>Solutions for growers</h3>
			<p>We support farmers with practical agricultural solutions, from the
			   preparation of the soil to the planning of the harvest, because
			   when growers do well, everything that comes after is better.</p>
		</div>

		<div class="fm-strategy-item">
			<span class="fm-strategy-icon"><i class="fa fa-cogs" aria-hidden="true"></i></span>
			<h3>Progressive technology</h3>
			<p>We invest in equipment and processes that keep up with the
			   standards our customers ask for, so that being careful does not
			   depend only on good intentions.</p>
		</div>

		<div class="fm-strategy-item">
			<span class="fm-strategy-icon"><i class="fa fa-lightbulb-o" aria-hidden="true"></i></span>
			<h3>Smart cultivation</h3>
			<p>Better growing conditions give better fruit, and that is
			   something we work on season after season, with patience.</p>
		</div>
	</div>
</section>

<!-- ============================================================== QUALITIES -->
<section class="fm-section">
	<div class="fm-section-head">
		<span class="fm-eyebrow">Why FoodMax</span>
		<h2>What we stand for</h2>
	</div>

	<div class="fm-quality-grid">
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-leaf"></i></span>
			<h3>Fresh</h3>
			<p>Picked at the right moment and kept cool, so it reaches you in the best possible shape.</p>
		</div>
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-heartbeat"></i></span>
			<h3>Healthy</h3>
			<p>Food we would be happy to serve to our own families.</p>
		</div>
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-pagelines"></i></span>
			<h3>Eco</h3>
			<p>Selected with care and packed to protect the product and to avoid waste.</p>
		</div>
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-heart"></i></span>
			<h3>Tasty</h3>
			<p>Easy to turn into good meals, which is what it is all for.</p>
		</div>
	</div>
</section>

<!-- ================================================================ MOROCCO -->
<section class="fm-morocco">
	<div class="fm-morocco-map">
		<img src="<?php echo $fm_app; ?>img/map-morocco.svg"
		     alt="Map of Morocco showing Marrakech, the Souss valley and our export routes" loading="lazy">
	</div>
	<div class="fm-morocco-body">
		<p class="fm-eyebrow">FoodMax Group and Morocco</p>
		<h2>From Morocco to the World</h2>
		<p class="fm-lead">
			Our roots are in Morocco, a land with a remarkable range of climates
			and a long agricultural tradition, and we are proud to carry its
			produce to tables far from home.
		</p>
		<p>
			Our office is in Marrakech, and from there we work with growers
			across the country, because every product has a region where it
			feels at home, and the people who farm it know things no book can
			teach.
		</p>
		<a class="btn" href="<?php echo $fm_app; ?>products/">Discover our products</a>
	</div>
</section>

<!-- ================================================================== CTA -->
<section class="fm-cta" style="background-image:url('<?php echo $fm_app; ?>img/banner-cta.jpg');">
	<div class="fm-cta-inner">
		<h2>Work with us</h2>
		<p>Tell us which product you are looking for, how much you need and
		   where it should go, and we will reply within one business day.</p>
		<a class="btn" href="<?php echo $fm_app; ?>contact/">Contact our team</a>
	</div>
</section>

<?php require_once((__DIR__ . "/includes/footer.php")); ?>

<?php require_once((__DIR__ . "/includes/analyticstracking.php")); ?>

</body>
</html>
