<?php $this->extend('layouts.app'); ?>

<?php $this->startSection('page_title', isset($section) ? $section['title'] : 'Documentation'); ?>
<?php $this->startSection('page_title_suffix', 'Veldora Docs'); ?>
<?php $this->startSection('meta_desc', isset($section) ? 'Veldora documentation: ' . $section['title'] . ' — Learn how to use the Veldora PHP framework.' : 'Full documentation for the Veldora PHP framework — routing, auth, templates, CLI, UI components and more.'); ?>

<?php $this->startSection('content'); ?>
<div class="layout-docs">

    <!-- ── Sidebar ──────────────────────────────────────────────────── -->
    <aside class="sidebar" id="docs-sidebar" aria-label="Documentation navigation">

        <!-- Search -->
        <div class="sidebar-search-wrap">
            <div class="sidebar-search-icon">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </div>
            <input
                id="docs-search"
                class="sidebar-search"
                type="search"
                placeholder="Search docs..."
                autocomplete="off"
                aria-label="Search documentation"
            >
            <kbd class="sidebar-search-kbd">/</kbd>
        </div>

        <!-- Nav tree -->
        <nav aria-label="Docs sections">
            <ul class="sidebar-nav" id="docs-nav">
                <?php foreach ($nav as $item): ?>
                    <?php
                        $isActive  = isset($current) && $current === $item['slug'];
                        $levelCls  = 'level-' . $item['level'];
                        $activeCls = $isActive ? ' active' : '';
                    ?>
                    <li class="<?= $levelCls . $activeCls ?>">
                        <a href="/docs/<?= htmlspecialchars($item['slug'], ENT_QUOTES, 'UTF-8') ?>">
                            <?php if ($item['level'] === 2): ?>
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            <?php endif; ?>
                            <?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <!-- Sidebar footer -->
        <div class="sidebar-footer">
            <a href="https://github.com/veldorahq/veldora-vscode/releases" target="_blank" rel="noopener noreferrer" class="sidebar-footer-link">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/></svg>
                View on GitHub
            </a>
            <span class="sidebar-version">v0.4.0</span>
        </div>
    </aside>

    <!-- ── Main Content ──────────────────────────────────────────────── -->
    <main class="content-wrap" id="main-content">

        <?php if (isset($error)): ?>
        <div class="doc-error">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
        <?php elseif (isset($section) && $section): ?>

        <!-- Breadcrumb -->
        <nav class="doc-breadcrumb" aria-label="Breadcrumb">
            <a href="/">Home</a>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
            <a href="/docs">Docs</a>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
            <span><?= htmlspecialchars($section['title'], ENT_QUOTES, 'UTF-8') ?></span>
        </nav>

        <!-- Section heading -->
        <header class="doc-header">
            <h1 id="doc-title"><?= htmlspecialchars($section['title'], ENT_QUOTES, 'UTF-8') ?></h1>
        </header>

        <!-- Markdown content -->
        <article class="prose" id="doc-content">
            <?= $section['html'] ?>
        </article>

        <!-- Prev / Next navigation -->
        <?php if ($prev || $next): ?>
        <nav class="doc-nav" aria-label="Page navigation">
            <?php if ($prev): ?>
            <a href="/docs/<?= htmlspecialchars($prev['slug'], ENT_QUOTES, 'UTF-8') ?>" class="doc-nav-btn prev" id="doc-nav-prev">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
                <div>
                    <span class="doc-nav-label">Previous</span>
                    <span class="doc-nav-title"><?= htmlspecialchars($prev['title'], ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            </a>
            <?php else: ?>
            <div></div>
            <?php endif; ?>

            <?php if ($next): ?>
            <a href="/docs/<?= htmlspecialchars($next['slug'], ENT_QUOTES, 'UTF-8') ?>" class="doc-nav-btn next" id="doc-nav-next">
                <div>
                    <span class="doc-nav-label">Next</span>
                    <span class="doc-nav-title"><?= htmlspecialchars($next['title'], ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <?php endif; ?>
        </nav>
        <?php endif; ?>

        <?php else: ?>
        <div class="doc-empty">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            <p>Select a section from the sidebar to begin reading.</p>
            <a href="/docs" class="btn btn-primary" style="margin-top:16px;">Browse All Docs</a>
        </div>
        <?php endif; ?>

    </main>
</div>
<?php $this->endSection(); ?>

<?php $this->startSection('scripts'); ?>
<script>
// Keyboard navigation between sections
document.addEventListener('keydown', (e) => {
    if (document.activeElement.tagName === 'INPUT') return;
    if (e.key === 'ArrowLeft') {
        const prev = document.getElementById('doc-nav-prev');
        if (prev) prev.click();
    }
    if (e.key === 'ArrowRight') {
        const next = document.getElementById('doc-nav-next');
        if (next) next.click();
    }
});

// Scroll active sidebar item into view
const activeItem = document.querySelector('.sidebar-nav li.active');
if (activeItem) {
    activeItem.scrollIntoView({ block: 'center', behavior: 'smooth' });
}
</script>
<?php $this->endSection(); ?>
