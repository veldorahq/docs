@extends('layouts.app')

@section('page_title', 'Veldora')
@section('page_title_suffix', 'Modern PHP Framework')
@section('meta_desc', 'Veldora is a modern PHP framework with expressive syntax, a powerful CLI, Blade-inspired templates, guard-based auth, and a beautiful 21-component UI system.')

@section('content')
<main class="home-layout">

    <!-- ── Hero ─────────────────────────────────────────────────────── -->
    <section class="hero" aria-label="Hero">
        <div class="hero-badge">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="13 2 13 9 20 9"/><polygon points="22 12 12 2 2 12 2 22 22 22"/></svg>
            v0.4.0 — 21 Production UI Components
        </div>

        <h1 class="hero-title">
            The PHP Framework<br>
            <span>Built for Developers</span>
        </h1>

        <p class="hero-sub">
            Expressive syntax. Powerful CLI. Blade-inspired templates. Guard-based auth.
            A 21-component UI system. Everything you need — nothing you don't.
        </p>

        <div class="hero-actions">
            <a href="/docs" class="btn btn-primary" id="hero-get-started">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Read the Docs
            </a>
            <a href="/components" class="btn btn-secondary" id="hero-view-components">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                UI Components
            </a>
            <a href="https://github.com/veldorahq/veldora-vscode" class="btn btn-ghost" target="_blank" rel="noopener noreferrer" id="hero-github">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/></svg>
                GitHub
            </a>
        </div>

        <!-- Code preview -->
        <div class="hero-code" aria-label="Code example">
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

$router->get(\'/\', [HomeController::class, \'index\']);
$router->post(\'/login\', [AuthController::class, \'login\'])
       ->middleware(\'guest\');

$router->group([\'middleware\' => [\'auth\']], function ($r) {
    $r->get(\'/dashboard\', [DashboardController::class, \'index\']);
    $r->resource(\'/posts\', PostController::class);
});'
, ENT_QUOTES, 'UTF-8'); ?></code></pre>
        </div>
    </section>

    <!-- ── Stats ─────────────────────────────────────────────────────── -->
    <section class="stats-section" aria-label="Framework stats">
        <div class="stats-inner">
            <div class="stat-item">
                <div class="stat-number">21</div>
                <div class="stat-label">UI Components</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">40+</div>
                <div class="stat-label">CLI Commands</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?= $sectionCount ?></div>
                <div class="stat-label">Doc Sections</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">PHP 8.2+</div>
                <div class="stat-label">Required</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">MIT</div>
                <div class="stat-label">License</div>
            </div>
        </div>
    </section>

    <!-- ── Features ─────────────────────────────────────────────────── -->
    <section class="features-section" aria-label="Features">
        <div class="section-label">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></polygon></svg>
            Why Veldora?
        </div>
        <h2 class="section-title">Everything in one framework</h2>
        <p class="section-sub">Built with modern PHP 8.2+ features — strict types, constructor promotion, named arguments, and more.</p>

        <div class="features-grid">

            <article class="feature-card">
                <div class="feature-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                </div>
                <div class="feature-title">MVC Architecture</div>
                <p class="feature-desc">Clean separation of concerns with Controllers, Models, and Views with automatic PSR-4 autoloading.</p>
            </article>

            <article class="feature-card">
                <div class="feature-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                </div>
                <div class="feature-title">Template Engine</div>
                <p class="feature-desc">Blade-inspired <code>&#64;directives</code>, <code>&#123;&#123;&nbsp;expressions&nbsp;&#125;&#125;</code>, and <code>&#64;extends</code> layout inheritance.</p>
            </article>

            <article class="feature-card">
                <div class="feature-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                </div>
                <div class="feature-title">Powerful CLI</div>
                <p class="feature-desc">Scaffold controllers, models, middleware, migrations, and auth with <code>php&nbsp;veldora&nbsp;make:*</code>.</p>
            </article>

            <article class="feature-card">
                <div class="feature-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </div>
                <div class="feature-title">Guard-based Auth</div>
                <p class="feature-desc">Session and JWT guards, role-based access, <code>&#64;auth</code> directives, and rate limiting middleware.</p>
            </article>

            <article class="feature-card">
                <div class="feature-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
                </div>
                <div class="feature-title">Database ORM</div>
                <p class="feature-desc">Lightweight active-record ORM with query builder, migrations, seeders, and SQLite / MySQL / Postgres support.</p>
            </article>

            <article class="feature-card">
                <div class="feature-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                </div>
                <div class="feature-title">21 UI Components</div>
                <p class="feature-desc">Ready-to-use Button, Tabs, Accordion, Modal, Table, Toast and 15 more via <code>veldora&nbsp;add</code>.</p>
            </article>

            <article class="feature-card">
                <div class="feature-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div class="feature-title">Middleware Pipeline</div>
                <p class="feature-desc">Before/after hooks, signed URLs, CSRF protection, throttle, and email verification middleware — all chainable.</p>
            </article>

            <article class="feature-card">
                <div class="feature-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <div class="feature-title">Validation Engine</div>
                <p class="feature-desc">Fluent validation rules — required, email, min, max, unique, confirmed and custom rules with error bags.</p>
            </article>

            <article class="feature-card">
                <div class="feature-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
                </div>
                <div class="feature-title">VS Code Extension</div>
                <p class="feature-desc">Full syntax highlighting, 32 snippets, PHP embedding, and IntelliSense for <code>.veldora.php</code> files.</p>
            </article>

        </div>
    </section>

    <!-- ── Quick start CTA ──────────────────────────────────────────── -->
    <section class="cta-section" aria-label="Quick start">
        <div class="cta-inner">
            <div class="cta-content">
                <h2 class="cta-title">Start in seconds</h2>
                <p class="cta-sub">Create a new Veldora project with a single CLI command.</p>
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
                <pre class="code-block language-bash"><code class="language-bash"># Install Veldora Framework
composer create-project veldora/veldora my-app

# Scaffold a controller
php veldora make:controller PostController

# Start dev server
php -S localhost:8000 -t public</code></pre>
            </div>
            <a href="/docs" class="btn btn-primary cta-btn" id="cta-read-docs">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Full Documentation
            </a>
        </div>
    </section>

</main>
@endsection
