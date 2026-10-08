<?php
/* _header.php ouvre la session : il doit etre inclus AVANT tout affichage,
   sinon les en-tetes HTTP sont deja partis et session_start() echoue. */
require_once(__DIR__ . "/includes/paths.php");
require_once(__DIR__ . "/includes/seo.php");
require_once(__DIR__ . "/includes/_header.php"); ?>
<!DOCTYPE html>
<html lang="en">

<head>
	<title>Request a Quote: Moroccan Fresh Produce | FoodMax Group</title>
	<meta name="description" content="Contact FoodMax Group in Marrakech, Morocco. Tell us the product, the quantity and the destination, and we reply within one business day.">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<?php fm_seo_head(array(
    'path'        => 'contact/',
    'title'       => 'Request a Quote: Moroccan Fresh Produce',
    'description' => 'Contact FoodMax Group in Marrakech, Morocco. Tell us the product, the quantity and the destination, and we reply within one business day.',
    'jsonld'      => array(
        array('@type' => 'ContactPage', 'name' => 'Contact FoodMax Group', 'url' => fm_seo_url('contact/'), 'about' => fm_seo_org()),
        fm_seo_breadcrumb(array(array('Home', ''), array('Contact', 'contact/'))),
    ),
)); ?>

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
		<h1>Let's Talk About Your Next Order</h1>
		<p>
			Whether you already know exactly what you need or you are only starting
			to look around, we would love to hear from you. You can ask us about our
			products, what is available and when, the packing, the volumes, or simply
			whether we could work together, and a real person from our team will
			reply within one business day.
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
			Every message is read by a person, never by a robot, and depending on
			the product and the season we can help you with most of the things
			below.
		</p>
	</div>

	<div class="fm-topic-grid">
		<article class="fm-topic">
			<span class="fm-topic-icon"><i class="fa fa-fw fa-cube" aria-hidden="true"></i></span>
			<h3>Products and varieties</h3>
			<p>
				Which varieties we handle at the moment, and which ones we might be
				able to develop together with a grower for a particular market.
			</p>
		</article>
		<article class="fm-topic">
			<span class="fm-topic-icon"><i class="fa fa-fw fa-calendar" aria-hidden="true"></i></span>
			<h3>Availability and seasons</h3>
			<p>
				When a product begins, when it is at its best, and when it is simply
				easier to find than at other times of the year.
			</p>
		</article>
		<article class="fm-topic">
			<span class="fm-topic-icon"><i class="fa fa-fw fa-cogs" aria-hidden="true"></i></span>
			<h3>Packing and conditioning</h3>
			<p>
				Which formats suit the way you distribute, what your labels need to
				say, and how a product should be packed for the journey ahead.
			</p>
		</article>
		<article class="fm-topic">
			<span class="fm-topic-icon"><i class="fa fa-fw fa-truck" aria-hidden="true"></i></span>
			<h3>Volumes and logistics</h3>
			<p>
				What quantities are realistic for a container, and how a shipment is
				organised so that it reaches your warehouse in good shape.
			</p>
		</article>
		<article class="fm-topic">
			<span class="fm-topic-icon"><i class="fa fa-fw fa-file-text-o" aria-hidden="true"></i></span>
			<h3>Documentation</h3>
			<p>
				Which documents travel with a shipment for your market, and how far
				in advance we can have them ready for you.
			</p>
		</article>
		<article class="fm-topic">
			<span class="fm-topic-icon"><i class="fa fa-fw fa-handshake-o" aria-hidden="true"></i></span>
			<h3>Working together</h3>
			<p>
				How a first trial shipment works, and what a regular relationship with
				us feels like once you have worked with us for a while.
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
				<h2>Tell us what you have in mind</h2>
				<p class="fm-card-lead">
					The fields with an asterisk are the only ones we really need. We use
					your details only to answer you, and nothing else.
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
						          minlength="20" required></textarea>
						<p class="fm-field-hint">
							A few lines are plenty, just tell us a little about what you need,
							for example the products, the quantities and where they should go.
						</p>
					</div>

					<button type="submit" class="btn fm-btn-send">
						<i class="fa fa-paper-plane" aria-hidden="true"></i>
						<span class="fm-btn-label">Send Message</span>
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
				<p class="fm-info-line">Kept for our existing clients</p>
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
				<p>Take a look at the catalogue first if you like, it may answer some of your questions before you write.</p>
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
			<p>Time is the biggest enemy of quality, so we build everything around it.</p>
		</div>
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-check-circle-o"></i></span>
			<h3>Selection</h3>
			<p>What does not match your specification is set aside, never slipped into your order.</p>
		</div>
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-map-marker"></i></span>
			<h3>Traceability</h3>
			<p>We keep the path from grower to lot to shipment as clear as we can.</p>
		</div>
		<div class="fm-quality">
			<span class="fm-quality-icon"><i class="fa fa-repeat"></i></span>
			<h3>Consistency</h3>
			<p>You plan around us, so the same specification has to arrive every week.</p>
		</div>
	</div>
</section>

<section class="fm-cta" style="background-image:url('<?php echo $fm_app; ?>img/banner-cta.jpg');">
	<div class="fm-cta-inner">
		<h2>Prefer to write to us directly?</h2>
		<p>Our phone line is kept for existing clients, so the easiest way to reach
		   us is the form above or a simple email, and we will get back to you
		   within one business day.</p>
		<a class="btn" href="mailto:info@foodmax-group.com">Email us</a>
	</div>
</section>

<?php require_once((__DIR__ . "/includes/footer.php")); ?>

<script type="text/javascript" src="<?php echo $fm_app; ?>js/contact.js"></script>
<?php require_once((__DIR__ . "/includes/analyticstracking.php")); ?>

</body>
</html>
