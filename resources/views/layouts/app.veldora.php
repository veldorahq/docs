<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#09090b">
    <meta name="robots" content="index, follow">
    <title>@yield('page_title', 'Veldora') · @yield('page_title_suffix', 'PHP Framework')</title>
    <meta name="description" content="@yield('meta_desc', 'Veldora — A modern, expressive PHP framework with powerful CLI, template engine, auth scaffolding and a beautiful component system.')">
    <meta property="og:title" content="@yield('page_title', 'Veldora') · PHP Framework">
    <meta property="og:description" content="@yield('meta_desc', 'Modern PHP framework by Veldora.')">
    <meta property="og:type" content="website">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/app.css?v=<?= time() ?>">
    <link rel="stylesheet" href="/css/veldora-ui.css?v=<?= time() ?>">
    <link rel="stylesheet" href="/css/prism.css?v=<?= time() ?>">
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
            <div class="logo-icon" style="background:none;padding:0;overflow:visible;">
                <img src="/favicon.svg" width="22" height="22" alt="Veldora" style="display:block;" aria-hidden="true">
            </div>
            <span>Veldora<?php if (str_starts_with($currentPath, '/docs')): ?><span class="header-docs-badge">Docs</span><?php endif; ?></span>
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
            <a href="/changelog" <?= $currentPath === '/changelog' ? 'class="active"' : '' ?>>
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                Changelog
            </a>
            <a href="/faq" <?= $currentPath === '/faq' ? 'class="active"' : '' ?>>
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                FAQ
            </a>
        </nav>

        <div class="header-actions">
            <a href="https://github.com/veldorahq/veldora" target="_blank" rel="noopener noreferrer" title="View on GitHub" class="header-gh-link">
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
            <div class="logo-icon" style="background:none;padding:0;overflow:visible;">
                <img src="/favicon.svg" width="22" height="22" alt="Veldora" style="display:block;" aria-hidden="true">
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
            <a href="/changelog" <?= $currentPath === '/changelog' ? 'class="active"' : '' ?>>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                Changelog
            </a>
            <a href="/faq" <?= $currentPath === '/faq' ? 'class="active"' : '' ?>>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                FAQ
            </a>
        </nav>
    </div>

    <div class="mobile-drawer-footer">
        <a href="https://github.com/veldorahq/veldora" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-sm" style="width:100%;justify-content:center;gap:8px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/></svg>
            GitHub
        </a>
        <a href="/docs/1-getting-started-installation" class="btn btn-primary btn-sm" style="width:100%;justify-content:center;margin-top:8px;">
            Get Started
        </a>
    </div>
</aside>

<!-- Backdrop overlay for mobile drawer -->
<div class="sidebar-backdrop" id="sidebar-backdrop" onclick="closeMobileNav()" aria-hidden="true"></div>

@yield('content')

<!-- ── Footer ─────────────────────────────────────────────────────────── -->
<footer class="footer" aria-label="Site footer">
    <div class="footer-inner">

        <!-- Brand Column -->
        <div class="footer-brand-col">
            <a href="/" class="footer-logo" aria-label="Veldora Home">
                <div class="footer-logo-icon" aria-hidden="true" style="background:none;padding:0;overflow:visible;">
                    <img src="/favicon.svg" width="22" height="22" alt="Veldora" style="display:block;">
                </div>
                <span>Veldora</span>
            </a>
            <p class="footer-tagline">Open-source PHP 8.2+ MVC framework with routing, auth, ORM, 48 CLI commands, mail, queues, and a 41-component UI system.</p>
            <div class="footer-badges">
                <span class="footer-badge">PHP 8.2+</span>
                <span class="footer-badge">MIT License</span>
                <span class="footer-badge">v<?= \Veldora\Framework\Foundation\Application::VERSION ?></span>
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
                <li><a href="/docs/10-cli-console-make-commands">48 CLI Commands</a></li>
            </ul>
        </div>

        <!-- Resources Links -->
        <div class="footer-col">
            <h4 class="footer-col-title">Resources</h4>
            <ul class="footer-links">
                <li><a href="/docs">Documentation</a></li>
                <li><a href="/components">UI Components</a></li>
                <li><a href="/extension">VS Code Extension</a></li>
                <li><a href="/changelog">Changelog</a></li>
                <li><a href="/faq">Frequently Asked Questions</a></li>
                <li><a href="/download/veldora-ai-prompt.md">AI Master Prompt</a></li>
            </ul>
        </div>

        <!-- About & Legal Links -->
        <div class="footer-col">
            <h4 class="footer-col-title">About & Legal</h4>
            <ul class="footer-links">
                <li><a href="/about">About Veldora</a></li>
                <li><a href="/privacy">Privacy Policy</a></li>
                <li><a href="/terms">Terms of Service</a></li>
                <li><a href="/license">MIT License</a></li>
                <li>
                    <a href="https://github.com/veldorahq/veldora" target="_blank" rel="noopener noreferrer">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/></svg>
                        GitHub
                    </a>
                </li>
            </ul>
        </div>

    </div><!-- /footer-inner -->

    <!-- Footer Bottom Bar -->
    <div class="footer-bottom">
        <div class="footer-bottom-inner">
<span class="footer-copyright">
    &copy; <?= date('Y') ?> Veldora Framework &mdash; Created by
    <svg width="13" height="13" viewBox="0 0 2048 1891" fill="none"
         aria-hidden="true"
         style="vertical-align:middle; display:inline-block; margin:0 2px;">
        <defs>
            <linearGradient id="footerVeldoraGradient" gradientUnits="userSpaceOnUse"
                x1="523.677" y1="1348.24" x2="1622.4" y2="270.409">
                <stop offset="0" stop-opacity="1" stop-color="rgb(35,0,156)"/>
                <stop offset="1" stop-opacity="1" stop-color="rgb(95,20,255)"/>
            </linearGradient>
        </defs>
        <path
            fill="url(#footerVeldoraGradient)"
            d="M 600.44 684.398 C 589.794 685.67 573.159 689.927 562.177 692.854 C 492.727 711.365 421.966 740.803 367.172 787.954 C 358.619 795.2 350.746 803.215 343.654 811.897 C 330.132 828.342 324.291 844.754 299.506 839.568 C 272.019 833.651 249.954 823.084 230.155 802.404 C 219.146 790.906 204.246 762.849 216.262 747.146 C 222.713 738.716 245.093 731.343 255.45 725.48 C 306.04 696.838 363.5 651.266 378.15 592.399 C 383.485 573.234 372.965 541.997 388.957 527.012 C 423.97 492.928 468.035 462.647 499.23 424.779 C 533.168 383.58 558.574 334.289 591.097 291.166 C 673.22 182.277 787.023 76.1554 903.482 4.77602 C 808.551 112.713 732.951 216.214 683.786 353.18 C 695.07 343.508 718.54 327.796 730.864 319.834 C 821.786 260.428 928.227 229.197 1036.83 230.061 C 1024.61 233.493 1008.1 240.535 996.444 245.679 C 931.924 274.166 871.611 316.077 826.458 370.571 C 898.064 351.248 961.277 361.246 1032.4 376.856 C 1123.2 396.787 1197.29 431.264 1272.08 485.256 C 1253.75 478.076 1244.96 477.658 1226.25 473.732 C 1174.64 462.905 1124.08 461.743 1071.83 468.476 C 1051.37 471.113 1038.3 476.196 1019.17 481.193 C 1044.5 485.774 1075.95 501.079 1097.96 514.329 C 1168.56 556.836 1219.72 630.497 1239.37 710.156 C 1264.52 812.056 1253.8 927.246 1199.08 1017.91 C 1198.12 973.062 1191.6 941.66 1163.09 906.574 C 1164.9 920.848 1164.12 945.435 1162.87 960.005 C 1153.04 1074.53 1077.73 1153.52 1026.5 1249.8 C 990.078 1318.99 974.393 1397.22 981.33 1475.11 C 982.695 1491.69 986.135 1517.12 990.938 1532.83 C 989.792 1445.72 1012.58 1396.35 1057.54 1324.24 L 1104.17 1249.85 L 1257.7 1003.79 C 1324.92 894.006 1390.63 783.314 1454.83 671.74 C 1481.65 625.117 1508.02 577.431 1535.3 531.133 C 1550.41 530.646 1566.71 530.723 1581.84 530.947 C 1682.26 532.436 1783.74 528.986 1884.06 531.258 C 1710.45 823.458 1531.46 1112.43 1347.19 1398.03 C 1258.46 1538.46 1168.47 1678.1 1077.24 1816.92 C 1056.35 1776.66 1035.02 1731.11 1015.11 1690.23 C 976.666 1611.25 934.25 1529.44 915.437 1443.43 C 891.475 1340.89 912.268 1238.69 961.806 1146.56 C 1008 1060.65 1044.03 982.327 1020.65 880.977 C 1019.76 875.784 1018.91 871.509 1017.7 866.37 C 1017.8 873.924 1017.64 883.804 1018.16 891.168 C 1016.12 910.478 1015.81 927.576 1011.16 946.652 C 994.961 1013.07 953.874 1069.45 920.799 1127.81 C 886.252 1188.77 860.871 1265.8 863.295 1336.74 C 868.749 1496.31 974.771 1681.09 1049.16 1818.15 C 911.039 1738.89 754.381 1635.34 708.602 1472.47 C 692.164 1413.98 694.132 1337.25 715.341 1280.04 C 676.108 1319.8 652.408 1361.8 633.791 1414.55 C 630.509 1423.85 625.488 1437.85 623.967 1447.28 C 619.946 1418.64 616.169 1393.72 615.295 1364.71 C 612.031 1279.22 638.641 1195.27 690.554 1127.26 C 703.802 1109.97 718.28 1093.65 733.876 1078.43 C 760.588 1050.79 795.944 1023.23 826.87 1000.88 C 783.246 1019.59 738.164 1044.04 694.378 1063.48 C 689.677 1065.41 683.902 1068.52 679.244 1070.87 C 680.383 1069.92 681.505 1068.95 682.608 1067.96 C 749.682 1006.73 867.223 892.95 871.075 797.474 C 872.263 768.035 862.368 744.287 842.565 722.889 C 844.678 722.073 846.797 721.275 848.922 720.494 C 885.918 706.928 911.53 711.433 947.057 727.871 C 919.212 704.513 892.381 689.479 857.524 678.621 C 802.246 661.402 742.586 657.69 685.762 669.175 C 676.61 671.025 660.736 675.185 652.935 679.425 C 664.768 681.395 676.165 682.345 688.284 684.733 C 717.042 690.399 747.413 702.95 771.465 719.846 C 778.482 724.775 785.134 731.531 791.998 737.162 C 752.184 728.371 709.49 729.997 669.93 739.674 C 612.439 753.736 541.024 785.022 509.497 837.72 C 504.663 845.801 501.443 853.12 506.431 861.902 C 507.589 863.941 509.505 866.246 509.929 868.571 C 510.442 871.383 508.707 874.156 506.943 876.193 C 494.246 890.858 471.244 897.757 452.498 898.649 C 430.073 899.717 404.663 892.006 388.021 876.528 C 380.468 869.503 375.078 859.792 374.967 849.284 C 374.919 844.811 376.473 838.122 379.943 835.044 C 390.502 825.678 417.772 823.643 431.485 819.614 C 442.139 816.484 452.316 812.118 462.237 807.165 C 497.914 789.354 571.225 714.168 600.44 684.398 z M 1007.06 1136.67 C 1067.86 1065.7 1108.09 984.578 1100.74 888.558 C 1094.91 803.315 1055.4 723.894 990.936 667.819 C 935.623 620.602 855.8 595.53 783.55 597.39 C 792.39 601.279 802.038 602.537 811.058 605.642 C 824.451 610.251 838.304 615.426 851.219 621.197 C 935.595 659.038 1002.57 727.342 1038.75 812.445 C 1093 942.884 1052.16 1045.51 984.973 1159.73 C 991.609 1153.77 1001.29 1143.72 1007.06 1136.67 z M 432.951 626.797 C 441.839 618.93 449.774 612.413 460.816 607.73 C 478.611 600.184 493.432 599.024 509.023 586.642 C 525.907 573.232 530.985 557.551 544.008 540.73 C 554.141 527.642 563.419 521.877 575.424 511.866 L 574.798 511.165 C 525.327 531.29 483.017 550.553 449.989 595.488 C 444.749 602.618 436.991 614.29 433.628 622.498 C 433.157 623.617 432.735 624.755 432.36 625.909 L 432.951 626.797 z"
        />
    </svg>
    <a href="/">Shahriyar Fahim</a>.
    Released under the
    <a href="https://opensource.org/licenses/MIT" target="_blank" rel="noopener noreferrer">MIT License</a>.
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
<script src="/js/app.js?v=<?= time() ?>"></script>
@yield('scripts')
</body>
</html>
