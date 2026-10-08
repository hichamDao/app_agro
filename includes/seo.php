<?php
/**
 * SEO commun : adresse canonique, Open Graph, Twitter, donnees structurees.
 *
 * Utilisation (dans le <head>, apres <title> et <meta description>) :
 *   fm_seo_head(array(
 *       'path'        => 'blog/mon-article/',   // chemin depuis la racine du site
 *       'title'       => '...',                 // titre pour les reseaux sociaux
 *       'description' => '...',
 *       'image'       => 'https://.../x.jpg',   // adresse absolue (facultatif)
 *       'type'        => 'website' | 'article',
 *       'noindex'     => false,
 *       'prev'        => 'blog/',  'next' => 'blog/?page=3',   // facultatif
 *       'jsonld'      => array( array(...), array(...) ),       // facultatif
 *   ));
 *
 * L'adresse du site est fixee ci-dessous (et non lue dans l'en-tete HTTP Host,
 * qu'un visiteur peut falsifier) : a adapter si le domaine change.
 */
if (!defined('FM_SITE_URL')) {
    define('FM_SITE_URL', 'https://foodmax-group.com');
}
if (!defined('FM_SITE_NAME')) {
    define('FM_SITE_NAME', 'FoodMax Group');
}

if (!function_exists('fm_seo_h')) {
    function fm_seo_h($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); }
}

if (!function_exists('fm_seo_url')) {
    /** Adresse absolue d'une page : fm_seo_url('blog/') -> https://foodmax-group.com/blog/ */
    function fm_seo_url($chemin = '')
    {
        return rtrim(FM_SITE_URL, '/') . '/' . ltrim((string) $chemin, '/');
    }
}

if (!function_exists('fm_seo_org')) {
    /** Fiche "Organization" (entreprise), reprise dans les donnees structurees. */
    function fm_seo_org()
    {
        return array(
            '@type'   => 'Organization',
            'name'    => FM_SITE_NAME,
            'url'     => fm_seo_url(''),
            'email'   => 'info@foodmax-group.com',
            'address' => array(
                '@type'           => 'PostalAddress',
                'streetAddress'   => '16 Rue AL Ikhae Apt 2, Zone industrielle',
                'addressLocality' => 'Marrakech',
                'addressCountry'  => 'MA',
            ),
        );
    }
}

if (!function_exists('fm_seo_breadcrumb')) {
    /** $etapes : array(array('Home', ''), array('Blog', 'blog/'), ...) */
    function fm_seo_breadcrumb($etapes)
    {
        $items = array();
        $i = 1;
        foreach ($etapes as $e) {
            $items[] = array(
                '@type'    => 'ListItem',
                'position' => $i++,
                'name'     => $e[0],
                'item'     => fm_seo_url($e[1]),
            );
        }
        return array('@type' => 'BreadcrumbList', 'itemListElement' => $items);
    }
}

if (!function_exists('fm_seo_head')) {
    function fm_seo_head($o)
    {
        $path  = isset($o['path']) ? $o['path'] : '';
        $url   = fm_seo_url($path);
        $title = isset($o['title']) ? $o['title'] : FM_SITE_NAME;
        $desc  = isset($o['description']) ? $o['description'] : '';
        $type  = isset($o['type']) ? $o['type'] : 'website';

        echo "\t<meta name=\"robots\" content=\"" . (!empty($o['noindex']) ? 'noindex, follow' : 'index, follow, max-image-preview:large') . "\">\n";
        if (empty($o['noindex'])) {
            echo "\t<link rel=\"canonical\" href=\"" . fm_seo_h($url) . "\">\n";
        }
        if (!empty($o['prev'])) { echo "\t<link rel=\"prev\" href=\"" . fm_seo_h(fm_seo_url($o['prev'])) . "\">\n"; }
        if (!empty($o['next'])) { echo "\t<link rel=\"next\" href=\"" . fm_seo_h(fm_seo_url($o['next'])) . "\">\n"; }

        echo "\t<meta property=\"og:site_name\" content=\"" . fm_seo_h(FM_SITE_NAME) . "\">\n";
        echo "\t<meta property=\"og:locale\" content=\"en_GB\">\n";
        echo "\t<meta property=\"og:type\" content=\"" . fm_seo_h($type) . "\">\n";
        echo "\t<meta property=\"og:title\" content=\"" . fm_seo_h($title) . "\">\n";
        echo "\t<meta property=\"og:description\" content=\"" . fm_seo_h($desc) . "\">\n";
        echo "\t<meta property=\"og:url\" content=\"" . fm_seo_h($url) . "\">\n";
        if (!empty($o['image'])) {
            echo "\t<meta property=\"og:image\" content=\"" . fm_seo_h($o['image']) . "\">\n";
        }
        echo "\t<meta name=\"twitter:card\" content=\"" . (!empty($o['image']) ? 'summary_large_image' : 'summary') . "\">\n";
        echo "\t<meta name=\"twitter:title\" content=\"" . fm_seo_h($title) . "\">\n";
        echo "\t<meta name=\"twitter:description\" content=\"" . fm_seo_h($desc) . "\">\n";
        if (!empty($o['image'])) {
            echo "\t<meta name=\"twitter:image\" content=\"" . fm_seo_h($o['image']) . "\">\n";
        }

        if ($type === 'article') {
            if (!empty($o['published'])) { echo "\t<meta property=\"article:published_time\" content=\"" . fm_seo_h($o['published']) . "\">\n"; }
            if (!empty($o['modified']))  { echo "\t<meta property=\"article:modified_time\" content=\"" . fm_seo_h($o['modified']) . "\">\n"; }
            if (!empty($o['section']))   { echo "\t<meta property=\"article:section\" content=\"" . fm_seo_h($o['section']) . "\">\n"; }
        }

        /* Donnees structurees : JSON_HEX_TAG empeche un "</script>" dans une
           valeur de casser la balise. */
        if (!empty($o['jsonld'])) {
            foreach ($o['jsonld'] as $bloc) {
                $bloc = array('@context' => 'https://schema.org') + $bloc;
                echo "\t<script type=\"application/ld+json\">"
                    . json_encode($bloc, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP)
                    . "</script>\n";
            }
        }
    }
}

if (!function_exists('fm_seo_cut')) {
    /** Texte brut, espaces normalises, coupe a la fin d'un mot avec "..." (meta description). */
    function fm_seo_cut($texte, $max = 155)
    {
        $texte = trim(preg_replace('/\s+/u', ' ', strip_tags((string) $texte)));
        if (mb_strlen($texte, 'UTF-8') <= $max) { return $texte; }
        $coupe = mb_substr($texte, 0, $max - 1, 'UTF-8');
        $dernier = mb_strrpos($coupe, ' ', 0, 'UTF-8');
        if ($dernier !== false && $dernier > $max * 0.6) { $coupe = mb_substr($coupe, 0, $dernier, 'UTF-8'); }
        return rtrim($coupe, " ,;:.-") . "\u{2026}";
    }
}

if (!function_exists('fm_seo_title')) {
    /** Retire le suffixe " | FoodMax Group" quand le titre depasserait ~60 caracteres
     *  (Google coupe au-dela : mieux vaut un titre complet qu'une marque tronquee). */
    function fm_seo_title($titre)
    {
        $titre = trim((string) $titre);
        $suffixe = ' | ' . FM_SITE_NAME;
        if (mb_strlen($titre, 'UTF-8') > 62 && substr($titre, -strlen($suffixe)) === $suffixe) {
            $titre = substr($titre, 0, -strlen($suffixe));
        }
        return $titre;
    }
}

if (!function_exists('fm_cat_label')) {
    /** Nom affiche d'une famille de produits : "Pits" devient "Stone fruits", etc. */
    function fm_cat_label($nom)
    {
        $propre = trim(str_replace('_', ' ', (string) $nom));
        $cle    = strtolower(preg_replace('/[^a-z]/i', '', $propre));
        $noms   = array('pits' => 'Stone fruits', 'driedfruits' => 'Dried fruits', 'greenleaves' => 'Green leaves');
        return isset($noms[$cle]) ? $noms[$cle] : $propre;
    }
}
