<?php
/**
 * Politique de confidentialite.
 *
 * Contenu ecrit d'apres le comportement reel du code : on ne declare que ce que
 * le site fait effectivement. La section "responsable de traitement" et les
 * coordonnees a usage administratif restent a completer.
 *
 * _header.php ouvre la session : il doit etre inclus AVANT tout affichage.
 */
require_once(__DIR__ . "/includes/paths.php");
require_once(__DIR__ . "/includes/_header.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<title>Privacy policy | FoodMax Group</title>
	<meta name="description" content="How FoodMax Group handles the personal data you send through this website.">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="stylesheet" type="text/css" href="<?php echo $fm_app; ?>css/phlox.css">
	<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,400i,600,600i,700,700i">
	<link rel="stylesheet" type="text/css" href="<?php echo $fm_app; ?>css/font-awesome.min.css">

	<script type="text/javascript" src="<?php echo $fm_app; ?>js/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo $fm_app; ?>js/setting.js"></script>

	<script async src="https://www.googletagmanager.com/gtag/js?id=G-ZPRWMT85SP"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag() { dataLayer.push(arguments); }
		gtag('js', new Date());
		gtag('config', 'G-ZPRWMT85SP');
	</script>
</head>

<?php require_once((__DIR__ . "/includes/header-inc.php")); ?>

<section class="fm-pagehead">
	<div class="fm-pagehead-inner">
		<span class="fm-eyebrow">Legal</span>
		<h1>Privacy policy</h1>
		<p>
			This page explains what this website does with the information you send
			us, and who can read it.
		</p>

		<nav class="fm-crumbs" aria-label="Breadcrumb">
			<ol>
				<li><a href="<?php echo $fm_app; ?>">Home</a></li>
				<li>Privacy policy</li>
			</ol>
		</nav>
	</div>
</section>

<section class="fm-section">
	<div class="fm-section-head">
		<h2>What this page covers</h2>
	</div>

	<div class="fm-legal">
		<p>
			This website exists to present FoodMax Group and its products, and to
			let professional buyers contact us. It is not an online shop: no order
			is placed and no payment is taken on this site.
		</p>

		<h3>Information you send us</h3>
		<p>
			When you use the contact form, you can send us your name, your company,
			your e-mail address, your phone number, your country, the product or
			category you are interested in, and your message. We receive this
			information in our internal message system.
		</p>
		<p>
			If you subscribe to the newsletter, we store your e-mail address only.
		</p>

		<h3>Why we ask for it</h3>
		<p>
			We use this information for one purpose: to answer your request. We need
			to reply to you, and the product, volume and destination you mention
			are what allow us to give you a useful answer.
		</p>

		<h3>How long we keep it</h3>
		<p>
			Messages are kept in our internal system for as long as they remain
			commercially relevant, so we can recall what was agreed if a conversation
			continues. Newsletter addresses are kept until you unsubscribe.
		</p>

		<h3>Who can read it</h3>
		<p>
			Messages are visible to the FoodMax Group team who handle commercial
			requests. We do not sell personal data and we do not share it with
			third parties for advertising.
		</p>

		<h3>Cookies and measurement</h3>
		<p>
			This site uses Google Analytics to measure how visitors use the pages.
			Google Analytics places cookies in your browser. We use this information
			only in aggregate, to understand which pages are useful and which are
			not.
		</p>

		<h3>Your choices</h3>
		<p>
			You can ask us at any time to correct or delete the information we hold
			about you, or to unsubscribe from the newsletter. Write to us from the
			address you used and we will handle the request.
		</p>

		<h3>Changes to this page</h3>
		<p>
			If our practices change, this page will be updated to match what the site
			actually does.
		</p>
	</div>

	<div class="fm-note">
		The name of the person legally responsible for data processing, and the
		postal address to use for formal requests, will be added here once
		confirmed.
	</div>
</section>

<?php require_once((__DIR__ . "/includes/footer.php")); ?>

</body>
</html>