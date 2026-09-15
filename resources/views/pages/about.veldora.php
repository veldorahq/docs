@extends('layouts.app')

@section('page_title', 'About Veldora')
@section('page_title_suffix', 'The Philosophy & Mission')
@section('meta_desc', 'Learn about Veldora — the modern PHP 8.2+ framework designed for developer joy, high performance, and zero bloat.')

@section('content')
<main class="page-container">

    <div class="static-page-hero">
        <div class="section-label" aria-hidden="true">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            About the Framework
        </div>
        <h1 class="static-page-title">The Veldora Philosophy</h1>
        <p class="static-page-sub">
            A fast, expressive PHP 8.2+ framework created to give developers complete ownership, instant CLI workflows, and a modern component-driven UI system.
        </p>
    </div>

    <section class="static-section">
        <h2 class="static-section-title">Why Veldora was Built</h2>
        <p>
            Modern web development often forces developers into two extremes: heavyweight frameworks with massive vendor footprints that take seconds to boot, or micro-frameworks requiring dozens of third-party packages just to build a simple authentication flow.
        </p>
        <p style="margin-top:1rem;">
            <strong>Veldora</strong> bridges this gap. It provides an intuitive, elegant developer experience — complete with routing, ActiveRecord ORM, session authentication, queue workers, mailers, and a 41+ UI component system — while keeping the entire framework core lightweight, self-contained, and blazingly fast.
        </p>
    </section>

    <div class="about-pillars-grid">
        <div class="about-pillar-card">
            <div class="about-pillar-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
            </div>
            <h3 class="about-pillar-title">Zero-Bloat Core</h3>
            <p class="about-pillar-desc">
                Only requires <code>psr/container</code>. No nested dependency trees or endless third-party updates slowing down your development.
            </p>
        </div>

        <div class="about-pillar-card">
            <div class="about-pillar-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
            </div>
            <h3 class="about-pillar-title">53 Built-in Commands</h3>
            <p class="about-pillar-desc">
                Complete CLI suite with <code>executeDirect()</code> technology, enabling instant execution in any environment or container.
            </p>
        </div>

        <div class="about-pillar-card">
            <div class="about-pillar-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            </div>
            <h3 class="about-pillar-title">Multi-Aesthetic UI</h3>
            <p class="about-pillar-desc">
                41+ ready-to-use components supporting Skeuomorphic 3D, Neumorphic Soft UI, Flat Minimalist, and Glassmorphic styling.
            </p>
        </div>
    </div>

    <section class="static-section">
        <h2 class="static-section-title">Creator &amp; Maintainer</h2>
        <p>
            Veldora is conceived, architected, and maintained by <strong>Shahriyar Fahim</strong> alongside open-source contributors around the globe.
        </p>
        <p style="margin-top:1rem;">
            The project is 100% independent and open-source under the <a href="/license" style="color:#a78bfa;text-decoration:none;">MIT License</a>.
        </p>
    </section>

    <div class="static-cta-box">
        <div>
            <h3 class="static-cta-title">Ready to try Veldora?</h3>
            <p class="static-cta-sub">Get started with our 5-minute quickstart guide.</p>
        </div>
        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <a href="/docs" class="btn btn-primary btn-sm">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                Read the Docs
            </a>
            <a href="https://github.com/veldorahq/veldora" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-sm">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/></svg>
                GitHub
            </a>
        </div>
    </div>

</main>
@endsection
