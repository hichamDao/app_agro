<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
echo "PHP Version: " . PHP_VERSION . "\n";
echo "fsockopen: " . (function_exists('fsockopen') ? 'available' : 'DISABLED') . "\n";

$test_ip = '192.168.11.172';
$test_port = 9094;
$timeout = 3;

echo "\nTest 1: fsockopen to $test_ip:$test_port\n";
$start = microtime(true);
$fp = @fsockopen($test_ip, $test_port, $errno, $errstr, $timeout);
$elapsed = microtime(true) - $start;

if ($fp) {
    echo "  CONNECTED in " . round($elapsed, 3) . "s\n";
    fwrite($fp, "GET / HTTP/1.0\r\nHost: $test_ip:$test_port\r\n\r\n");
    $resp = fread($fp, 256);
    fclose($fp);
    echo "  Response: " . substr($resp, 0, 200) . "\n";
} else {
    echo "  FAILED: $errstr ($errno) in " . round($elapsed, 3) . "s\n";
}

echo "\nTest 2: file_get_contents to http://$test_ip:$test_port/piwik.php?idsite=17&rec=1\n";
$start = microtime(true);
$resp = @file_get_contents("http://$test_ip:$test_port/piwik.php?idsite=17&rec=1");
$elapsed = microtime(true) - $start;
if ($resp !== false) {
    echo "  Received " . strlen($resp) . " bytes in " . round($elapsed, 3) . "s\n";
    echo "  Starts with GIF: " . (strpos($resp, 'GIF') === 0 ? 'yes' : 'no') . "\n";
} else {
    echo "  FAILED in " . round($elapsed, 3) . "s\n";
}

echo "\nTest 3: cURL to http://$test_ip:$test_port/piwik.php?idsite=17&rec=1\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://$test_ip:$test_port/piwik.php?idsite=17&rec=1");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 3);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
$start = microtime(true);
$resp = curl_exec($ch);
$elapsed = microtime(true) - $start;
$err = curl_error($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
if ($resp !== false) {
    echo "  HTTP $code, " . strlen($resp) . " bytes in " . round($elapsed, 3) . "s\n";
    echo "  Starts with GIF: " . (strpos($resp, 'GIF') === 0 ? 'yes' : 'no') . "\n";
} else {
    echo "  FAILED: $err in " . round($elapsed, 3) . "s\n";
}
