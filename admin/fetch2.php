<?php
/**
 * Auto-completion des produits (JSON pour jQuery UI).
 *
 * Securise : requete preparee (plus aucune concatenation de $_GET dans le SQL),
 * texte et chemins echappes dans le HTML renvoye, connexion commune du site.
 */
require_once(__DIR__ . "/../includes/connection.php");

if (isset($_GET["term"]) && is_string($_GET["term"])) {
    header('Content-Type: application/json; charset=utf-8');

    /* Les % et _ saisis par le visiteur sont des caracteres ordinaires. */
    $motif = '%' . addcslashes($_GET["term"], '%_\\') . '%';

    $stmt = mysqli_prepare($conn,
        "SELECT id_prod, Designation, Ref_prod, Photo FROM produits
         WHERE Designation LIKE ?
         ORDER BY Designation ASC");
    $output = array();

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 's', $motif);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($res)) {
            $photo = explode(",", (string) $row['Photo']);

            $output[] = array(
                'id'    => $row['id_prod'],
                'value' => $row['Designation'],
                'label' => '<img src="../images/' . rawurlencode((string) $row['Ref_prod']) . '/' . rawurlencode($photo[0])
                         . '" width="100"><span>' . htmlspecialchars((string) $row['Designation'], ENT_QUOTES, 'UTF-8') . '</span>',
            );
        }
        mysqli_stmt_close($stmt);
    }

    if (!$output) {
        $output = array('id' => '', 'value' => '', 'label' => 'No Record Found');
    }
    echo json_encode($output);
}
