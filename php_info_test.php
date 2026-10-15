<?php
echo "PHP " . PHP_VERSION . "\n";
echo "OS: " . PHP_OS . "\n";
echo "Ext zip: " . (extension_loaded('zip') ? 'YES' : 'NO') . "\n";
echo "Ext curl: " . (extension_loaded('curl') ? 'YES' : 'NO') . "\n";
echo "Ext openssl: " . (extension_loaded('openssl') ? 'YES' : 'NO') . "\n";
echo "Ext phar: " . (extension_loaded('phar') ? 'YES' : 'NO') . "\n";
echo "Exec: " . (function_exists('exec') ? 'YES' : 'NO') . "\n";
echo "Shell: " . (function_exists('shell_exec') ? 'YES' : 'NO') . "\n";
echo "Tmp writable: " . (is_writable('/tmp') ? 'YES' : 'NO') . "\n";
echo "CWD: " . getcwd() . "\n";
$max_upload = ini_get('upload_max_filesize');
$post_max = ini_get('post_max_size');
echo "Upload max: $max_upload, Post max: $post_max\n";
echo "Max exec time: " . ini_get('max_execution_time') . "\n";
echo "Memory limit: " . ini_get('memory_limit') . "\n";
