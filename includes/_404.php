<?php
/**
 * Page 404 partagée : utilisée par blog-post.php quand un article n'existe pas
 * ou n'est pas publié.
 *
 * $fm_app est déjà défini par paths.php dans la page appelante.
 */
if (!isset($fm_app)) {
    require_once(__DIR__ . "/paths.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Page not found | Foodmax</title>
	<meta name="description" content="Page not found.">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="<?php echo $fm_app; ?>css/phlox.css">
	<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,400i,600,600i,700,700i">
	<link rel="stylesheet" type="text/css" href="<?php echo $fm_app; ?>css/font-awesome.min.css">
</head>
<?php require_once((__DIR__ . "/header-inc.php")); ?>

<section class="fm-pagehead fm-pagehead-contact">
	<div class="fm-pagehead-inner" style="text-align:center;">
		<span class="fm-eyebrow">Error 404</span>
		<h1>Page not found</h1>
		<p>
			The page you are looking for does not exist, has been moved,
			or you may not have permission to view it.
		</p>
		<a class="btn" href="<?php echo $fm_app; ?>">Return to homepage</a>
	</div>
</section>

<?php require_once((__DIR__ . "/footer.php")); ?>
</body>
</html>
