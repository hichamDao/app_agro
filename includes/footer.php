<?php
require_once(__DIR__ . "/paths.php");

/* Année courante : on ne fige plus "2025" en dur. */
$fm_annee = date('Y');
?>
<footer class="fm-footer">
	<div class="fm-footer-inner">

		<!-- ------------------------------------------------- logo + description -->
		<div class="fm-footer-col fm-footer-brand">
			<a class="fm-footer-logo" href="<?php echo $fm_app; ?>" aria-label="Foodmax, accueil">
				<img src="<?php echo $fm_app; ?>images/logo.png" alt="Foodmax">
			</a>

			<p class="fm-footer-desc">
				Foodmax Group is a Moroccan company specialising in the export and
				distribution of fresh food. We grow, pack and ship our produce to
				distributors worldwide, with full traceability and a cold chain that
				never breaks.
			</p>

			<!-- Pas de liens Facebook / LinkedIn : aucun compte officiel n existe
			     pour FoodMax Group, et un lien vers facebook.com/ mènerait le
			     visiteur vers la page d'accueil de Facebook. Seule l'adresse e-mail,
			     réelle, est proposée. Les réseaux sociaux pourront être ajoutés
			     quand leurs adresses seront connues. -->
			<ul class="fm-footer-social">
				<li>
					<a href="mailto:info@foodmax-group.com" aria-label="E-mail">
						<i class="fa fa-envelope" aria-hidden="true"></i>
					</a>
				</li>
			</ul>
		</div>

		<!-- --------------------------------------------------------- navigation -->
		<nav class="fm-footer-col" aria-label="Footer navigation">
			<h2 class="fm-footer-title">Company</h2>
			<ul class="fm-footer-links">
				<li><a href="<?php echo $fm_app; ?>about-us/">About us</a></li>
				<li><a href="<?php echo $fm_app; ?>products/">Products</a></li>
				<li><a href="<?php echo $fm_app; ?>gallery/">Gallery</a></li>
				<li><a href="<?php echo $fm_app; ?>contact/">Contact</a></li>
			</ul>

			<h2 class="fm-footer-title fm-footer-title-spaced">Legal</h2>
			<ul class="fm-footer-links">
				<li><a href="<?php echo $fm_app; ?>privacy-policy/">Privacy policy</a></li>
				<li><a href="<?php echo $fm_app; ?>terms/">Terms of sale</a></li>
			</ul>
		</nav>

		<!-- ------------------------------------------------------------ contact -->
		<div class="fm-footer-col">
			<h2 class="fm-footer-title">Morocco office</h2>
			<ul class="fm-footer-info">
				<li>
					<i class="fa fa-map-marker" aria-hidden="true"></i>
					<span>Foodmax Group, 16 Rue AL Ikhae Apt 2,<br>Zone industrielle &mdash; Marrakech, Morocco</span>
				</li>
				<li>
					<i class="fa fa-phone" aria-hidden="true"></i>
					<span>Reserved for clients only</span>
				</li>
				<li>
					<i class="fa fa-envelope" aria-hidden="true"></i>
					<span><a href="mailto:info@foodmax-group.com">info@foodmax-group.com</a></span>
				</li>
				<li>
					<i class="fa fa-clock-o" aria-hidden="true"></i>
					<span>Monday to Friday, 9:00 &ndash; 18:00</span>
				</li>
			</ul>
		</div>

		<!-- -------------------------------------------------------- newsletter -->
		<div class="fm-footer-col">
			<h2 class="fm-footer-title">Newsletter</h2>
			<p class="fm-footer-desc">
				Seasonal availability, new varieties and export news. One short
				e-mail per month, no spam.
			</p>

			<div id="fmNewsletterResult" role="status" aria-live="polite"></div>

			<form class="fm-footer-form" id="fmNewsletterForm" method="post"
			      action="<?php echo $fm_app; ?>admin/subscribe_controle.php" novalidate>
				<input type="hidden" name="date" value="<?php echo date('j-n-Y H:i:s'); ?>">

				<label class="sr-only" for="fmNewsletterEmail">Your e-mail address</label>
				<input type="email" id="fmNewsletterEmail" name="email" required
				       maxlength="100" autocomplete="email"
				       placeholder="your@email.com">
				<button type="submit">
					Subscribe <i class="fa fa-arrow-right" aria-hidden="true"></i>
				</button>
			</form>
		</div>

	</div>

	<div class="fm-footer-bottom">
		<div class="fm-footer-bottom-inner">
			<p>&copy; <?php echo $fm_annee; ?> Foodmax Group. All rights reserved.</p>
			<p>Fresh produce exported from Morocco.</p>
		</div>
	</div>
</footer>

<script type="text/javascript" src="<?php echo $fm_app; ?>js/newsletter.js"></script>
