@extends('layouts.app')

@section('page_title', 'Frequently Asked Questions')
@section('page_title_suffix', 'Veldora PHP Framework')
@section('meta_desc', 'Frequently asked questions about Veldora PHP Framework — architecture, features, CLI commands, UI components, and production deployment.')

@section('content')
<main class="page-container" style="max-width:960px;">

    <div class="static-page-hero">
        <div class="section-label" aria-hidden="true">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            Help &amp; Knowledge Base
        </div>
        <h1 class="static-page-title">Frequently Asked Questions</h1>
        <p class="static-page-sub">
            Everything you need to know about Veldora, its architecture, CLI tools, UI component library, and how it compares to other frameworks.
        </p>
    </div>

    <?php foreach ($faqs as $catIndex => $category): ?>
        <div class="faq-category-group">
            <h2 class="faq-category-title">
                <span class="faq-category-dot"></span>
                <?= htmlspecialchars($category['category'], ENT_QUOTES, 'UTF-8') ?>
            </h2>

            <div class="faq-list">
                <?php foreach ($category['items'] as $itemIndex => $item): ?>
                    <details class="faq-accordion-item" ontoggle="this.style.borderColor = this.open ? '#8b5cf6' : 'var(--border)'">
                        <summary class="faq-accordion-summary">
                            <span><?= htmlspecialchars($item['q'], ENT_QUOTES, 'UTF-8') ?></span>
                            <span class="faq-chevron-icon" aria-hidden="true">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                            </span>
                        </summary>
                        <div class="faq-accordion-body">
                            <?= $item['a'] ?>
                        </div>
                    </details>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="static-cta-box" style="text-align:center;margin-top:3rem;">
        <h3 class="static-cta-title">Still have questions?</h3>
        <p class="static-cta-sub" style="margin-bottom:1.5rem;">Explore the official documentation or join the community on GitHub.</p>
        <div style="display:flex;justify-content:center;gap:12px;flex-wrap:wrap;">
            <a href="/docs" class="btn btn-primary btn-sm">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Browse Documentation
            </a>
            <a href="https://github.com/veldorahq/veldora/discussions" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-sm">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                Ask on GitHub Discussions
            </a>
        </div>
    </div>

</main>

<style>
.faq-chevron-icon {
    flex-shrink: 0;
    color: var(--text-dim);
    display: flex;
    align-items: center;
    transition: transform 0.2s ease, color 0.2s;
}
details[open] .faq-chevron-icon {
    transform: rotate(180deg);
    color: var(--accent);
}
details summary::-webkit-details-marker { display: none; }
</style>
@endsection
