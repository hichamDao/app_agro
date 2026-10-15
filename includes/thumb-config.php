<?php
/**
 * REGLAGES DES MINIATURES : un seul endroit.
 *
 * Pour changer la taille des miniatures, modifiez les listes de largeurs
 * ci-dessous (en pixels) : toutes les pages du site (accueil, catalogue,
 * fiches produit, blog, guides) et thumb.php les utilisent.
 *
 *  - Plus petit  = pages plus rapides, image moins nette sur grand ecran.
 *  - Plus grand  = image plus nette, un peu plus lourde.
 *
 * Le navigateur choisit lui-meme la largeur adaptee a l'ecran du visiteur parmi
 * celles proposees (un telephone prendra la plus petite, un ecran Retina la plus
 * grande). Une miniature n'est jamais plus grande que la photo d'origine.
 *
 * Apres une modification, videz le dossier cache/thumbs/ (les miniatures
 * deja creees gardent l'ancienne taille tant qu'elles y sont).
 */
return array(

    /* Largeurs que thumb.php accepte de fabriquer. Une demande est arrondie a la
       valeur autorisee superieure. Cette liste limite aussi la taille du cache :
       toute largeur utilisee plus bas doit y figurer. */
    'largeurs' => array(240, 320, 400, 480, 640, 800, 1000, 1200),

    /* Qualite de compression (0-100) : 75-85 est un bon compromis. */
    'qualite_webp' => 80,
    'qualite_jpeg' => 82,

    /* Cartes produit (accueil, catalogue, produits lies, cartes du blog). */
    'carte'  => array(320, 480, 640),

    /* Image principale (fiche produit, article de blog, guide). */
    'grande' => array(640, 800, 1000),
);
