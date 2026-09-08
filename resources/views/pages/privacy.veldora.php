@extends('layouts.app')

@section('page_title', 'Privacy Policy')
@section('page_title_suffix', 'Veldora Framework')
@section('meta_desc', 'Privacy policy for the Veldora open-source project and documentation website.')

@section('content')
<main class="page-container static-prose-page">

    <div class="static-prose-header">
        <h1 class="static-page-title" style="font-size:2.2rem;">Privacy Policy</h1>
        <p class="static-prose-meta">Last updated: August 30, 2026</p>
    </div>

    <section class="static-section">
        <h2 class="static-section-title">1. Overview</h2>
        <p>
            Veldora is committed to respecting your privacy. As an open-source software project, we believe in radical transparency, minimal data collection, and user autonomy.
        </p>
    </section>

    <section class="static-section">
        <h2 class="static-section-title">2. Framework Telemetry</h2>
        <p>
            <strong>Veldora does not collect, transmit, or store any telemetry or usage tracking data.</strong> When you install Veldora via Composer or npx, or run commands via <code>php veldora</code>, no analytics, tracking beacons, or diagnostics are ever sent to external servers.
        </p>
    </section>

    <section class="static-section">
        <h2 class="static-section-title">3. Documentation Website</h2>
        <p>
            Our documentation website does not use tracking cookies, advertising trackers, or personal data harvesting scripts. Standard web server access logs (such as IP addresses and requested URLs) may be temporarily processed by hosting providers for DDoS mitigation, infrastructure health, and security defense.
        </p>
    </section>

    <section class="static-section">
        <h2 class="static-section-title">4. Third-Party Services</h2>
        <p>
            When downloading packages or visiting our source repositories, you are subject to the privacy policies of GitHub and Packagist.
        </p>
    </section>

    <section class="static-section">
        <h2 class="static-section-title">5. Contact</h2>
        <p>
            For any questions or privacy inquiries regarding Veldora, please open a discussion on our official <a href="https://github.com/veldorahq/veldora" target="_blank" rel="noopener noreferrer" style="color:#a78bfa;text-decoration:none;">GitHub repository</a>.
        </p>
    </section>

    <div class="static-back-nav">
        <a href="/" class="btn btn-ghost btn-sm">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
            Back to Home
        </a>
        <a href="/terms" class="btn btn-ghost btn-sm">
            Terms of Service
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
        </a>
    </div>

</main>
@endsection
