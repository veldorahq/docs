<?php

declare(strict_types=1);

/** @var Veldora\Framework\Http\Router $router */

// Home / Landing page
$router->get('/', [App\Controllers\HomeController::class, 'index']);

// Docs — overview + per-section
$router->get('/docs', [App\Controllers\DocsController::class, 'index']);
$router->get('/docs/{section}', [App\Controllers\DocsController::class, 'section']);

// Component showcase
$router->get('/components', [App\Controllers\ComponentsController::class, 'index']);
