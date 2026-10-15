<?php
/**
 * Export PDF des conversations chatbot
 * Utilise FPDF (inclus via require si dispo, sinon fallback HTML->PDF simple)
 */

function fm_chatbot_export_pdf($conn, $id) {
    $stmt = mysqli_prepare($conn, 'SELECT * FROM chat_conversations WHERE id = ? LIMIT 1');
    if (!$stmt) { die('Erreur DB'); }
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $conv = $res ? mysqli_fetch_assoc($res) : null;
    mysqli_stmt_close($stmt);

    if (!$conv) { die('Conversation introuvable'); }

    $lead = json_decode($conv['lead_data'], true) ?? [];
    $messages = json_decode($conv['messages'], true) ?? [];

    // Try to use FPDF if available
    $fpdfPath = __DIR__ . '/../fpdf/fpdf.php';
    if (is_file($fpdfPath)) {
        require_once($fpdfPath);
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'FoodMax Group - Conversation Chatbot', 0, 1, 'C');
        $pdf->SetFont('Helvetica', '', 10);
        $pdf->Cell(0, 6, 'Export le ' . date('d/m/Y à H:i'), 0, 1, 'C');
        $pdf->Ln(5);

        // Info générale
        $pdf->SetFont('Helvetica', 'B', 12);
        $pdf->Cell(0, 8, 'Informations de la conversation', 0, 1);
        $pdf->SetFont('Helvetica', '', 10);
        $pdf->Cell(40, 6, 'ID:', 0, 0);
        $pdf->Cell(0, 6, (string) $conv['id'], 0, 1);
        $pdf->Cell(40, 6, 'Session ID:', 0, 0);
        $pdf->Cell(0, 6, $conv['session_id'], 0, 1);
        $pdf->Cell(40, 6, 'IP:', 0, 0);
        $pdf->Cell(0, 6, $conv['ip_address'] ?? 'N/A', 0, 1);
        $pdf->Cell(40, 6, 'Ville:', 0, 0);
        $pdf->Cell(0, 6, $conv['city'] ?? 'N/A', 0, 1);
        $pdf->Cell(40, 6, 'Pays:', 0, 0);
        $pdf->Cell(0, 6, $conv['country'] ?? 'N/A', 0, 1);
        $pdf->Cell(40, 6, 'Statut:', 0, 0);
        $pdf->Cell(0, 6, ucfirst($conv['status']), 0, 1);
        $pdf->Cell(40, 6, 'Messages:', 0, 0);
        $pdf->Cell(0, 6, (string) $conv['message_count'], 0, 1);
        $pdf->Cell(40, 6, 'Debut:', 0, 0);
        $pdf->Cell(0, 6, date('d/m/Y H:i', strtotime($conv['started_at'])), 0, 1);
        $pdf->Cell(40, 6, 'MAJ:', 0, 0);
        $pdf->Cell(0, 6, date('d/m/Y H:i', strtotime($conv['updated_at'])), 0, 1);
        if ($conv['qualified_at']) {
            $pdf->Cell(40, 6, 'Qualifiee le:', 0, 0);
            $pdf->Cell(0, 6, date('d/m/Y H:i', strtotime($conv['qualified_at'])), 0, 1);
        }
        $pdf->Ln(5);

        // Lead data
        if (!empty($lead)) {
            $pdf->SetFont('Helvetica', 'B', 12);
            $pdf->Cell(0, 8, 'Donnees du lead', 0, 1);
            $pdf->SetFont('Helvetica', '', 10);
            foreach ($lead as $k => $v) {
                if (is_array($v)) $v = implode(', ', $v);
                $pdf->Cell(40, 6, ucfirst(str_replace('_', ' ', $k)) . ':', 0, 0);
                $pdf->MultiCell(0, 6, (string) $v, 0, 'L');
            }
            $pdf->Ln(5);
        }

        // Messages
        $pdf->SetFont('Helvetica', 'B', 12);
        $pdf->Cell(0, 8, 'Echange complet', 0, 1);
        foreach ($messages as $msg) {
            $role = ucfirst($msg['role']);
            $time = isset($msg['timestamp']) ? date('H:i', $msg['timestamp']) : '';
            $pdf->SetFont('Helvetica', 'B', 10);
            $pdf->Cell(0, 6, "$role ($time)", 0, 1);
            $pdf->SetFont('Helvetica', '', 10);
            $pdf->MultiCell(0, 5, $msg['message'], 0, 'L');
            $pdf->Ln(3);
        }

        $pdf->Output('D', 'chatbot_conversation_' . $conv['id'] . '_' . date('Ymd_His') . '.pdf');
    } else {
        // Fallback: HTML download (browser will print to PDF)
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: attachment; filename="chatbot_conversation_' . $conv['id'] . '_' . date('Ymd_His') . '.html"');
        ?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Conversation Chatbot #<?php echo (int) $conv['id']; ?></title>
<style>
body { font-family: Arial, sans-serif; margin: 20px; color: #333; }
h1 { color: #4a7a18; border-bottom: 2px solid #4a7a18; padding-bottom: 10px; }
h2 { color: #2f5010; margin-top: 30px; }
.info-grid { display: grid; grid-template-columns: 150px 1fr; gap: 8px; margin-bottom: 20px; }
.info-grid div:nth-child(odd) { font-weight: bold; }
.message { margin: 20px 0; padding: 15px; border-radius: 8px; }
.message.user { background: #e8f5e9; text-align: right; }
.message.bot { background: #f5f5f5; }
.message-header { font-weight: bold; margin-bottom: 8px; font-size: 14px; }
.message-body { white-space: pre-wrap; line-height: 1.6; }
.lead-data { background: #fafbf9; padding: 15px; border-radius: 8px; margin: 20px 0; }
@media print { .no-print { display: none; } }
</style>
</head>
<body>
<div class="no-print" style="margin-bottom:20px;padding:10px;background:#e8f5e9;border-radius:4px;text-align:center;">
<b>Imprimez cette page (Ctrl+P) et choisissez "Enregistrer en PDF" pour obtenir le PDF.</b>
</div>

<h1>FoodMax Group - Conversation Chatbot</h1>
<p>Export le <?php echo date('d/m/Y à H:i'); ?></p>

<h2>Informations de la conversation</h2>
<div class="info-grid">
<div>ID:</div><div><?php echo (int) $conv['id']; ?></div>
<div>Session ID:</div><div><?php echo htmlspecialchars($conv['session_id']); ?></div>
<div>IP:</div><div><?php echo htmlspecialchars($conv['ip_address'] ?? 'N/A'); ?></div>
<div>Ville:</div><div><?php echo htmlspecialchars($conv['city'] ?? 'N/A'); ?></div>
<div>Pays:</div><div><?php echo htmlspecialchars($conv['country'] ?? 'N/A'); ?></div>
<div>Statut:</div><div><?php echo ucfirst($conv['status']); ?></div>
<div>Messages:</div><div><?php echo (int) $conv['message_count']; ?></div>
<div>Début:</div><div><?php echo date('d/m/Y H:i', strtotime($conv['started_at'])); ?></div>
<div>MAJ:</div><div><?php echo date('d/m/Y H:i', strtotime($conv['updated_at'])); ?></div>
<?php if ($conv['qualified_at']) { ?>
<div>Qualifiée le:</div><div><?php echo date('d/m/Y H:i', strtotime($conv['qualified_at'])); ?></div>
<?php } ?>
</div>

<?php if (!empty($lead)) { ?>
<h2>Données du lead</h2>
<div class="lead-data info-grid">
<?php foreach ($lead as $k => $v) {
    if (is_array($v)) $v = implode(', ', $v);
?>
<div><?php echo ucfirst(str_replace('_', ' ', $k)); ?>:</div><div><?php echo htmlspecialchars((string) $v); ?></div>
<?php } ?>
</div>
<?php } ?>

<h2>Échange complet</h2>
<?php foreach ($messages as $msg) {
    $role = ucfirst($msg['role']);
    $time = isset($msg['timestamp']) ? date('H:i', $msg['timestamp']) : '';
    $cls = $msg['role'] === 'user' ? 'user' : 'bot';
?>
<div class="message <?php echo $cls; ?>">
<div class="message-header"><?php echo $role; ?> • <?php echo $time; ?></div>
<div class="message-body"><?php echo htmlspecialchars($msg['message']); ?></div>
</div>
<?php } ?>

</body>
</html>
        <?php
    }
}

// Export all conversations as PDF (HTML fallback)
function fm_chatbot_export_all_pdf($conn) {
    $stmt = mysqli_prepare($conn, 'SELECT * FROM chat_conversations ORDER BY started_at DESC LIMIT 100');
    if (!$stmt) { die('Erreur DB'); }
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $convs = $res ? mysqli_fetch_all($res, MYSQLI_ASSOC) : [];
    mysqli_stmt_close($stmt);

    header('Content-Type: text/html; charset=utf-8');
    header('Content-Disposition: attachment; filename="chatbot_all_conversations_' . date('Ymd_His') . '.html"');
    ?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Toutes les conversations Chatbot</title>
<style>
body { font-family: Arial, sans-serif; margin: 20px; color: #333; }
h1 { color: #4a7a18; }
table { width: 100%; border-collapse: collapse; margin: 20px 0; }
th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; }
th { background: #4a7a18; color: white; }
tr:nth-child(even) { background: #fafbf9; }
.status-qualified { background: #d4edda; color: #155724; }
.status-active { background: #fff3cd; color: #856404; }
.status-abandoned { background: #f8d7da; color: #721c24; }
@media print { .no-print { display: none; } }
</style>
</head>
<body>
<div class="no-print" style="margin-bottom:20px;padding:10px;background:#e8f5e9;border-radius:4px;text-align:center;">
<b>Imprimez cette page (Ctrl+P) et choisissez "Enregistrer en PDF" pour obtenir le PDF.</b>
</div>

<h1>FoodMax Group - Toutes les conversations Chatbot</h1>
<p>Export le <?php echo date('d/m/Y à H:i'); ?> - <?php echo count($convs); ?> conversations</p>

<table>
<thead>
<tr>
<th>ID</th><th>Session</th><th>IP</th><th>Ville</th><th>Pays</th><th>Client</th><th>Produits</th><th>Messages</th><th>Statut</th><th>Début</th>
</tr>
</thead>
<tbody>
<?php foreach ($convs as $c) {
    $lead = json_decode($c['lead_data'], true) ?? [];
    $client = !empty($lead['contact']) ? $lead['contact'] : ($lead['origin'] ?? '—');
    $products = !empty($lead['products']) ? implode(', ', $lead['products']) : '—';
    $statusClass = 'status-' . $c['status'];
?>
<tr>
<td><?php echo (int) $c['id']; ?></td>
<td><?php echo htmlspecialchars($c['session_id']); ?></td>
<td><?php echo htmlspecialchars($c['ip_address'] ?? 'N/A'); ?></td>
<td><?php echo htmlspecialchars($c['city'] ?? 'N/A'); ?></td>
<td><?php echo htmlspecialchars($c['country'] ?? 'N/A'); ?></td>
<td><?php echo htmlspecialchars($client); ?></td>
<td><?php echo htmlspecialchars($products); ?></td>
<td><?php echo (int) $c['message_count']; ?></td>
<td class="<?php echo $statusClass; ?>"><?php echo ucfirst($c['status']); ?></td>
<td><?php echo date('d/m/Y H:i', strtotime($c['started_at'])); ?></td>
</tr>
<?php } ?>
</tbody>
</table>
</body>
</html>
    <?php
}