<?php
/**
 * piwik-proxy.php — Production tracking proxy
 *
 * On first request, tries to forward to local Matomo 5.x (/matomo/matomo.php).
 * If Matomo is not installed or unreachable, logs the raw POST data for later
 * processing by forward-tracking.php (the log-based fallback).
 */

error_reporting(0);
@ini_set('display_errors', '0');
@ini_set('max_execution_time', '5');
ob_start();

$matomoPath = __DIR__ . '/matomo/matomo.php';
$matomoExists = file_exists($matomoPath) && is_readable($matomoPath);

if ($matomoExists && file_exists(__DIR__ . '/matomo/config/config.ini.php')) {
    $postData = @file_get_contents('php://input');
    $visitorIp = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';

    if ($postData !== false && strlen($postData) > 2) {
        if ($visitorIp && strpos($postData, 'cip=') === false) {
            $postData .= '&cip=' . urlencode($visitorIp);
        }

        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/matomo/matomo.php';

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n" .
                           "Content-Length: " . strlen($postData) . "\r\n" .
                           "Connection: Close\r\n",
                'content' => $postData,
                'timeout' => 3,
                'ignore_errors' => true,
            ]
        ]);

        @file_get_contents($url, false, $context);

        header('Content-Type: image/gif');
        header('Cache-Control: no-store');
        header('Content-Length: 43');
        readfile(__DIR__ . '/tracking.gif');
        exit();
    }

    header('Content-Type: image/gif');
    header('Cache-Control: no-store');
    header('Content-Length: 43');
    readfile(__DIR__ . '/tracking.gif');
    exit();
}

// --- Fallback: log-based approach ---
$logFile = __DIR__ . '/pikiwi-tracking.log';
$postData = @file_get_contents('php://input');
$visitorIp = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

if ($postData !== false && strlen($postData) > 2) {
    if ($visitorIp && strpos($postData, 'cip=') === false) {
        $postData .= '&cip=' . urlencode($visitorIp);
    }
    $logEntry = date('c') . '|' . $visitorIp . '|' . $referer . '|' . $postData . "\n";
    @file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

@ob_end_clean();
header('Content-Type: image/gif');
header('Cache-Control: no-store');
header('Content-Length: 43');
@readfile(__DIR__ . '/tracking.gif');
