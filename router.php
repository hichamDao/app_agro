<?php
// Router script for PHP built-in server to handle .htaccess rewrite rules
$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);

// If the requested file or directory exists, serve it directly
if ($path !== '/' && file_exists(__DIR__ . $path)) {
    return false;
}

// Handle /products/search/{keyword}/ -> products/products.php?motCle={keyword}
if (preg_match('#^/products/search/([A-Za-z0-9-]+)/?$#', $path, $m)) {
    $_GET['motCle'] = $m[1];
    require_once __DIR__ . '/products/products.php';
    exit;
}

// Handle /products/tags/{keyword}/ -> products/products.php?tags={keyword}
if (preg_match('#^/products/tags/([A-Za-z0-9-]+)/?$#', $path, $m)) {
    $_GET['tags'] = $m[1];
    require_once __DIR__ . '/products/products.php';
    exit;
}

// Handle /products/{category}/ -> products/products.php?catslug={category}
if (preg_match('#^/products/([A-Za-z0-9-]+)/?$#', $path, $m)) {
    $_GET['catslug'] = $m[1];
    require_once __DIR__ . '/products/products.php';
    exit;
}

// Handle /products/?{query params} -> products/products.php
if (preg_match('#^/products/?$#', $path)) {
    require_once __DIR__ . '/products/products.php';
    exit;
}

// Handle other rewrites
$rewrites = [
    '#^/home/?$#' => '/index.php',
    '#^/about-us/?$#' => '/about-us.php',
    '#^/blog/?$#' => '/blog.php',
    '#^/blog/([A-Za-z0-9-]+)/?$#' => '/blog-post.php?slug=$1',
    '#^/contact/?$#' => '/contact.php',
    '#^/quality/?$#' => '/quality.php',
    '#^/how-we-work/?$#' => '/how-we-work.php',
    '#^/why-foodmax/?$#' => '/why-foodmax.php',
    '#^/export-services/?$#' => '/export-services.php',
    '#^/for-importers/?$#' => '/for-importers.php',
    '#^/privacy-policy/?$#' => '/privacy-policy.php',
    '#^/terms/?$#' => '/terms.php',
    '#^/morocco/?$#' => '/morocco.php',
    '#^/fresh-produce-exporter-morocco/?$#' => '/fresh-produce-exporter-morocco.php',
];

foreach ($rewrites as $pattern => $target) {
    if (preg_match($pattern, $path, $m)) {
        $target = preg_replace_callback('/\$(\d+)/', function($match) use ($m) {
            return isset($m[$match[1]]) ? $m[$match[1]] : '';
        }, $target);
        // Parse query string from target
        if (strpos($target, '?') !== false) {
            list($file, $query) = explode('?', $target, 2);
            parse_str($query, $params);
            $_GET = array_merge($_GET, $params);
        } else {
            $file = $target;
        }
        require_once __DIR__ . $file;
        exit;
    }
}

// Default: try index.php
if ($path === '/') {
    require_once __DIR__ . '/index.php';
    exit;
}

// 404
http_response_code(404);
require_once __DIR__ . '/includes/_404.php';
exit;