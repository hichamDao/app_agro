<?php
/**
 * FoodMax Group and Morocco
 * Page de presentation. Le contenu vient de l'ancienne page d'accueil, deplace ici
 * pour que l'accueil reste simple a lire.
 */
require_once(__DIR__ . "/includes/paths.php");
/* _header.php ouvre la session : il doit etre inclus AVANT tout affichage. */
require_once(__DIR__ . "/includes/_header.php");

$fm_page_title = 'FoodMax Group and Morocco';
$fm_page_path  = 'morocco/';
$fm_page_desc  = 'Why Morocco: a land of many climates, generations of grower know-how and a wide range of fruits and vegetables for export.';
require_once(__DIR__ . "/includes/page-open.php");

fm_pagehead('Where we come from', 'FoodMax Group and Morocco', 'Our roots are in Morocco, a country with a remarkable range of climates and a long agricultural tradition, and we are proud to carry its produce to tables far from home.', true);
?>

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


<?php fm_explore(['morocco'], 'Keep exploring', 'Continue where you like'); ?>

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

<?php require_once(__DIR__ . "/includes/page-close.php"); ?>
