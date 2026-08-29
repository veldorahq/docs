@extends('layouts.app')

@section('page_title', 'UI Components')
@section('page_title_suffix', 'Veldora')
@section('meta_desc', 'Interactive showcase of all 39 Veldora UI components with live previews and template usage.')

@section('content')
<main class="components-page" id="main-content">

    <!-- ── Header ──────────────────────────────────────────────────────────── -->
    <header class="components-header">
        <div class="section-label" aria-hidden="true">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            Veldora UI Components
        </div>
        <h1 class="section-title">Component Showcase</h1>
        <p class="section-sub">
            41+ production-ready UI components built for Veldora. Click on any component to view all design styles, multi-variations, and interactive live previews.
        </p>
    </header>

    <!-- ── Category Directory Box ─────────────────────────────────────────── -->
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:24px;margin-bottom:36px;box-shadow:0 4px 20px rgba(0,0,0,0.15)">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;flex-wrap:wrap;gap:10px">
            <span style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--text-muted)">Components Directory</span>
            <span style="font-size:12px;color:var(--accent);font-weight:600">Select a component to view all design variations →</span>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:20px">
            <?php foreach ($categories as $catKey => $cat): ?>
                <div>
                    <div style="display:flex;align-items:center;gap:6px;font-size:12px;font-weight:700;color:var(--text);margin-bottom:8px;text-transform:uppercase;letter-spacing:0.04em">
                        <span style="color:var(--accent)"><?= $cat['icon'] ?></span>
                        <?= htmlspecialchars($cat['label']) ?>
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:6px">
                        <?php foreach ($cat['items'] as $slug): ?>
                            <?php
                                $compItem = null;
                                foreach ($components as $c) {
                                    if ($c['id'] === $slug) { $compItem = $c; break; }
                                }
                            ?>
                            <a href="/components/<?= htmlspecialchars($slug) ?>"
                               class="comp-cat-chip"
                               title="View all <?= htmlspecialchars($compItem['name'] ?? ucfirst($slug)) ?> styles">
                                <?= htmlspecialchars($compItem['name'] ?? ucfirst($slug)) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ── Quick Jump Navigation ──────────────────────────────────────────── -->
    <nav class="comp-quick-nav" aria-label="Component Quick Jump" style="margin-bottom:32px">
        <span class="comp-quick-nav-label">Quick Jump:</span>
        <div class="comp-quick-nav-links">
            <?php foreach ($components as $comp): ?>
                <a href="#comp-<?= htmlspecialchars($comp['id']) ?>" class="btn btn-ghost btn-sm"><?= htmlspecialchars($comp['name']) ?></a>
            <?php endforeach; ?>
        </div>
    </nav>

    <!-- ── Component Cards ─────────────────────────────────────────────────── -->
    <?php foreach ($components as $comp): ?>
        <?php
            $uid = 'comp-' . $comp['id'];
            $safeCode = htmlspecialchars($comp['code'], ENT_QUOTES, 'UTF-8');
        ?>
        <section class="component-section" id="<?= $uid ?>" aria-labelledby="title-<?= $uid ?>">
            <div class="component-section-header">
                <div class="component-section-info">
                    <div class="component-section-name">
                        <h2 id="title-<?= $uid ?>"><?= htmlspecialchars($comp['name']) ?></h2>
                        <span class="comp-badge">&lt;x-<?= htmlspecialchars($comp['id']) ?>&gt;</span>
                        <a href="/components/<?= htmlspecialchars($comp['id']) ?>"
                           class="btn btn-ghost btn-sm"
                           style="display:inline-flex;align-items:center;gap:5px;font-size:12px;color:var(--accent);border-color:rgba(124,110,245,0.25)"
                           title="Explore all design styles for <?= htmlspecialchars($comp['name']) ?>">
                            <span>All Styles & Variations</span>
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                        </a>
                        <button type="button" class="comp-cli-badge" onclick="copyCliBadge(this, '<?= htmlspecialchars($comp['cli'], ENT_QUOTES, 'UTF-8') ?>')" title="Click to copy command" aria-label="Copy CLI command <?= htmlspecialchars($comp['cli']) ?>">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                            </svg>
                            <span><?= htmlspecialchars($comp['cli']) ?></span>
                        </button>
                    </div>
                    <p class="component-desc"><?= htmlspecialchars($comp['desc']) ?></p>
                </div>
            </div>

            <!-- Unified Tabbed Box (Preview active by default, Code in Tab) -->
            <div class="comp-box" id="box-<?= $uid ?>">
                <div class="comp-box-header">
                    <div class="comp-tabs-list" role="tablist" aria-label="<?= htmlspecialchars($comp['name']) ?> view options">
                        <button type="button" role="tab" class="comp-tab-btn tab-btn-preview active" id="tab-prev-<?= $uid ?>"
                                aria-selected="true" aria-controls="panel-prev-<?= $uid ?>"
                                onclick="switchCompTab('<?= $uid ?>', 'preview')">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            Preview
                        </button>
                        <button type="button" role="tab" class="comp-tab-btn tab-btn-code" id="tab-code-<?= $uid ?>"
                                aria-selected="false" aria-controls="panel-code-<?= $uid ?>"
                                onclick="switchCompTab('<?= $uid ?>', 'code')">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                            Code
                        </button>
                    </div>

                    <div class="comp-box-actions">
                        <button type="button" class="comp-copy-btn" onclick="copyCompCode('<?= $uid ?>', this)" aria-label="Copy template code for <?= htmlspecialchars($comp['name']) ?>">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                            </svg>
                            <span>Copy Code</span>
                        </button>
                    </div>
                </div>

                <!-- Live Preview Panel (Visible by default) -->
                <div class="comp-preview-panel" id="panel-prev-<?= $uid ?>" role="tabpanel" aria-labelledby="tab-prev-<?= $uid ?>">
                    <?= $comp['preview'] ?>
                </div>

                <!-- Code Panel (Hidden by default, shown when Code tab is active) -->
                <div class="comp-code-panel hidden" id="panel-code-<?= $uid ?>" role="tabpanel" aria-labelledby="tab-code-<?= $uid ?>">
                    <pre class="code-block language-php"><code class="language-php"><?= $safeCode ?></code></pre>
                </div>
            </div>
        </section>
    <?php endforeach; ?>

</main>
@endsection
