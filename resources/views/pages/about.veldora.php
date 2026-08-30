@extends('layouts.app')

@section('page_title', 'About Veldora')
@section('page_title_suffix', 'The Philosophy & Mission')
@section('meta_desc', 'Learn about Veldora — the modern PHP 8.2+ framework designed for developer joy, high performance, and zero bloat.')

@section('content')
<main class="page-container" style="max-width:880px;margin:2rem auto;padding:1rem 1.5rem;">

    <div style="text-align:center;margin-bottom:3.5rem;">
        <div style="display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;color:#8b5cf6;background:rgba(139,92,246,0.1);padding:4px 12px;border-radius:9999px;border:1px solid rgba(139,92,246,0.25);margin-bottom:1rem;">
            About the Framework
        </div>
        <h1 style="font-size:2.6rem;font-weight:800;color:#f4f4f5;margin:0 0 0.85rem;">The Veldora Philosophy</h1>
        <p style="font-size:1.15rem;color:#a1a1aa;max-width:620px;margin:0 auto;line-height:1.6;">
            A fast, expressive PHP 8.2+ framework created to give developers complete ownership, instant CLI workflows, and a modern component-driven UI system.
        </p>
    </div>

    <section style="margin-bottom:3rem;line-height:1.8;color:#d4d4d8;font-size:1.05rem;">
        <h2 style="font-size:1.6rem;font-weight:700;color:#f4f4f5;margin-bottom:1rem;border-bottom:1px solid #27272a;padding-bottom:0.5rem;">Why Veldora was Built</h2>
        <p>
            Modern web development often forces developers into two extremes: heavyweight frameworks with massive vendor footprints that take seconds to boot, or micro-frameworks requiring dozens of third-party packages just to build a simple authentication flow.
        </p>
        <p>
            <strong>Veldora</strong> bridges this gap. It provides an intuitive, elegant developer experience—complete with routing, ActiveRecord ORM, session authentication, queue workers, mailers, and a 41+ UI component system—while keeping the entire framework core lightweight, self-contained, and blazingly fast.
        </p>
    </section>

    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(260px, 1fr));gap:1.5rem;margin-bottom:3.5rem;">
        <div style="background:#18181b;border:1px solid #27272a;border-radius:0.875rem;padding:1.5rem;">
            <div style="font-size:1.5rem;margin-bottom:0.5rem;">⚡</div>
            <h3 style="font-size:1.15rem;font-weight:700;color:#f4f4f5;margin:0 0 0.5rem;">Zero-Bloat Core</h3>
            <p style="font-size:0.92rem;color:#a1a1aa;line-height:1.6;margin:0;">
                Only requires <code>psr/container</code>. No nested dependency trees or endless third-party updates slowing down your development.
            </p>
        </div>

        <div style="background:#18181b;border:1px solid #27272a;border-radius:0.875rem;padding:1.5rem;">
            <div style="font-size:1.5rem;margin-bottom:0.5rem;">🛠️</div>
            <h3 style="font-size:1.15rem;font-weight:700;color:#f4f4f5;margin:0 0 0.5rem;">48 Built-in Commands</h3>
            <p style="font-size:0.92rem;color:#a1a1aa;line-height:1.6;margin:0;">
                Complete CLI suite with <code>executeDirect()</code> technology, enabling instant execution in any environment or container.
            </p>
        </div>

        <div style="background:#18181b;border:1px solid #27272a;border-radius:0.875rem;padding:1.5rem;">
            <div style="font-size:1.5rem;margin-bottom:0.5rem;">🎨</div>
            <h3 style="font-size:1.15rem;font-weight:700;color:#f4f4f5;margin:0 0 0.5rem;">Multi-Aesthetic UI</h3>
            <p style="font-size:0.92rem;color:#a1a1aa;line-height:1.6;margin:0;">
                41+ ready-to-use components supporting Skeuomorphic 3D, Neumorphic Soft UI, Flat Minimalist, and Glassmorphic styling.
            </p>
        </div>
    </div>

    <section style="margin-bottom:3rem;line-height:1.8;color:#d4d4d8;font-size:1.05rem;">
        <h2 style="font-size:1.6rem;font-weight:700;color:#f4f4f5;margin-bottom:1rem;border-bottom:1px solid #27272a;padding-bottom:0.5rem;">Creator & Maintainer</h2>
        <p>
            Veldora is conceived, architected, and maintained by <strong>Shahriyar Fahim</strong> alongside open-source contributors around the globe.
        </p>
        <p>
            The project is 100% independent and open-source under the <a href="/license" style="color:#a78bfa;text-decoration:none;">MIT License</a>.
        </p>
    </section>

    <div style="background:#18181b;border:1px solid #27272a;border-radius:1rem;padding:2rem;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1.5rem;">
        <div>
            <h3 style="font-size:1.25rem;font-weight:700;color:#f4f4f5;margin:0 0 0.25rem;">Ready to try Veldora?</h3>
            <p style="font-size:0.92rem;color:#a1a1aa;margin:0;">Get started with our 5-minute quickstart guide.</p>
        </div>
        <div style="display:flex;gap:12px;">
            <a href="/docs" class="btn btn-primary btn-sm" style="padding:8px 18px;">Read the Docs</a>
            <a href="https://github.com/veldorahq/veldora" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-sm" style="padding:8px 18px;">GitHub</a>
        </div>
    </div>

</main>
@endsection
