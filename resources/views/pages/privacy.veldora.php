@extends('layouts.app')

@section('page_title', 'Privacy Policy')
@section('page_title_suffix', 'Veldora Framework')
@section('meta_desc', 'Privacy policy for the Veldora open-source project and documentation website.')

@section('content')
<main class="page-container" style="max-width:880px;margin:2rem auto;padding:1rem 1.5rem;line-height:1.8;color:#d4d4d8;">

    <div style="margin-bottom:2.5rem;">
        <h1 style="font-size:2.4rem;font-weight:800;color:#f4f4f5;margin:0 0 0.5rem;">Privacy Policy</h1>
        <p style="color:#71717a;font-size:0.95rem;margin:0;">Last updated: August 30, 2026</p>
    </div>

    <section style="margin-bottom:2rem;">
        <h2 style="font-size:1.4rem;font-weight:700;color:#f4f4f5;margin-bottom:0.75rem;">1. Overview</h2>
        <p>
            Veldora is committed to respecting your privacy. As an open-source software project, we believe in radical transparency, minimal data collection, and user autonomy.
        </p>
    </section>

    <section style="margin-bottom:2rem;">
        <h2 style="font-size:1.4rem;font-weight:700;color:#f4f4f5;margin-bottom:0.75rem;">2. Framework Telemetry</h2>
        <p>
            <strong>Veldora does not collect, transmit, or store any telemetry or usage tracking data.</strong> When you install Veldora via Composer or npx, or run commands via <code>php veldora</code>, no analytics, tracking beacons, or diagnostics are ever sent to external servers.
        </p>
    </section>

    <section style="margin-bottom:2rem;">
        <h2 style="font-size:1.4rem;font-weight:700;color:#f4f4f5;margin-bottom:0.75rem;">3. Documentation Website</h2>
        <p>
            Our documentation website does not use tracking cookies, advertising trackers, or personal data harvesting scripts. Standard web server access logs (such as IP addresses and requested URLs) may be temporarily processed by hosting providers for DDoS mitigation, infrastructure health, and security defense.
        </p>
    </section>

    <section style="margin-bottom:2rem;">
        <h2 style="font-size:1.4rem;font-weight:700;color:#f4f4f5;margin-bottom:0.75rem;">4. Third-Party Services</h2>
        <p>
            When downloading packages or visiting our source repositories, you are subject to the privacy policies of GitHub and Packagist.
        </p>
    </section>

    <section style="margin-bottom:2rem;">
        <h2 style="font-size:1.4rem;font-weight:700;color:#f4f4f5;margin-bottom:0.75rem;">5. Contact</h2>
        <p>
            For any questions or privacy inquiries regarding Veldora, please open a discussion on our official <a href="https://github.com/veldorahq/veldora" target="_blank" rel="noopener noreferrer" style="color:#a78bfa;">GitHub repository</a>.
        </p>
    </section>

</main>
@endsection
