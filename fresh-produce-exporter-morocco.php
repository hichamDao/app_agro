<?php
/**
 * Fresh Produce Exporter from Morocco
 * SEO landing page: Morocco fresh produce exporter,
 * fresh vegetables exporter Morocco, Moroccan fruit exporter, Morocco agricultural products exporter.
 */
require_once(__DIR__ . "/includes/paths.php");
require_once(__DIR__ . "/includes/_header.php");

$fm_page_title = 'Fresh Produce Exporter Morocco';
$fm_page_path  = 'fresh-produce-exporter-morocco/';
$fm_page_desc  = 'FoodMax Group is a Moroccan exporter of fresh fruit and vegetables for international markets: tomatoes, citrus, melons, peppers, berries, figs and more.';
require_once(__DIR__ . "/includes/page-open.php");
?>

<!-- ============================================================ PAGE HEAD -->
<section class="fm-pagehead">
	<div class="fm-pagehead-inner">
		<span class="fm-eyebrow">Moroccan exporter</span>
		<h1>Fresh Produce Exporter Morocco</h1>
		<p>FoodMax Group is a Moroccan exporter of fresh fruit and vegetables, shipping worldwide, with a team that cares about what it sends and about the growers it works with.</p>

		<nav class="fm-crumbs" aria-label="Breadcrumb">
			<ol>
				<li><a href="<?php echo $fm_app; ?>">Home</a></li>
				<li>Fresh Produce Exporter Morocco</li>
			</ol>
		</nav>
	</div>
</section>

<!-- ============================================================ INTRODUCTION -->
<section class="fm-section">
	<div class="fm-section-head">
		<p class="fm-eyebrow">Who we are</p>
		<h2>A Moroccan fresh produce exporter you can count on</h2>
		<p class="fm-section-sub">
			Based in Marrakech, FoodMax Group works with growers across Morocco to select, pack and ship quality fruit and vegetables to distributors, wholesalers and retailers in Europe and beyond.
		</p>
	</div>

	<div class="fm-about fm-about-page">
		<div class="fm-about-media">
			<img src="<?php echo $fm_app; ?>images/grow-food.png"
			     alt="Fresh produce at FoodMax Group" loading="lazy" width="1672" height="941">
		</div>
		<div class="fm-about-body">
			<p class="fm-lead">
				We see ourselves as a partner you can count on, not a supplier you have to keep an eye on. Every box we send starts in a Moroccan field, in the hands of people who care about what they grow, and it stays with us all the way to your warehouse.
			</p>
			<p>
				As a fresh vegetables exporter and a Moroccan fruit exporter, we combine local knowledge with export experience. Each product has a region and a season where it does best, and we work to keep the cold chain unbroken from the field to your door.
			</p>
			<p>
				If you are looking for a Morocco agricultural products exporter that answers quickly, tells you plainly what to expect and treats every order with care, that is how we try to work every day.
			</p>
			<a class="btn" href="<?php echo $fm_app; ?>export-services/">See our export services</a>
		</div>
	</div>
</section>

<!-- ============================================================ WHAT WE EXPORT -->
<section class="fm-section fm-section-alt">
	<div class="fm-section-head">
		<p class="fm-eyebrow">Our catalogue</p>
		<h2>What we export</h2>
		<p class="fm-section-sub">
			The main families of Moroccan produce we work with, each with its own season and character. Click on a product to read our guide for buyers.
		</p>
	</div>

	<?php fm_export_grid(); ?>
</section>

<!-- ============================================================ WHY FOODMAX -->
<section class="fm-section">
	<div class="fm-section-head">
		<p class="fm-eyebrow">Why FoodMax</p>
		<h2>What we stand for</h2>
	</div>

	<div class="fm-quality-grid">
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-leaf"></i></span>
			<h3>Fresh</h3>
			<p>Picked at the right moment and kept cool, so that it reaches you in the best possible shape.</p>
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

<!-- ============================================================ MOROCCO -->
<section class="fm-morocco">
	<div class="fm-morocco-map">
		<img src="<?php echo $fm_app; ?>img/map-morocco.svg"
		     alt="Map of Morocco showing Marrakech, the Souss valley and our export routes" loading="lazy">
	</div>
	<div class="fm-morocco-body">
		<p class="fm-eyebrow">FoodMax Group and Morocco</p>
		<h2>From Morocco to the World</h2>
		<p class="fm-lead">
			Our roots are in Morocco, a land with a remarkable range of climates and a long agricultural tradition, and we are proud to carry its produce to tables far from home.
		</p>
		<p>
			Our office is in Marrakech, and from there we work with growers across the country, because every product has a region where it feels at home, and the people who farm it know things no book can teach.
		</p>
		<a class="btn" href="<?php echo $fm_app; ?>morocco/">More about Morocco</a>
	</div>
</section>

<!-- ============================================================ CTA -->
<section class="fm-cta">
	<div class="fm-cta-inner">
		<p class="fm-eyebrow">Next step</p>
		<h2>Ready for your next order?</h2>
		<p>
			Tell us which product you have in mind, how much you need and where it should go,
			and we will get back to you within one business day.
		</p>
		<div class="fm-cta-actions">
			<a class="btn" href="<?php echo $fm_app; ?>contact/">Start the conversation</a>
			<a class="btn btn-outline" href="<?php echo $fm_app; ?>products/">Browse the catalogue</a>
		</div>
	</div>
</section>

<?php require_once(__DIR__ . "/includes/page-close.php"); ?>
