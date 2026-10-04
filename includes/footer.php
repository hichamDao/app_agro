<?php
require_once(__DIR__ . "/paths.php");

/* Année courante : on ne fige plus "2025" en dur. */
$fm_annee = date('Y');

/* Reseaux sociaux : renseigner ici l'adresse COMPLETE de chaque page officielle
   (ex. 'https://www.linkedin.com/company/...'). Une icone n'apparait que si
   son adresse est remplie : aucun lien factice vers la page d'accueil d'un
   reseau. Laisser vide tant que le compte n'existe pas. */
$fm_social = array(
    'linkedin'  => array('url' => '', 'icon' => 'fa-linkedin',  'label' => 'LinkedIn'),
    'facebook'  => array('url' => '', 'icon' => 'fa-facebook',  'label' => 'Facebook'),
    'instagram' => array('url' => '', 'icon' => 'fa-instagram', 'label' => 'Instagram'),
);
?>
<footer class="fm-footer">
	<div class="fm-footer-inner">

		<!-- ------------------------------------------------- logo + description -->
		<div class="fm-footer-col fm-footer-brand">
			<a class="fm-footer-logo" href="<?php echo $fm_app; ?>" aria-label="Foodmax, accueil">
				<img src="<?php echo $fm_app; ?>images/logo.png" alt="Foodmax">
			</a>

			<p class="fm-footer-desc">
				FoodMax Group is a Moroccan company that loves fresh food and the
				people who grow it. We select, pack and ship our produce to
				distributors around the world, keeping track of where it comes from
				and keeping it cold all the way, because that is how a good product
				stays good.
			</p>

			<ul class="fm-footer-social">
				<li>
					<a href="mailto:info@foodmax-group.com" aria-label="E-mail">
						<i class="fa fa-envelope" aria-hidden="true"></i>
					</a>
				</li>
				<?php foreach ($fm_social as $fm_r) { if ($fm_r['url'] === '') { continue; } ?>
				<li>
					<a href="<?php echo htmlspecialchars($fm_r['url'], ENT_QUOTES, 'UTF-8'); ?>"
					   target="_blank" rel="noopener" aria-label="<?php echo $fm_r['label']; ?>">
						<i class="fa <?php echo $fm_r['icon']; ?>" aria-hidden="true"></i>
					</a>
				</li>
				<?php } ?>
			</ul>
		</div>

		<!-- --------------------------------------------------------- navigation -->
		<nav class="fm-footer-col" aria-label="Footer navigation">
			<h2 class="fm-footer-title">FoodMax Group</h2>
			<ul class="fm-footer-links">
				<li><a href="<?php echo $fm_app; ?>about-us/">About us</a></li>
				<li><a href="<?php echo $fm_app; ?>products/">Products</a></li>
				<li><a href="<?php echo $fm_app; ?>gallery/">Gallery</a></li>
				<li><a href="<?php echo $fm_app; ?>contact/">Contact</a></li>
			</ul>

			<h2 class="fm-footer-title fm-footer-title-spaced">Legal</h2>
			<ul class="fm-footer-links">
				<li><a href="<?php echo $fm_app; ?>privacy-policy/">Privacy policy</a></li>
				<li><a href="<?php echo $fm_app; ?>terms/">Terms</a></li>
			</ul>
		</nav>

		<!-- ------------------------------------------------------------ contact -->
		<div class="fm-footer-col">
			<h2 class="fm-footer-title">Contact</h2>
			<ul class="fm-footer-info">
				<li>
					<i class="fa fa-map-marker" aria-hidden="true"></i>
					<span>Foodmax Group, 16 Rue AL Ikhae Apt 2,<br>Zone industrielle &mdash; Marrakech, Morocco</span>
				</li>
				<li>
					<i class="fa fa-phone" aria-hidden="true"></i>
					<span>Phone line reserved for our clients</span>
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
			<p>From Morocco to the world, with care.</p>
		</div>
	</div>
</footer>

<script type="text/javascript" src="<?php echo $fm_app; ?>js/newsletter.js"></script>
<script type="text/javascript" src="<?php echo $fm_app; ?>js/reveal.js"></script>
