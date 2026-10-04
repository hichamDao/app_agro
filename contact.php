<?php
/* _header.php ouvre la session : il doit etre inclus AVANT tout affichage,
   sinon les en-tetes HTTP sont deja partis et session_start() echoue. */
require_once(__DIR__ . "/includes/paths.php");
require_once(__DIR__ . "/includes/_header.php"); ?>
<!DOCTYPE html>
<html lang="en">

<head>
	<title>Contact | Foodmax</title>
	<meta name="description" content="Contact Foodmax Group: Moroccan fresh produce exporter. Phone, email and office address in Marrakech, Morocco.">
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

<?php require_once((__DIR__ . "/includes/paths.php"));
require_once((__DIR__ . "/includes/header-inc.php")); ?>

<!-- ============================================================ EN-TETE PAGE -->
<section class="fm-pagehead fm-pagehead-contact">
	<div class="fm-pagehead-inner">
		<span class="fm-eyebrow">Start a conversation</span>
		<h1>Let's talk about your next order</h1>
		<p>
			Tell us what you are looking for and we will come back to you with what
			is actually possible. You can ask us about our products, their
			availability, the season, the packing, the volumes or simply whether we
			could work together. Our team answers within one business day.
		</p>

		<nav class="fm-crumbs" aria-label="Fil d'Ariane">
			<ol>
				<li><a href="<?php echo $fm_app; ?>">Home</a></li>
				<li>Contact</li>
			</ol>
		</nav>
	</div>
</section>

<!-- ================================================ CE QUE NOUS POUVONS REPONDRE -->
<section class="fm-section fm-contacttopics">
	<div class="fm-section-head">
		<span class="fm-eyebrow">How we can help</span>
		<h2>What to ask us about</h2>
		<p class="fm-section-sub">
			Every request is read by a person. Depending on the product and the
			season, we can answer on most of the following.
		</p>
	</div>

	<div class="fm-topic-grid">
		<article class="fm-topic">
			<span class="fm-topic-icon"><i class="fa fa-fw fa-cube" aria-hidden="true"></i></span>
			<h3>Products and varieties</h3>
			<p>
				Which varieties we currently handle, and which ones we can develop
				with a grower for a specific market.
			</p>
		</article>
		<article class="fm-topic">
			<span class="fm-topic-icon"><i class="fa fa-fw fa-calendar" aria-hidden="true"></i></span>
			<h3>Availability and seasons</h3>
			<p>
				When a product starts, when it peaks, and when it is easier to get
				than at other times of the year.
			</p>
		</article>
		<article class="fm-topic">
			<span class="fm-topic-icon"><i class="fa fa-fw fa-cogs" aria-hidden="true"></i></span>
			<h3>Packing and conditioning</h3>
			<p>
				Which formats suit your distribution, labelling requirements, and
				how a product should be packed for its destination.
			</p>
		</article>
		<article class="fm-topic">
			<span class="fm-topic-icon"><i class="fa fa-fw fa-truck" aria-hidden="true"></i></span>
			<h3>Volumes and logistics</h3>
			<p>
				Realistic quantities per container, and how a shipment is organised
				to reach your warehouse.
			</p>
		</article>
		<article class="fm-topic">
			<span class="fm-topic-icon"><i class="fa fa-fw fa-file-text-o" aria-hidden="true"></i></span>
			<h3>Documentation</h3>
			<p>
				Which documents accompany a shipment for your market, and how far
				in advance they can be prepared.
			</p>
		</article>
		<article class="fm-topic">
			<span class="fm-topic-icon"><i class="fa fa-fw fa-handshake-o" aria-hidden="true"></i></span>
			<h3>Working together</h3>
			<p>
				How a first trial shipment works, and what a regular supply
				relationship looks like on our side.
			</p>
		</article>
	</div>
</section>

<!-- ================================================================= CONTACT -->
<section class="fm-contactwrap">
	<div class="fm-contactwrap-inner">

		<!-- ------------------------------------------------------ formulaire -->
		<div class="fm-contact-form-col">
			<div class="fm-card">
				<span class="fm-eyebrow">Send a request</span>
				<h2>Tell us about your needs</h2>
				<p class="fm-card-lead">
					Fields marked with an asterisk are required. We only use your details
					to answer your request.
				</p>

				<div id="resultContact" role="status" aria-live="polite"></div>

				<form method="post" action="contact/" class="fm-form" id="contactform" novalidate>
					<input type="hidden" name="date" value="<?php echo date('j-n-Y H:i:s'); ?>">

					<div class="fm-field-row">
						<div class="fm-field">
							<label for="fm-name">Name <span aria-hidden="true">*</span></label>
							<input type="text" id="fm-name" name="name" class="form-input"
							       placeholder="Your name" maxlength="120" required>
						</div>

						<div class="fm-field">
							<label for="fm-company">Company</label>
							<input type="text" id="fm-company" name="company" class="form-input"
							       placeholder="Company name" maxlength="150">
						</div>
					</div>

					<div class="fm-field-row">
						<div class="fm-field">
							<label for="fm-email">Email <span aria-hidden="true">*</span></label>
							<input type="email" id="fm-email" name="email" class="form-input"
							       placeholder="name@company.com" maxlength="250" required>
						</div>

						<div class="fm-field">
							<label for="fm-phone">Phone</label>
							<input type="tel" id="fm-phone" name="phone" class="form-input"
							       placeholder="+212 6 00 00 00 00" maxlength="50">
						</div>
					</div>

					<div class="fm-field-row">
						<div class="fm-field">
							<label for="fm-country">Country</label>
							<input type="text" id="fm-country" name="country" class="form-input"
							       placeholder="Where you are based" maxlength="120">
						</div>

						<div class="fm-field">
							<label for="fm-product">Product / category</label>
							<input type="text" id="fm-product" name="product" class="form-input"
							       placeholder="e.g. Citrus, berries, tomatoes" maxlength="150"
							       list="fm-product-list">
							<datalist id="fm-product-list">
								<option value="Citrus"></option>
								<option value="Berries"></option>
								<option value="Melons"></option>
								<option value="Tomatoes"></option>
								<option value="Peppers"></option>
								<option value="Courgettes"></option>
								<option value="Eggplants"></option>
								<option value="Green leaves"></option>
								<option value="Dried fruits"></option>
								<option value="Figs"></option>
								<option value="Pits"></option>
								<option value="Other / several products"></option>
							</datalist>
						</div>
					</div>

					<div class="fm-field">
						<label for="fm-message">Message <span aria-hidden="true">*</span></label>
						<textarea id="fm-message" name="message" class="form-textarea" rows="7"
						          placeholder="Products, quantities, packing and destination&hellip;"
						          minlength="10" required></textarea>
						<p class="fm-field-hint">
							A few lines are enough, but please describe your needs: the
							products, the quantities and where they should be delivered.
						</p>
					</div>

					<button type="submit" class="btn fm-btn-send">
						<i class="fa fa-paper-plane" aria-hidden="true"></i>
						<span class="fm-btn-label">Send request</span>
					</button>
				</form>
			</div>
		</div>

		<!-- ------------------------------------------------------ coordonnees -->
		<div class="fm-contact-info-col">

			<div class="fm-info-card">
				<span class="fm-info-icon"><i class="fa fa-map-marker"></i></span>
				<div>
					<h3>Morocco office</h3>
					<p class="fm-info-line">Foodmax Group</p>
					<p class="fm-info-line">16, Rue AL Ikhae Apt 2,<br>Zone industrielle &mdash; Marrakech, Morocco</p>
				</div>
			</div>

			<div class="fm-info-card">
				<span class="fm-info-icon"><i class="fa fa-envelope"></i></span>
				<div>
					<h3>E-mail</h3>
					<p class="fm-info-line"><a href="mailto:info@foodmax-group.com">info@foodmax-group.com</a></p>
				</div>
			</div>

			<div class="fm-info-card">
				<span class="fm-info-icon"><i class="fa fa-phone"></i></span>
				<div>
				<h3>Phone</h3>
				<p class="fm-info-line">Reserved for clients only</p>
				</div>
			</div>

			<div class="fm-info-card">
				<span class="fm-info-icon"><i class="fa fa-clock-o"></i></span>
				<div>
					<h3>Opening hours</h3>
					<p class="fm-info-line">Monday to Friday, 9:00 &ndash; 18:00</p>
					<p class="fm-info-line">Saturday on request</p>
				</div>
			</div>

			<div class="fm-info-note">
				<h3>Looking for our products?</h3>
				<p>Browse the catalogue to check availability and grades before you write.</p>
				<a class="btn btn-outline btn-sm" href="<?php echo $fm_app; ?>products/">
					See all products
				</a>
			</div>
		</div>

	</div>
</section>

<!-- ================================================================ QUALITES -->
<section class="fm-section fm-section-alt">
	<div class="fm-section-head">
		<span class="fm-eyebrow">Why Foodmax</span>
		<h2>What you can expect from us</h2>
	</div>

	<div class="fm-quality-grid">
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-leaf"></i></span>
			<h3>Freshness</h3>
			<p>Time is the main enemy of produce quality, so we organise the chain around it.</p>
		</div>
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-check-circle-o"></i></span>
			<h3>Selection</h3>
			<p>What does not match your specification is set aside, not mixed into your order.</p>
		</div>
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-map-marker"></i></span>
			<h3>Traceability</h3>
			<p>We keep the link between the grower, the lot and your shipment as clear as we can.</p>
		</div>
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-repeat"></i></span>
			<h3>Consistency</h3>
			<p>Professional buyers plan around us, so the same specification has to arrive every week.</p>
		</div>
	</div>
</section>

<section class="fm-cta" style="background-image:url('<?php echo $fm_app; ?>img/banner-cta.jpg');">
	<div class="fm-cta-inner">
		<h2>Prefer to talk it through?</h2>
		<p>Our phone line is reserved for clients, so the quickest way to reach us is
		   the form above or a direct email. We answer within one business day.</p>
		<a class="btn" href="mailto:contact@foodmax-group.com">Email us</a>
	</div>
</section>

<?php require_once((__DIR__ . "/includes/footer.php")); ?>

<script type="text/javascript" src="<?php echo $fm_app; ?>js/contact.js"></script>
<?php require_once((__DIR__ . "/includes/analyticstracking.php")); ?>

</body>
</html>
