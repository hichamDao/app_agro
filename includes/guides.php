<?php
/**
 * Bloc "Buyer's guides" : liste les fiches produit SEO publiees (table
 * product_seo) sous forme de cartes. Sert a relier le catalogue et les fiches
 * entre eux : un visiteur trouve l'information, et Google trouve toutes les
 * fiches depuis les pages importantes.
 *
 * Silencieux si la table n'existe pas encore ou est vide.
 */
require_once(__DIR__ . "/paths.php");

if (!function_exists('fm_guides_liste')) {
    function fm_guides_liste($conn, $exclure = '', $limite = 12)
    {
        $out = array();
        if (!$conn) { return $out; }
        try {
            $limite = max(1, (int) $limite);
            $stmt = mysqli_prepare($conn,
                "SELECT slug, title, subtitle FROM product_seo
                  WHERE status = 'published' AND slug <> ? ORDER BY title ASC LIMIT ?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'si', $exclure, $limite);
                mysqli_stmt_execute($stmt);
                $res = mysqli_stmt_get_result($stmt);
                if ($res) { while ($r = mysqli_fetch_assoc($res)) { $out[] = $r; } }
                mysqli_stmt_close($stmt);
            }
        } catch (Throwable $e) { $out = array(); }
        return $out;
    }
}

if (!function_exists('fm_guides_bloc')) {
    function fm_guides_bloc($conn, $exclure = '', $titre = "Buyer's guides", $sous = '', $alt = false)
    {
        global $fm_app;
        $guides = fm_guides_liste($conn, $exclure);
        if (!$guides) { return; }
        $h = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
        ?>
<section class="fm-section<?php echo $alt ? ' fm-section-alt' : ''; ?> fm-explore">
	<div class="fm-section-head">
		<p class="fm-eyebrow">Product guides</p>
		<h2><?php echo $h($titre); ?></h2>
		<?php if ($sous !== '') { ?><p class="fm-section-sub"><?php echo $h($sous); ?></p><?php } ?>
	</div>
	<div class="fm-explore-grid">
		<?php foreach ($guides as $g) { ?>
		<a class="fm-explore-card" href="<?php echo $fm_app . "products/" . $h(rawurlencode($g["slug"])); ?>/">
			<span class="fm-explore-icon"><i class="fa fa-fw fa-leaf" aria-hidden="true"></i></span>
			<h3><?php echo $h($g['title']); ?></h3>
			<p><?php echo $h($g['subtitle']); ?></p>
			<span class="fm-explore-more">Read the guide <i class="fa fa-arrow-right" aria-hidden="true"></i></span>
		</a>
		<?php } ?>
	</div>
</section>
<?php
    }
}
