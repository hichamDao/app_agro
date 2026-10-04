<?php
/* Amorce commune aux pages publiques : connexion SQL et session.
   Le panier having ete retire du site, ce fichier ne prepare plus
   aucun etat de commande. */
require_once __DIR__ . "/connection.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}