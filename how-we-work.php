<?php
/**
 * How we work
 * Page de presentation. Le contenu vient de l'ancienne page d'accueil, deplace ici
 * pour que l'accueil reste simple a lire.
 */
require_once(__DIR__ . "/includes/paths.php");
/* _header.php ouvre la session : il doit etre inclus AVANT tout affichage. */
require_once(__DIR__ . "/includes/_header.php");

$fm_page_title = 'How we work';
$fm_page_desc  = 'Five steps from the grower to your warehouse: production, selection, quality control, packing and delivery.';
require_once(__DIR__ . "/includes/page-open.php");

fm_pagehead('From the farm to you', 'How we work', 'Five steps, and each of them is something we do with care every single day, so that what reaches you is the result of a lot of quiet work you never have to worry about.');
?>

<!-- ============================================================ PROCESS -->
<section class="fm-process">
	<ol class="fm-steps">
		<li class="fm-step">
			<span class="fm-step-num">01</span>
			<h3>Production</h3>
			<p>
				Everything begins with the growers. We work with farms in the
				regions where each product naturally does best, we agree on
				varieties and volumes before the season starts, and we follow
				the crop as it grows instead of simply buying what the market
				offers that day, because knowing the people behind a harvest
				makes everything after it easier and more honest.
			</p>
		</li>
		<li class="fm-step">
			<span class="fm-step-num">02</span>
			<h3>Selection</h3>
			<p>
				When a lot reaches us it is sorted by people who know what a
				good fruit looks like. Anything that does not match the size,
				colour, ripeness or condition you asked for is put aside rather
				than slipped into your order, and this quiet step is really what
				decides what you end up receiving.
			</p>
		</li>
		<li class="fm-step">
			<span class="fm-step-num">03</span>
			<h3>Quality Control</h3>
			<p>
				We check at several moments, when the produce arrives, while it
				is being packed and once more before loading, looking at
				condition, size, maturity, temperature and cleanliness. What we
				find is written down, so that if you ever have a question about
				a shipment there is a real answer waiting for you.
			</p>
		</li>
		<li class="fm-step">
			<span class="fm-step-num">04</span>
			<h3>Packing</h3>
			<p>
				Each product is packed for the journey it is about to make, with
				ventilated cartons for berries, extra protection for delicate
				fruit and stacking that holds on long routes. Labels and
				documents follow your requirements, so that when the boxes are
				opened at the other end, everything is where you expect it to be.
			</p>
		</li>
		<li class="fm-step">
			<span class="fm-step-num">05</span>
			<h3>Delivery</h3>
			<p>
				A cold chain that holds is what protects everything we did
				before, so we plan the route with care, keep an eye on the
				conditions during transport and let you know how things are going,
				which gives you time to organise your side before the goods
				arrive.
			</p>
		</li>
	</ol>
</section>


<?php fm_explore(['how-we-work'], 'Keep exploring', 'Continue where you like'); ?>

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
