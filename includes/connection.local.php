<?php
/**
 * Configuration de developpement local — A NE PAS DEPLOYER.
 *
 * Meme base que la production, mais sur le poste de travail ou EasyPHP sert
 * le site (http://127.0.0.1:8080/foodmaxorg/).
 *
 * Ce fichier n est lu que si l hote de la requete est localhost ou
 * 127.0.0.1 : meme televerse par megarde sur WEDOS, il resterait inoffensif.
 * Le deploiement ne demande donc que connection.php.
 */
return array(
    'host' => 'localhost',
    'user' => 'root',
    'pass' => '',
    'db'   => 'd150242_foodmax',
);