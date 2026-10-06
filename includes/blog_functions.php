<?php
/**
 * Fonctions partagees du blog Foodmax Group.
 *
 * Utilisee par blog.php (liste publique), blog-post.php (article publique)
 * et admin/GestionBlog.php (gestion des articles).
 *
 * Les fonctions sont declarees une seule fois et verrouillees par
 * function_exists() pour eviter les redeclarations en cas d'inclusion
 * multiple.
 */

if (!function_exists('fm_blog_slugify')) {
    /**
     * Genere un slug URL-friendly a partir d'un titre.
     * Exemple : "Moroccan Tomatoes: Varieties, Season and Export" -> "moroccan-tomatoes-varieties-season-and-export"
     *
     * @param string $text
     * @return string
     */
    function fm_blog_slugify($text)
    {
        /* Convertit en UTF-8 pour les operations de chaine. */
        $text = (string) $text;

        /* Remplace les espaces par des tirets. */
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        /* Supprime les caracteres non alphanumeriques restants (garde les tirets). */
        $text = preg_replace('~[^-\w]+~', '', $text);

        /* Supprime les tirets en debut/fin. */
        $text = trim($text, '-');

        /* Met en minuscules. */
        $text = strtolower($text);

        /* Supprime les caracteres non-ASCII restants (accents, etc.). */
        if (function_exists('iconv')) {
            $text = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
            $text = preg_replace('~[^-\w]+~', '-', $text);
            $text = trim($text, '-');
            $text = strtolower($text);
        }

        return $text !== '' ? $text : 'article';
    }
}

if (!function_exists('fm_blog_categories')) {
    /**
     * Liste les categories de blog publiees, avec le nombre d'articles.
     *
     * @param mysqli $conn
     * @return array  Chaque element : ['category' => string, 'count' => int]
     */
    function fm_blog_categories($conn)
    {
        $out = array();
        $sql = 'SELECT category, COUNT(*) AS nb
                FROM blog_posts
                WHERE status = \'published\' AND category IS NOT NULL AND category != \'\'
                GROUP BY category
                ORDER BY category ASC';
        $rs = mysqli_query($conn, $sql);
        if ($rs) {
            while ($row = mysqli_fetch_assoc($rs)) {
                $out[] = array(
                    'category' => $row['category'],
                    'count'    => (int) $row['nb'],
                );
            }
        }
        return $out;
    }
}

if (!function_exists('fm_blog_recent')) {
    /**
     * Derniers articles publies (pour les "articles populaires" ou "recents").
     *
     * @param mysqli $conn
     * @param int    $limit
     * @return array
     */
    function fm_blog_recent($conn, $limit = 5)
    {
        $limit = max(1, (int) $limit);
        $stmt = mysqli_prepare(
            $conn,
            'SELECT id, title, slug, excerpt, image, created_at
             FROM blog_posts
             WHERE status = \'published\'
             ORDER BY created_at DESC LIMIT ?'
        );
        $posts = array();
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'i', $limit);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if ($res) {
                while ($p = mysqli_fetch_assoc($res)) {
                    $posts[] = $p;
                }
            }
            mysqli_stmt_close($stmt);
        }
        return $posts;
    }
}

if (!function_exists('fm_blog_echapper')) {
    /** Helper d'echappement pour le blog (identique au reste du site). */
    function fm_blog_echapper($valeur)
    {
        return htmlspecialchars((string) $valeur, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('fm_blog_resume')) {
    /**
     * Extrait un extrait (excerpt) d'un contenu HTML.
     * Si la colonne excerpt est vide, on en derive un a partir de content.
     *
     * @param string $content
     * @param int    $longueur
     * @return string  Texte brut tronque, echappe pour du HTML.
     */
    function fm_blog_resume($content, $longueur = 160)
    {
        $texte = trim(preg_replace('/\s+/u', ' ', strip_tags((string) $content)));
        $texte = html_entity_decode($texte, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        if (function_exists('mb_strlen')) {
            if (mb_strlen($texte, 'UTF-8') <= $longueur) {
                return fm_blog_echapper($texte);
            }
            return fm_blog_echapper(rtrim(mb_substr($texte, 0, $longueur, 'UTF-8')) . "\u{2026}");
        }
        if (strlen($texte) <= $longueur) {
            return fm_blog_echapper($texte);
        }
        return fm_blog_echapper(rtrim(substr($texte, 0, $longueur)) . '...');
    }
}

if (!function_exists('fm_blog_date_fr')) {
    /**
     * Formate une date MySQL pour l'affichage public (site en anglais).
     * Exemple : "2026-05-15 09:00:00" -> "May 15, 2026"
     * (Le nom est conserve pour ne pas casser les fichiers qui l'utilisent.)
     *
     * @param string $dateSql
     * @return string  Texte deja echappe pour du HTML.
     */
    function fm_blog_date_fr($dateSql)
    {
        if (empty($dateSql)) {
            return '';
        }
        try {
            $d = new DateTime((string) $dateSql);
        } catch (Exception $e) {
            return fm_blog_echapper($dateSql);
        }
        return fm_blog_echapper($d->format('F j, Y'));
    }
}

if (!function_exists('fm_blog_date_iso')) {
    /** Date au format ISO 8601 (donnees structurees). */
    function fm_blog_date_iso($dateSql)
    {
        if (empty($dateSql)) { return ''; }
        try {
            $d = new DateTime((string) $dateSql);
        } catch (Exception $e) {
            return '';
        }
        return $d->format('c');
    }
}

if (!function_exists('fm_blog_lien')) {
    /**
     * Adresse sure pour un lien saisi dans l'administration :
     *  - adresse complete http(s) : conservee ;
     *  - chemin du site ("products/3/Citrus/") : prefixe par $fm_app ;
     *  - tout le reste (javascript:, data:...) : refuse (renvoie '').
     * Le resultat est echappe pour un attribut HTML.
     */
    function fm_blog_lien($fm_app, $lien)
    {
        $lien = trim((string) $lien);
        if ($lien === '') { return ''; }
        if (preg_match('~^https?://~i', $lien)) { return fm_blog_echapper($lien); }
        if (preg_match('~^[a-z][a-z0-9+.-]*:~i', $lien)) { return ''; }   /* autre schema */
        return fm_blog_echapper($fm_app . ltrim($lien, '/'));
    }
}

if (!function_exists('fm_blog_nettoyer')) {
    /**
     * Nettoie le HTML d'un article avant affichage.
     *
     * Ne garde que les balises et attributs utiles a un article (titres,
     * paragraphes, listes, tableaux, liens, images). Retire scripts, styles,
     * iframes, formulaires, gestionnaires d'evenements (onclick...) et
     * adresses javascript:. Les liens relatifs ("products/", "contact/")
     * sont rendus valides depuis n'importe quelle page grace a $fm_app : sans
     * cela, ils pointeraient vers /blog/mon-article/products/ (erreur 404).
     */
    function fm_blog_nettoyer($html, $fm_app = '/')
    {
        $html = (string) $html;
        if (trim($html) === '') { return ''; }

        $ok = array_flip(array(
            'h2', 'h3', 'h4', 'p', 'ul', 'ol', 'li', 'strong', 'b', 'em', 'i', 'a', 'br',
            'table', 'thead', 'tbody', 'tfoot', 'tr', 'th', 'td', 'blockquote', 'img',
            'figure', 'figcaption', 'span', 'div', 'hr', 'small', 'sup', 'sub', 'caption',
        ));
        $retirer = array_flip(array('script', 'style', 'iframe', 'object', 'embed', 'form', 'input',
            'button', 'textarea', 'select', 'link', 'meta', 'svg', 'math', 'base', 'noscript', 'title'));
        $attrs = array(
            'a'   => array('href', 'title', 'target', 'rel', 'class'),
            'img' => array('src', 'alt', 'width', 'height', 'class'),
            'th'  => array('colspan', 'rowspan', 'scope'),
            'td'  => array('colspan', 'rowspan'),
        );
        $classesOk = array('btn', 'btn-sm', 'btn-outline');

        $prev = libxml_use_internal_errors(true);
        $dom  = new DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML('<?xml encoding="UTF-8"><html><body><div id="fm-racine">' . $html . '</div></body></html>');
        libxml_clear_errors();
        libxml_use_internal_errors($prev);

        $racine = $dom->getElementById('fm-racine');
        if (!$racine) { return ''; }

        $nettoyerUrl = function ($url) use ($fm_app) {
            $url = trim($url);
            $test = preg_replace('/[\x00-\x20]+/', '', $url);     /* "java\nscript:" */
            if ($test === '') { return null; }
            if (preg_match('~^(https?:|mailto:|tel:)~i', $test)) { return $url; }
            if ($test[0] === '#') { return $url; }
            if (preg_match('~^[a-z][a-z0-9+.-]*:~i', $test)) { return null; }   /* javascript:, data:... */
            if ($test[0] === '/') { return $url; }
            return $fm_app . ltrim($url, '/');                       /* lien relatif -> depuis la racine */
        };

        $parcourir = function ($noeud) use (&$parcourir, $ok, $retirer, $attrs, $classesOk, $nettoyerUrl) {
            foreach (iterator_to_array($noeud->childNodes) as $enfant) {
                if ($enfant->nodeType === XML_COMMENT_NODE) { $noeud->removeChild($enfant); continue; }
                if ($enfant->nodeType !== XML_ELEMENT_NODE) { continue; }

                $nom = strtolower($enfant->nodeName);
                if (isset($retirer[$nom])) { $noeud->removeChild($enfant); continue; }

                $parcourir($enfant);                                  /* d'abord les descendants */

                if (!isset($ok[$nom])) {                               /* balise inconnue : on garde son contenu */
                    while ($enfant->firstChild) { $noeud->insertBefore($enfant->firstChild, $enfant); }
                    $noeud->removeChild($enfant);
                    continue;
                }

                $permis = isset($attrs[$nom]) ? $attrs[$nom] : array();
                foreach (iterator_to_array($enfant->attributes) as $a) {
                    $an = strtolower($a->nodeName);
                    if (!in_array($an, $permis, true)) { $enfant->removeAttribute($a->nodeName); continue; }
                    if ($an === 'href' || $an === 'src') {
                        $u = $nettoyerUrl($a->nodeValue);
                        if ($u === null) { $enfant->removeAttribute($a->nodeName); } else { $enfant->setAttribute($a->nodeName, $u); }
                    } elseif ($an === 'class') {
                        $garde = array_values(array_intersect(preg_split('/\s+/', trim($a->nodeValue)), $classesOk));
                        if ($garde) { $enfant->setAttribute('class', implode(' ', $garde)); } else { $enfant->removeAttribute('class'); }
                    }
                }
                if ($nom === 'a' && $enfant->getAttribute('target') === '_blank') {
                    $enfant->setAttribute('rel', 'noopener');
                }
                if ($nom === 'img' && !$enfant->hasAttribute('alt')) { $enfant->setAttribute('alt', ''); }
            }
        };
        $parcourir($racine);

        $sortie = '';
        foreach ($racine->childNodes as $n) { $sortie .= $dom->saveHTML($n); }
        return trim($sortie);
    }
}

if (!function_exists('fm_blog_texte')) {
    /** Texte brut (sans balises) d'un contenu HTML, pour les meta et le JSON-LD. */
    function fm_blog_texte($contenu, $longueur = 0)
    {
        $texte = trim(preg_replace('/\s+/u', ' ', strip_tags((string) $contenu)));
        $texte = html_entity_decode($texte, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if ($longueur > 0 && mb_strlen($texte, 'UTF-8') > $longueur) {
            $texte = rtrim(mb_substr($texte, 0, $longueur - 1, 'UTF-8')) . "\u{2026}";
        }
        return $texte;
    }
}

if (!function_exists('fm_blog_image_url')) {
    /**
     * Retourne l'URL d'image d'un article avec repli sur une image generique.
     *
     * @param mysqli $conn
     * @param array  $post
     * @return string
     */
    function fm_blog_image_url($fm_app, $post)
    {
        $image = isset($post['image']) ? trim((string) $post['image']) : '';
        if ($image !== '' && strpos($image, 'http') === 0) {
            return $image;
        }
        if ($image !== '' && is_file(__DIR__ . '/../img/blog/' . $image)) {
            return $fm_app . 'img/blog/' . rawurlencode($image);
        }
        /* Image par défaut, catégorie-dépendante. */
        $catMap = array(
            'Morocco'   => 'blog-morocco.jpg',
            'Tomatoes'  => 'blog-tomatoes.jpg',
            'Citrus'    => 'blog-citrus.jpg',
            'Watermelon'=> 'blog-watermelon.jpg',
            'Logistics' => 'blog-logistics.jpg',
            'Cold chain'=> 'blog-cold-chain.jpg',
        );
        $cat = isset($post['category']) ? (string) $post['category'] : '';
        if (isset($catMap[$cat]) && is_file(__DIR__ . '/../img/blog/' . $catMap[$cat])) {
            return $fm_app . 'img/blog/' . $catMap[$cat];
        }
        return $fm_app . 'img/blog-placeholder.svg';
    }
}

if (!function_exists('fm_prod_photo')) {
    /** Photo d'un produit (images/REF/fichier) avec repli sur une image generique. */
    function fm_prod_photo($fm_app, $ref, $photo)
    {
        $liste = explode(',', (string) $photo);
        foreach ($liste as $ph) {
            $ph = trim($ph);
            if ($ph !== '' && is_file(__DIR__ . '/../images/' . $ref . '/' . $ph)) {
                return $fm_app . 'images/' . rawurlencode((string) $ref) . '/' . rawurlencode($ph);
            }
        }
        return $fm_app . 'img/product-placeholder.svg';
    }
}

if (!function_exists('fm_img_absolue')) {
    /** Adresse absolue d'une image du site (pour Open Graph). '' pour une image generique (.svg). */
    function fm_img_absolue($fm_app, $url)
    {
        if ($url === '' || preg_match('~\.svg$~i', $url)) { return ''; }
        if (preg_match('~^https?://~i', $url)) { return $url; }
        $rel = (strpos($url, $fm_app) === 0) ? substr($url, strlen($fm_app)) : ltrim($url, '/');
        return rtrim(FM_SITE_URL, '/') . '/' . ltrim($rel, '/');
    }
}
