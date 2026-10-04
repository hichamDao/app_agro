<?php
/**
 * Vue d'impression des messages recus.
 *
 * Mise en page epuree, sans barre laterale ni boutons, avec un en-tete qui
 * n'apparait qu'a l'impression (voir .fm-adm-print-head dans admin-style.css).
 *
 * « Imprimer en PDF » ouvre la boite d'impression du navigateur : on y
 * choisit « Enregistrer au format PDF » comme destination. Aucune librairie
 * PDF n'est installee dans le projet, d'ou ce passage par le navigateur.
 */

require_once(__DIR__ . '/../includes/admin-auth.php');
require_once(__DIR__ . '/../includes/paths.php');

fm_admin_exiger_login();

$formatDate = function ($brut) {
    return fm_admin_date_contact($brut);
};

/* Filtre eventuel, repris depuis GestionContacts.php. */
$motCle = isset($_GET['q']) ? trim((string) $_GET['q']) : '';

/* Un message precis, ou la selection filtree, ou tout. */
$ids = isset($_GET['ids']) ? $_GET['ids'] : '';
$listeIds = array();
if (is_string($ids) && $ids !== '') {
    foreach (explode(',', $ids) as $brut) {
        $n = (int) trim($brut);
        if ($n > 0) { $listeIds[] = $n; }
    }
}
$unSeul = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($unSeul > 0) {
    $listeIds = array($unSeul);
}

$where  = array();
$params = array();
$types  = '';

if ($listeIds) {
    $where[] = 'contact_id IN (' . implode(',', array_fill(0, count($listeIds), '?')) . ')';
    foreach ($listeIds as $n) { $params[] = $n; }
    $types .= str_repeat('i', count($listeIds));
} elseif ($motCle !== '') {
    $where[]  = '(name LIKE ? OR email LIKE ? OR message LIKE ?)';
    $params[] = '%' . $motCle . '%';
    $params[] = '%' . $motCle . '%';
    $params[] = '%' . $motCle . '%';
    $types   .= 'sss';
}
$sqlWhere = $where ? ' WHERE ' . implode(' AND ', $where) : '';

$contacts = array();
$stmt = mysqli_prepare(
    $conn,
    'SELECT contact_id, name, email, phone, message, date FROM contact'
        . $sqlWhere
        . ' ORDER BY contact_id DESC'
);
if ($stmt) {
    if ($types !== '') { mysqli_stmt_bind_param($stmt, $types, ...$params); }
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res) {
        while ($c = mysqli_fetch_assoc($res)) { $contacts[] = $c; }
    }
    mysqli_stmt_close($stmt);
}

/* Extrait d'un message precis, ou export d'une selection. */
$estUnSeul = ($unSeul > 0);

if ($estUnSeul) {
    $premier = $contacts ? $contacts[0] : null;
    $titre   = $premier ? 'Message de ' . $premier['name'] : 'Message introuvable';
    $filtre  = $premier
        ? 'Message n° ' . (int) $premier['contact_id'] . ' — recu le ' . $formatDate($premier['date'])
        : 'Aucun message ne correspond a cet identifiant';
} else {
    $titre  = $motCle !== '' ? 'Messages — filtre « ' . $motCle . ' »' : 'Messages recus';
    $filtre = $motCle !== '' ? 'Filtre : ' . $motCle : 'Tous les messages';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?php echo fm_admin_echapper($titre); ?> - <?php echo fm_admin_echapper(FM_ADMIN_TITRE); ?></title>
<link rel="stylesheet" href="<?php echo $fm_app; ?>css/font-awesome.min.css">
<link rel="stylesheet" href="admin-style.css">
</head>
<body class="fm-admin">
<div class="fm-adm-body">

    <div class="fm-no-print" style="margin-bottom:18px;display:flex;gap:9px;flex-wrap:wrap;">
        <button class="fm-adm-btn" type="button" onclick="window.print();">
            <i class="fa fa-print" aria-hidden="true"></i> Imprimer / Enregistrer en PDF
        </button>
        <a class="fm-adm-btn is-light" href="GestionContacts.php<?php echo ($motCle !== '') ? '?q=' . rawurlencode($motCle) : ''; ?>">
            <i class="fa fa-arrow-left" aria-hidden="true"></i> Retour
        </a>
        <span style="margin-left:auto;font-size:13px;color:var(--fm-muted);align-self:center;">
            Dans la boite d'impression, choisir la destination
            <b>&laquo;&nbsp;Enregistrer au format PDF&nbsp;&raquo;</b>.
        </span>
    </div>

    <div class="fm-adm-print-head">
        <h2>Foodmax Group &mdash; <?php echo fm_admin_echapper($titre); ?></h2>
        <p>
            <?php if (!$estUnSeul) { ?>
            <?php echo count($contacts); ?> message(s) &mdash; <?php echo fm_admin_echapper($filtre); ?>
            &mdash; edite le <?php echo fm_admin_echapper(date('d/m/Y a H:i')); ?>
            par <?php echo fm_admin_echapper(fm_admin_login()); ?>
            <?php } else { ?>
            Extrait edite le <?php echo fm_admin_echapper(date('d/m/Y a H:i')); ?>
            par <?php echo fm_admin_echapper(fm_admin_login()); ?>
            <?php } ?>
        </p>
    </div>

    <?php if (!$contacts) { ?>
    <div class="fm-adm-empty">
        <i class="fa fa-envelope-o" aria-hidden="true"></i>
        Aucun message a imprimer.
    </div>
    <?php } elseif ($estUnSeul) { ?>
    <?php $c = $contacts[0]; ?>
    <div class="fm-adm-card">
        <div class="fm-adm-pad">
            <dl class="fm-adm-detail" style="grid-template-columns:120px 1fr;margin-bottom:16px;">
                <dt>Expediteur</dt><dd><?php echo fm_admin_echapper($c['name']); ?></dd>
                <dt>E-mail</dt><dd><?php echo fm_admin_echapper($c['email']); ?></dd>
                <dt>Telephone</dt><dd><?php echo fm_admin_echapper(trim((string) $c['phone']) !== '' ? $c['phone'] : '—'); ?></dd>
                <dt>Recu le</dt><dd><?php echo fm_admin_echapper($formatDate($c['date'])); ?></dd>
                <dt>Reference</dt><dd>Message n° <?php echo (int) $c['contact_id']; ?></dd>
            </dl>
            <h3 style="margin:0 0 8px;font-size:12pt;">Message</h3>
            <div class="fm-adm-message"><?php echo nl2br(fm_admin_echapper(trim((string) $c['message']))); ?></div>
        </div>
    </div>
    <?php } else { ?>
    <div class="fm-adm-card">
        <div class="fm-adm-table-wrap">
            <table class="fm-adm-table">
                <thead>
                    <tr>
                        <th class="fm-adm-num" style="width:48px;">N&deg;</th>
                        <th style="width:150px;">Date</th>
                        <th style="width:170px;">Expediteur</th>
                        <th style="width:150px;">E-mail</th>
                        <th style="width:100px;">Tel.</th>
                        <th>Message</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($contacts as $c) { ?>
                    <tr>
                        <td class="fm-adm-num"><?php echo (int) $c['contact_id']; ?></td>
                        <td><?php echo fm_admin_echapper($formatDate($c['date'])); ?></td>
                        <td><strong><?php echo fm_admin_echapper($c['name']); ?></strong></td>
                        <td><?php echo fm_admin_echapper($c['email']); ?></td>
                        <td><?php echo fm_admin_echapper($c['phone']); ?></td>
                        <td><?php echo fm_admin_echapper(trim((string) $c['message'])); ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php } ?>

</div>
</body>
</html>