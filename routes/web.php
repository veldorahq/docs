<?php

declare(strict_types=1);

/** @var Veldora\Framework\Http\Router $router */

// Home / Landing page
$router->get('/', [App\Controllers\HomeController::class, 'index']);

// Docs — overview + per-section
$router->get('/docs', [App\Controllers\DocsController::class, 'index']);
$router->get('/docs/{section}', [App\Controllers\DocsController::class, 'section']);

// AI Context & Skill Downloads
$router->get('/download/veldora-ai-prompt.md', [App\Controllers\DocsController::class, 'downloadPrompt']);

// VS Code Extension page
$router->get('/extension', [App\Controllers\ExtensionController::class, 'index']);

// Component showcase — grid overview + individual detail pages
$router->get('/components', [App\Controllers\ComponentsController::class, 'index']);
$router->get('/components/{component}', [App\Controllers\ComponentsController::class, 'show']);

// Changelog
$router->get('/changelog', [App\Controllers\ChangelogController::class, 'index']);

// FAQ page
$router->get('/faq', [App\Controllers\FaqController::class, 'index']);

// Static Informational Pages
$router->get('/about', [App\Controllers\PageController::class, 'about']);
$router->get('/privacy', [App\Controllers\PageController::class, 'privacy']);
$router->get('/terms', [App\Controllers\PageController::class, 'terms']);
$router->get('/license', [App\Controllers\PageController::class, 'license']);
