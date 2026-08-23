<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#09090b">
    <meta name="robots" content="index, follow">
    <title><?php echo $this->yieldSection('page_title', 'Veldora'); ?> · <?php echo $this->yieldSection('page_title_suffix', 'PHP Framework'); ?></title>
    <meta name="description" content="<?php echo $this->yieldSection('meta_desc', 'Veldora — A modern, expressive PHP framework with powerful CLI, template engine, auth scaffolding and a beautiful component system.'); ?>">
    <meta property="og:title" content="<?php echo $this->yieldSection('page_title', 'Veldora'); ?> · PHP Framework">
    <meta property="og:description" content="<?php echo $this->yieldSection('meta_desc', 'Modern PHP framework by Veldora.'); ?>">
    <meta property="og:type" content="website">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/app.css">
    <link rel="stylesheet" href="/css/veldora-ui.css">
    <link rel="stylesheet" href="/css/prism.css">
</head>
<body>

<?php $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH); ?>

<!-- ── Header ─────────────────────────────────────────────────────────── -->
<header class="header">
    <div class="header-inner">
        <!-- Mobile Sidebar Toggle -->
        <button type="button" class="header-mobile-toggle" id="sidebar-toggle-btn" onclick="toggleMobileNav(event)" aria-label="Toggle Navigation Menu">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <line x1="3" y1="6" x2="21" y2="6"/>
                <line x1="3" y1="12" x2="21" y2="12"/>
                <line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
        </button>

        <a href="/" class="header-brand" aria-label="Veldora Home">
            <div class="logo-icon">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="white" aria-hidden="true"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
            </div>
            <span>Veldora</span>
        </a>

        <nav class="header-nav" id="main-nav" aria-label="Main navigation">
            <a href="/" <?= $currentPath === '/' ? 'class="active"' : '' ?>>
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Home
            </a>
            <a href="/docs" <?= str_starts_with($currentPath, '/docs') ? 'class="active"' : '' ?>>
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                Documentation
            </a>
            <a href="/components" <?= $currentPath === '/components' ? 'class="active"' : '' ?>>
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Components
            </a>
            <a href="/extension" <?= $currentPath === '/extension' ? 'class="active"' : '' ?>>
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                VS Code
            </a>
        </nav>

        <div class="header-actions">
            <a href="https://github.com/veldorahq/veldora-core" target="_blank" rel="noopener noreferrer" title="View on GitHub" class="header-gh-link">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/></svg>
                <span class="sr-only">GitHub</span>
            </a>
            <a href="/docs/1-getting-started-installation" class="btn btn-primary btn-sm" style="padding:6px 14px;font-size:12.5px;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
                Get Started
            </a>
        </div>
    </div>
</header>

<!-- ── Mobile Navigation Drawer ──────────────────────────────────────── -->
<aside class="mobile-drawer" id="mobile-drawer" aria-label="Mobile Navigation">
    <div class="mobile-drawer-header">
        <a href="/" class="header-brand" style="margin:0;">
            <div class="logo-icon">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="white" aria-hidden="true"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
            </div>
            <span>Veldora</span>
        </a>
        <button type="button" class="mobile-drawer-close" onclick="closeMobileNav()" aria-label="Close Navigation">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>

    <div class="mobile-drawer-body">
        <div class="mobile-nav-label">Navigation</div>
        <nav class="mobile-nav-links">
            <a href="/" <?= $currentPath === '/' ? 'class="active"' : '' ?>>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Home
            </a>
            <a href="/docs" <?= str_starts_with($currentPath, '/docs') ? 'class="active"' : '' ?>>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                Documentation
            </a>
            <a href="/components" <?= $currentPath === '/components' ? 'class="active"' : '' ?>>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Components
            </a>
            <a href="/extension" <?= $currentPath === '/extension' ? 'class="active"' : '' ?>>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                VS Code Extension
            </a>
        </nav>
    </div>

    <div class="mobile-drawer-footer">
        <a href="https://github.com/veldorahq/veldora-core" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-sm" style="width:100%;justify-content:center;gap:8px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/></svg>
            GitHub Repository
        </a>
        <a href="/docs/1-getting-started-installation" class="btn btn-primary btn-sm" style="width:100%;justify-content:center;margin-top:8px;">
            Get Started
        </a>
    </div>
</aside>

<!-- Backdrop overlay for mobile drawer -->
<div class="sidebar-backdrop" id="sidebar-backdrop" onclick="closeMobileNav()" aria-hidden="true"></div>

<?php echo $this->yieldSection('content'); ?>

<!-- ── Footer ─────────────────────────────────────────────────────────── -->
<footer class="footer" aria-label="Site footer">
    <div class="footer-inner">

        <!-- Brand Column -->
        <div class="footer-brand-col">
            <a href="/" class="footer-logo" aria-label="Veldora Home">
                <div class="footer-logo-icon" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="white"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                </div>
                <span>Veldora</span>
            </a>
            <p class="footer-tagline">A modern PHP 8.2+ MVC framework you actually own. Zero magic, full control.</p>
            <div class="footer-badges">
                <span class="footer-badge">PHP 8.2+</span>
                <span class="footer-badge">MIT License</span>
                <span class="footer-badge">v1.0.0</span>
            </div>
        </div>

        <!-- Framework Links -->
        <div class="footer-col">
            <h4 class="footer-col-title">Framework</h4>
            <ul class="footer-links">
                <li><a href="/docs/1-getting-started-installation">Getting Started</a></li>
                <li><a href="/docs/2-routing-http-layer">Routing & HTTP</a></li>
                <li><a href="/docs/3-controllers-requests">Controllers</a></li>
                <li><a href="/docs/6-activerecord-models-query-builder">ActiveRecord ORM</a></li>
                <li><a href="/docs/8-authentication-system">Authentication</a></li>
                <li><a href="/docs/10-cli-console-make-commands">CLI Commands</a></li>
            </ul>
        </div>

        <!-- Resources Links -->
        <div class="footer-col">
            <h4 class="footer-col-title">Resources</h4>
            <ul class="footer-links">
                <li><a href="/docs">Documentation</a></li>
                <li><a href="/components">UI Components</a></li>
                <li><a href="/extension">VS Code Extension</a></li>
                <li><a href="/docs/18-api-json-resources">API Resources</a></li>
                <li><a href="/docs/19-testing-model-factories">Testing Guide</a></li>
                <li><a href="/docs/22-ai-context-prompt-ai-skills">AI Master Prompt</a></li>
            </ul>
        </div>

        <!-- Community Links -->
        <div class="footer-col">
            <h4 class="footer-col-title">Community</h4>
            <ul class="footer-links">
                <li>
                    <a href="https://github.com/veldorahq/veldora-core" target="_blank" rel="noopener noreferrer">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/></svg>
                        GitHub — veldora-core
                    </a>
                </li>
                <li>
                    <a href="https://github.com/veldorahq/veldora-docs" target="_blank" rel="noopener noreferrer">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/></svg>
                        GitHub — Docs Site
                    </a>
                </li>
                <li>
                    <a href="https://github.com/veldorahq" target="_blank" rel="noopener noreferrer">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        Veldora Organization
                    </a>
                </li>
                <li>
                    <a href="https://marketplace.visualstudio.com/items?itemName=veldora.veldora-vscode" target="_blank" rel="noopener noreferrer">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                        VS Code Marketplace
                    </a>
                </li>
            </ul>
        </div>

    </div><!-- /footer-inner -->

    <!-- Footer Bottom Bar -->
    <div class="footer-bottom">
        <div class="footer-bottom-inner">
            <span class="footer-copyright">
                &copy; <?= date('Y') ?> Veldora Framework &mdash; Built with
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="color:var(--accent);vertical-align:middle;"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                <a href="/">Veldora</a>. Released under the <a href="https://opensource.org/licenses/MIT" target="_blank" rel="noopener noreferrer">MIT License</a>.
            </span>
            <span class="footer-made">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="color:#ef4444;"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                Made for PHP developers
            </span>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/prism.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/components/prism-markup.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/components/prism-markup-templating.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/components/prism-php.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/components/prism-bash.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/components/prism-javascript.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/components/prism-json.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/components/prism-ini.min.js"></script>
<script src="/js/app.js"></script>
<?php echo $this->yieldSection('scripts'); ?>
</body>
</html>
