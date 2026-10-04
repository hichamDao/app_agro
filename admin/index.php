<?php
/**
 * Tableau de bord de l'administration.
 *
 * Point d'entree des pages admin : quelques compteurs et les raccourcis vers
 * les trois modules. Le repertoire /admin/ n'avait pas d'index : la racine
 * renvoyait le catalogue, il fallait connaitre les URLs.
 */

require_once(__DIR__ . '/../includes/admin-auth.php');
require_once(__DIR__ . '/../includes/admin-layout.php');

fm_admin_exiger_login();

/** Compte d'une table, 0 si la table est absente. */
function fm_admin_compter($conn, $table, $where = '') {
    $r = mysqli_query($conn, 'SELECT COUNT(*) AS n FROM `' . $table . '`' . $where);
    return $r ? (int) mysqli_fetch_assoc($r)['n'] : 0;
}

$totalProduits   = fm_admin_compter($conn, 'produits');
$totalCategories = fm_admin_compter($conn, 'categories');
$totalMessages   = fm_admin_compter($conn, 'contact');
$totalMembres    = fm_admin_compter($conn, 'members');

/* Messages des sept derniers jours, pour le bloc « recents ». */
$recents = array();
$stmt = mysqli_query($conn, 'SELECT contact_id, name, email, message, date FROM contact ORDER BY contact_id DESC LIMIT 5');
if ($stmt) {
    while ($c = mysqli_fetch_assoc($stmt)) { $recents[] = $c; }
}

/* Produits sans categorie valide : la barre laterale publique ne peut pas
   les joindre, ils n'apparaissent donc dans aucune famille. */
$orphelins = 0;
$stmt = mysqli_query(
    $conn,
    'SELECT COUNT(*) AS n FROM produits p
     LEFT JOIN categories c ON c.Code_cat = p.Code_cat
     WHERE c.Code_cat IS NULL'
);
if ($stmt) {
    $orphelins = (int) mysqli_fetch_assoc($stmt)['n'];
}

/* Produits sans photo : la vignette generique remplace la photo sur le site. */
$sansPhoto = 0;
$stmt = mysqli_query($conn, "SELECT COUNT(*) AS n FROM produits WHERE Photo IS NULL OR Photo = ''");
if ($stmt) {
    $sansPhoto = (int) mysqli_fetch_assoc($stmt)['n'];
}

admin_entete('Tableau de bord', 'index');
?>

<div class="fm-adm-stats">
    <a class="fm-adm-stat" href="GestionProduits.php">
        <span class="fm-adm-stat-n"><?php echo (int) $totalProduits; ?></span>
        <span class="fm-adm-stat-l">Produits au catalogue</span>
    </a>
    <a class="fm-adm-stat" href="GestionCategories.php">
        <span class="fm-adm-stat-n"><?php echo (int) $totalCategories; ?></span>
        <span class="fm-adm-stat-l">Categories</span>
    </a>
    <a class="fm-adm-stat is-terra" href="GestionContacts.php">
        <span class="fm-adm-stat-n"><?php echo (int) $totalMessages; ?></span>
        <span class="fm-adm-stat-l">Messages recus</span>
    </a>
    <a class="fm-adm-stat" href="GestionProduits.php?action=nouveau">
        <span class="fm-adm-stat-n"><?php echo (int) $totalMembres; ?></span>
        <span class="fm-adm-stat-l">Comptes autorises</span>
    </a>
</div>

<?php if ($orphelins > 0 || $sansPhoto > 0) { ?>
<div class="fm-adm-alert">
    <i class="fa fa-info-circle" aria-hidden="true"></i>
    <span>
        <?php if ($orphelins > 0) { ?>
        <b><?php echo (int) $orphelins; ?></b> produit(s) sans categorie valide :
        ils n'apparaissent dans aucune famille du site.
        <?php } ?>
        <?php if ($sansPhoto > 0) { ?>
        <b><?php echo (int) $sansPhoto; ?></b> produit(s) sans photo :
        le site affiche la vignette generique.
        <?php } ?>
    </span>
</div>
<?php } ?>

<div class="fm-adm-card">
    <header>
        <h2>Modules</h2>
        <span class="fm-adm-spacer"></span>
    </header>
    <div class="fm-adm-pad">
        <div class="fm-adm-grid">
            <div>
                <p style="font-weight:700;margin:0 0 4px;">Produits</p>
                <p style="font-size:13px;color:var(--fm-muted);margin:0 0 10px;">
                    Liste filtrable par titre et par categorie, ajout, modification,
                    suppression et gestion des photos.
                </p>
                <a class="fm-adm-btn is-sm" href="GestionProduits.php">Ouvrir</a>
                <a class="fm-adm-btn is-light is-sm" href="GestionProduits.php?action=nouveau">Ajouter</a>
            </div>
            <div>
                <p style="font-weight:700;margin:0 0 4px;">Categories</p>
                <p style="font-size:13px;color:var(--fm-muted);margin:0 0 10px;">
                    Libelles et descriptions, avec le nombre de produits rattaches.
                    Une categorie encore utilisee n'est pas supprimable.
                </p>
                <a class="fm-adm-btn is-sm" href="GestionCategories.php">Ouvrir</a>
                <a class="fm-adm-btn is-light is-sm" href="GestionCategories.php?action=nouveau">Ajouter</a>
            </div>
            <div>
                <p style="font-weight:700;margin:0 0 4px;">Messages</p>
                <p style="font-size:13px;color:var(--fm-muted);margin:0 0 10px;">
                    Messages du formulaire de contact, lecture, reponse et export
                    pour impression ou PDF.
                </p>
                <a class="fm-adm-btn is-sm" href="GestionContacts.php">Ouvrir</a>
                <a class="fm-adm-btn is-light is-sm" href="contact_print.php">Imprimer</a>
            </div>
        </div>
    </div>
</div>

<div class="fm-adm-card">
    <header>
        <h2>Derniers messages</h2>
        <span class="fm-adm-spacer"></span>
        <a class="fm-adm-btn is-light is-sm" href="GestionContacts.php">Tout voir</a>
    </header>

    <?php if (!$recents) { ?>
    <div class="fm-adm-empty"><i class="fa fa-inbox" aria-hidden="true"></i>Aucun message recu.</div>
    <?php } else { ?>
    <div class="fm-adm-table-wrap">
        <table class="fm-adm-table">
            <thead>
                <tr>
                    <th>Expediteur</th>
                    <th>Message</th>
                    <th style="width:150px;">Date</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($recents as $c) { ?>
                <tr>
                    <td>
                        <strong><?php echo fm_admin_echapper($c['name']); ?></strong><br>
                        <a href="mailto:<?php echo fm_admin_echapper($c['email']); ?>" style="font-size:12.5px;">
                            <?php echo fm_admin_echapper($c['email']); ?>
                        </a>
                    </td>
                    <td><?php echo fm_admin_echapper(mb_strimwidth(trim((string) $c['message']), 0, 100, '...', 'UTF-8')); ?></td>
                    <td><?php echo fm_admin_echapper(fm_admin_date_contact($c['date'])); ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <?php } ?>
</div>

<?php
admin_pied();