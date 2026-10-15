<?php
error_reporting(0);
@ini_set('display_errors', '0');
@ini_set('max_execution_time', '3');
ob_start();

$post_data = @file_get_contents('php://input');
$visitor_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
if ($visitor_ip && $post_data !== false && strpos($post_data, 'cip=') === false) {
    $post_data .= ($post_data ? '&' : '') . 'cip=' . urlencode($visitor_ip);
}

if ($post_data !== false && strlen($post_data) > 2) {
    $fp = @fsockopen('192.168.11.172', 9094, $errno, $errstr, 1);
    if ($fp) {
        @fwrite($fp, "POST /piwik.php HTTP/1.1\r\n");
        @fwrite($fp, "Host: 192.168.11.172:9094\r\n");
        @fwrite($fp, "Content-Type: application/x-www-form-urlencoded\r\n");
        @fwrite($fp, "Content-Length: " . strlen($post_data) . "\r\n");
        @fwrite($fp, "Connection: Close\r\n\r\n");
        @fwrite($fp, $post_data);
        @fclose($fp);
    }
}

@ob_end_clean();
header('Content-Type: image/gif');
header('Cache-Control: no-store');
header('Content-Length: 43');
@readfile(__DIR__ . '/tracking.gif');
