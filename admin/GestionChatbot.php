<?php
/**
 * Gestion des conversations Chatbot - Administration FoodMax
 * Liste, détails et export PDF des conversations
 */
require_once(__DIR__ . '/../includes/admin-auth.php');
require_once(__DIR__ . '/../includes/admin-layout.php');

fm_admin_exiger_login();
fm_admin_exiger_droit('contacts');

$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

$statusFilter = isset($_GET['status']) ? (string) $_GET['status'] : '';
$search = isset($_GET['q']) ? trim((string) $_GET['q']) : '';

$where = [];
$params = [];
$types = '';

if ($statusFilter !== '') {
    $where[] = 'status = ?';
    $params[] = $statusFilter;
    $types .= 's';
}
if ($search !== '') {
    $where[] = '(session_id LIKE ? OR ip_address LIKE ? OR JSON_UNQUOTE(JSON_EXTRACT(lead_data, "$.contact")) LIKE ?)';
    $like = '%' . $search . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= 'sss';
}

$sqlWhere = $where ? ' WHERE ' . implode(' AND ', $where) : '';

$stmt = mysqli_prepare($conn, 'SELECT COUNT(*) FROM chat_conversations' . $sqlWhere);
if ($stmt) {
    if ($types !== '') { mysqli_stmt_bind_param($stmt, $types, ...$params); }
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $total);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
} else {
    $total = 0;
}

$totalPages = max(1, (int) ceil($total / $perPage));
if ($page > $totalPages) $page = $totalPages;
$offset = ($page - 1) * $perPage;

$conversations = [];
$sql = 'SELECT id, session_id, ip_address, city, country, status, lead_data, message_count, started_at, updated_at, qualified_at
        FROM chat_conversations' . $sqlWhere . ' ORDER BY started_at DESC LIMIT ? OFFSET ?';
$stmt = mysqli_prepare($conn, $sql);
if ($stmt) {
    $bindParams = $params;
    $bindParams[] = $perPage;
    $bindParams[] = $offset;
    $bindTypes = $types . 'ii';
    mysqli_stmt_bind_param($stmt, $bindTypes, ...$bindParams);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $row['lead_data'] = json_decode($row['lead_data'], true) ?? [];
            $conversations[] = $row;
        }
    }
    mysqli_stmt_close($stmt);
}

$action = isset($_GET['action']) ? (string) $_GET['action'] : '';
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($action === 'delete' && $id > 0) {
    $stmt = mysqli_prepare($conn, 'DELETE FROM chat_conversations WHERE id = ?');
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        fm_admin_flash('ok', 'Conversation supprimée.');
    }
    header('Location: GestionChatbot.php');
    exit;
}

if ($action === 'export_pdf' && $id > 0) {
    require_once(__DIR__ . '/../includes/chatbot_pdf.php');
    fm_chatbot_export_pdf($conn, $id);
    exit;
}

if ($action === 'export_all_pdf') {
    require_once(__DIR__ . '/../includes/chatbot_pdf.php');
    fm_chatbot_export_all_pdf($conn);
    exit;
}

admin_entete('Chatbot Conversations', 'contacts', array(
    '<a class="fm-adm-btn is-light is-sm" href="GestionChatbot.php?action=export_all_pdf"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Exporter tout (PDF)</a>'
));
?>

<div class="fm-adm-card">
    <header>
        <h2>Conversations Chatbot</h2>
    </header>
    <div class="fm-adm-pad">
        <form method="get" class="fm-adm-filters">
            <input type="hidden" name="page" value="1">
            <div class="fm-adm-filter-row">
                <div class="fm-adm-field">
                    <label for="q">Rechercher</label>
                    <input type="text" id="q" name="q" class="fm-adm-input" placeholder="Session ID, email, IP..."
                           value="<?php echo fm_admin_echapper($search); ?>">
                </div>
                <div class="fm-adm-field">
                    <label for="status">Statut</label>
                    <select id="status" name="status" class="fm-adm-input">
                        <option value="">Tous</option>
                        <option value="active"<?php echo $statusFilter === 'active' ? ' selected' : ''; ?>>Active</option>
                        <option value="qualified"<?php echo $statusFilter === 'qualified' ? ' selected' : ''; ?>>Qualifiée</option>
                        <option value="abandoned"<?php echo $statusFilter === 'abandoned' ? ' selected' : ''; ?>>Abandonnée</option>
                    </select>
                </div>
                <div class="fm-adm-field" style="align-self: flex-end;">
                    <button type="submit" class="fm-adm-btn"><i class="fa fa-search" aria-hidden="true"></i> Filtrer</button>
                    <?php if ($search !== '' || $statusFilter !== '') { ?>
                    <a href="GestionChatbot.php" class="fm-adm-btn is-light">Réinitialiser</a>
                    <?php } ?>
                </div>
            </div>
        </form>

        <?php if ($conversations) { ?>
        <div class="fm-adm-table-wrap">
            <table class="fm-adm-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Session</th>
                        <th>IP</th>
                        <th>Ville</th>
                        <th>Pays</th>
                        <th>Client</th>
                        <th>Produits</th>
                        <th>Messages</th>
                        <th>Statut</th>
                        <th>Début</th>
                        <th>Dernière MAJ</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($conversations as $c) {
                        $lead = $c['lead_data'];
                        $client = '';
                        if (!empty($lead['contact'])) {
                            $client = '<a href="mailto:' . fm_admin_echapper($lead['contact']) . '">' . fm_admin_echapper($lead['contact']) . '</a>';
                        } elseif (!empty($lead['origin'])) {
                            $client = fm_admin_echapper($lead['origin']);
                        } else {
                            $client = '—';
                        }
                        $products = !empty($lead['products']) ? implode(', ', $lead['products']) : '—';
                        $statusClass = $c['status'] === 'qualified' ? 'is-ok' : ($c['status'] === 'active' ? 'is-warn' : 'is-err');
                    ?>
                    <tr>
                        <td><?php echo (int) $c['id']; ?></td>
                        <td><code><?php echo fm_admin_echapper($c['session_id']); ?></code></td>
                        <td><?php echo fm_admin_echapper($c['ip_address']); ?></td>
                        <td><?php echo fm_admin_echapper($c['city'] ?? '—'); ?></td>
                        <td><?php echo fm_admin_echapper($c['country'] ?? '—'); ?></td>
                        <td><?php echo $client; ?></td>
                        <td><?php echo fm_admin_echapper($products); ?></td>
                        <td><?php echo (int) $c['message_count']; ?></td>
                        <td><span class="fm-adm-badge <?php echo $statusClass; ?>"><?php echo ucfirst($c['status']); ?></span></td>
                        <td><?php echo fm_admin_echapper(date('d/m/Y H:i', strtotime($c['started_at']))); ?></td>
                        <td><?php echo fm_admin_echapper(date('d/m/Y H:i', strtotime($c['updated_at']))); ?></td>
                        <td>
                            <a class="fm-adm-btn is-light is-sm" href="GestionChatbot.php?action=view&id=<?php echo (int) $c['id']; ?>">
                                <i class="fa fa-eye" aria-hidden="true"></i> Voir
                            </a>
                            <a class="fm-adm-btn is-light is-sm" href="GestionChatbot.php?action=export_pdf&id=<?php echo (int) $c['id']; ?>">
                                <i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF
                            </a>
                            <a class="fm-adm-btn is-light is-sm is-danger" href="GestionChatbot.php?action=delete&id=<?php echo (int) $c['id']; ?>" onclick="return confirm('Supprimer cette conversation ?');">
                                <i class="fa fa-trash" aria-hidden="true"></i>
                            </a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1) { ?>
        <nav class="fm-adm-pager" aria-label="Pagination">
            <ul class="fm-adm-pager-list">
                <?php if ($page > 1) { ?>
                <li><a class="fm-adm-pager-item" href="GestionChatbot.php?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" rel="prev"><i class="fa fa-angle-left" aria-hidden="true"></i> Précédent</a></li>
                <?php } ?>
                <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++) { ?>
                <li><?php if ($i === $page) { ?><span class="fm-adm-pager-item is-active" aria-current="page"><?php echo $i; ?></span><?php } else { ?><a class="fm-adm-pager-item" href="GestionChatbot.php?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a><?php } ?></li>
                <?php } ?>
                <?php if ($page < $totalPages) { ?>
                <li><a class="fm-adm-pager-item" href="GestionChatbot.php?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" rel="next">Suivant <i class="fa fa-angle-right" aria-hidden="true"></i></a></li>
                <?php } ?>
            </ul>
        </nav>
        <?php } ?>

        <?php } else { ?>
        <p style="text-align:center;color:#888;padding:40px;">Aucune conversation trouvée.</p>
        <?php } ?>
    </div>
</div>

<?php if ($action === 'view' && $id > 0) {
    $stmt = mysqli_prepare($conn, 'SELECT * FROM chat_conversations WHERE id = ? LIMIT 1');
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if ($conv = mysqli_fetch_assoc($res)) {
            $conv['lead_data'] = json_decode($conv['lead_data'], true) ?? [];
            $conv['messages'] = json_decode($conv['messages'], true) ?? [];
        }
        mysqli_stmt_close($stmt);
    }
    if (isset($conv)) { ?>
<div class="fm-adm-card" style="margin-top:24px;">
    <header>
        <h2>Conversation #<?php echo (int) $conv['id']; ?> — <?php echo ucfirst($conv['status']); ?></h2>
    </header>
    <div class="fm-adm-pad">
        <div class="fm-adm-grid" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:24px;">
            <div><strong>Session ID</strong><br><code><?php echo fm_admin_echapper($conv['session_id']); ?></code></div>
            <div><strong>IP</strong><br><?php echo fm_admin_echapper($conv['ip_address']); ?></div>
            <div><strong>Ville</strong><br><?php echo fm_admin_echapper($conv['city'] ?? '—'); ?></div>
            <div><strong>Pays</strong><br><?php echo fm_admin_echapper($conv['country'] ?? '—'); ?></div>
            <div><strong>Statut</strong><br><span class="fm-adm-badge <?php echo $conv['status'] === 'qualified' ? 'is-ok' : ($conv['status'] === 'active' ? 'is-warn' : 'is-err'); ?>"><?php echo ucfirst($conv['status']); ?></span></div>
            <div><strong>Messages</strong><br><?php echo (int) $conv['message_count']; ?></div>
            <div><strong>Début</strong><br><?php echo fm_admin_echapper(date('d/m/Y H:i', strtotime($conv['started_at']))); ?></div>
            <div><strong>MAJ</strong><br><?php echo fm_admin_echapper(date('d/m/Y H:i', strtotime($conv['updated_at']))); ?></div>
        </div>

        <?php if (!empty($conv['lead_data'])) { ?>
        <h3>Données du lead</h3>
        <div class="fm-adm-grid" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:24px;">
            <?php foreach ($conv['lead_data'] as $k => $v) {
                if (is_array($v)) $v = implode(', ', $v);
            ?>
            <div><strong><?php echo ucfirst(str_replace('_', ' ', $k)); ?></strong><br><?php echo fm_admin_echapper($v); ?></div>
            <?php } ?>
        </div>
        <?php } ?>

        <h3>Échange complet</h3>
        <div style="max-height:500px;overflow-y:auto;border:1px solid #e3e8da;border-radius:4px;padding:16px;background:#fafbf9;">
            <?php foreach ($conv['messages'] as $msg) {
                $roleClass = $msg['role'] === 'user' ? 'style="text-align:right;margin:16px 0;"' : 'style="margin:16px 0;"';
                $bubbleStyle = $msg['role'] === 'user' ? 'background:#4a7a18;color:#fff;border-radius:18px 18px 4px 18px;display:inline-block;padding:10px 14px;max-width:80%;' : 'background:#fff;color:#333;border:1px solid #e3e8da;border-radius:18px 18px 18px 4px;display:inline-block;padding:10px 14px;max-width:80%;';
                $time = isset($msg['timestamp']) ? date('H:i', $msg['timestamp']) : '';
            ?>
            <div <?php echo $roleClass; ?>>
                <div style="<?php echo $bubbleStyle; ?>">
                    <?php echo nl2br(fm_admin_echapper($msg['message'])); ?>
                </div>
                <div style="font-size:11px;color:#888;margin-top:4px;"><?php echo ucfirst($msg['role']); ?> • <?php echo $time; ?></div>
            </div>
            <?php } ?>
        </div>

        <div style="margin-top:16px;display:flex;gap:8px;">
            <a class="fm-adm-btn" href="GestionChatbot.php?action=export_pdf&id=<?php echo (int) $conv['id']; ?>"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Exporter en PDF</a>
            <a class="fm-adm-btn is-light" href="GestionChatbot.php">Retour à la liste</a>
        </div>
    </div>
</div>
<?php } } ?>

<?php require_once(__DIR__ . '/../includes/admin-pied.php'); ?>