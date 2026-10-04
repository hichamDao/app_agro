<?php
/**
 * Why work with us
 * Page de presentation. Le contenu vient de l'ancienne page d'accueil, deplace ici
 * pour que l'accueil reste simple a lire.
 */
require_once(__DIR__ . "/includes/paths.php");
/* _header.php ouvre la session : il doit etre inclus AVANT tout affichage. */
require_once(__DIR__ . "/includes/_header.php");

$fm_page_title = 'Why work with us';
$fm_page_desc  = 'Six reasons to work with FoodMax Group, and the kind of clients and partners we supply.';
require_once(__DIR__ . "/includes/page-open.php");

fm_pagehead('Why work with us', 'Why work with FoodMax Group', 'Choosing a supplier is a matter of trust, so here is, as plainly as we can put it, what you can expect from us and who we usually work with.');
?>

<!-- ================================================================ WHY -->
<section class="fm-section fm-section-alt">
	<div class="fm-why-grid">
		<article class="fm-why">
			<h3><i class="fa fa-fw fa-star" aria-hidden="true"></i> Quality Products</h3>
			<p>
				We choose carefully what we send and we set aside what does not
				match, so you receive produce sorted to your specification
				instead of whatever happened to arrive in the crate, and that
				makes a real difference on the shelf.
			</p>
		</article>

		<article class="fm-why">
			<h3><i class="fa fa-fw fa-handshake-o" aria-hidden="true"></i> Reliable Partnership</h3>
			<p>
				We build relationships over seasons rather than selling lot by
				lot, so you deal with the same people, the same specifications
				and the same way of working each time, and you know what to
				expect when you pick up the phone or write to us.
			</p>
		</article>

		<article class="fm-why">
			<h3><i class="fa fa-fw fa-briefcase" aria-hidden="true"></i> Professional Service</h3>
			<p>
				We work the way a professional buyer hopes to be worked with,
				with clear documents, defined specifications, agreed timelines
				and answers that are direct instead of vague.
			</p>
		</article>

		<article class="fm-why">
			<h3><i class="fa fa-fw fa-cogs" aria-hidden="true"></i> Flexible Solutions</h3>
			<p>
				Every market asks for something a little different, so we adapt
				quantities, packaging and documents to the destination and to
				the way you distribute your goods, and we will gladly think it
				through with you.
			</p>
		</article>

		<article class="fm-why">
			<h3><i class="fa fa-fw fa-globe" aria-hidden="true"></i> International Vision</h3>
			<p>
				We keep international requirements in mind from the start, with
				documents, labels and packing that match how produce is handled
				at its destination, so there are fewer surprises when the goods
				arrive.
			</p>
		</article>

		<article class="fm-why">
			<h3><i class="fa fa-fw fa-smile-o" aria-hidden="true"></i> Customer Satisfaction</h3>
			<p>
				We measure ourselves by whether you come back, which means
				telling you the truth about quality and availability even when
				the answer is not the easy one, because we would rather earn your
				trust than win one order.
			</p>
		</article>
	</div>
</section>

<!-- ========================================================== PARTNERS -->
<section class="fm-section">
	<div class="fm-section-head">
		<p class="fm-eyebrow">Clients and partners</p>
		<h2>Who we work with</h2>
		<p class="fm-section-sub">
			We work with professionals who need a quality they can rely on, and
			depending on the product and the market that can mean importers and
			distributors, wholesalers, retailers or partners who buy to their own
			specifications. What matters most to all of them, and to us, is
			trust, good communication, regular supply, steady quality and
			commitments that are kept.
		</p>
	</div>

	<div class="fm-partner-grid">
		<article class="fm-partner">
			<h3>Importers and distributors</h3>
			<p>Regular volumes, clear specifications and documents prepared with customs clearance in mind.</p>
		</article>
		<article class="fm-partner">
			<h3>Wholesalers</h3>
			<p>A consistent grading from one delivery to the next, so your own customers find the same quality each week.</p>
		</article>
		<article class="fm-partner">
			<h3>Retail and fresh produce chains</h3>
			<p>Packing and labelling adapted to your stores and to the way you present your fruit and vegetables.</p>
		</article>
		<article class="fm-partner">
			<h3>Private label partners</h3>
			<p>We can work to an agreed specification, including the presentation and the packaging.</p>
		</article>
	</div>

	<p class="fm-note fm-partner-note">
		This space is kept for our real partners, certifications and references,
		which we will publish here once they have been checked and cleared for
		publication, because we would rather leave it empty than show anything we
		cannot prove.
	</p>
</section>


<?php fm_explore(['why-foodmax'], 'Keep exploring', 'Continue where you like'); ?>

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
