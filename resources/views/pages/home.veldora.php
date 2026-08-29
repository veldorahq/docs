@extends('layouts.app')

@section('page_title', 'Veldora')
@section('page_title_suffix', 'PHP 8.2+ MVC Framework')
@section('meta_desc', 'Veldora is a PHP 8.2+ framework with routing, Blade-style templates, session auth, ActiveRecord ORM, CLI scaffolding, job queues, SMTP mail, and a 21-component UI library.')

@section('content')
<main class="home-layout">

    <!-- ── Hero ─────────────────────────────────────────────────────── -->
    <section class="hero" aria-label="Hero">

        <div class="hero-badge">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="13 2 13 9 20 9"/><polygon points="22 12 12 2 2 12 2 22 22 22"/></svg>
            v0.5.0 &mdash; PHP 8.2+ MVC Framework
        </div>

        <h1 class="hero-title">
            Veldora PHP Framework
        </h1>

        <p class="hero-sub">
            PHP 8.2+ MVC framework with expressive routing, Blade-style templates, session auth,
            ActiveRecord ORM, job queues, SMTP mail, and a 21-component UI library.
            CLI scaffolding gets a project running in seconds.
        </p>

        <div class="hero-actions">
            <a href="/docs/getting-started" class="btn btn-primary" id="hero-get-started">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Get Started
            </a>
            <a href="/components" class="btn btn-secondary" id="hero-view-components">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                UI Components
            </a>
            <a href="https://github.com/veldorahq/veldora-core" class="btn btn-ghost" target="_blank" rel="noopener noreferrer" id="hero-github">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/></svg>
                GitHub
            </a>
        </div>

        <!-- Hero single prominent code preview -->
        <div class="hero-code" aria-label="Veldora code example">
            <div class="hero-code-toolbar">
                <div class="hero-code-toolbar-left">
                    <span class="dot-red"></span>
                    <span class="dot-yellow"></span>
                    <span class="dot-green"></span>
                    <span class="hero-code-label">routes/web.php</span>
                </div>
                <button type="button" class="code-copy-btn" onclick="copyCode(this)" aria-label="Copy routes code">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                    Copy
                </button>
            </div>
            <pre class="code-block language-php"><code class="language-php"><?php echo htmlspecialchars(
'<?php
// routes/web.php — Expressive routing with groups, middleware & route parameters

$router->get(\'/\', [HomeController::class, \'index\']);
$router->get(\'/posts/{slug}\', [PostController::class, \'show\']);

// Authenticated route group
$router->group([\'middleware\' => [\'auth\']], function ($r) {
    $r->get(\'/dashboard\', [DashboardController::class, \'index\']);
    $r->get(\'/profile\', [ProfileController::class, \'edit\']);
    $r->put(\'/profile\', [ProfileController::class, \'update\']);
});

// Auth endpoints
$router->post(\'/login\', [AuthController::class, \'login\'])->middleware([\'guest\']);
$router->post(\'/logout\', [AuthController::class, \'logout\'])->middleware([\'auth\']);
'
, ENT_QUOTES, 'UTF-8'); ?></code></pre>
        </div>

    </section>

    <!-- ── Stats ─────────────────────────────────────────────────────── -->
    <section class="stats-section" aria-label="Framework stats">
        <div class="stats-inner">
            <div class="stat-item">
                <div class="stat-number">41+</div>
                <div class="stat-label">UI Components</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">41+</div>
                <div class="stat-label">CLI Commands</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?= $sectionCount ?></div>
                <div class="stat-label">Doc Chapters</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">PHP 8.2+</div>
                <div class="stat-label">Modern & Fast</div>
            </div>
        </div>
    </section>

    <!-- ── Why Veldora ───────────────────────────────────────────────── -->
    <section class="why-section" aria-label="Why Veldora">
        <div class="why-inner">
            <div class="why-text">
                <div class="section-label">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></polygon></svg>
                    Why Veldora?
                </div>
                <h2 class="why-title">A framework that keeps code readable</h2>
                <p class="why-desc">Veldora gives you a structured MVC project where each file has a clear purpose. No hidden service containers, no auto-magic resolution that breaks silently — just straightforward PHP classes you can open and read.</p>

                <ul class="why-list">
                    <li>
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                        <span><strong>No magic strings.</strong> Everything is type-hinted and IDE-friendly.</span>
                    </li>
                    <li>
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                        <span><strong>You own the code.</strong> Components live in your project, not in vendor.</span>
                    </li>
                    <li>
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                        <span><strong>Batteries included.</strong> Events, queues, mail, cache, storage — all built in.</span>
                    </li>
                    <li>
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                        <span><strong>Familiar syntax.</strong> Laravel-like conventions — readable by any PHP dev, no framework-specific knowledge needed.</span>
                    </li>
                    <li>
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                        <span><strong>PHP 8.2+ throughout.</strong> Strict types, constructor promotion, enums, and readonly properties used consistently.</span>
                    </li>
                </ul>

                <a href="/docs/getting-started" class="btn btn-primary" style="margin-top:28px;" id="why-cta">
                    Read the Docs
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            </div>

            <div class="why-code-stack">
                <div class="why-terminal">
                    <div class="why-terminal-bar">
                        <div class="why-terminal-dots">
                            <span class="dot-red"></span>
                            <span class="dot-yellow"></span>
                            <span class="dot-green"></span>
                        </div>
                        <span class="why-terminal-label">bash · Terminal</span>
                    </div>
                    <pre class="code-block language-bash"><code class="language-bash"># Create a new project
composer create-project veldora/veldora my-app
cd my-app

# Scaffold authentication in seconds
php veldora make:auth
php veldora migrate

# Start the dev server
php veldora serve
# → Listening on http://localhost:8000</code></pre>
                </div>

                <div class="why-badges">
                    <span class="why-badge">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        Auth in 1 command
                    </span>
                    <span class="why-badge">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
                        SQLite / MySQL / PG
                    </span>
                    <span class="why-badge">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                        VS Code Extension
                    </span>
                    <span class="why-badge">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        87 Tests · All Green
                    </span>
                </div>
            </div>
        </div>
    </section>

    <!-- ── Features Grid ────────────────────────────────────────────── -->
    <section class="features-section" aria-label="Features">
        <div class="section-label" style="justify-content:center;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></polygon></svg>
            Everything you need
        </div>
        <h2 class="section-title">Everything included in a single install</h2>
        <p class="section-sub">Routing, ORM, auth, CLI, mail, queues, and UI components — all part of the framework with no extra packages needed.</p>

        <div class="features-grid">

            <article class="feature-card">
                <div class="feature-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                </div>
                <div class="feature-title">MVC Architecture</div>
                <p class="feature-desc">Clean Controllers, Models, and Views with automatic PSR-4 autoloading. One class, one responsibility.</p>
            </article>

            <article class="feature-card">
                <div class="feature-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                </div>
                <div class="feature-title">Template Engine</div>
                <p class="feature-desc">Blade-inspired <code>&#64;directives</code>, <code>&#123;&#123; $variable &#125;&#125;</code>, layout inheritance, and reusable components.</p>
            </article>

            <article class="feature-card">
                <div class="feature-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                </div>
                <div class="feature-title">Powerful CLI</div>
                <p class="feature-desc">Scaffold anything with <code>php veldora make:*</code> — controllers, models, migrations, auth, jobs, and more.</p>
            </article>

            <article class="feature-card">
                <div class="feature-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </div>
                <div class="feature-title">Guard-based Auth</div>
                <p class="feature-desc">Session guards, <code>&#64;auth / &#64;guest</code> directives, CSRF protection, and a complete login/register scaffold.</p>
            </article>

            <article class="feature-card">
                <div class="feature-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
                </div>
                <div class="feature-title">ActiveRecord ORM</div>
                <p class="feature-desc">Fluent query builder, mass assignment, type casting, relations, pagination, and lifecycle hooks.</p>
            </article>

            <article class="feature-card">
                <div class="feature-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                </div>
                <div class="feature-title">41+ UI Components</div>
                <p class="feature-desc">Button, Tabs, Modal, Datatable, Sidebar, Rating and 35 more. Add them with <code>php veldora add button modal</code>.</p>
            </article>

            <article class="feature-card">
                <div class="feature-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                </div>
                <div class="feature-title">Mail & Queues</div>
                <p class="feature-desc">SMTP-powered Mailable classes, background job queues with retry logic and a persistent database driver.</p>
            </article>

            <article class="feature-card">
                <div class="feature-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div class="feature-title">Middleware Pipeline</div>
                <p class="feature-desc">Before/after hooks, CSRF protection, rate limiting, signed URLs, and email verification — all chainable.</p>
            </article>

            <article class="feature-card">
                <div class="feature-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div class="feature-title">Testing Infrastructure</div>
                <p class="feature-desc">PHPUnit integration with HTTP helpers, model factories, and full assertion suite — 87 tests ship with the framework.</p>
            </article>

        </div>
    </section>

    <!-- ── Quick Start CTA ──────────────────────────────────────────── -->
    <section class="cta-section" aria-label="Quick start">
        <div class="cta-inner">
            <div class="cta-content">
                <div class="section-label">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></polygon></svg>
                    Fast Setup
                </div>
                <h2 class="cta-title">Set up a new project in under a minute</h2>
                <p class="cta-sub">Create a full project with auth, migrations, and templates already wired using Composer or npx. Start building immediately.</p>
                <div class="cta-steps">
                    <div class="cta-step">
                        <span class="cta-step-num">1</span>
                        <span>Create your project</span>
                    </div>
                    <div class="cta-step">
                        <span class="cta-step-num">2</span>
                        <span>Scaffold authentication</span>
                    </div>
                    <div class="cta-step">
                        <span class="cta-step-num">3</span>
                        <span>Start building</span>
                    </div>
                </div>
                <a href="/docs/getting-started" class="btn btn-primary cta-btn" id="cta-read-docs">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    Full Documentation
                </a>
            </div>
            <div class="cta-terminal" aria-label="Quick start command">
                <div class="cta-terminal-bar">
                    <div class="cta-terminal-bar-left">
                        <span class="dot-red"></span>
                        <span class="dot-yellow"></span>
                        <span class="dot-green"></span>
                        <span class="cta-terminal-label">bash &bull; Terminal</span>
                    </div>
                    <button type="button" class="code-copy-btn" onclick="copyCode(this)" aria-label="Copy terminal commands">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                        Copy
                    </button>
                </div>
                <pre class="code-block language-bash"><code class="language-bash"># Option 1: Using Composer
composer create-project veldora/veldora my-app
cd my-app

# Option 2: Using npx
# npx create-veldora-app my-app

# Run migrations & scaffold auth
php veldora migrate
php veldora make:auth

# Start the dev server
php veldora serve</code></pre>
            </div>
        </div>
    </section>

</main>
@endsection
