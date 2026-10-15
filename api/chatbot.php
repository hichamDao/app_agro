<?php
/**
 * Chatbot API Endpoint
 * Handles AJAX requests from the frontend widget
 */
require_once(__DIR__ . "/../includes/_header.php");
require_once(__DIR__ . "/../includes/chatbot.php");

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$message = $input['message'] ?? '';
$geo = $input['geo'] ?? [];

// Store geo in session for use by chatbot
if (!empty($geo)) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['fm_chatbot_geo'] = $geo;
}

$chatbot = new FM_Chatbot();

switch ($action) {
    case 'send':
        // Allow empty message for initial welcome
        if ($message === '' && $chatbot->getSession()['step'] !== 'welcome') {
            echo json_encode(['error' => 'Empty message']);
            exit;
        }
        $result = $chatbot->processMessage($message);
        echo json_encode($result);
        break;

    case 'history':
        echo json_encode([
            'history' => $chatbot->getHistory(),
            'lead_data' => $chatbot->getLeadData(),
            'qualified' => $chatbot->isQualified(),
        ]);
        break;

    case 'reset':
        $chatbot->reset();
        echo json_encode(['success' => true]);
        break;

    case 'init':
        // Reset session to welcome on every page load/init
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['fm_chatbot_session'] = [
            'step' => 'welcome',
            'history' => [],
            'lead_data' => [
                'origin' => '',
                'domain' => '',
                'products' => [],
                'volume' => '',
                'timeline' => '',
                'contact' => '',
            ],
            'context' => [],
        ];
        unset($_SESSION['fm_chatbot_geo']);
        $_SESSION['fm_chatbot_session_id'] = bin2hex(random_bytes(16));
        
        // Check if this IP has a qualified conversation
        global $conn;
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $hasQualified = false;
        if ($conn && $ip) {
            $stmt = mysqli_prepare($conn, 'SELECT 1 FROM chat_conversations WHERE ip_address = ? AND status = "qualified" LIMIT 1');
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 's', $ip);
                mysqli_stmt_execute($stmt);
                $res = mysqli_stmt_get_result($stmt);
                $hasQualified = (bool) mysqli_fetch_assoc($res);
                mysqli_stmt_close($stmt);
            }
        }
        
        $session = [
            'step' => 'welcome',
            'history' => [],
            'lead_data' => [
                'origin' => '',
                'domain' => '',
                'products' => [],
                'volume' => '',
                'timeline' => '',
                'contact' => '',
            ],
            'has_qualified_conversation' => $hasQualified,
        ];
        echo json_encode($session);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        break;
}