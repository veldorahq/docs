@extends('layouts.app')

@section('page_title', 'VS Code Extension')
@section('page_title_suffix', 'Veldora')
@section('meta_desc', 'Official VS Code extension for Veldora PHP Framework. Complete syntax highlighting for .veldora.php templates, 32 snippets, bracket matching, and PHP IntelliSense.')

@section('content')
<main class="extension-page">

    <!-- ── Extension Hero ───────────────────────────────────────────── -->
    <section class="ext-hero">
        <div class="ext-badge">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
            Official VS Code Extension · v0.5.6
        </div>

        <h1 class="ext-title">
            First-Class Tooling for<br>
            <span>.veldora.php Templates</span>
        </h1>

        <p class="ext-sub">
            Syntax highlighting, 32 intelligent snippets, directive bracket matching,
            and seamless PHP IntelliSense inside your template files.
        </p>

        <!-- Install Terminal Box -->
        <div class="ext-install-box">
            <div class="ext-install-bar">
                <span class="dot-red"></span>
                <span class="dot-yellow"></span>
                <span class="dot-green"></span>
                <span class="ext-install-label">Terminal · Install Extension</span>
            </div>
            <div class="ext-install-cmd">
                <code>code --install-extension veldora.veldora-vscode</code>
                <button type="button" class="code-copy-btn" onclick="copyInstallCmd(this)">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                    Copy Command
                </button>
            </div>
        </div>

        <div class="ext-actions">
            <a href="https://marketplace.visualstudio.com/items?itemName=veldora.veldora-vscode" target="_blank" rel="noopener noreferrer" class="btn btn-primary" id="ext-marketplace-btn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                View on Marketplace
            </a>
            <a href="https://github.com/veldorahq/veldora-vscode" target="_blank" rel="noopener noreferrer" class="btn btn-secondary" id="ext-github-btn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/></svg>
                GitHub Repository
            </a>
        </div>
    </section>

    <!-- ── Key Features ─────────────────────────────────────────────── -->
    <section class="ext-features-section">
        <div class="section-label" style="justify-content:center;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></polygon></svg>
            Features
        </div>
        <h2 class="section-title">Built for developer happiness</h2>
        <p class="section-sub">Writing Veldora templates feels as natural and fast as writing Blade or JSX.</p>

        <div class="ext-features-grid">
            <div class="ext-feature-card">
                <div class="ext-feature-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                </div>
                <h3>Syntax Highlighting</h3>
                <p>Full TextMate grammar highlighting for template tags, directives, and embedded PHP blocks with theme-adaptive colors.</p>
                <div class="ext-card-tags">
                    <span class="ext-tag">&#64;directives</span>
                    <span class="ext-tag">&#123;&#123; $variable &#125;&#125;</span>
                    <span class="ext-tag">&#123;!! $raw !!&#125;</span>
                </div>
            </div>

            <div class="ext-feature-card">
                <div class="ext-feature-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                </div>
                <h3>55+ Fast Snippets</h3>
                <p>Type snippet prefixes and press Tab to instantly expand complete template structures and boilerplate.</p>
                <div class="ext-card-tags">
                    <span class="ext-tag">v-if</span>
                    <span class="ext-tag">v-foreach</span>
                    <span class="ext-tag">v-extends</span>
                    <span class="ext-tag">v-comp</span>
                </div>
            </div>

            <div class="ext-feature-card">
                <div class="ext-feature-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="9" y1="3" x2="9" y2="21"/></svg>
                </div>
                <h3>Directive Bracket Matching</h3>
                <p>Pairs opening and closing directives with smart code folding and bracket colorization.</p>
                <div class="ext-card-tags">
                    <span class="ext-tag">&#64;if / &#64;endif</span>
                    <span class="ext-tag">&#64;section / &#64;endsection</span>
                    <span class="ext-tag">&#64;auth / &#64;endauth</span>
                </div>
            </div>

            <div class="ext-feature-card">
                <div class="ext-feature-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                </div>
                <h3>PHP IntelliSense</h3>
                <p>Variables, classes, methods, and global helper functions enjoy complete autocompletion inside template tags.</p>
                <div class="ext-card-tags">
                    <span class="ext-tag">auth()</span>
                    <span class="ext-tag">csrf_token()</span>
                    <span class="ext-tag">route()</span>
                    <span class="ext-tag">config()</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ── Snippet Reference Table ──────────────────────────────────── -->
    <section class="ext-snippets-section">
        <div class="section-label" style="justify-content:center;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
            Snippet Reference
        </div>
        <h2 class="section-title">All 32 Snippet Triggers</h2>
        <p class="section-sub">Type any prefix in a <code>.veldora.php</code> file and press <kbd>Tab</kbd>.</p>

        <div class="ext-table-wrap">
            <table class="ext-table">
                <thead>
                    <tr>
                        <th>Prefix</th>
                        <th>Expanded Template Code</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>v-if</code></td>
                        <td><code>&#64;if ($condition) ... &#64;endif</code></td>
                        <td>If conditional block</td>
                    </tr>
                    <tr>
                        <td><code>v-ifelse</code></td>
                        <td><code>&#64;if ($cond) ... &#64;else ... &#64;endif</code></td>
                        <td>If-Else conditional</td>
                    </tr>
                    <tr>
                        <td><code>v-foreach</code></td>
                        <td><code>&#64;foreach ($items as $item) ... &#64;endforeach</code></td>
                        <td>Foreach loop block</td>
                    </tr>
                    <tr>
                        <td><code>v-forelse</code></td>
                        <td><code>&#64;forelse ($items as $item) ... &#64;empty ... &#64;endforelse</code></td>
                        <td>Foreach with fallback</td>
                    </tr>
                    <tr>
                        <td><code>v-extends</code></td>
                        <td><code>&#64;extends('layouts.app')</code></td>
                        <td>Extend master layout</td>
                    </tr>
                    <tr>
                        <td><code>v-section</code></td>
                        <td><code>&#64;section('content') ... &#64;endsection</code></td>
                        <td>Define content section</td>
                    </tr>
                    <tr>
                        <td><code>v-yield</code></td>
                        <td><code>&#64;yield('title', 'Default')</code></td>
                        <td>Yield placeholder</td>
                    </tr>
                    <tr>
                        <td><code>v-auth</code></td>
                        <td><code>&#64;auth ... &#64;endauth</code></td>
                        <td>Show only for authenticated users</td>
                    </tr>
                    <tr>
                        <td><code>v-guest</code></td>
                        <td><code>&#64;guest ... &#64;endguest</code></td>
                        <td>Show only for guests</td>
                    </tr>
                    <tr>
                        <td><code>v-comp</code></td>
                        <td><code>&lt;x-button variant="primary"&gt;...&lt;/x-button&gt;</code></td>
                        <td>Veldora UI component</td>
                    </tr>
                    <tr>
                        <td><code>v-csrf</code></td>
                        <td><code>&lt;input type="hidden" name="_token" value="&#123;&#123; csrf_token() &#125;&#125;"&gt;</code></td>
                        <td>CSRF hidden token input</td>
                    </tr>
                    <tr>
                        <td><code>v-method</code></td>
                        <td><code>&lt;input type="hidden" name="_method" value="PUT"&gt;</code></td>
                        <td>HTTP method spoofing</td>
                    </tr>
                    <tr>
                        <td><code>v-esc</code></td>
                        <td><code>&#123;&#123; $variable &#125;&#125;</code></td>
                        <td>Escaped variable echo</td>
                    </tr>
                    <tr>
                        <td><code>v-raw</code></td>
                        <td><code>&#123;!! $html !!&#125;</code></td>
                        <td>Unescaped raw output</td>
                    </tr>
                    <tr>
                        <td><code>v-dump</code></td>
                        <td><code>&#64;dump($variable)</code></td>
                        <td>Debug dump variable</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

</main>
@endsection

@section('scripts')
<script>
function copyInstallCmd(btn) {
    navigator.clipboard.writeText('code --install-extension veldora.veldora-vscode').then(() => {
        btn.innerHTML = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Copied!';
        setTimeout(() => {
            btn.innerHTML = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg> Copy Command';
        }, 2000);
    });
}
</script>
@endsection
