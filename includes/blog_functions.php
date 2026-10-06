<?php
/**
 * Fonctions partagees du blog Foodmax Group.
 *
 * Utilisee par blog.php (liste publique), blog-post.php (article publique)
 * et admin/GestionProduits.php (gestion des articles).
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
            return fm_blog_echapper(rtrim(mb_substr($texte, 0, $longueur, 'UTF-8')) . '&hellip;');
        }
        if (strlen($texte) <= $longueur) {
            return fm_blog_echapper($texte);
        }
        return fm_blog_echapper(rtrim(substr($texte, 0, $longueur)) . '&hellip;');
    }
}

if (!function_exists('fm_blog_date_fr')) {
    /**
     * Formate une date MySQL en français lisible.
     * Exemple : "2024-06-15 14:30:00" -> "15 juin 2024"
     *
     * @param string $dateSql
     * @return string
     */
    function fm_blog_date_fr($dateSql)
    {
        if (empty($dateSql)) {
            return '';
        }

        $mois = array(
            1  => 'janvier',   2 => 'février',  3 => 'mars',
            4  => 'avril',     5 => 'mai',      6 => 'juin',
            7  => 'juillet',   8 => 'août',     9 => 'septembre',
            10 => 'octobre',  11 => 'novembre', 12 => 'décembre',
        );

        $d = DateTime::createFromFormat('Y-m-d H:i:s', (string) $dateSql);
        if (!$d) {
            $d = new DateTime((string) $dateSql);
        }
        if (!$d) {
            return fm_blog_echapper($dateSql);
        }

        $jour  = (int) $d->format('j');
        $moisN = (int) $d->format('n');
        $annee = (int) $d->format('Y');

        return $jour . ' ' . $mois[$moisN] . ' ' . $annee;
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
