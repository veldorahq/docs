<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Veldora Zero-Config Built-in Autoloader
|--------------------------------------------------------------------------
|
| Maps PSR-4 namespaces for App, Framework, and UI. Also includes vendor
| autoload if present, registers global helpers, and boots exception handler.
|
*/

$basePath = dirname(__DIR__);

// 1. If Composer vendor autoload exists, load it first
$vendorAutoload = $basePath . '/vendor/autoload.php';
if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
}

// 2. Register PSR-4 autoloader for Veldora application & bundled core
spl_autoload_register(function (string $class) use ($basePath): void {
    $prefixes = [
        'App\\'                => $basePath . '/app/',
        'Veldora\\Framework\\' => [
            $basePath . '/src/Framework/',
            $basePath . '/vendor/veldora/framework/src/',
            dirname($basePath) . '/veldora-core/src/',
        ],
        'Veldora\\UI\\'        => [
            $basePath . '/src/UI/',
            $basePath . '/vendor/veldora/ui/src/',
            dirname($basePath) . '/veldora-ui/src/',
        ],
    ];

    foreach ($prefixes as $prefix => $dirs) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }

        $relativeClass = substr($class, $len);
        $dirs = (array) $dirs;

        foreach ($dirs as $baseDir) {
            $file = rtrim($baseDir, '/\\') . '/' . str_replace('\\', '/', $relativeClass) . '.php';
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
});

// 3. Load global framework helpers if available
$helperPaths = [
    $basePath . '/src/Framework/helpers.php',
    $basePath . '/vendor/veldora/framework/src/helpers.php',
    dirname($basePath) . '/veldora-core/src/helpers.php',
];
foreach ($helperPaths as $helpersFile) {
    if (file_exists($helpersFile)) {
        require_once $helpersFile;
        break;
    }
}

// 4. Register the global exception / error / fatal-shutdown handler
if (class_exists(\Veldora\Framework\Foundation\Exception\Handler::class)) {
    \Veldora\Framework\Foundation\Exception\Handler::register();
}
