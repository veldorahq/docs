@extends('layouts.app')

@section('page_title', 'Frequently Asked Questions')
@section('page_title_suffix', 'Veldora PHP Framework')
@section('meta_desc', 'Frequently asked questions about Veldora PHP Framework — architecture, features, CLI commands, UI components, and production deployment.')

@section('content')
<main class="page-container" style="max-width:960px;margin:2rem auto;padding:1rem 1.5rem;">

    <div style="text-align:center;margin-bottom:3rem;">
        <div style="display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;color:#8b5cf6;background:rgba(139,92,246,0.1);padding:4px 12px;border-radius:9999px;border:1px solid rgba(139,92,246,0.25);margin-bottom:1rem;">
            Help & Knowledge Base
        </div>
        <h1 style="font-size:2.5rem;font-weight:800;color:#f4f4f5;margin:0 0 0.75rem;">Frequently Asked Questions</h1>
        <p style="font-size:1.1rem;color:#a1a1aa;max-width:640px;margin:0 auto;line-height:1.6;">
            Everything you need to know about Veldora, its architecture, CLI tools, UI component library, and how it compares to other frameworks.
        </p>
    </div>

    <?php foreach ($faqs as $catIndex => $category): ?>
        <div style="margin-bottom:2.5rem;">
            <h2 style="font-size:1.35rem;font-weight:700;color:#f4f4f5;margin-bottom:1rem;display:flex;align-items:center;gap:8px;">
                <span style="display:inline-block;width:8px;height:8px;background:#8b5cf6;border-radius:50%;"></span>
                <?= htmlspecialchars($category['category'], ENT_QUOTES, 'UTF-8') ?>
            </h2>

            <div style="display:flex;flex-direction:column;gap:0.75rem;">
                <?php foreach ($category['items'] as $itemIndex => $item): ?>
                    <?php $id = "faq-{$catIndex}-{$itemIndex}"; ?>
                    <details class="faq-accordion-item" style="background:#18181b;border:1px solid #27272a;border-radius:0.75rem;padding:0.75rem 1.25rem;transition:border-color 0.2s;" ontoggle="this.style.borderColor = this.open ? '#8b5cf6' : '#27272a'">
                        <summary style="font-size:1rem;font-weight:600;color:#f4f4f5;cursor:pointer;list-style:none;display:flex;justify-content:space-between;align-items:center;padding:0.5rem 0;user-select:none;">
                            <span><?= htmlspecialchars($item['q'], ENT_QUOTES, 'UTF-8') ?></span>
                            <span class="faq-chevron" style="color:#71717a;font-size:1.25rem;transition:transform 0.2s;">＋</span>
                        </summary>
                        <div style="font-size:0.95rem;color:#a1a1aa;line-height:1.7;padding:0.75rem 0 0.5rem;border-top:1px solid #27272a;margin-top:0.5rem;">
                            <?= $item['a'] ?>
                        </div>
                    </details>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <div style="background:linear-gradient(135deg,rgba(139,92,246,0.1),rgba(99,102,241,0.05));border:1px solid rgba(139,92,246,0.25);border-radius:1rem;padding:2rem;text-align:center;margin-top:4rem;">
        <h3 style="font-size:1.35rem;font-weight:700;color:#f4f4f5;margin:0 0 0.5rem;">Still have questions?</h3>
        <p style="font-size:0.95rem;color:#a1a1aa;margin:0 0 1.5rem;">
            Explore the official documentation or join the community on GitHub.
        </p>
        <div style="display:flex;justify-content:center;gap:12px;flex-wrap:wrap;">
            <a href="/docs" class="btn btn-primary btn-sm" style="padding:8px 18px;">Browse Documentation</a>
            <a href="https://github.com/veldorahq/veldora/discussions" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-sm" style="padding:8px 18px;">Ask on GitHub Discussions</a>
        </div>
    </div>

</main>

<style>
details[open] summary .faq-chevron {
    transform: rotate(45deg);
    color: #8b5cf6;
}
details summary::-webkit-details-marker {
    display: none;
}
</style>
@endsection
