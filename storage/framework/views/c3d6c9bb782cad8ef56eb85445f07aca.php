<?php $this->extend('layouts.app'); ?>

<?php $this->startSection('page_title', 'UI Components Catalog'); ?>
<?php $this->startSection('page_title_suffix', 'Veldora'); ?>
<?php $this->startSection('meta_desc', 'Explore 41+ production-ready Veldora UI components with multi-style variations, pure CSS animations, and live interactive previews.'); ?>

<?php $this->startSection('content'); ?>
<main class="components-catalog-page" id="main-content">

    <!-- ── Hero Section ─────────────────────────────────────────────────────── -->
    <header class="comp-catalog-hero">
        <div class="section-label" aria-hidden="true">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            Veldora UI Library
        </div>
        <h1 class="comp-catalog-title">Component Catalog</h1>
        <p class="comp-catalog-sub">
            41+ accessible, modular UI components engineered with pure CSS and zero external package lock-in. Click any component card to explore multiple design variants, code snippets, and live interactive previews.
        </p>

        <!-- Search Bar & Category Filter Pills -->
        <div class="comp-filter-wrap">
            <div class="comp-search-box">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="comp-catalog-search" placeholder="Search 41+ components by name or tag (e.g., button, spinner, modal)..." aria-label="Search components">
            </div>

            <div class="comp-pills-row" role="tablist" aria-label="Category filters">
                <button type="button" class="comp-filter-pill active" onclick="filterCatalog('all', this)">
                    All Components <span class="comp-pill-count">41</span>
                </button>
                <?php foreach ($categories as $catKey => $cat): ?>
                    <button type="button" class="comp-filter-pill" onclick="filterCatalog('<?= $catKey ?>', this)">
                        <?= $cat['icon'] ?>
                        <span><?= htmlspecialchars($cat['label']) ?></span>
                        <span class="comp-pill-count"><?= count($cat['items']) ?></span>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    </header>

    <!-- ── Category Sections & Component Grids ─────────────────────────────── -->
    <div class="comp-catalog-sections">
        <?php foreach ($categories as $catKey => $cat): ?>
            <section class="comp-cat-section" data-category="<?= $catKey ?>" id="cat-<?= $catKey ?>">
                <!-- Category Section Header -->
                <div class="comp-cat-header">
                    <div class="comp-cat-title-wrap">
                        <div class="comp-cat-icon"><?= $cat['icon'] ?></div>
                        <div>
                            <h2 class="comp-cat-title"><?= htmlspecialchars($cat['label']) ?></h2>
                            <span class="comp-cat-subtitle"><?= count($cat['items']) ?> components available</span>
                        </div>
                    </div>
                </div>

                <!-- Component Cards Grid -->
                <div class="comp-grid">
                    <?php foreach ($cat['items'] as $slug): ?>
                        <?php
                            $comp = null;
                            foreach ($components as $c) {
                                if ($c['id'] === $slug) { $comp = $c; break; }
                            }
                            $compName = $comp['name'] ?? ucfirst($slug);
                            $compDesc = $comp['desc'] ?? 'Customizable Veldora UI component.';
                            $compCli  = $comp['cli'] ?? "php veldora add {$slug}";
                        ?>
                        <article class="comp-card" data-slug="<?= htmlspecialchars($slug) ?>" data-name="<?= strtolower(htmlspecialchars($compName)) ?>">
                            <a href="/components/<?= htmlspecialchars($slug) ?>" class="comp-card-main-link" aria-label="View <?= htmlspecialchars($compName) ?> component styles">
                                <div class="comp-card-top">
                                    <h3 class="comp-card-name"><?= htmlspecialchars($compName) ?></h3>
                                    <span class="comp-card-tag">&lt;x-<?= htmlspecialchars($slug) ?>&gt;</span>
                                </div>
                                <p class="comp-card-desc"><?= htmlspecialchars($compDesc) ?></p>
                            </a>
                            <div class="comp-card-footer">
                                <a href="/components/<?= htmlspecialchars($slug) ?>" class="comp-card-btn">
                                    <span>Explore Variations</span>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                                </a>
                                <button type="button" class="comp-cli-mini" onclick="copyCliBadge(this, '<?= htmlspecialchars($compCli, ENT_QUOTES, 'UTF-8') ?>')" title="Copy CLI Command" aria-label="Copy CLI command">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                </button>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>
    </div>

</main>

<script>
function filterCatalog(category, btn) {
    document.querySelectorAll('.comp-filter-pill').forEach(p => p.classList.remove('active'));
    if (btn) btn.classList.add('active');

    const sections = document.querySelectorAll('.comp-cat-section');
    sections.forEach(sec => {
        if (category === 'all' || sec.getAttribute('data-category') === category) {
            sec.style.display = '';
        } else {
            sec.style.display = 'none';
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('comp-catalog-search');
    if (!searchInput) return;

    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        const cards = document.querySelectorAll('.comp-card');
        const sections = document.querySelectorAll('.comp-cat-section');

        cards.forEach(card => {
            const name = card.getAttribute('data-name') || '';
            const slug = card.getAttribute('data-slug') || '';
            const desc = card.querySelector('.comp-card-desc')?.innerText.toLowerCase() || '';

            if (!query || name.includes(query) || slug.includes(query) || desc.includes(query)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });

        // Hide empty sections during search
        sections.forEach(sec => {
            const visibleCards = sec.querySelectorAll('.comp-card:not([style*="display: none"])');
            sec.style.display = (visibleCards.length > 0) ? '' : 'none';
        });
    });
});
</script>
<?php $this->endSection(); ?>

