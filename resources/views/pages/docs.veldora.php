@extends('layouts.app')

@section('page_title', isset($section) ? $section['title'] : 'Documentation')
@section('page_title_suffix', 'Veldora Docs')
@section('meta_desc', isset($section) ? 'Veldora documentation: ' . $section['title'] . ' — Learn how to use the Veldora PHP framework.' : 'Full documentation for the Veldora PHP framework — routing, auth, templates, CLI, UI components and more.')

@section('content')
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
                placeholder="Search 22 chapters..."
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
                        $isActive  = isset($current) && ($current === $item['slug'] || str_ends_with($current, $item['slug']));
                        $levelCls  = 'level-' . $item['level'];
                        $activeCls = $isActive ? ' active' : '';
                    ?>
                    <li class="<?= $levelCls . $activeCls ?>">
                        <a href="/docs/<?= htmlspecialchars($item['slug'], ENT_QUOTES, 'UTF-8') ?>">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            <?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <!-- Sidebar footer -->
        <div class="sidebar-footer">
            <a href="https://github.com/veldorahq/veldora" target="_blank" rel="noopener noreferrer" class="sidebar-footer-link">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/></svg>
                GitHub Repo
            </a>
            <span class="sidebar-version">v1.0.0</span>
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
            <div class="doc-header-actions">
                <button type="button" class="btn-copy-page" id="btn-copy-page-docs" onclick="copyPageDocs(this)" title="Copy page as Markdown" aria-label="Copy page as Markdown">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                    </svg>
                    <span>Copy Page</span>
                </button>
            </div>
        </header>

        <!-- Hidden raw markdown container for copying -->
        <textarea id="raw-doc-markdown" style="display:none;" aria-hidden="true"><?= htmlspecialchars($section['content'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>

        <!-- Markdown content -->
        <article class="prose" id="doc-content">
            <?= $section['html'] ?>
        </article>

        <!-- ── AI Prompt Actions (shown on AI Context chapter) ── -->
        <?php if (isset($current) && (str_contains($current, 'ai') || str_contains($current, '22-'))): ?>
        <div class="ai-prompt-card" style="margin-top: 32px;">
            <div class="ai-prompt-card-header">
                <div class="ai-prompt-card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a2 2 0 0 1 2 2c0 .74-.4 1.39-1 1.73V7h1a7 7 0 0 1 7 7h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v1a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-1H1a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h1a7 7 0 0 1 7-7h1V5.73c-.6-.34-1-.99-1-1.73a2 2 0 0 1 2-2z"/></svg>
                    <span>Veldora AI Developer Assistant Prompt</span>
                </div>
                <div class="ai-prompt-card-actions">
                    <button type="button" class="btn btn-primary btn-sm" id="btn-copy-ai-master" onclick="copyAiMasterPrompt()">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                        Copy Full Master Prompt
                    </button>
                    <a href="/download/veldora-ai-prompt.md" class="btn btn-secondary btn-sm" id="btn-download-ai-prompt">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download as .md
                    </a>
                </div>
            </div>
            <p class="ai-prompt-card-desc">Paste this prompt at the start of any session with Claude, ChatGPT, Gemini, Cursor, or Copilot. It gives the model exact knowledge of Veldora's 30+ modules and prevents framework hallucinations.</p>
        </div>
        <?php endif; ?>

        <!-- ── AI Skills Banner (shows on all doc pages except AI chapter) ─────────────── -->
        <?php if (!isset($current) || (!str_contains($current, 'ai') && !str_contains($current, '22-'))): ?>
        <div class="ai-skills-banner" id="ai-skills-banner">
            <div class="ai-skills-inner">
                <div class="ai-skills-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2a2 2 0 0 1 2 2c0 .74-.4 1.39-1 1.73V7h1a7 7 0 0 1 7 7h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v1a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-1H1a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h1a7 7 0 0 1 7-7h1V5.73c-.6-.34-1-.99-1-1.73a2 2 0 0 1 2-2z"/></svg>
                </div>
                <div class="ai-skills-text">
                    <div class="ai-skills-title">Building with AI Assistants?</div>
                    <div class="ai-skills-sub">Copy the Veldora AI Developer Prompt to teach ChatGPT, Claude, Cursor, or Gemini all framework APIs for error-free coding.</div>
                </div>
                <div class="ai-skills-actions">
                    <a href="/docs/22-ai-context-prompt-ai-skills" class="btn btn-primary btn-sm" id="ai-skills-view-prompt">
                        Get AI Prompt
                    </a>
                    <button type="button" class="btn btn-secondary btn-sm" id="ai-skills-dismiss" onclick="dismissAiBanner()" aria-label="Dismiss banner">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
            </div>
        </div>
        <?php endif; ?>

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
@endsection

@section('scripts')
<script>
// Keyboard navigation between sections
document.addEventListener('keydown', (e) => {
    if (document.activeElement.tagName === 'INPUT' || document.activeElement.tagName === 'TEXTAREA') return;
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

// AI banner dismiss
function dismissAiBanner() {
    const banner = document.getElementById('ai-skills-banner');
    if (banner) {
        banner.style.opacity = '0';
        banner.style.transform = 'translateY(-8px)';
        setTimeout(() => banner.style.display = 'none', 300);
        try { sessionStorage.setItem('veldora_ai_banner_dismissed', '1'); } catch(e) {}
    }
}

if (typeof sessionStorage !== 'undefined') {
    try {
        if (sessionStorage.getItem('veldora_ai_banner_dismissed')) {
            const b = document.getElementById('ai-skills-banner');
            if (b) b.style.display = 'none';
        }
    } catch(e) {}
}

// Copy Master AI Prompt from the code block
function copyAiMasterPrompt() {
    const codeEl = document.querySelector('#doc-content pre code');
    if (!codeEl) return;
    const text = codeEl.innerText || codeEl.textContent;
    navigator.clipboard.writeText(text).then(() => {
        const btn = document.getElementById('btn-copy-ai-master');
        if (btn) {
            btn.innerHTML = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Copied to Clipboard!';
            btn.classList.add('btn-success');
            setTimeout(() => {
                btn.innerHTML = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg> Copy Full Master Prompt';
                btn.classList.remove('btn-success');
            }, 2500);
        }
    });
}
</script>
@endsection
