@extends('layouts.app')

@section('page_title', 'Changelog')
@section('page_title_suffix', 'Veldora')
@section('meta_desc', 'Full release history for the Veldora PHP Framework — veldora/framework core, veldora/ui components, and create-veldora-app scaffolder.')

@section('content')
<main class="changelog-page" id="main-content">

    <!-- ── Hero ─────────────────────────────────────────────────────────── -->
    <header class="cl-hero">
        <div class="cl-hero-badge">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
            Release History
        </div>
        <h1 class="cl-hero-title">Changelog</h1>
        <p class="cl-hero-sub">
            Full release history across the Veldora ecosystem — framework core,
            UI components, and the CLI scaffolder.
        </p>
        <nav class="cl-repo-tabs" aria-label="Repository filter">
            <button class="cl-tab active" data-filter="all" onclick="filterRepo('all', this)">
                All Packages
            </button>
            <?php foreach ($changelogs as $pkg): ?>
            <button class="cl-tab" data-filter="<?= htmlspecialchars($pkg['repo']) ?>" onclick="filterRepo('<?= htmlspecialchars($pkg['repo'], ENT_QUOTES) ?>', this)">
                <span class="cl-tab-dot cl-dot-<?= htmlspecialchars($pkg['color']) ?>"></span>
                <?= htmlspecialchars($pkg['repo']) ?>
            </button>
            <?php endforeach; ?>
        </nav>
    </header>

    <!-- ── Timeline ─────────────────────────────────────────────────────── -->
    <div class="cl-timeline" id="cl-timeline">

        <?php foreach ($changelogs as $pkg): ?>

        <section class="cl-package-block" data-repo="<?= htmlspecialchars($pkg['repo']) ?>" aria-labelledby="pkg-<?= htmlspecialchars($pkg['repo']) ?>">

            <!-- Package header -->
            <div class="cl-pkg-header">
                <div class="cl-pkg-icon cl-icon-<?= htmlspecialchars($pkg['color']) ?>">
                    <?php if ($pkg['icon'] === 'core'): ?>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                    <?php elseif ($pkg['icon'] === 'ui'): ?>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    <?php else: ?>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="4 17 10 11 4 5"/><line x1="12" y1="19" x2="20" y2="19"/></svg>
                    <?php endif; ?>
                </div>
                <div class="cl-pkg-info">
                    <h2 id="pkg-<?= htmlspecialchars($pkg['repo']) ?>" class="cl-pkg-name">
                        <?= htmlspecialchars($pkg['repo']) ?>
                    </h2>
                    <a href="<?= htmlspecialchars($pkg['github']) ?>" target="_blank" rel="noopener noreferrer" class="cl-pkg-gh">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/></svg>
                        View on GitHub
                    </a>
                </div>
            </div>

            <!-- Release entries -->
            <div class="cl-releases">
                <?php foreach ($pkg['releases'] as $i => $release): ?>
                <article class="cl-release <?= $i === 0 ? 'cl-release--latest' : '' ?>" id="<?= htmlspecialchars($pkg['repo']) ?>-<?= htmlspecialchars($release['version']) ?>">

                    <!-- Timeline dot -->
                    <div class="cl-dot-rail">
                        <div class="cl-rail-line"></div>
                        <div class="cl-release-dot cl-dot-<?= htmlspecialchars($pkg['color']) ?> <?= $i === 0 ? 'cl-release-dot--pulse' : '' ?>"></div>
                    </div>

                    <!-- Release content -->
                    <div class="cl-release-body">
                        <div class="cl-release-head">
                            <div class="cl-release-version-row">
                                <span class="cl-version cl-version-<?= htmlspecialchars($pkg['color']) ?>">
                                    v<?= htmlspecialchars($release['version']) ?>
                                </span>
                                <?php if ($release['tag']): ?>
                                <span class="cl-latest-badge">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                    Latest
                                </span>
                                <?php endif; ?>
                            </div>
                            <time class="cl-date" datetime="<?= htmlspecialchars($release['date']) ?>">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                <?= date('F j, Y', strtotime($release['date'])) ?>
                            </time>
                        </div>

                        <?php if (!empty($release['added'])): ?>
                        <div class="cl-section">
                            <div class="cl-section-label cl-label-added">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                Added
                            </div>
                            <ul class="cl-items">
                                <?php foreach ($release['added'] as $item): ?>
                                <li><?= $renderInlineMarkdown($item) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($release['fixed'])): ?>
                        <div class="cl-section">
                            <div class="cl-section-label cl-label-fixed">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                                Fixed
                            </div>
                            <ul class="cl-items">
                                <?php foreach ($release['fixed'] as $item): ?>
                                <li><?= $renderInlineMarkdown($item) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>

                        <a href="<?= htmlspecialchars($pkg['github']) ?>/releases/tag/v<?= htmlspecialchars($release['version']) ?>" class="cl-compare-link" target="_blank" rel="noopener noreferrer">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="18" cy="18" r="3"/><circle cx="6" cy="6" r="3"/><path d="M13 6h3a2 2 0 0 1 2 2v7"/><line x1="6" y1="9" x2="6" y2="21"/></svg>
                            View release on GitHub →
                        </a>
                    </div>

                </article>
                <?php endforeach; ?>
            </div>

        </section>

        <?php endforeach; ?>

    </div><!-- /cl-timeline -->

</main>
@endsection

@section('scripts')
<script>
function filterRepo(repo, btn) {
    // Update active tab
    document.querySelectorAll('.cl-tab').forEach(function(t) { t.classList.remove('active'); });
    btn.classList.add('active');

    // Show/hide package blocks
    document.querySelectorAll('.cl-package-block').forEach(function(block) {
        if (repo === 'all' || block.dataset.repo === repo) {
            block.style.display = '';
            // Animate in
            block.style.opacity = '0';
            block.style.transform = 'translateY(10px)';
            requestAnimationFrame(function() {
                block.style.transition = 'opacity .25s ease, transform .25s ease';
                block.style.opacity = '1';
                block.style.transform = 'translateY(0)';
            });
        } else {
            block.style.display = 'none';
        }
    });
}
</script>
@endsection
