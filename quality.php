<?php
/**
 * Quality and commitment
 * Page de presentation. Le contenu vient de l'ancienne page d'accueil, deplace ici
 * pour que l'accueil reste simple a lire.
 */
require_once(__DIR__ . "/includes/paths.php");
/* _header.php ouvre la session : il doit etre inclus AVANT tout affichage. */
require_once(__DIR__ . "/includes/_header.php");

$fm_page_title = 'Quality and Cold Chain for Moroccan Fresh Produce';
$fm_page_path  = 'quality/';
$fm_page_desc  = 'How FoodMax Group handles freshness, quality control, traceability and the cold chain for Moroccan fruit and vegetables, and how we work responsibly.';
require_once(__DIR__ . "/includes/page-open.php");

fm_pagehead('Our promise', 'Quality and commitment', 'Quality is not a sentence on a web page, it is a handful of things we do every day, and we also try to do them responsibly, with respect for the land, the water and the people involved.', true);
?>

<!-- ============================================================ QUALITY -->
<section class="fm-section fm-section-alt">
	<div class="fm-section-head">
		<p class="fm-eyebrow">Our quality commitment</p>
		<h2>What we promise ourselves</h2>
		<p class="fm-section-sub">
			Quality is not a sentence on a web page, it is a handful of things we
			do at every stage and hold ourselves to, even when nobody is checking.
		</p>
	</div>

	<div class="fm-commit-grid">
		<article class="fm-commit">
			<span class="fm-commit-icon"><i class="fa fa-fw fa-leaf" aria-hidden="true"></i></span>
			<h3>Freshness</h3>
			<p>
				We always look for freshness and the right point of maturity, and
				we would rather shorten our chain than let time pass between the
				harvest and the cold room. Time is the biggest enemy of quality,
				so we organise everything around it.
			</p>
		</article>

		<article class="fm-commit">
			<span class="fm-commit-icon"><i class="fa fa-fw fa-check-circle-o" aria-hidden="true"></i></span>
			<h3>Quality</h3>
			<p>
				We choose products that meet our own standards before they ever
				meet your order, and if a variety does not reach the level we
				expect, it simply does not leave, even in a tight season when
				others are shipping anyway.
			</p>
		</article>

		<article class="fm-commit">
			<span class="fm-commit-icon"><i class="fa fa-fw fa-map-marker" aria-hidden="true"></i></span>
			<h3>Traceability</h3>
			<p>
				We keep the link between the grower, the lot and your shipment as
				clear as we can. Where our records are complete we can tell you
				where a product comes from and how it reached you, and where they
				are not, we say so instead of guessing.
			</p>
		</article>

		<article class="fm-commit">
			<span class="fm-commit-icon"><i class="fa fa-fw fa-repeat" aria-hidden="true"></i></span>
			<h3>Consistency</h3>
			<p>
				Professional buyers plan around us, so the same specification has
				to arrive week after week. We work towards a steady quality across
				the whole season, and we tell you early when the conditions make
				that difficult.
			</p>
		</article>
	</div>

	<p class="fm-note">
		Documentation and third-party certifications can be provided on request
		for each shipment and destination, so just ask us for whatever applies
		to your market.
	</p>
</section>

<!-- ====================================================== COMMITMENT -->
<section class="fm-section" id="commitment">
	<div class="fm-section-head">
		<p class="fm-eyebrow">Our commitment</p>
		<h2>Doing this responsibly, and getting better as we go</h2>
		<p class="fm-section-sub">
			Fresh produce depends on land, water and people, and we feel
			responsible for each of them. What follows are the principles we work
			by and the places where we keep trying to improve, not labels we claim
			to have already earned.
		</p>
	</div>

	<div class="fm-commit-grid">
		<article class="fm-commit">
			<span class="fm-commit-icon"><i class="fa fa-fw fa-tree" aria-hidden="true"></i></span>
			<h3>Responsible agriculture</h3>
			<p>
				We prefer to work with growers who look after their land, because
				good soil is what the next season depends on, and we talk openly
				about farming practices and tell you what we are able to document.
			</p>
		</article>

		<article class="fm-commit">
			<span class="fm-commit-icon"><i class="fa fa-fw fa-tint" aria-hidden="true"></i></span>
			<h3>Sensible use of resources</h3>
			<p>
				Water, energy and packaging all have a cost, so we aim to use what
				a shipment truly needs, with packing suited to the product and
				loads planned with care instead of more material than necessary.
			</p>
		</article>

		<article class="fm-commit">
			<span class="fm-commit-icon"><i class="fa fa-fw fa-recycle" aria-hidden="true"></i></span>
			<h3>Less waste</h3>
			<p>
				Throwing away good produce is a loss for the grower and for
				everyone after them, so when a lot does not suit one order we look
				for another place for it before we ever call it waste.
			</p>
		</article>

		<article class="fm-commit">
			<span class="fm-commit-icon"><i class="fa fa-fw fa-pagelines" aria-hidden="true"></i></span>
			<h3>Respect for the environment</h3>
			<p>
				Our work depends on nature, so we try to limit what we leave
				behind. It is a long road and we are still on it, and we prefer to
				say that plainly rather than announce results we could not back up.
			</p>
		</article>

		<article class="fm-commit">
			<span class="fm-commit-icon"><i class="fa fa-fw fa-refresh" aria-hidden="true"></i></span>
			<h3>Continuous improvement</h3>
			<p>
				After every season we look at what worked and what did not, and we
				adjust our selection, our packing and our transport. Small, steady
				improvements matter more to us than big announcements.
			</p>
		</article>

		<article class="fm-commit">
			<span class="fm-commit-icon"><i class="fa fa-fw fa-handshake-o" aria-hidden="true"></i></span>
			<h3>Lasting relationships with growers</h3>
			<p>
				A grower who knows we will be there next season can plan, invest
				and farm with a longer view, and that stability is good for the
				land as well as for the quality that reaches you.
			</p>
		</article>
	</div>

	<p class="fm-note">
		We do not display environmental labels or certifications on this site. If
		one applies to a product or to a grower, we will tell you exactly which
		one it is and send you the document.
	</p>
</section>


<?php fm_explore(['quality'], 'Keep exploring', 'Continue where you like'); ?>

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
