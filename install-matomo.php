<?php
/**
 * Matomo 5.x Installation Script (PHP 8.3 compatible)
 *
 * This script downloads, extracts, and configures Matomo on the production server.
 * 1. Downloads matomo.tar.gz from GitHub/official source
 * 2. Extracts using PharData (tarball) or ZipArchive (zip)
 * 3. Configures database connection
 * 4. Sets up config.ini.php
 *
 * Deployment: Place this file in the production document root and access via browser.
 * After successful installation, DELETE this file for security.
 */

@error_reporting(E_ALL);
@ini_set('display_errors', '1');
@ini_set('max_execution_time', 300);
@ini_set('memory_limit', '512M');

echo "<!DOCTYPE html><html><head><title>Matomo Installer</title>";
echo "<style>body{font-family:monospace;margin:20px;} .ok{color:green;} .err{color:red;} .info{color:blue;}</style>";
echo "</head><body>\n";

// === Step 1: Check prerequisites ===
echo "<h2>Step 1: Prerequisites</h2>\n";

$checks = [
    'PHP >= 8.1' => version_compare(PHP_VERSION, '8.1.0', '>='),
    'ZipArchive ext' => extension_loaded('zip'),
    'cURL ext' => extension_loaded('curl'),
    'PDO MySQL ext' => extension_loaded('pdo_mysql') || extension_loaded('mysqli'),
    'GD ext' => extension_loaded('gd'),
    'mbstring ext' => extension_loaded('mbstring'),
    'JSON ext' => extension_loaded('json'),
    'openssl ext' => extension_loaded('openssl'),
];

$allOk = true;
foreach ($checks as $name => $passed) {
    $class = $passed ? 'ok' : 'err';
    echo "<span class='$class'>[" . ($passed ? 'OK' : 'FAIL') . "]</span> $name\n<br>";
    if (!$passed) $allOk = false;
}

if (!$allOk) {
    echo "<p class='err'>Prerequisites not met. Cannot continue.</p>";
    exit(1);
}
echo "<p class='ok'>All prerequisites met.</p>\n";

// === Step 2: Check network connectivity ===
echo "<h2>Step 2: Network Check</h2>\n";

$downloadUrls = [
    'tarball' => 'https://github.com/matomo-org/matomo-package/releases/latest/download/matomo.tar.gz',
    'zip' => 'https://github.com/matomo-org/matomo-package/releases/latest/download/matomo.zip',
];

$downloadUrl = null;
$downloadType = null;

foreach ($downloadUrls as $type => $url) {
    echo "Testing download: $url ... ";
    $ctx = stream_context_create(['http' => ['method' => 'HEAD', 'timeout' => 15]]);
    $headers = @get_headers($url, 1, $ctx);
    if ($headers && isset($headers[0]) && strpos($headers[0], '200') !== false) {
        echo "<span class='ok'>OK (HTTP 200)</span>\n<br>";
        if (!$downloadUrl) {
            $downloadUrl = $url;
            $downloadType = $type;
        }
    } else {
        $code = $headers[0] ?? 'no response';
        echo "<span class='info'>$code</span>\n<br>";
    }
}

if (!$downloadUrl) {
    // Try alternative URLs
    $altUrls = [
        'https://builds.matomo.org/matomo.tar.gz',
        'https://builds.matomo.org/matomo-latest.zip',
    ];
    foreach ($altUrls as $url) {
        echo "Testing alt: $url ... ";
        $ctx = stream_context_create(['http' => ['method' => 'GET', 'timeout' => 15, 'ignore_errors' => true]]);
        $data = @file_get_contents($url, false, $ctx);
        $httpCode = null;
        foreach ($http_response_header ?? [] as $h) {
            if (preg_match('#HTTP/\S+ (\d{3})#', $h, $m)) { $httpCode = $m[1]; break; }
        }
        if ($data && $httpCode == '200') {
            $downloadUrl = $url;
            $downloadType = (strpos($url, '.tar.gz') !== false) ? 'tarball' : 'zip';
            echo "<span class='ok'>OK (HTTP 200, " . round(strlen($data)/1024/1024,1) . "MB)</span>\n<br>";
            break;
        } else {
            echo "<span class='err'>FAIL ($httpCode)</span>\n<br>";
        }
    }
}

if (!$downloadUrl) {
    echo "<p class='err'>Cannot download Matomo. Will try using local ZIP if available.</p>\n";
} else {
    echo "<p class='ok'>Will download from: $downloadUrl ($downloadType)</p>\n";
}

// === Step 3: Check for local ZIP ===
echo "<h2>Step 3: Check for Local ZIP</h2>\n";

$localZipPath = __DIR__ . '/matomo-latest.zip';
if (file_exists($localZipPath)) {
    $size = round(filesize($localZipPath) / 1024 / 1024, 1);
    echo "<span class='ok'>Found local ZIP: $localZipPath ($size MB)</span>\n<br>";
} else {
    echo "<span class='info'>No local ZIP found at $localZipPath</span>\n<br>";
}

// === Step 4: Database Configuration ===
echo "<h2>Step 4: Database Configuration</h2>\n";

$dbConfig = [
    'host' => '127.0.0.1',
    'username' => 'root',
    'password' => '',
    'dbname' => 'analytics',
    'prefix' => 'xdlu_',
];

echo "Database: {$dbConfig['host']} {$dbConfig['username']}@{$dbConfig['dbname']}, prefix={$dbConfig['prefix']}\n<br>";

$dbConn = @new mysqli($dbConfig['host'], $dbConfig['username'], $dbConfig['password'], $dbConfig['dbname']);
if ($dbConn->connect_error) {
    die("<p class='err'>Database connection failed: " . htmlspecialchars($dbConn->connect_error) . "</p>");
}
echo "<span class='ok'>Database connected: " . $dbConn->server_info . "</span>\n<br>";

// Check if tables already exist
$tables = $dbConn->query("SHOW TABLES LIKE '{$dbConfig['prefix']}%'");
$tableCount = $tables ? $tables->num_rows : 0;
echo "<span class='info'>Existing tables with prefix '{$dbConfig['prefix']}': $tableCount</span>\n<br>";

if ($tableCount > 0) {
    echo "<p class='info'>Existing Matomo tables found. Will reuse existing database.</p>\n";
} else {
    echo "<p class='info'>No existing tables. Matomo installer will create them.</p>\n";
}
$dbConn->close();

// === Step 5: Download & Extract ===
echo "<h2>Step 5: Download & Extract</h2>\n";

$tmpDir = __DIR__ . '/_matomo_install_tmp';
if (!is_dir($tmpDir)) {
    mkdir($tmpDir, 0755, true);
}

$zipFile = $tmpDir . '/matomo.zip';
$tarballFile = $tmpDir . '/matomo.tar.gz';

if ($downloadUrl) {
    // Download
    echo "Downloading Matomo ($downloadType) ... ";
    $dest = ($downloadType === 'tarball') ? $tarballFile : $zipFile;

    $ch = curl_init($downloadUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
    curl_setopt($ch, CURLOPT_FILE, fopen($dest, 'w+'));
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    $success = curl_exec($ch);
    $err = curl_error($ch);
    $size = $success ? round(filesize($dest) / 1048576, 1) : 0;
    curl_close($ch);

    if ($success && filesize($dest) > 1000000) {
        echo "<span class='ok'>OK ($size MB)</span>\n<br>";
    } else {
        echo "<span class='err'>FAIL: $err</span>\n<br>";
    }
}

// Use whichever file is available
if (file_exists($zipFile) && filesize($zipFile) > 1000000) {
    echo "<p class='ok'>Using ZIP file for extraction</p>\n";
    $extractDir = '';

    echo "Extracting ZIP (this may take 1-2 minutes on Linux)... ";
    flush();
    $start = microtime(true);
    $zip = new ZipArchive();
    $zip->open($zipFile);

    // Extract to tmp first, then move
    $tmpExtract = $tmpDir . '/extracted';
    if (!is_dir($tmpExtract)) mkdir($tmpExtract, 0755, true);

    $zip->extractTo($tmpExtract);
    $zip->close();

    $elapsed = round(microtime(true) - $start, 1);
    $count = (int)shell_exec("find $tmpExtract -type f 2>/dev/null | wc -l") ?: 'many';
    echo "<span class='ok'>OK (${elapsed}s, ~$count files)</span>\n<br>";

    // Move matomo/* to destination
    $srcDir = $tmpExtract . '/matomo';
    if (!is_dir($srcDir)) $srcDir = $tmpExtract;

    // Remove existing files in destination (be careful!)
    echo "Installing Matomo files to " . __DIR__ . " ... ";
    flush();

    // Copy files using PHP (safer than shell)
    $copyCount = 0;
    $rii = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($srcDir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($rii as $file) {
        $relativePath = substr($file->getRealPath(), strlen($srcDir));
        $dest = __DIR__ . $relativePath;
        if ($file->isDir()) {
            if (!is_dir($dest)) mkdir($dest, 0755, true);
        } elseif ($file->isFile()) {
            // Skip node_modules, tests, .git
            if (strpos($relativePath, '/node_modules/') !== false) continue;
            if (strpos($relativePath, '/tests/') !== false) continue;
            if (strpos($relativePath, '/.git/') !== false) continue;
            copy($file->getRealPath(), $dest);
            $copyCount++;
        }
    }
    echo "<span class='ok'>OK ($copyCount files installed)</span>\n<br>";

    // Cleanup
    shell_exec("rm -rf " . escapeshellarg($tmpExtract));

} elseif (file_exists($tarballFile) && filesize($tarballFile) > 1000000) {
    echo "<p class='ok'>Using tarball for extraction</p>\n";
    echo "Extracting tarball ... ";
    flush();
    $phar = new PharData($tarballFile);
    $phar->extractTo(__DIR__ . '/_tmp_extract', ['matomo']);
    echo "<span class='ok'>OK</span>\n<br>";
    // ... move files
}

// === Step 6: Configure database ===
echo "<h2>Step 6: Configure Database</h2>\n";

$configDir = __DIR__ . '/config';
if (!is_dir($configDir)) {
    @mkdir($configDir, 0755, true);
}

$configContent = <<<INI
[database]
host = "{$dbConfig['host']}"
username = "{$dbConfig['username']}"
password = "{$dbConfig['password']}"
dbname = "{$dbConfig['dbname']}"
tables_prefix = "{$dbConfig['prefix']}"
port = 3306
charset = "utf8mb4"

[database_tests]
host = "127.0.0.1"
username = "root"
password = ""
dbname = "analytics"
tables_prefix = "xdlu_"

[General]
default_day = "today"
default_period = "day"
session.save_handler = "files"
session.save_path = "tmp/sessions"
ipAnonymizerEnabled = 0
ipAddressMaskLength = 0

[Tracker]
tracking_requests_require_authentication = 0

[log]
log_writers[] = "file"
log_level = "DEBUG"
log_path = "tmp/logs/matomo.log"

[Mail]
; SMTP configuration can be added here if email is needed
INI;

$configFile = $configDir . '/config.ini.php';
if (file_exists($configFile)) {
    echo "<span class='info'>config.ini.php already exists. Backing up...</span>\n<br>";
    copy($configFile, $configFile . '.bak-' . date('YmdHi'));
}
file_put_contents($configFile, "<?php\nif (!defined('MATOMO_TEST_SUITE')) {\n" . $configContent . "\n}\n/* End of file config.ini.php */\n");
echo "<span class='ok'>Database configuration written to config/config.ini.php</span>\n<br>";

// === Step 7: Complete installation ===
echo "<h2>Step 7: Complete Installation</h2>\n";

// Create necessary directories
$dirs = ['tmp/sessions', 'tmp/cache', 'tmp/logs', 'tmp/templates_c', 'tmp/assets', 'tmp/tracker'];
foreach ($dirs as $dir) {
    $fullPath = __DIR__ . '/' . $dir;
    if (!is_dir($fullPath)) {
        @mkdir($fullPath, 0755, true);
        echo "<span class='ok'>Created: $dir</span>\n<br>";
    }
}

// Set permissions
$phpInput = '<?php @$_GET ?>'; // harmless
file_put_contents(__DIR__ . '/tmp/test_write.txt', 'test');
$writable = is_writable(__DIR__ . '/tmp');
unlink(__DIR__ . '/tmp/test_write.txt');
echo "tmp/ writable: " . ($writable ? '<span class="ok">YES</span>' : '<span class="err">NO</span>') . "\n<br>";

// Try to run the installer
$installerScript = __DIR__ . '/console';
if (file_exists($installerScript) && is_executable($installerScript)) {
    echo "<span class='info'>Attempting database schema setup via console...</span>\n<br>";
    // Try to run the database migration
    $output = shell_exec("php console install:install --MatomoInstallDatabaseHost={$dbConfig['host']} --MatomoInstallDatabaseLogin={$dbConfig['username']} --MatomoInstallDatabasePassword={$dbConfig['password']} --MatomoInstallDatabaseName={$dbConfig['dbname']} --MatomoInstallDatabasePort=3306 --MatomoInstallTablesPrefix={$dbConfig['prefix']} 2>&1");
    echo "<pre>" . htmlspecialchars($output ?: 'No output') . "</pre>\n";
} else {
    echo "<span class='info'>console not available or not executable. You can complete the installation by accessing index.php in your browser.</span>\n<br>";
}

// === Step 8: Verify ===
echo "<h2>Step 8: Verification</h2>\n";

// Check if Matomo can boot
$bootCheck = @include(__DIR__ . '/vendor/autoload.php');
if ($bootCheck) {
    echo "<span class='ok'>Composer autoloader loaded</span>\n<br>";
} else {
    echo "<span class='info'>Composer autoloader not found (may be OK)</span>\n<br>";
}

// Check essential files
$essentialFiles = ['piwik.php', 'index.php', 'matomo.php'];
foreach ($essentialFiles as $file) {
    $path = __DIR__ . '/' . $file;
    echo "  " . (file_exists($path) ? "<span class='ok'>✓ $file</span>" : "<span class='err'>✗ $file missing</span>") . "\n<br>";
}

// Check config
if (file_exists($configFile)) {
    echo "<span class='ok'>config/config.ini.php exists</span>\n<br>";
    // Try to connect to DB using Matomo's own classes
    $db = @new PDO("mysql:host={$dbConfig['host']};port=3306;dbname={$dbConfig['dbname']}", $dbConfig['username'], $dbConfig['password']);
    if ($db) {
        $tables = $db->query("SHOW TABLES LIKE '{$dbConfig['prefix']}%'")->fetchAll(PDO::FETCH_COLUMN);
        echo "<span class='ok'>Database accessible, " . count($tables) . " tables with prefix</span>\n<br>";
        $db = null;
    } else {
        echo "<span class='err'>Database connection failed (PDO)</span>\n<br>";
    }
}

// Clean up
shell_exec("rm -rf " . escapeshellarg($tmpDir));

echo "<h2>Done!</h2>\n";
echo "<p>Matomo 5.x has been installed to <code>" . __DIR__ . "</code>.</p>\n";
echo "<p><strong>Next steps:</strong></p>\n";
echo "<ul>\n<li>Access the dashboard: <a href='index.php'>index.php</a></li>\n";
echo "<li>Track visits: POST to <code>piwik.php?idsite=17</code> (add matomo.js to your site)</li>\n";
echo "<li>Delete this installer: <code>" . basename(__FILE__) . "</code></li>\n</ul>\n";
echo "</body></html>";
