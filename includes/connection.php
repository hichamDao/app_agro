<?php
/**
 * Connexion a la base de donnees.
 *
 * Un seul point de connexion pour tout le site : les 40 fichiers qui
 * utilisent $conn le font via cet include.
 *
 * Deux environnements partagent le meme code :
 *
 *  - la production WEDOS, sert depuis https://foodmax-group.com ;
 *  - le poste de developpement, ou EasyPHP sert le site sur
 *    http://127.0.0.1:8080/foodmaxorg/ avec la meme base recopiee en local.
 *
 * Plutot que de dupliquer le fichier, les identifiants de developpement sont
 * isoles dans connection.local.php. Ce fichier n est lu QUE pour un hote
 * local (localhost ou 127.0.0.1) : meme s il se retrouve par megarde sur le
 * serveur, la production continue d utiliser ses propres identifiants.
 */
$fm_config = array(
    'host' => 'wm133.wedos.net',
    'user' => 'a150242_foodmax',
    'pass' => '48HsUqeq',
    'db'   => 'd150242_foodmax',
);

$fm_local = __DIR__ . '/connection.local.php';
if (is_file($fm_local)) {
    $fm_hote = isset($_SERVER['HTTP_HOST']) ? strtolower($_SERVER['HTTP_HOST']) : '';
    /* also_un : couvre 127.0.0.1:8080 comme 127.0.0.1. */
    $fm_est_local = ($fm_hote !== '')
        && (strpos($fm_hote, 'localhost') === 0 || strpos($fm_hote, '127.0.0.1') === 0);

    if ($fm_est_local) {
        $fm_config = array_merge($fm_config, (array) require $fm_local);
    }
}

$conn = mysqli_connect(
    $fm_config['host'],
    $fm_config['user'],
    $fm_config['pass'],
    $fm_config['db']
) or die(mysqli_connect_error());

if (mysqli_set_charset($conn, 'utf8mb4') === false) {
    die(mysqli_error($conn));
}