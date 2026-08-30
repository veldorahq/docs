<?php

declare(strict_types=1);

namespace App\Controllers;

use Veldora\Framework\Http\Request;
use Veldora\Framework\Http\Response;
use Veldora\Framework\View\Engine;

class FaqController
{
    public function __construct(protected Engine $view) {}

    public function index(Request $request): Response
    {
        $faqs = $this->getFaqData();
        $html = $this->view->render('pages.faq', ['faqs' => $faqs]);
        return new Response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    public static function getFaqData(): array
    {
        return [
            [
                'category' => 'General & Architecture',
                'items' => [
                    [
                        'q' => 'What is Veldora?',
                        'a' => 'Veldora is a modern, lightweight PHP 8.2+ MVC framework designed to give developers the full power of a complete web framework (routing, ORM, auth, templates, CLI, queues, mail, caching) without bloated vendor dependencies or heavy runtime overhead.',
                    ],
                    [
                        'q' => 'How is Veldora different from Laravel or Symfony?',
                        'a' => 'Veldora provides an intuitive, elegant developer experience similar to Laravel, but with a significantly smaller footprint, instant zero-dependency CLI execution (via <code>executeDirect()</code>), zero-config autoloader fallback, and a built-in 41+ component UI library with multi-aesthetic design systems (Skeuomorphic, Neumorphic, Flat, Glassmorphic).',
                    ],
                    [
                        'q' => 'What PHP version is required?',
                        'a' => 'Veldora requires PHP 8.2 or PHP 8.3+. It leverages modern PHP features such as typed properties, readonly classes, native enums, and match expressions.',
                    ],
                    [
                        'q' => 'Does Veldora have any required third-party dependencies?',
                        'a' => 'The core framework only requires <code>psr/container</code>. Everything else—including the router, view compiler, database query builder, migration engine, auth system, and CLI command runner—is built-in natively.',
                    ],
                ],
            ],
            [
                'category' => 'Getting Started & Scaffolding',
                'items' => [
                    [
                        'q' => 'How do I start a new Veldora project?',
                        'a' => 'You can create a project instantly using Composer or npx:<br><pre><code class="language-bash">composer create-project veldora/veldora my-app\n# or\nnpx create-veldora-app my-app</code></pre>',
                    ],
                    [
                        'q' => 'Does a fresh project contain a src/ folder?',
                        'a' => 'No. User applications follow standard PSR-4 MVC structure (<code>app/</code>, <code>bootstrap/</code>, <code>config/</code>, <code>database/</code>, <code>public/</code>, <code>resources/</code>, <code>routes/</code>, <code>storage/</code>, <code>tests/</code>). The framework core itself resides cleanly in <code>vendor/veldora/framework</code>.',
                    ],
                    [
                        'q' => 'How do I set up complete user authentication?',
                        'a' => 'Run <code>php veldora make:auth</code> and then <code>php veldora migrate</code>. This scaffolds Login, Registration, Password Reset, Profile Management, Email Verification, views, controllers, and routes in seconds.',
                    ],
                ],
            ],
            [
                'category' => 'CLI & Development',
                'items' => [
                    [
                        'q' => 'How many CLI commands are included?',
                        'a' => 'Veldora includes <strong>48 built-in CLI commands</strong> covering application serving, diagnostics, generators (<code>make:*</code>), database migrations, seeding, caching, maintenance mode, queue processing, and UI component management.',
                    ],
                    [
                        'q' => 'How does Maintenance Mode work in Veldora?',
                        'a' => 'You can take your app offline with <code>php veldora down --secret=your-token</code>. All visitors will see a 503 Maintenance page, while you can bypass it by appending <code>?secret=your-token</code> in the URL. Bring it back online with <code>php veldora up</code>.',
                    ],
                    [
                        'q' => 'Can I run commands without Symfony Console installed?',
                        'a' => 'Yes! All 48 commands implement native <code>executeDirect()</code> execution, meaning they work seamlessly in minimalist environments and shared hosting without requiring Symfony Console packages.',
                    ],
                ],
            ],
            [
                'category' => 'UI Components & Templates',
                'items' => [
                    [
                        'q' => 'What is Veldora UI?',
                        'a' => 'Veldora UI is an accessible, modern component library with 41+ components (Buttons, Modals, Tabs, Dropdowns, Sidebars, DataTables, Alerts, Badges, etc.) that can be installed into your project using <code>php veldora add &lt;component&gt;</code>.',
                    ],
                    [
                        'q' => 'What styling variants are supported in Veldora UI?',
                        'a' => 'Veldora UI supports standard modern dark/light mode, Skeuomorphic 3D tactile, Flat Minimalist 2D, Neumorphic Soft UI, and Glassmorphic variants.',
                    ],
                    [
                        'q' => 'How does the Veldora template engine work?',
                        'a' => 'Veldora uses a native template compiler supporting Blade-style syntax: <code>{{ $var }}</code>, <code>{!! $raw !!}</code>, <code>@if</code>, <code>@foreach</code>, <code>@extends</code>, <code>@section</code>, <code>@yield</code>, <code>@component</code>, <code>@method</code>, and <code>@csrf</code>.',
                    ],
                ],
            ],
            [
                'category' => 'Deployment & Production',
                'items' => [
                    [
                        'q' => 'How do I optimize Veldora for production?',
                        'a' => 'Run <code>php veldora optimize</code> before deploying. This compiles and caches all configuration values, routes, and view templates into optimized PHP bytecode.',
                    ],
                    [
                        'q' => 'Can I safely deploy environment variables?',
                        'a' => 'Yes. Use <code>php veldora env:encrypt</code> to encrypt your <code>.env</code> file with AES-256 using your <code>APP_KEY</code>. On your server, use <code>php veldora env:decrypt</code>.',
                    ],
                    [
                        'q' => 'What license is Veldora released under?',
                        'a' => 'Veldora and all its official ecosystem packages are open-source software licensed under the permissive <a href="/license">MIT License</a>.',
                    ],
                ],
            ],
        ];
    }
}
