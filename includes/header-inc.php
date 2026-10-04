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
?>
<body id='url_div'>

<header class="fm-nav" id="fmNav">
	<div class="fm-nav-inner">

		<a class="fm-nav-logo" href="<?php echo $fm_app; ?>" aria-label="Foodmax, accueil">
			<img src="<?php echo $fm_app; ?>images/logo.png" alt="Foodmax">
		</a>

		<button type="button" class="fm-nav-burger" id="fmBurger"
		        aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="fmMenu">
			<span></span><span></span><span></span>
		</button>

		<nav class="fm-nav-menu" id="fmMenu" aria-label="Navigation principale">
			<ul class="fm-nav-list">
				<li<?php echo ($fm_self === 'index.php' && $fm_path === '/') ? ' class="is-current"' : ''; ?>>
					<a href="<?php echo $fm_app; ?>">Home</a>
				</li>
				<li<?php echo (strpos($fm_path, 'about-us') !== false) ? ' class="is-current"' : ''; ?>>
					<a href="<?php echo $fm_app; ?>about-us/">About us</a>
				</li>
				<li class="has-sub<?php echo (strpos($fm_path, 'products') !== false || strpos($fm_path, 'offers') !== false) ? ' is-current' : ''; ?>">
					<a href="<?php echo $fm_app; ?>products/" class="fm-sub-toggle">
						Products <i class="fa fa-angle-down" aria-hidden="true"></i>
					</a>
					<ul class="fm-sub">
						<li><a href="<?php echo $fm_app; ?>products/7/Tomatoes/">Tomatoes</a></li>
						<li><a href="<?php echo $fm_app; ?>products/3/Citrus/">Citrus</a></li>
						<li><a href="<?php echo $fm_app; ?>products/8/Peppers/">Peppers</a></li>
						<li><a href="<?php echo $fm_app; ?>products/14/Driedfruits/">Dried Fruits</a></li>
						<li class="fm-sub-all"><a href="<?php echo $fm_app; ?>products/">All products</a></li>
					</ul>
				</li>
				<li<?php echo (strpos($fm_path, 'gallery') !== false) ? ' class="is-current"' : ''; ?>>
					<a href="<?php echo $fm_app; ?>gallery/">Gallery</a>
				</li>
				<li<?php echo (strpos($fm_path, 'contact') !== false) ? ' class="is-current"' : ''; ?>>
					<a href="<?php echo $fm_app; ?>contact/">Contact</a>
				</li>
			</ul>
		</nav>

		<div class="fm-nav-actions">
			<button type="button" class="fm-search-toggle" id="searchtoggl"
			        aria-label="Rechercher" aria-expanded="false" aria-controls="searchbar">
				<i class="fa fa-search" aria-hidden="true"></i>
			</button>
		</div>
	</div>

	<div id="searchbar" class="fm-searchbar">
		<div class="fm-searchbar-inner">
			<form id="searchform" method="get" action="<?php echo $fm_app; ?>products/" role="search">
				<label class="sr-only" for="s">Rechercher un produit</label>
				<i class="fa fa-search fm-search-leading" aria-hidden="true"></i>
				<input type="search" name="motCle" id="s"
				       placeholder="Search our products&hellip;" autocomplete="off">
				<button type="submit" id="searchsubmit">Search</button>
			</form>
		</div>
	</div>
</header>

<script type="text/javascript" src="<?php echo $fm_app; ?>js/nav.js"></script>
