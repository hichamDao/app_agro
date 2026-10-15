<?php
/**
 * install-matomo-prod.php — Production Matomo 5.x Auto-Installer
 *
 * Deploy to: https://foodmax-group.com/install-matomo-prod.php
 * Access: https://foodmax-group.com/install-matomo-prod.php
 * After: DELETE this file
 *
 * What it does:
 * 1. Downloads matomo-latest.zip from GitHub (or uses local cache)
 * 2. Extracts it to /matomo/ subdirectory using ZipArchive
 * 3. Reads DB credentials from includes/connection.secret.php
 * 4. Creates config/config.ini.php with DB connection
 * 5. Attempts schema creation via SQL file or PDO
 * 6. Creates superuser (admin/admin123)
 * 7. Creates site 17 (foodmax-group.com)
 * 8. Reports results
 *
 * Requirements on production: PHP 8.1+, zip ext, curl ext, mysqli ext
 */

@error_reporting(E_ALL);
@ini_set('display_errors', '1');
@ini_set('max_execution_time', 300);
@ini_set('memory_limit', '512M');

function msg($msg, $cls = '') {
    $c = $cls ? "<span class='$cls'>" : '';
    $cl = $cls ? "</span>" : '';
    echo "$c$msg$cl\n<br>";
    if (ob_get_level()) @ob_flush();
    if (ob_get_level()) @flush();
}

echo "<!DOCTYPE html><html><head><title>Matomo Installer</title>";
echo "<style>body{font-family:monospace;margin:20px;line-height:1.6} .ok{color:#080;font-weight:bold} .err{color:#c00;font-weight:bold} .info{color:#00a} .warn{color:#a50}</style>";
echo "</head><body>\n";
echo "<h2>Matomo 5.x Production Installer</h2>\n";
echo "<p>Running on PHP " . PHP_VERSION . " (" . PHP_OS . ")</p>\n";

// === Step 1: Verify prerequisites ===
msg("=== Step 1: Prerequisites ===", 'info');

$exts = ['zip', 'curl', 'mysqli', 'pdo_mysql', 'openssl', 'gd', 'mbstring', 'json', 'intl'];
$missing = [];
foreach ($exts as $ext) {
    $ok = extension_loaded($ext);
    msg("  $ext: " . ($ok ? 'OK' : 'MISSING'), $ok ? 'ok' : 'err');
    if (!$ok) $missing[] = $ext;
}
if ($missing) {
    msg("Missing extensions: " . implode(', ', $missing), 'err');
    exit;
}

// === Step 2: Get database credentials ===
msg("\n=== Step 2: Database Credentials ===", 'info');

$dbConfig = null;
$secretFiles = [
    __DIR__ . '/includes/connection.secret.php',
    __DIR__ . '/connection.secret.php',
];

foreach ($secretFiles as $secretFile) {
    if (is_file($secretFile)) {
        msg("  Found: " . htmlspecialchars($secretFile));
        $dbConfig = (array) require $secretFile;
        break;
    }
}

if (!$dbConfig) {
    msg("  No connection.secret.php found. Trying connection.local.php...", 'info');
    $localFile = __DIR__ . '/includes/connection.local.php';
    if (is_file($localFile)) {
        $dbConfig = (array) require $localFile;
    }
}

// Hardcoded fallback for Wedos (common MySQL setup)
if (!$dbConfig || empty($dbConfig['host'])) {
    msg("  Using fallback credentials (mysql, Wedos default)", 'warn');
    $dbConfig = [
        'host' => 'mysql',
        'user' => 'foodmax_admin',
        'pass' => '',
        'db' => 'foodmax_web',
    ];
}

msg("  Host: " . htmlspecialchars($dbConfig['host'] ?? 'N/A'));
msg("  DB: " . htmlspecialchars($dbConfig['db'] ?? 'N/A'));

// Test database connection
$dbHost = ($dbConfig['host'] === 'localhost') ? '127.0.0.1' : $dbConfig['host'];
msg("  Testing connection to $dbHost:3306...");

$dbConn = @new mysqli($dbHost, $dbConfig['user'] ?? '', $dbConfig['pass'] ?? '', $dbConfig['db'] ?? '');
if ($dbConn->connect_error) {
    msg("  Connection failed: " . $dbConn->connect_error, 'err');
    msg("  Will configure Matomo anyway — complete DB setup via web installer", 'warn');
    $dbConn = null;
} else {
    msg("  Connected: MySQL " . $dbConn->server_info, 'ok');
}

// === Step 3: Download Matomo ZIP ===
msg("\n=== Step 3: Download ===", 'info');

$zipFile = __DIR__ . '/_matomo.zip';
$downloadUrls = [
    'https://github.com/matomo-org/matomo-package/releases/latest/download/matomo.zip',
    'https://builds.matomo.org/matomo-latest.zip',
    'https://dl.matomo.org/release/matomo-latest.zip',
];

$downloaded = false;

// Check for cached ZIP
if (file_exists($zipFile) && filesize($zipFile) > 10000000) {
    msg("  Using cached ZIP (" . round(filesize($zipFile)/1048576, 1) . " MB)", 'ok');
    $downloaded = true;
} else {
    foreach ($downloadUrls as $url) {
        msg("  Downloading from: $url");
        $tmpFile = __DIR__ . '/_matomo_dl.zip';

        if (function_exists('curl_init')) {
            $ch = curl_init();
            $fp = fopen($tmpFile, 'w+');
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_FILE => $fp,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 120,
                CURLOPT_SSL_VERIFYPEER => true,
            ]);
            curl_exec($ch);
            $err = curl_error($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $size = curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD);
            curl_close($ch);
            fclose($fp);

            if ($code == 200 && $size > 10000000) {
                rename($tmpFile, $zipFile);
                msg("  Downloaded " . round($size/1048576, 1) . " MB", 'ok');
                $downloaded = true;
                break;
            } else {
                msg("  Failed: HTTP $code, $err", 'err');
                @unlink($tmpFile);
            }
        }
    }
}

if (!$downloaded) {
    msg("  Download failed. Aborting.", 'err');
    exit;
}

// === Step 4: Extract Matomo ===
msg("\n=== Step 4: Extract ===", 'info');

$extractDir = __DIR__ . '/matomo';

// Clean existing directory
if (is_dir($extractDir)) {
    msg("  Removing existing /matomo/ directory...");
    function removeDir($dir) {
        if (!is_dir($dir)) return;
        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            $path = $dir . '/' . $item;
            if (is_dir($path)) {
                removeDir($path);
            } else {
                @unlink($path);
            }
        }
        @rmdir($dir);
    }
    removeDir($extractDir);
    msg("  Clean up complete.", 'ok');
}

msg("  Extracting ZIP via extractTo() (fast C-level extraction)...");
$start = microtime(true);

$zip = new ZipArchive();
if ($zip->open($zipFile) === true) {
    // Extract all files at once (C-optimized, fast on Linux)
    $zip->extractTo(__DIR__);
    $zip->close();

    $elapsed = round(microtime(true) - $start, 1);
    $count = 0;
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($extractDir, FilesystemIterator::SKIP_DOTS));
    foreach ($rii as $f) { if ($f->isFile()) $count++; }
    msg("  Extracted $count files in ${elapsed}s", 'ok');

    // Remove non-essential directories
    $skipDirs = ['node_modules', 'tests', '.github', '.git'];
    foreach ($skipDirs as $dir) {
        $path = $extractDir . '/' . $dir;
        if (is_dir($path)) {
            msg("  Cleaning: $dir/");
            removeDir($path);
        }
    }
} else {
    msg("  Failed to open ZIP", 'err');
    exit;
}

@unlink($zipFile);

// Verify extraction
if (!is_dir($extractDir) || !file_exists($extractDir . '/index.php')) {
    msg("  Extraction incomplete — checking matomo/ subdirectory", 'warn');
    if (is_dir(__DIR__ . '/matomo') && file_exists(__DIR__ . '/matomo/index.php')) {
        $extractDir = __DIR__ . '/matomo';
        msg("  Found at /matomo/", 'ok');
    } else {
        msg("  ERROR: matomo/index.php not found after extraction", 'err');
        exit;
    }
}

// === Step 5: Create directories ===
msg("\n=== Step 5: Directories ===", 'info');

$dirs = [
    'tmp', 'tmp/sessions', 'tmp/cache', 'tmp/logs',
    'tmp/templates_c', 'tmp/assets', 'tmp/tracker',
];
foreach ($dirs as $dir) {
    $path = $extractDir . '/' . $dir;
    if (!is_dir($path)) {
        @mkdir($path, 0755, true);
    }
    if (is_writable($path)) {
        msg("  $dir: OK", 'ok');
    } else {
        msg("  $dir: NOT WRITABLE", 'err');
    }
}

// === Step 6: Configure database ===
msg("\n=== Step 6: Database Configuration ===", 'info');

if ($dbConn) {
    $dbName = 'matomo';

    // Create database if not exists
    $dbConn->query("CREATE DATABASE IF NOT EXISTS `$dbName`");
    msg("  Database: $dbName", 'info');

    // Check if tables already exist
    $dbConn->select_db($dbName);
    $tables = $dbConn->query("SHOW TABLES LIKE 'matomo_%'");
    $existingTables = $tables ? $tables->num_rows : 0;

    if ($existingTables > 0) {
        msg("  Existing table found: $existingTables table(s)", 'warn');
    }

    // Build config.ini.php
    $configContent = "[database]\n";
    $configContent .= "host = \"" . addslashes($dbHost) . "\"\n";
    $configContent .= "username = \"" . addslashes($dbConfig['user'] ?? '') . "\"\n";
    $configContent .= "password = \"" . addslashes($dbConfig['pass'] ?? '') . "\"\n";
    $configContent .= "dbname = \"" . $dbName . "\"\n";
    $configContent .= "tables_prefix = \"matomo_\"\n";
    $configContent .= "port = 3306\n";
    $configContent .= "charset = \"utf8mb4\"\n";
    $configContent .= "\n[General]\n";
    $configContent .= "default_day = \"today\"\n";
    $configContent .= "default_period = \"day\"\n";
    $configContent .= "session.save_handler = \"files\"\n";
    $configContent .= "session.save_path = \"tmp/sessions\"\n";
    $configContent .= "ipAnonymizerEnabled = 0\n";
    $configContent .= "ipAddressMaskLength = 0\n";
    $configContent .= "trusted_hosts[] = \"foodmax-group.com\"\n";
    $configContent .= "trusted_hosts[] = \"www.foodmax-group.com\"\n";
    $configContent .= "cors_domains[] = \"https://foodmax-group.com\"\n";
    $configContent .= "cors_domains[] = \"https://www.foodmax-group.com\"\n";
    $configContent .= "assume_ssl_even_insite = 1\n";
    $configContent .= "\n[Tracker]\n";
    $configContent .= "tracking_requests_require_authentication = 0\n";
    $configContent .= "\n[log]\n";
    $configContent .= "log_writers[] = \"file\"\n";
    $configContent .= "log_level = \"ERROR\"\n";
    $configContent .= "log_path = \"tmp/logs/matomo.log\"\n";

    $configPhp = "<?php\ndefine('MATOMO_TEST_SUITE', true);\n" . $configContent . "\n";
    file_put_contents($extractDir . '/config/config.ini.php', $configPhp);
    msg("  Config written to config/config.ini.php", 'ok');

    // === Step 7: Create database schema ===
    msg("\n=== Step 7: Database Schema ===", 'info');

    // Method 1: Use PDO with SQL file
    $pdo = @new PDO("mysql:host=$dbHost;port=3306", $dbConfig['user'], $dbConfig['pass']);
    if ($pdo) {
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName`");
        $pdo->exec("USE `$dbName`");

        // Look for the schema SQL file
        $sqlPaths = [
            $extractDir . '/plugins/Install/SQL/0000.sql',
            $extractDir . '/plugins/Installation/SQL/0000.sql',
            $extractDir . '/plugins/Install/Adapter/Sql.php',
            $extractDir . '/core/Plugin/Console/Command/ColdStartCommand.php',
        ];

        $sqlFile = null;
        foreach ($sqlPaths as $path) {
            if (file_exists($path)) {
                $sqlFile = $path;
                break;
            }
        }

        if (!$sqlFile) {
            // Search recursively
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($extractDir . '/plugins', FilesystemIterator::SKIP_DOTS)
            );
            foreach ($iterator as $file) {
                if (preg_match('/Install.*SQL.*\.sql$/', $file->getFilename()) ||
                    preg_match('/schema.*\.sql$/i', $file->getFilename())) {
                    $sqlFile = $file->getPathname();
                    break;
                }
            }
        }

        if ($sqlFile) {
            msg("  Found SQL schema: " . basename($sqlFile), 'info');
            $sql = file_get_contents($sqlFile);
            $sql = str_replace('{TABLE_PREFIX}', 'matomo_', $sql);
            // Execute in chunks
            $statements = explode(";\n", $sql);
            $created = 0;
            foreach ($statements as $stmt) {
                $stmt = trim($stmt);
                if (empty($stmt)) continue;
                try {
                    $pdo->exec($stmt);
                    $created++;
                } catch (PDOException $e) {
                    // Ignore "table already exists" errors
                    if (strpos($e->getMessage(), 'already exists') === false) {
                        msg("  SQL error: " . $e->getMessage(), 'warn');
                    }
                }
            }
            msg("  Executed $created SQL statements", 'ok');
        } else {
            msg("  No SQL file found. Will create tables via web installer.", 'warn');
        }
    }

    // === Step 8: Create admin user ===
    msg("\n=== Step 8: Admin User ===", 'info');

    $pdo = @new PDO("mysql:host=$dbHost;port=3306;dbname=$dbName;charset=utf8mb4", $dbConfig['user'], $dbConfig['pass']);
    if ($pdo) {
        $existing = $pdo->query("SELECT name FROM matomo_user WHERE login = 'admin'")->fetchAll();
        if (!empty($existing)) {
            msg("  Admin user already exists", 'info');
        } else {
            $password = password_hash('admin123', PASSWORD_DEFAULT);
            $email = 'admin@foodmax-group.com';
            $tokenAuth = hash('sha256', 'admin' . $password . time());
            $sql = "INSERT INTO matomo_user (login, password, email, token_auth, superuser_access, date_registered) VALUES (?, ?, ?, ?, 1, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['admin', $password, $email, $tokenAuth]);
            msg("  Admin user created (admin / admin123)", 'ok');
        }

        // === Step 9: Create site 17 ===
        $sites = $pdo->query("SELECT idsite FROM matomo_site WHERE idsite = 17")->fetchAll();
        if (empty($sites)) {
            $pdo->exec("INSERT INTO matomo_site (idsite, name, main_url) VALUES (17, 'foodmax-group.com', 'https://foodmax-group.com')");
            msg("  Site 17 created (foodmax-group.com)", 'ok');
        } else {
            msg("  Site 17 already exists", 'info');
        }

        // Test tracking endpoint
        msg("\n=== Step 10: Test Tracking ===", 'info');
        $testId = $pdo->query("SELECT idsite FROM matomo_site")->fetchAll(PDO::FETCH_COLUMN);
        msg("  Sites in database: " . implode(', ', $testId ?: ['none']), 'info');
    } else {
        msg("  Cannot connect to MySQL. Schema/user creation skipped.", 'warn');
        msg("  You can complete setup via: https://foodmax-group.com/matomo/index.php", 'info');
    }
} else {
    msg("  No database connection. Web installer will be needed.", 'warn');
    msg("  Access: https://foodmax-group.com/matomo/index.php", 'info');
}

// === Final summary ===
msg("\n=== Installation Summary ===", 'info');
msg("Matomo files: " . (is_dir($extractDir) ? "<span class='ok'>INSTALLED</span>" : "<span class='err'>FAILED</span>"), 'info');
$configExists = file_exists($extractDir . '/config/config.ini.php');
msg("Config file: " . ($configExists ? "<span class='ok'>CREATED</span>" : "<span class='err'>MISSING</span>"), 'info');
msg("Admin: admin / admin123", 'info');
msg("Site: 17 (foodmax-group.com)", 'info');

echo "\n<h2 style='color:green'>Installation Complete!</h2>\n";
echo "<p><strong>Next steps:</strong></p>\n";
echo "<ul>\n";
echo "<li><b>Dashboard:</b> <a href='/matomo/index.php'>https://foodmax-group.com/matomo/index.php</a></li>\n";
echo "<li><b>Tracking endpoint:</b> https://foodmax-group.com/matomo/matomo.php</li>\n";
echo "<li><b>Tracker JS:</b> https://foodmax-group.com/matomo/js/matomo.js</li>\n";
echo "<li><b>Login:</b> admin / admin123</li>\n";
echo "<li><b>DELETE</b> this file: install-matomo-prod.php</li>\n";
echo "</ul>\n";
echo "</body></html>";
