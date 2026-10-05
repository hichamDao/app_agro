<?php
/**
 * Consultation des messages recus par le formulaire de contact.
 *
 * Liste paginee avec recherche, lecture d'un message, suppression, et export
 * vers une vue d'impression. Chaque ligne porte un bouton « Extrait PDF »
 * qui ouvre contact_print.php?id=N : la mise en page de ce message seul,
 * prete a etre enregistree au format PDF depuis la boite d'impression.
 *
 * L'export PDF passe par l'impression du navigateur (destination
 * « Enregistrer au format PDF ») : aucune librairie PDF — TCPDF, FPDF ou
 * equivalente — n'est presente dans le projet, et en ajouter une aurait
 * affiche une dependance de plusieurs Mo pour une liste de contacts. La
 * feuille admin-style.css contient une tranche @media print qui retire la
 * navigation et garde les colonnes utiles.
 */

require_once(__DIR__ . '/../includes/admin-auth.php');
require_once(__DIR__ . '/../includes/admin-layout.php');

fm_admin_exiger_login();

define('FM_CONTACTS_PAR_PAGE', 20);

$formatDate = function ($brut) {
    /* Le formulaire public stocke « d-m-Y H:i:s » (cf. contact.php). */
    return fm_admin_date_contact($brut);
};

/* --------------------------------------------------------- traitements POST */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    fm_admin_verifier_csrf();
    $operation = isset($_POST['operation']) ? (string) $_POST['operation'] : '';

    if ($operation === 'supprimer') {
        $id = isset($_POST['contact_id']) ? (int) $_POST['contact_id'] : 0;
        if ($id > 0) {
            $stmt = mysqli_prepare($conn, 'DELETE FROM contact WHERE contact_id = ?');
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'i', $id);
                mysqli_stmt_execute($stmt);
                $ok = mysqli_stmt_affected_rows($stmt) > 0;
                mysqli_stmt_close($stmt);
                fm_admin_flash($ok ? 'ok' : 'err', $ok ? 'Message supprime.' : 'Message introuvable.');
            }
        }
        header('Location: GestionContacts.php');
        exit;
    }
}

/* ------------------------------------------------------------------ lecture */

$motCle   = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
$page     = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$voirId   = isset($_GET['voir']) ? (int) $_GET['voir'] : 0;

$where  = array();
$params = array();
$types  = '';

if ($motCle !== '') {
    /* Recherche sur l'expediteur et le corps du message : ici il s'agit d'une
       archive de correspondance, pas du catalogue. Le titre d'un produit
       reste le seul champ recherche cote public ; ici c'est le contenu du
       message qu'on cherche. */
    $where[]  = '(name LIKE ? OR email LIKE ? OR message LIKE ?)';
    $params[] = '%' . $motCle . '%';
    $params[] = '%' . $motCle . '%';
    $params[] = '%' . $motCle . '%';
    $types   .= 'sss';
}
$sqlWhere = $where ? ' WHERE ' . implode(' AND ', $where) : '';

$nombreTotal = 0;
$stmt = mysqli_prepare($conn, 'SELECT COUNT(*) FROM contact' . $sqlWhere);
if ($stmt) {
    if ($types !== '') { mysqli_stmt_bind_param($stmt, $types, ...$params); }
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    mysqli_stmt_bind_result($stmt, $nombreTotal);
    mysqli_stmt_fetch($stmt);
    $nombreTotal = (int) $nombreTotal;
    mysqli_stmt_close($stmt);
}

$nombrePages = max(1, (int) ceil($nombreTotal / FM_CONTACTS_PAR_PAGE));
if ($page > $nombrePages) { $page = $nombrePages; }
$offset = ($page - 1) * FM_CONTACTS_PAR_PAGE;

$contacts = array();
$stmt = mysqli_prepare(
    $conn,
    'SELECT contact_id, name, email, phone, message, date, company, country, product
     FROM contact' . $sqlWhere
        . ' ORDER BY contact_id DESC LIMIT ? OFFSET ?'
);
if ($stmt) {
    $limit = FM_CONTACTS_PAR_PAGE;
    $off = $offset;
    $bindVals = $params;
    $bindVals[] = $limit;
    $bindVals[] = $off;
    mysqli_stmt_bind_param($stmt, $types . 'ii', ...$bindVals);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res) {
        while ($c = mysqli_fetch_assoc($res)) { $contacts[] = $c; }
    }
    mysqli_stmt_close($stmt);
}

admin_entete(
    'Messages recus',
    'contacts',
    array(
        '<a class="fm-adm-btn is-light is-sm" href="contact_print.php'
            . ($motCle !== '' ? '?q=' . rawurlencode($motCle) : '')
            . '"><i class="fa fa-print" aria-hidden="true"></i> Imprimer en PDF</a>',
    )
);
?>

<div class="fm-adm-card">
    <header>
        <h2><?php echo (int) $nombreTotal; ?> message<?php echo ((int) $nombreTotal > 1) ? 's' : ''; ?></h2>
        <span class="fm-adm-spacer"></span>
    </header>

    <div class="fm-adm-pad" style="border-bottom:1px solid var(--fm-border);">
        <form class="fm-adm-filter" method="get" action="GestionContacts.php">
            <div class="fm-adm-field fm-adm-grow">
                <label for="q">Rechercher</label>
                <input class="fm-adm-input" type="search" id="q" name="q"
                       value="<?php echo fm_admin_echapper($motCle); ?>"
                       placeholder="Nom, e-mail ou contenu du message">
            </div>
            <button class="fm-adm-btn" type="submit"><i class="fa fa-search" aria-hidden="true"></i> Filtrer</button>
            <?php if ($motCle !== '') { ?>
            <a class="fm-adm-btn is-light" href="GestionContacts.php">Reinitialiser</a>
            <?php } ?>
        </form>
    </div>

    <?php if (!$contacts) { ?>
    <div class="fm-adm-empty">
        <i class="fa fa-envelope-o" aria-hidden="true"></i>
        Aucun message<?php echo ($motCle !== '') ? ' ne correspond a «&nbsp;' . fm_admin_echapper($motCle) . '&nbsp;»' : ' pour le moment'; ?>.
    </div>
    <?php } else { ?>
    <div class="fm-adm-table-wrap">
        <table class="fm-adm-table">
            <thead>
                <tr>
                    <th class="fm-adm-num" style="width:70px;">N&deg;</th>
                    <th>Expediteur</th>
                    <th>Message</th>
                    <th style="width:150px;">Date</th>
                    <th class="fm-adm-actions" style="width:225px;">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($contacts as $c) { ?>
                <tr<?php echo ($voirId === (int) $c['contact_id']) ? ' style="background:#f4f9ee;"' : ''; ?>>
                    <td class="fm-adm-num"><?php echo (int) $c['contact_id']; ?></td>
                    <td>
                        <strong><?php echo fm_admin_echapper($c['name']); ?></strong><br>
                        <a href="mailto:<?php echo fm_admin_echapper($c['email']); ?>" style="font-size:12.5px;">
                            <?php echo fm_admin_echapper($c['email']); ?>
                        </a>
                        <?php if (trim((string) $c['phone']) !== '') { ?>
                        <br><span style="font-size:12.5px;color:var(--fm-muted);"><?php echo fm_admin_echapper($c['phone']); ?></span>
                        <?php } ?>
                    </td>
                    <td>
                        <?php echo fm_admin_echapper(mb_strimwidth(trim((string) $c['message']), 0, 110, '...', 'UTF-8')); ?>
                    </td>
                    <td><?php echo fm_admin_echapper($formatDate($c['date'])); ?></td>
                    <td class="fm-adm-actions">
                        <a class="fm-adm-btn is-light is-sm"
                           href="GestionContacts.php?<?php echo $motCle !== '' ? 'q=' . rawurlencode($motCle) . '&amp;' : ''; ?>voir=<?php echo (int) $c['contact_id']; ?>#msg<?php echo (int) $c['contact_id']; ?>"
                           title="Lire"><i class="fa fa-eye" aria-hidden="true"></i></a>
                        <a class="fm-adm-btn is-light is-sm"
                           href="mailto:<?php echo fm_admin_echapper($c['email']); ?>?subject=<?php echo rawurlencode('Re: votre demande - Foodmax'); ?>"
                           title="Repondre"><i class="fa fa-reply" aria-hidden="true"></i></a>
                        <a class="fm-adm-btn is-light is-sm"
                           href="contact_print.php?id=<?php echo (int) $c['contact_id']; ?>"
                           target="_blank" rel="noopener"
                           title="Extrait PDF de ce message"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>
                        <form method="post" action="GestionContacts.php" style="display:inline;"
                              onsubmit="return confirm('Supprimer ce message ?');">
                            <?php echo fm_admin_champ_csrf(); ?>
                            <input type="hidden" name="operation" value="supprimer">
                            <input type="hidden" name="contact_id" value="<?php echo (int) $c['contact_id']; ?>">
                            <button class="fm-adm-btn is-terra is-sm" type="submit" title="Supprimer">
                                <i class="fa fa-trash" aria-hidden="true"></i>
                            </button>
                        </form>
                    </td>
                </tr>

                <?php if ($voirId === (int) $c['contact_id']) { ?>
                <tr id="msg<?php echo (int) $c['contact_id']; ?>">
                    <td colspan="5" style="background:#fbfcfa;">
                        <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;margin-bottom:12px;">
                            <a class="fm-adm-btn is-light is-sm"
                               href="contact_print.php?id=<?php echo (int) $c['contact_id']; ?>"
                               target="_blank" rel="noopener">
                                <i class="fa fa-file-pdf-o" aria-hidden="true"></i> Extrait PDF de ce message
                            </a>
                            <a class="fm-adm-btn is-light is-sm"
                               href="mailto:<?php echo fm_admin_echapper($c['email']); ?>?subject=<?php echo rawurlencode('Re: votre demande - Foodmax'); ?>">
                                <i class="fa fa-reply" aria-hidden="true"></i> Repondre
                            </a>
                        </div>
                        <div class="fm-adm-detail" style="grid-template-columns:130px 1fr;margin-bottom:12px;">
<dt>Expediteur</dt><dd><?php echo fm_admin_echapper($c['name']); ?></dd>
                        <?php if (trim((string) $c['company']) !== '') { ?>
                        <dt>Societe</dt><dd><?php echo fm_admin_echapper($c['company']); ?></dd>
                        <?php } ?>
                        <dt>E-mail</dt><dd><a href="mailto:<?php echo fm_admin_echapper($c['email']); ?>"><?php echo fm_admin_echapper($c['email']); ?></a></dd>
                        <dt>Telephone</dt><dd><?php echo fm_admin_echapper($c['phone'] !== '' ? $c['phone'] : '&mdash;'); ?></dd>
                        <?php if (trim((string) $c['country']) !== '') { ?>
                        <dt>Pays</dt><dd><?php echo fm_admin_echapper($c['country']); ?></dd>
                        <?php } ?>
                        <?php if (trim((string) $c['product']) !== '') { ?>
                        <dt>Produit</dt><dd><?php echo fm_admin_echapper($c['product']); ?></dd>
                        <?php } ?>
                        <dt>Recu le</dt><dd><?php echo fm_admin_echapper($formatDate($c['date'])); ?></dd>
                        </div>
                        <div class="fm-adm-message"><?php echo fm_admin_echapper(trim((string) $c['message'])); ?></div>
                    </td>
                </tr>
                <?php } ?>
            <?php } ?>
            </tbody>
        </table>
    </div>

    <?php if ($nombrePages > 1) { ?>
    <footer>
        <div class="fm-adm-pager">
            <?php
            $lien = function ($p) use ($motCle) {
                $qs = array('page' => $p);
                if ($motCle !== '') { $qs['q'] = $motCle; }
                return 'GestionContacts.php?' . http_build_query($qs);
            };
            ?>
            <?php if ($page > 1) { ?>
            <a href="<?php echo $lien($page - 1); ?>"><i class="fa fa-chevron-left" aria-hidden="true"></i></a>
            <?php } else { ?>
            <span class="is-off"><i class="fa fa-chevron-left" aria-hidden="true"></i></span>
            <?php } ?>

            <?php for ($p = 1; $p <= $nombrePages; $p++) { ?>
                <?php if ($p === $page) { ?>
                <span class="is-on"><?php echo $p; ?></span>
                <?php } else { ?>
                <a href="<?php echo $lien($p); ?>"><?php echo $p; ?></a>
                <?php } ?>
            <?php } ?>

            <?php if ($page < $nombrePages) { ?>
            <a href="<?php echo $lien($page + 1); ?>"><i class="fa fa-chevron-right" aria-hidden="true"></i></a>
            <?php } else { ?>
            <span class="is-off"><i class="fa fa-chevron-right" aria-hidden="true"></i></span>
            <?php } ?>
        </div>
    </footer>
    <?php } ?>
    <?php } ?>
</div>

<?php
admin_pied();