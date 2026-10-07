<?php
/**
 * Liste des pages de presentation du site et blocs de navigation entre elles.
 *
 * Pour ajouter une page : l'ajouter dans fm_site_pages(), creer le fichier .php
 * a la racine et ajouter la regle correspondante dans .htaccess.
 */
require_once(__DIR__ . "/paths.php");

function fm_site_pages()
{
    return array(
        'about-us' => array(
            'title' => 'Our story',
            'icon'  => 'fa-heart',
            'text'  => 'Who we are, what we believe in and what we want to be for the people we work with.',
        ),
        'how-we-work' => array(
            'title' => 'How we work',
            'icon'  => 'fa-road',
            'text'  => 'Five steps from the grower to your warehouse, and the care we put into each one.',
        ),
        'quality' => array(
            'title' => 'Quality and commitment',
            'icon'  => 'fa-check-circle-o',
            'text'  => 'What we promise ourselves on quality, and how we try to work responsibly.',
        ),
        'morocco' => array(
            'title' => 'FoodMax Group and Morocco',
            'icon'  => 'fa-globe',
            'text'  => 'A land of many climates and generations of know-how, and why it matters to us.',
        ),
        'export-services' => array(
            'title' => 'Export services',
            'icon'  => 'fa-ship',
            'text'  => 'From sourcing and packing to documents and shipping: what we take care of for you.',
        ),
        'for-importers' => array(
            'title' => 'For importers',
            'icon'  => 'fa-briefcase',
            'text'  => 'What an importer can expect from us, and how to start a first order.',
        ),
        'why-foodmax' => array(
            'title' => 'Why work with us',
            'icon'  => 'fa-star',
            'text'  => 'Six reasons to talk to us, and the kind of clients and partners we work with.',
        ),
    );
}

/* Cartes "pour aller plus loin". $exclure : slugs a ne pas afficher. */
function fm_explore($exclure = array(), $eyebrow = 'Keep exploring', $titre = 'Continue where you like', $sous = '')
{
    global $fm_app;
    ?>
<section class="fm-section fm-explore">
	<div class="fm-section-head">
		<p class="fm-eyebrow"><?php echo htmlspecialchars($eyebrow, ENT_QUOTES, 'UTF-8'); ?></p>
		<h2><?php echo htmlspecialchars($titre, ENT_QUOTES, 'UTF-8'); ?></h2>
		<?php if ($sous !== '') { ?><p class="fm-section-sub"><?php echo htmlspecialchars($sous, ENT_QUOTES, 'UTF-8'); ?></p><?php } ?>
	</div>
	<div class="fm-explore-grid">
		<?php foreach (fm_site_pages() as $slug => $pg) {
			if (in_array($slug, (array) $exclure, true)) { continue; } ?>
		<a class="fm-explore-card" href="<?php echo $fm_app . $slug; ?>/">
			<span class="fm-explore-icon"><i class="fa fa-fw <?php echo $pg['icon']; ?>" aria-hidden="true"></i></span>
			<h3><?php echo htmlspecialchars($pg['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
			<p><?php echo htmlspecialchars($pg['text'], ENT_QUOTES, 'UTF-8'); ?></p>
			<span class="fm-explore-more">Read more <i class="fa fa-arrow-right" aria-hidden="true"></i></span>
		</a>
		<?php } ?>
	</div>
</section>
<?php
}

/* En-tete de page avec fil d'Ariane. $titre_courant : dernier element. */
function fm_pagehead($eyebrow, $h1, $texte, $parent = false)
{
    global $fm_app;
    ?>
<section class="fm-pagehead">
	<div class="fm-pagehead-inner">
		<span class="fm-eyebrow"><?php echo htmlspecialchars($eyebrow, ENT_QUOTES, 'UTF-8'); ?></span>
		<h1><?php echo htmlspecialchars($h1, ENT_QUOTES, 'UTF-8'); ?></h1>
		<p><?php echo htmlspecialchars($texte, ENT_QUOTES, 'UTF-8'); ?></p>

		<nav class="fm-crumbs" aria-label="Breadcrumb">
			<ol>
				<li><a href="<?php echo $fm_app; ?>">Home</a></li>
				<?php if ($parent) { ?><li><a href="<?php echo $fm_app; ?>about-us/">About us</a></li><?php } ?>
				<li><?php echo htmlspecialchars($h1, ENT_QUOTES, 'UTF-8'); ?></li>
			</ol>
		</nav>
	</div>
</section>
<?php
}

/**
 * Grille "ce que nous exportons" : les familles de produits, chacune avec un
 * lien vers son guide acheteur (products/{slug}/). Partagee par les pages
 * d'export pour que les textes restent identiques et qu'un seul endroit soit
 * a modifier. Les saisons sont indicatives (voir les guides).
 */
function fm_export_grid()
{
    global $fm_app;
    $familles = array(
        array('Tomatoes',     'tomatoes',     'Round, cherry, plum and truss tomatoes, sorted by size and colour. Main season from October to May.'),
        array('Citrus',       'oranges',      'Oranges, clementines and lemons, picked by hand. Generally from autumn to spring.'),
        array('Melons',       'watermelon',   'Seeded, seedless and mini watermelons. Generally from March to August.'),
        array('Peppers',      'peppers',      'Bell peppers in several colours, and sweet pointed types. Generally from October to June.'),
        array('Courgettes',   'courgettes',   'Firm, smooth courgettes, picked young. Generally from autumn to late spring.'),
        array('Berries',      'berries',      'Strawberries, blueberries and more, kept cold from the moment they are picked.'),
        array('Figs',         'figs',         'Fresh figs from late summer to autumn, soft and sweet, and dried figs.'),
        array('Dried fruits', 'dried-fruits', 'Dried figs, dates and other dried fruits, depending on the harvest.'),
        array('Eggplants',    'eggplants',    'Glossy eggplants with firm flesh. Generally from autumn to early summer.'),
        array('Green leaves', 'green-leaves', 'Leafy greens and herbs, cut by hand and cooled straight away.'),
        array('Stone fruits', 'pits',         'Peaches, apricots, plums and cherries, from spring to summer.'),
    );
    ?>
	<div class="fm-export-grid">
		<?php foreach ($familles as $f) { ?>
		<article class="fm-export">
			<span class="fm-export-icon"><i class="fa fa-fw fa-circle" aria-hidden="true"></i></span>
			<h3><a href="<?php echo $fm_app . 'products/' . $f[1]; ?>/"><?php echo htmlspecialchars($f[0], ENT_QUOTES, 'UTF-8'); ?></a></h3>
			<p><?php echo htmlspecialchars($f[2], ENT_QUOTES, 'UTF-8'); ?></p>
		</article>
		<?php } ?>
		<article class="fm-export fm-export-more">
			<span class="fm-export-icon"><i class="fa fa-fw fa-ellipsis-h" aria-hidden="true"></i></span>
			<h3><a href="<?php echo $fm_app; ?>products/">And more</a></h3>
			<p>Other seasonal products too, so ask us what is available right now.</p>
		</article>
	</div>
<?php
}
