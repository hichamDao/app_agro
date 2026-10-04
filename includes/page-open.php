<?php
/**
 * Debut commun des pages de presentation : <head>, ouverture de <body> et menu.
 * A inclure APRES paths.php et _header.php (la session doit etre ouverte avant
 * tout affichage). Variables attendues : $fm_page_title, $fm_page_desc.
 */
require_once(__DIR__ . "/paths.php");
require_once(__DIR__ . "/explore.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<title><?php echo htmlspecialchars($fm_page_title, ENT_QUOTES, 'UTF-8'); ?> | FoodMax Group</title>
	<meta name="description" content="<?php echo htmlspecialchars($fm_page_desc, ENT_QUOTES, 'UTF-8'); ?>">
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

<?php require_once(__DIR__ . "/header-inc.php"); /* ouvre <body> et affiche le menu */ ?>
