@extends('layouts.app')

@section('page_title', $component['name'] . ' Component')
@section('page_title_suffix', 'Veldora UI')
@section('meta_desc', $component['desc'])

@section('content')
<div class="comp-detail-layout" id="main-content">

    <!-- Sidebar -->
    <aside class="comp-detail-sidebar" aria-label="Component Navigation">
        <div class="comp-sidebar-inner">
            <div class="comp-sidebar-header">
                <a href="/components" class="comp-sidebar-back">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                    All Components
                </a>
            </div>
            <?php foreach ($categories as $catKey => $cat): ?>
                <div class="comp-sidebar-group">
                    <div class="comp-sidebar-group-label">
                        <?= $cat['icon'] ?>
                        <?= htmlspecialchars($cat['label']) ?>
                    </div>
                    <ul class="comp-sidebar-list">
                        <?php foreach ($cat['items'] as $slug): ?>
                            <?php
                                $compItem = null;
                                foreach ($allComponents as $c) {
                                    if ($c['id'] === $slug) { $compItem = $c; break; }
                                }
                                $isActive = $slug === $currentSlug;
                            ?>
                            <li>
                                <a href="/components/<?= htmlspecialchars($slug) ?>"
                                   class="comp-sidebar-link <?= $isActive ? 'active' : '' ?>">
                                    <?= htmlspecialchars($compItem['name'] ?? ucfirst($slug)) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="comp-detail-main">
        <div class="comp-detail-header">
            <div>
                <div class="comp-detail-breadcrumb">
                    <a href="/components">Components</a>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                    <span><?= htmlspecialchars($component['name']) ?></span>
                </div>
                <h1 class="comp-detail-title"><?= htmlspecialchars($component['name']) ?></h1>
                <p class="comp-detail-desc"><?= htmlspecialchars($component['desc']) ?></p>
            </div>
            <div class="comp-detail-meta">
                <button type="button" class="comp-cli-badge" onclick="copyCliBadge(this, '<?= htmlspecialchars($component['cli'], ENT_QUOTES, 'UTF-8') ?>')" title="Click to copy">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                    <span><?= htmlspecialchars($component['cli']) ?></span>
                </button>
                <span class="comp-detail-tag">&lt;x-<?= htmlspecialchars($component['id']) ?>&gt;</span>
            </div>
        </div>

        <?php foreach ($component['variations'] as $vIdx => $variation): ?>
            <?php $vid = 'var-' . $component['id'] . '-' . $vIdx; ?>
            <section class="comp-variation-section" aria-labelledby="vtitle-<?= $vid ?>">
                <div class="comp-variation-header">
                    <h2 id="vtitle-<?= $vid ?>" class="comp-variation-title"><?= htmlspecialchars($variation['title']) ?></h2>
                    <p class="comp-variation-desc"><?= $variation['desc'] ?></p>
                </div>
                <div class="comp-box" id="box-<?= $vid ?>">
                    <div class="comp-box-header">
                        <div class="comp-tabs-list" role="tablist">
                            <button type="button" role="tab" class="comp-tab-btn tab-btn-preview active"
                                    id="tab-prev-<?= $vid ?>" aria-selected="true" aria-controls="panel-prev-<?= $vid ?>"
                                    onclick="switchCompTab('<?= $vid ?>', 'preview')">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                Preview
                            </button>
                            <button type="button" role="tab" class="comp-tab-btn tab-btn-code"
                                    id="tab-code-<?= $vid ?>" aria-selected="false" aria-controls="panel-code-<?= $vid ?>"
                                    onclick="switchCompTab('<?= $vid ?>', 'code')">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                                Code
                            </button>
                        </div>
                        <div class="comp-box-actions">
                            <button type="button" class="comp-copy-btn" onclick="copyCompCode('<?= $vid ?>', this)">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                <span>Copy Code</span>
                            </button>
                        </div>
                    </div>
                    <div id="panel-prev-<?= $vid ?>" class="comp-preview" role="tabpanel">
                        <?= $variation['preview'] ?>
                    </div>
                    <div id="panel-code-<?= $vid ?>" class="comp-code" role="tabpanel" style="display:none">
                        <pre class="comp-code-pre language-markup no-copy-btn"><code class="language-markup" data-comp-code="<?= $vid ?>"><?= htmlspecialchars($variation['code'], ENT_QUOTES, 'UTF-8') ?></code></pre>
                    </div>
                </div>
            </section>
        <?php endforeach; ?>

        <div class="docs-nav">
            <?php
                $flatSlugs = [];
                foreach ($categories as $cat) { foreach ($cat['items'] as $s) $flatSlugs[] = $s; }
                $flatSlugs = array_unique($flatSlugs);
                $curIdx    = array_search($currentSlug, $flatSlugs, true);
                $prevSlug  = ($curIdx > 0) ? $flatSlugs[$curIdx - 1] : null;
                $nextSlug  = ($curIdx !== false && $curIdx < count($flatSlugs) - 1) ? $flatSlugs[$curIdx + 1] : null;
                function getCompNameFn(array $all, string $sl): string {
                    foreach ($all as $c) { if ($c['id'] === $sl) return $c['name']; }
                    return ucfirst($sl);
                }
            ?>
            <?php if ($prevSlug): ?>
                <a href="/components/<?= htmlspecialchars($prevSlug) ?>" class="docs-nav-link">
                    <span class="docs-nav-dir">← Previous</span>
                    <span class="docs-nav-title"><?= htmlspecialchars(getCompNameFn($allComponents, $prevSlug)) ?></span>
                </a>
            <?php endif; ?>
            <?php if ($nextSlug): ?>
                <a href="/components/<?= htmlspecialchars($nextSlug) ?>" class="docs-nav-link next">
                    <span class="docs-nav-dir">Next →</span>
                    <span class="docs-nav-title"><?= htmlspecialchars(getCompNameFn($allComponents, $nextSlug)) ?></span>
                </a>
            <?php endif; ?>
        </div>
    </main>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.Prism) {
            Prism.highlightAll();
        }
    });
</script>
@endsection

