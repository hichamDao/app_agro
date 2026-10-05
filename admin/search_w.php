<?php
/**
 * Recherche d'une "work" par son nom puis redirection vers sa page.
 *
 * Securise : requete preparee, nom encode dans l'adresse, redirection par
 * en-tete HTTP (plus de JavaScript compose avec des donnees), et un resultat
 * vide renvoie vers le catalogue au lieu d'une adresse cassee.
 */
require_once(__DIR__ . "/../includes/paths.php");
require_once(__DIR__ . "/../includes/connection.php");

if (isset($_POST["Search"]) && is_string($_POST["Search"])) {
    $stmt = mysqli_prepare($conn, "SELECT namew FROM works WHERE namew LIKE ? LIMIT 1");
    $n = '';

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 's', $_POST["Search"]);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($res)) {
            $n = str_replace(" ", "_", (string) $row['namew']);
        }
        mysqli_stmt_close($stmt);
    }

    header('Location: ' . $fm_app . ($n !== '' ? 'offers/' . rawurlencode($n) . '/' : 'products/'));
    exit;
}
