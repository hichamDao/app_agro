<?php
/**
 * Recherche d'un produit par son nom puis redirection vers sa fiche.
 *
 * Securise : requete preparee, identifiant force en entier, redirection par
 * en-tete HTTP (plus de JavaScript compose avec des donnees), et un resultat
 * vide renvoie vers le catalogue au lieu d'une adresse cassee.
 */
require_once(__DIR__ . "/../includes/paths.php");
require_once(__DIR__ . "/../includes/connection.php");

if (isset($_POST["Search"]) && is_string($_POST["Search"])) {
    $stmt = mysqli_prepare($conn, "SELECT id_prod FROM produits WHERE Designation LIKE ? LIMIT 1");
    $id = 0;

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 's', $_POST["Search"]);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($res)) {
            $id = (int) $row['id_prod'];
        }
        mysqli_stmt_close($stmt);
    }

    header('Location: ' . $fm_app . ($id > 0 ? 'products_detail/' . $id . '/' : 'products/'));
    exit;
}
