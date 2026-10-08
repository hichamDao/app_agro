<?php
require_once(__DIR__ . "/../includes/_header.php");
require_once(__DIR__ . "/paths.php");

/* Toutes les URL de ce fichier utilisent $fm_app, le chemin absolu de la racine
   du site calcule dans includes/paths.php.
   On ne peut pas utiliser un chemin relatif ici : les URL publiques sont
   reecrites vers des fichiers situes ailleurs (par exemple /about-us/ est servi
   par about-us.php, et /products/3/Citrus/ par products/products.php). Le
   navigateur resout un chemin relatif par rapport a l'URL demandee, pas par
   rapport au fichier PHP : "../css/" sur /products/3/Citrus/ viserait
   /products/css/, qui n'existe pas. */

/* Onglet courant mis en avant dans le menu. */
$fm_self = basename($_SERVER['SCRIPT_FILENAME']);
$fm_path = str_replace('\\', '/', isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '');

/* Familles de produits du menu : lues dans la base, donc toujours a jour et
   toujours des liens qui existent. Si la base ne repond pas, le menu reste
   utilisable avec le seul lien "All products". */
require_once(__DIR__ . '/seo.php');
$fm_nav_cats = array();
if (isset($conn) && $conn) {
    try {
        $fm_rs = mysqli_query($conn, 'SELECT Code_cat, Nom_cat FROM categories ORDER BY Nom_cat ASC');
        if ($fm_rs) {
            while ($fm_c = mysqli_fetch_assoc($fm_rs)) {
                $fm_slug = preg_replace('/[^A-Za-z0-9-]/', '', str_replace('_', '', (string) $fm_c['Nom_cat']));
                if ($fm_slug === '') { continue; }
                $fm_nav_cats[] = array(
                    'label' => fm_cat_label($fm_c['Nom_cat']),
                    'url'   => 'products/' . (int) $fm_c['Code_cat'] . '/' . $fm_slug . '/',
                );
            }
        }
    } catch (Throwable $fm_e) { $fm_nav_cats = array(); }
}
/* Repli si la base ne repond pas : les familles et leurs identifiants. */
if (!$fm_nav_cats) {
    $fm_nav_cats = array(
        array('label' => 'Tomatoes',     'url' => 'products/7/Tomatoes/'),
        array('label' => 'Citrus',       'url' => 'products/3/Citrus/'),
        array('label' => 'Melons',       'url' => 'products/6/Melons/'),
        array('label' => 'Peppers',      'url' => 'products/8/Peppers/'),
        array('label' => 'Courgettes',   'url' => 'products/10/Courgettes/'),
        array('label' => 'Berries',      'url' => 'products/4/Berries/'),
        array('label' => 'Figs',         'url' => 'products/16/Figs/'),
        array('label' => 'Dried Fruits', 'url' => 'products/14/Driedfruits/'),
        array('label' => 'Eggplants',    'url' => 'products/11/Eggplants/'),
        array('label' => 'Green Leaves', 'url' => 'products/15/Greenleaves/'),
        array('label' => 'Stone fruits', 'url' => 'products/5/Pits/'),
    );
}

/* Onglets actifs : on compare le premier segment de l'adresse a l'identique
   (avec strpos, "morocco" correspondait aussi a "fresh-produce-exporter-morocco"). */
$fm_segment = '';
if (preg_match('~^(?:/[^/]+)*?/(about-us|how-we-work|quality|morocco|why-foodmax|gallery|fresh-produce-exporter-morocco|export-services|for-importers)(?:\\.php)?(?:/|\\?|$)~', $fm_path, $fm_m)) {
    $fm_segment = $fm_m[1];
}
$fm_services_actif = in_array($fm_segment, array('fresh-produce-exporter-morocco', 'export-services', 'for-importers', 'how-we-work'), true);
$fm_about_actif    = in_array($fm_segment, array('about-us', 'quality', 'morocco', 'why-foodmax', 'gallery'), true);
?>
<body id='url_div'>

<header class="fm-nav" id="fmNav">
	<div class="fm-nav-inner">

		<a class="fm-nav-logo" href="<?php echo $fm_app; ?>" aria-label="FoodMax Group, home">
			<img src="<?php echo $fm_app; ?>images/logo.png" alt="FoodMax Group">
		</a>

		<button type="button" class="fm-nav-burger" id="fmBurger"
		        aria-label="Open the menu" aria-expanded="false" aria-controls="fmMenu">
			<span></span><span></span><span></span>
		</button>

		<nav class="fm-nav-menu" id="fmMenu" aria-label="Main navigation">
			<ul class="fm-nav-list">
			<li<?php echo ($fm_self === 'index.php' && $fm_path === '/') ? ' class="is-current"' : ''; ?>>
				<a href="<?php echo $fm_app; ?>">Home</a>
			</li>
				<li class="has-sub<?php echo (strpos($fm_path, 'products') !== false || strpos($fm_path, 'offers') !== false) ? ' is-current' : ''; ?>">
					<a href="<?php echo $fm_app; ?>products/" class="fm-sub-toggle">
						Products <i class="fa fa-angle-down" aria-hidden="true"></i>
					</a>
					<ul class="fm-sub">
						<?php foreach ($fm_nav_cats as $fm_c) { ?>
						<li><a href="<?php echo $fm_app . htmlspecialchars($fm_c['url'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($fm_c['label'], ENT_QUOTES, 'UTF-8'); ?></a></li>
						<?php } ?>
						<li class="fm-sub-all"><a href="<?php echo $fm_app; ?>products/">All products</a></li>
					</ul>
				</li>
				<li class="has-sub<?php echo $fm_services_actif ? ' is-current' : ''; ?>">
					<a href="<?php echo $fm_app; ?>fresh-produce-exporter-morocco/" class="fm-sub-toggle">
						Services <i class="fa fa-angle-down" aria-hidden="true"></i>
					</a>
					<ul class="fm-sub">
						<li><a href="<?php echo $fm_app; ?>fresh-produce-exporter-morocco/">Fresh produce exporter</a></li>
						<li><a href="<?php echo $fm_app; ?>export-services/">Export services</a></li>
						<li><a href="<?php echo $fm_app; ?>for-importers/">For importers</a></li>
						<li><a href="<?php echo $fm_app; ?>how-we-work/">How we work</a></li>
					</ul>
				</li>
			<li class="has-sub<?php echo $fm_about_actif ? ' is-current' : ''; ?>">
					<a href="<?php echo $fm_app; ?>about-us/" class="fm-sub-toggle">
						About us <i class="fa fa-angle-down" aria-hidden="true"></i>
					</a>
					<ul class="fm-sub">
						<li><a href="<?php echo $fm_app; ?>about-us/">Our story</a></li>
						<li><a href="<?php echo $fm_app; ?>why-foodmax/">Why work with us</a></li>
						<li><a href="<?php echo $fm_app; ?>quality/">Quality and commitment</a></li>
						<li><a href="<?php echo $fm_app; ?>morocco/">FoodMax Group and Morocco</a></li>
						<li><a href="<?php echo $fm_app; ?>gallery/">Photo gallery</a></li>
					</ul>
				</li>
				<li<?php echo (strpos($fm_path, 'blog') !== false) ? ' class="is-current"' : ''; ?>>
					<a href="<?php echo $fm_app; ?>blog/">Blog</a>
				</li>
				<li class="fm-nav-cta<?php echo (strpos($fm_path, 'contact') !== false) ? ' is-current' : ''; ?>">
					<a href="<?php echo $fm_app; ?>contact/">Contact us</a>
				</li>
			</ul>
		</nav>

		<div class="fm-nav-actions">
			<button type="button" class="fm-search-toggle" id="searchtoggl"
			        aria-label="Search products" aria-expanded="false" aria-controls="searchbar">
				<i class="fa fa-search" aria-hidden="true"></i>
			</button>
		</div>
	</div>

	<div id="searchbar" class="fm-searchbar">
		<div class="fm-searchbar-inner">
			<form id="searchform" method="get" action="<?php echo $fm_app; ?>products/" role="search">
				<label class="sr-only" for="s">Search for a product</label>
				<i class="fa fa-search fm-search-leading" aria-hidden="true"></i>
				<input type="search" name="motCle" id="s"
				       placeholder="Search our products&hellip;" autocomplete="off">
				<button type="submit" id="searchsubmit">Search</button>
			</form>
		</div>
	</div>
</header>

<script type="text/javascript" src="<?php echo $fm_app; ?>js/nav.js"></script>
