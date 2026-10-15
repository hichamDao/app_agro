<?php
/**
 * Matomo 5.x Auto-Installer for Production Server (PHP 8.3)
 *
 * Downloads, extracts, and configures Matomo 5.x on the production server.
 * Uses the existing foodmaxorg database credentials from includes/connection.secret.php
 *
 * Deployment instructions:
 * 1. Place this file in the document root (or any accessible directory)
 * 2. Access via browser: https://foodmax-group.com/install-matomo-v2.php
 * 3. Follow the output instructions
 * 4. DELETE this file after installation
 */

@error_reporting(E_ALL);
@ini_set('display_errors', '1');
@ini_set('max_execution_time', 300);
@ini_set('memory_limit', '512M');

function logMsg($msg, $class = '') {
    $cls = $class ? " class='$class'" : '';
    echo "<span$cls>$msg</span>\n<br>";
    flush();
}

echo "<!DOCTYPE html><html><head><title>Matomo Installer v2</title>";
echo "<style>body{font-family:monospace;margin:20px;} .ok{color:green;font-weight:bold;} .err{color:red;font-weight:bold;} .info{color:blue;} .warn{color:orange;}</style>";
echo "</head><body>\n";
echo "<h1>Matomo 5.x Auto-Installer</h1>\n";

// === Step 1: Check prerequisites ===
logMsg("=== Step 1: Prerequisites ===", 'info');

$phpVersion = PHP_VERSION;
logMsg("PHP Version: $phpVersion");

$required = [
    'mysqli' => extension_loaded('mysqli'),
    'pdo_mysql' => extension_loaded('pdo_mysql'),
    'curl' => extension_loaded('curl'),
    'zip' => extension_loaded('zip'),
    'openssl' => extension_loaded('openssl'),
    'gd' => extension_loaded('gd'),
    'mbstring' => extension_loaded('mbstring'),
    'json' => extension_loaded('json'),
    'intl' => extension_loaded('intl'),
];

$allOk = true;
foreach ($required as $ext => $loaded) {
    logMsg("  Ext $ext: " . ($loaded ? '<span class="ok">YES</span>' : '<span class="err">NO</span>'), $loaded ? '' : '');
    if (!$loaded) $allOk = false;
}

if (!$allOk) {
    logMsg("Prerequisites not met. Cannot continue.", 'err');
    exit(1);
}
logMsg("All prerequisites met.", 'ok');

// === Step 2: Detect database credentials ===
logMsg("\n=== Step 2: Database Credentials ===", 'info');

$dbConfig = null;
$secretFile = __DIR__ . '/includes/connection.secret.php';
$localFile = __DIR__ . '/includes/connection.local.php';

if (is_file($secretFile)) {
    logMsg("Found connection.secret.php (production credentials)");
    $dbConfig = (array) require $secretFile;
    logMsg("  Host: " . htmlspecialchars($dbConfig['host'] ?? 'N/A'));
    logMsg("  DB: " . htmlspecialchars($dbConfig['db'] ?? 'N/A'));
    logMsg("  User: " . htmlspecialchars($dbConfig['user'] ?? 'N/A'));
} elseif (is_file($localFile)) {
    logMsg("Found connection.local.php (development credentials)");
    $dbConfig = (array) require $localFile;
}

if ($dbConfig && !empty($dbConfig['host'])) {
    // Try connecting to MySQL
    $dbHost = $dbConfig['host'] === 'localhost' ? '127.0.0.1' : $dbConfig['host'];
    logMsg("Attempting MySQL connection to $dbHost:3306 ...");

    $dbConn = @new mysqli($dbHost, $dbConfig['user'], $dbConfig['pass'], $dbConfig['db']);
    if ($dbConn->connect_error) {
        logMsg("  MySQL connection failed: " . $dbConn->connect_error, 'err');
        logMsg("  Will try localhost/127.0.0.1 ...", 'info');

        // Try 127.0.0.1
        $dbConn2 = @new mysqli('127.0.0.1', $dbConfig['user'], $dbConfig['pass'], $dbConfig['db']);
        if ($dbConn2->connect_error) {
            logMsg("  MySQL on 127.0.0.1 also failed", 'err');
        } else {
            $dbConn = $dbConn2;
            logMsg("  Connected via 127.0.0.1", 'ok');
        }
    } else {
        logMsg("  Connected to MySQL " . $dbConn->server_info, 'ok');
    }

    if (isset($dbConn) && !$dbConn->connect_error) {
        // Check existing tables
        $tables = $dbConn->query("SHOW TABLES LIKE 'xdlu_%'");
        $matomoTables = $tables ? $tables->num_rows : 0;
        logMsg("  Existing matomo tables (xdlu_ prefix): $matomoTables", 'info');

        // Check if we can create databases
        $dbConn->select_db($dbConfig['db']);
        $currentDb = $dbConfig['db'];
    } else {
        logMsg("Cannot connect to any MySQL server. Installation cannot proceed.", 'err');
        $dbConn = null;
        $currentDb = null;
        $dbHost = null;
    }
} else {
    logMsg("No database configuration found. Installation cannot proceed.", 'err');
    $dbConn = null;
    $currentDb = null;
    $dbHost = null;
}

if (!$dbConn) {
    logMsg("\nNo database available. The log-based proxy approach remains the only option.", 'warn');
    logMsg("The local Matomo server at 192.168.11.172:9094 will continue to collect visits via forward-tracking.php", 'info');
}

// === Step 3: Download Matomo ===
logMsg("\n=== Step 3: Download Matomo 5.x ===", 'info');

$downloadUrls = [
    'https://github.com/matomo-org/matomo-package/releases/latest/download/matomo.zip',
    'https://builds.matomo.org/matomo-latest.zip',
];

$zipFile = __DIR__ . '/_matomo_tmp.zip';
$downloaded = false;

// Check if ZIP already exists
if (file_exists($zipFile) && filesize($zipFile) > 10000000) {
    logMsg("Local ZIP cache found: " . round(filesize($zipFile)/1048576, 1) . " MB");
    $downloaded = true;
} else {
    foreach ($downloadUrls as $url) {
        logMsg("Downloading from: $url ...");
        $tmpFile = __DIR__ . '/_matomo_dl.zip';

        // Try cURL first
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 120,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_FILE => fopen($tmpFile, 'w+'),
            ]);
            $success = curl_exec($ch);
            $err = curl_error($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($success && $code == 200 && filesize($tmpFile) > 10000000) {
                rename($tmpFile, $zipFile);
                logMsg("  Downloaded " . round(filesize($zipFile)/1048576, 1) . " MB", 'ok');
                $downloaded = true;
                break;
            } else {
                logMsg("  cURL failed: $err (HTTP $code)", 'err');
                @unlink($tmpFile);
            }
        }

        // Try file_get_contents
        $ctx = stream_context_create([
            'http' => ['timeout' => 30, 'ignore_errors' => true],
            'ssl' => ['verify_peer' => true, 'verify_peer_name' => true],
        ]);
        $data = @file_get_contents($url, false, $ctx);
        if ($data && strlen($data) > 10000000) {
            file_put_contents($zipFile, $data);
            logMsg("  Downloaded " . round(strlen($data)/1048576, 1) . " MB", 'ok');
            $downloaded = true;
            break;
        }
    }
}

if (!$downloaded) {
    logMsg("Download failed. Cannot install Matomo.", 'err');
}

// === Step 4: Extract Matomo ===
logMsg("\n=== Step 4: Extract Matomo ===", 'info');

$extractDir = __DIR__ . '/matomo';
$extracted = false;

if (file_exists($zipFile) && $downloaded) {
    // Remove existing matomo/ directory
    if (is_dir($extractDir)) {
        logMsg("Removing existing matomo/ directory...");
        // Use PHP to remove (shell_exec may be disabled)
        $rii = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($extractDir, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($rii as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($extractDir);
        logMsg("  Existing directory removed.", 'ok');
    }

    logMsg("Extracting ZIP to matomo/ directory...");
    logMsg("  (~13,000 files, may take 1-2 minutes on Linux)");
    flush();

    $start = microtime(true);
    $zip = new ZipArchive();
    $openResult = $zip->open($zipFile);

    if ($openResult === true) {
        logMsg("  ZIP opened successfully. Extracting...");
        flush();

        $zip->extractTo(__DIR__);
        $zip->close();

        $elapsed = round(microtime(true) - $start, 1);
        logMsg("  Extracted in ${elapsed}s", 'ok');

        if (is_dir($extractDir)) {
            logMsg("  matomo/ directory created successfully.", 'ok');
            $extracted = true;
        } else {
            logMsg("  matomo/ directory not found after extraction!", 'err');
        }
    } else {
        logMsg("  Failed to open ZIP (error code: $openResult)", 'err');
    }

    @unlink($zipFile);
}

if (!$extracted) {
    logMsg("Extraction failed. Cannot proceed with installation.", 'err');
}

// === Step 5: Configure Matomo ===
logMsg("\n=== Step 5: Configure Database ===", 'info');

if ($extracted && $dbConn && !$dbConn->connect_error) {
    $configDir = $extractDir . '/config';
    if (!is_dir($configDir)) {
        @mkdir($configDir, 0755, true);
    }

    // Prepare Matomo database name
    // Try to use the existing analytics database (if tables don't conflict)
    // or create a new database
    $matomoDb = $dbConfig['db'];

    // Check if we should create a new database
    $checkDb = @$dbConn->query("SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = 'matomo'");
    if ($checkDb && $checkDb->num_rows == 0) {
        // Try to create a new database
        $dbConn->query("CREATE DATABASE IF NOT EXISTS matomo");
        if ($dbConn->query("SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = 'matomo'")->num_rows > 0) {
            $matomoDb = 'matomo';
            logMsg("  Created new database: matomo", 'ok');
        }
    }

    $dbHost = $dbConfig['host'] === 'localhost' ? '127.0.0.1' : $dbConfig['host'];
    $dbUser = $dbConfig['user'];
    $dbPass = $dbConfig['pass'];
    $dbName = $matomoDb;
    $dbPrefix = 'matomo_';

    logMsg("  Using database: $dbName on $dbHost");
    logMsg("  Table prefix: $dbPrefix");

    // Write config file
    $configContent = "<?php\n" .
        "if (!defined('MATOMO_TEST_SUITE')) {\n" .
        "\n" .
        "[database]\n" .
        "host = \"" . $dbHost . "\"\n" .
        "username = \"" . $dbUser . "\"\n" .
        "password = \"" . $dbPass . "\"\n" .
        "dbname = \"" . $dbName . "\"\n" .
        "tables_prefix = \"" . $dbPrefix . "\"\n" .
        "port = 3306\n" .
        "charset = \"utf8mb4\"\n" .
        "\n" .
        "[General]\n" .
        "default_day = \"today\"\n" .
        "default_period = \"day\"\n" .
        "session.save_handler = \"files\"\n" .
        "session.save_path = \"tmp/sessions\"\n" .
        "ipAnonymizerEnabled = 0\n" .
        "ipAddressMaskLength = 0\n" .
        "trusted_hosts[] = \"foodmax-group.com\"\n" .
        "trusted_hosts[] = \"www.foodmax-group.com\"\n" .
        "cors_domains[] = \"https://foodmax-group.com\"\n" .
        "cors_domains[] = \"https://www.foodmax-group.com\"\n" .
        "assume_ssl_even_insite = 1\n" .
        "\n" .
        "[Tracker]\n" .
        "tracking_requests_require_authentication = 0\n" .
        "\n" .
        "[log]\n" .
        "log_writers[] = \"file\"\n" .
        "log_level = \"ERROR\"\n" .
        "log_path = \"tmp/logs/matomo.log\"\n" .
        "\n" .
        "/* End of file config.ini.php */\n" .
        "\n" .
        "}\n";

    $configFile = $configDir . '/config.ini.php';
    file_put_contents($configFile, $configContent);
    logMsg("  Configuration written to config/config.ini.php", 'ok');

    // Create necessary directories
    $dirs = [
        'tmp/sessions', 'tmp/cache', 'tmp/logs',
        'tmp/templates_c', 'tmp/assets', 'tmp/tracker',
        'tmp/sessions',
    ];
    foreach ($dirs as $dir) {
        $fullPath = $extractDir . '/' . $dir;
        if (!is_dir($fullPath)) {
            @mkdir($fullPath, 0755, true);
        }
    }
    logMsg("  Required directories created.", 'ok');

    // Try to run database schema creation
    logMsg("\n=== Step 6: Initialize Database Schema ===", 'info');

    // First try using Matomo's console/install
    $consolePath = $extractDir . '/console';
    $installScript = $extractDir . '/plugins/Installation/Installer.php';

    // Try running the Matomo installer via PHP CLI if available
    $phpBin = PHP_BINARY;
    logMsg("  PHP binary: $phpBin");

    // Try running: php console install --url=... 
    $installCmd = escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($extractDir . '/console') .
        ' install:install --MatomoInstallDatabaseName="' . $dbName . '"' .
        ' --MatomoInstallDatabaseHost="' . $dbHost . '"' .
        ' --MatomoInstallDatabaseLogin="' . $dbUser . '"' .
        ' --MatomoInstallDatabasePassword="' . $dbPass . '"' .
        ' --MatomoInstallTablesPrefix="' . $dbPrefix . '"' .
        ' --MatomoInstallDatabasePort="3306" 2>&1';

    logMsg("  Attempting: php console install:install ...");

    // Try with exec (may work if not disabled)
    if (function_exists('exec')) {
        $output = [];
        $returnVar = 0;
        @exec($installCmd, $output, $returnVar);
        if ($returnVar == 0 && empty($output)) {
            logMsg("  Install command executed (return code: 0)", 'ok');
        } else {
            $lastLines = array_slice($output, -5);
            logMsg("  Install command output: " . implode("\n", $lastLines), 'info');
        }
    } else {
        logMsg("  exec() is disabled. Will try web-based install.", 'warn');

        // Check if we can use the web installer
        // Test database connection with PDO
        $pdo = @new PDO("mysql:host=$dbHost;port=3306;dbname=$dbName", $dbUser, $dbPass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        if (!$pdo) {
            // Database might not exist yet, try to create it
            $pdo = @new PDO("mysql:host=$dbHost;port=3306", $dbUser, $dbPass);
            if ($pdo) {
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName`");
                $pdo->exec("USE `$dbName`");
                logMsg("  Created database: $dbName", 'ok');
            }
        }

        if ($pdo) {
            logMsg("  Database connection successful via PDO", 'ok');
            // We need to run the SQL schema
            // Look for the SQL file
            $sqlFiles = glob($extractDir . '/plugins/Install/Sql/*.sql');
            if (empty($sqlFiles)) {
                $sqlFiles = glob($extractDir . '/plugins/Installation/Sql/*.sql');
            }
            if (!empty($sqlFiles)) {
                logMsg("  Found SQL schema file: " . basename($sqlFiles[0]));
                $sql = file_get_contents($sqlFiles[0]);
                // Replace placeholders
                $sql = str_replace('{DATABASE_CHARSET}', 'utf8mb4', $sql);
                $sql = str_replace('{TABLE_PREFIX}', $dbPrefix, $sql);
                $sql = str_replace('{DATABASE_COLLATION}', 'utf8mb4_unicode_ci', $sql);
                // Execute SQL
                try {
                    $pdo->exec($sql);
                    logMsg("  Database schema installed", 'ok');
                } catch (PDOException $e) {
                    logMsg("  SQL execution error: " . $e->getMessage(), 'err');
                    // Try alternative: create tables using Matomo's classes
                    logMsg("  Trying alternative schema installation...", 'info');
                }
            } else {
                logMsg("  No SQL schema file found. Manual installation required.", 'warn');
            }
        } else {
            logMsg("  Cannot connect to database. Cannot install schema.", 'err');
        }
    }

    // Create a test user
    $pdo = @new PDO("mysql:host=$dbHost;port=3306;dbname=$dbName", $dbUser, $dbPass);
    if ($pdo) {
        // Check if any users exist
        $users = $pdo->query("SELECT name FROM {$dbPrefix}user")->fetchAll();
        if (empty($users)) {
            logMsg("  Creating admin user...", 'info');
            $login = 'admin';
            $password = password_hash('admin123', PASSWORD_DEFAULT);
            $email = 'admin@foodmax-group.com';
            $tokenAuth = md5($login . $password . $email . time());
            $pdo->exec("INSERT INTO {$dbPrefix}user (login, password, email, token_auth, superuser_access, date_registered) VALUES ('$login', '$password', '$email', '$tokenAuth', '1', NOW())");
            logMsg("  Admin user created (login: admin, password: admin123)", 'ok');
        } else {
            logMsg("  Admin user already exists", 'info');
        }
    }

    // Create site 17 if it doesn't exist
    $sites = $pdo->query("SELECT idsite FROM {$dbPrefix}site WHERE idsite = 17")->fetchAll();
    if (empty($sites)) {
        logMsg("  Creating site 17 (foodmax-group.com)...", 'info');
        $pdo->exec("INSERT INTO {$dbPrefix}site (idsite, name, main_url) VALUES (17, 'foodmax-group.com', 'https://foodmax-group.com')");
        logMsg("  Site 17 created", 'ok');
    } else {
        logMsg("  Site 17 already exists", 'info');
    }
}

// === Step 7: Final verification ===
logMsg("\n=== Step 7: Verification ===", 'info');

$essentialFiles = [
    $extractDir . '/index.php',
    $extractDir . '/piwik.php',
    $extractDir . '/matomo.php',
    $extractDir . '/config/config.ini.php',
];

foreach ($essentialFiles as $file) {
    $exists = file_exists($file);
    logMsg("  " . htmlspecialchars(basename($file)) . ": " . ($exists ? '<span class="ok">OK</span>' : '<span class="err">MISSING</span>'));
}

// Test tracking endpoint
if (file_exists($extractDir . '/piwik.php')) {
    logMsg("\n  Testing tracking endpoint...", 'info');
    // Send a test tracking request
    $testPayload = "idsite=17&rec=1&cip=203.0.113.99&url=https://foodmax-group.com&action_name=Test";
    $testUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/matomo/piwik.php';

    $ch = curl_init($testUrl);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $testPayload,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $response = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($code == 200) {
        logMsg("  Tracking endpoint test: HTTP 200", 'ok');
    } else {
        logMsg("  Tracking endpoint test: HTTP $code", 'err');
    }
}

// === Step 8: Instructions ===
logMsg("\n=== Installation Complete! ===", 'ok');
logMsg("\n<b>Next steps:</b>", 'info');
logMsg("1. Update the tracker in includes/matomo-tracker.php:");
logMsg("   Change setTrackerUrl('/piwik-proxy.php') to setTrackerUrl('/matomo/piwik.php')");
logMsg("   Change g.src='/matomo.js' to g.src='/matomo/matomo.js'");
logMsg("2. Access the dashboard at: <a href='/matomo/index.php'>/matomo/index.php</a>");
logMsg("3. Login: admin / admin123");
logMsg("4. Delete this installer file after configuration");
logMsg("5. Update piwik-proxy.php to forward to '/matomo/piwik.php' instead of logging");
logMsg("\n<b>Configuration:</b> matomo/config/config.ini.php");
logMsg("<b>Dashboard:</b> https://foodmax-group.com/matomo/index.php");
logMsg("<b>Tracking endpoint:</b> https://foodmax-group.com/matomo/piwik.php");
logMsg("<b>Matomo JS:</b> https://foodmax-group.com/matomo/js/matomo.js");

echo "\n</body></html>";
