@extends('layouts.app')

@section('page_title', 'Terms of Service')
@section('page_title_suffix', 'Veldora Framework')
@section('meta_desc', 'Terms of service for the Veldora open-source project and documentation website.')

@section('content')
<main class="page-container static-prose-page">

    <div class="static-prose-header">
        <h1 class="static-page-title" style="font-size:2.2rem;">Terms of Service</h1>
        <p class="static-prose-meta">Last updated: August 30, 2026</p>
    </div>

    <section class="static-section">
        <h2 class="static-section-title">1. Acceptance of Terms</h2>
        <p>
            By accessing this website or using the Veldora PHP Framework and its associated tools, you agree to comply with and be bound by these terms.
        </p>
    </section>

    <section class="static-section">
        <h2 class="static-section-title">2. Open Source License</h2>
        <p>
            The Veldora framework codebase is licensed under the permissive <a href="/license" style="color:#a78bfa;text-decoration:none;">MIT License</a>. You are free to use, copy, modify, merge, publish, distribute, sublicense, and sell copies of the software subject to the terms of the MIT license.
        </p>
    </section>

    <section class="static-section">
        <h2 class="static-section-title">3. Disclaimer of Warranty</h2>
        <p>
            The software and documentation are provided "AS IS", without warranty of any kind, express or implied, including but not limited to the warranties of merchantability, fitness for a particular purpose, and noninfringement.
        </p>
    </section>

    <section class="static-section">
        <h2 class="static-section-title">4. Limitation of Liability</h2>
        <p>
            In no event shall the authors or copyright holders be liable for any claim, damages, or other liability arising from the use of the software.
        </p>
    </section>

    <div class="static-back-nav">
        <a href="/privacy" class="btn btn-ghost btn-sm">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
            Privacy Policy
        </a>
        <a href="/license" class="btn btn-ghost btn-sm">
            MIT License
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
        </a>
    </div>

</main>
@endsection
