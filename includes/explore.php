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
function fm_pagehead($eyebrow, $h1, $texte, $parent = true)
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
