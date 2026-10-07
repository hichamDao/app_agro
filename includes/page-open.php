<?php
/**
 * Debut commun des pages de presentation : <head>, ouverture de <body> et menu.
 * A inclure APRES paths.php et _header.php (la session doit etre ouverte avant
 * tout affichage). Variables attendues : $fm_page_title, $fm_page_desc.
 */
require_once(__DIR__ . "/paths.php");
require_once(__DIR__ . "/explore.php");
require_once(__DIR__ . "/seo.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<title><?php echo htmlspecialchars(fm_seo_title($fm_page_title . ' | ' . FM_SITE_NAME), ENT_QUOTES, 'UTF-8'); ?></title>
	<meta name="description" content="<?php echo htmlspecialchars($fm_page_desc, ENT_QUOTES, 'UTF-8'); ?>">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
/* SEO commun : $fm_page_path (ex. "quality/") est defini par chaque page. */
if (isset($fm_page_path) && $fm_page_path !== '') {
    fm_seo_head(array(
        'path'        => $fm_page_path,
        'title'       => $fm_page_title,
        'description' => $fm_page_desc,
        'jsonld'      => array(fm_seo_breadcrumb(array_values(array_filter(array(
            array('Home', ''),
            /* Les pages "entreprise" ont About us comme etape intermediaire. */
            in_array($fm_page_path, array('quality/', 'morocco/', 'why-foodmax/'), true) ? array('About us', 'about-us/') : null,
            array($fm_page_title, $fm_page_path),
        ))))),
    ));
}
?>

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
