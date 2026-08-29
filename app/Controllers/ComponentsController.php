<?php

declare(strict_types=1);

namespace App\Controllers;

use Veldora\Framework\Http\Request;
use Veldora\Framework\Http\Response;
use Veldora\Framework\View\Engine;
use App\Helpers\DocsParser;

class ComponentsController
{
    public function __construct(protected Engine $view) {}

    public function index(Request $request): Response
    {
        $parser = new DocsParser();
        $nav    = $parser->getNav();

        $components = $this->getComponentsData();
        $categories = $this->getCategories();

        $html = $this->view->render('pages.components', [
            'nav'        => $nav,
            'components' => $components,
            'categories' => $categories,
        ]);

        return new Response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    public function show(string $component): Response
    {
        $parser = new DocsParser();
        $nav    = $parser->getNav();

        $detail = $this->getComponentDetail($component);

        if (!$detail) {
            return new Response('Not Found', 404);
        }

        $allComponents = $this->getComponentsData();
        $categories    = $this->getCategories();

        $html = $this->view->render('pages.component-detail', [
            'nav'           => $nav,
            'component'     => $detail,
            'allComponents' => $allComponents,
            'categories'    => $categories,
            'currentSlug'   => $component,
        ]);

        return new Response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    /**
     * Returns categories with their member component slugs.
     * @return array<string, array{label: string, icon: string, items: string[]}>
     */
    private function getCategories(): array
    {
        return [
            'forms' => [
                'label' => 'Forms & Inputs',
                'icon'  => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>',
                'items' => ['input', 'textarea', 'select', 'checkbox', 'radio', 'inputgroup', 'fileupload', 'datepicker', 'combobox'],
            ],
            'actions' => [
                'label' => 'Actions & Controls',
                'icon'  => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 3 19 12 5 21 5 3"/></svg>',
                'items' => ['button', 'dropdown', 'switch', 'datatable'],
            ],
            'feedback' => [
                'label' => 'Feedback & Status',
                'icon'  => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>',
                'items' => ['alert', 'badge', 'toast', 'spinner', 'progress', 'skeleton', 'empty', 'confirm', 'rating'],
            ],
            'display' => [
                'label' => 'Data Display',
                'icon'  => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>',
                'items' => ['card', 'table', 'stat', 'timeline', 'accordion', 'avatar', 'tooltip', 'tabs'],
            ],
            'navigation' => [
                'label' => 'Navigation',
                'icon'  => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>',
                'items' => ['navbar', 'breadcrumb', 'pagination', 'stepper', 'sidebar'],
            ],
            'layout' => [
                'label' => 'Layout & Overlay',
                'icon'  => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>',
                'items' => ['modal', 'drawer', 'popover', 'divider', 'container', 'footer'],
            ],
        ];
    }

    /**
     * Returns the multi-variation detail for a single component.
     * @return array{id:string,name:string,desc:string,cli:string,category:string,variations:array}|null
     */
    private function getComponentDetail(string $slug): ?array
    {
        $map = $this->getComponentDetailMap();
        if (isset($map[$slug])) {
            return $map[$slug];
        }

        // Fallback to getComponentsData()
        $all = $this->getComponentsData();
        foreach ($all as $c) {
            if ($c['id'] === $slug) {
                // Find category
                $catKey = 'display';
                foreach ($this->getCategories() as $k => $cat) {
                    if (in_array($slug, $cat['items'], true)) {
                        $catKey = $k;
                        break;
                    }
                }

                return [
                    'id'         => $c['id'],
                    'name'       => $c['name'],
                    'desc'       => $c['desc'],
                    'cli'        => $c['cli'],
                    'category'   => $catKey,
                    'variations' => [
                        [
                            'title'   => 'Default Style',
                            'desc'    => 'Standard live preview and template usage for &lt;x-' . htmlspecialchars($c['id']) . '&gt;.',
                            'preview' => $c['preview'],
                            'code'    => $c['code'],
                        ],
                    ],
                ];
            }
        }

        return null;
    }

    /**
     * Full multi-variation data keyed by component slug.
     * @return array<string, array>
     */
    private function getComponentDetailMap(): array
    {
        return [

            // ────────────────────────────────────────────────────────────────
            // BUTTON
            // ────────────────────────────────────────────────────────────────
            'button' => [
                'id'       => 'button',
                'name'     => 'Button',
                'desc'     => 'Versatile clickable button with multiple style variants, sizes, loading states, icon support, and button groups.',
                'cli'      => 'php veldora add button',
                'category' => 'actions',
                'variations' => [
                    [
                        'title'   => 'Solid Variants',
                        'desc'    => 'The default filled button style. Use <code>variant</code> to choose the semantic colour.',
                        'preview' => '<div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;padding:8px 0">
                            <button type="button" class="vui-btn vui-btn-primary vui-btn-md">Primary</button>
                            <button type="button" class="vui-btn vui-btn-secondary vui-btn-md">Secondary</button>
                            <button type="button" class="vui-btn vui-btn-success vui-btn-md">Success</button>
                            <button type="button" class="vui-btn vui-btn-danger vui-btn-md">Danger</button>
                            <button type="button" class="vui-btn vui-btn-warning vui-btn-md">Warning</button>
                        </div>',
                        'code'    => '<x-button variant="primary">Primary</x-button>
<x-button variant="secondary">Secondary</x-button>
<x-button variant="success">Success</x-button>
<x-button variant="danger">Danger</x-button>
<x-button variant="warning">Warning</x-button>',
                    ],
                    [
                        'title'   => 'Outline Variants',
                        'desc'    => 'Transparent background with a coloured border. Great for secondary actions.',
                        'preview' => '<div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;padding:8px 0">
                            <button type="button" class="vui-btn vui-btn-outline-primary vui-btn-md">Primary</button>
                            <button type="button" class="vui-btn vui-btn-outline-secondary vui-btn-md">Secondary</button>
                            <button type="button" class="vui-btn vui-btn-outline-danger vui-btn-md">Danger</button>
                            <button type="button" class="vui-btn vui-btn-ghost vui-btn-md">Ghost</button>
                        </div>',
                        'code'    => '<x-button variant="outline-primary">Primary</x-button>
<x-button variant="outline-secondary">Secondary</x-button>
<x-button variant="outline-danger">Danger</x-button>
<x-button variant="ghost">Ghost</x-button>',
                    ],
                    [
                        'title'   => 'Sizes',
                        'desc'    => 'Five size tokens: <code>xs</code>, <code>sm</code>, <code>md</code> (default), <code>lg</code>, <code>xl</code>.',
                        'preview' => '<div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;padding:8px 0">
                            <button type="button" class="vui-btn vui-btn-primary" style="font-size:11px;padding:3px 10px;border-radius:5px;">XS</button>
                            <button type="button" class="vui-btn vui-btn-primary vui-btn-sm">Small</button>
                            <button type="button" class="vui-btn vui-btn-primary vui-btn-md">Medium</button>
                            <button type="button" class="vui-btn vui-btn-primary vui-btn-lg">Large</button>
                            <button type="button" class="vui-btn vui-btn-primary" style="font-size:16px;padding:12px 28px;border-radius:10px;">XL</button>
                        </div>',
                        'code'    => '<x-button size="xs">XS</x-button>
<x-button size="sm">Small</x-button>
<x-button size="md">Medium</x-button>
<x-button size="lg">Large</x-button>
<x-button size="xl">XL</x-button>',
                    ],
                    [
                        'title'   => 'With Icons',
                        'desc'    => 'Slot in any SVG icon before or after the label text.',
                        'preview' => '<div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;padding:8px 0">
                            <button type="button" class="vui-btn vui-btn-primary vui-btn-md" style="display:inline-flex;align-items:center;gap:7px">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                Download
                            </button>
                            <button type="button" class="vui-btn vui-btn-secondary vui-btn-md" style="display:inline-flex;align-items:center;gap:7px">
                                Settings
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/><path d="M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
                            </button>
                            <button type="button" class="vui-btn vui-btn-ghost vui-btn-md" style="display:inline-flex;align-items:center;gap:7px">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                Add Item
                            </button>
                        </div>',
                        'code'    => '<x-button variant="primary" icon-left="download">Download</x-button>
<x-button variant="secondary" icon-right="settings">Settings</x-button>
<x-button variant="ghost" icon-left="plus">Add Item</x-button>',
                    ],
                    [
                        'title'   => 'Loading State',
                        'desc'    => 'Show a spinner inside the button when an async action is in progress.',
                        'preview' => '<div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;padding:8px 0">
                            <button type="button" class="vui-btn vui-btn-primary vui-btn-md" style="display:inline-flex;align-items:center;gap:8px" disabled>
                                <span class="vui-spinner vui-spinner-sm" role="status" aria-label="Loading" style="width:14px;height:14px;border-width:2px"></span>
                                Saving...
                            </button>
                            <button type="button" class="vui-btn vui-btn-secondary vui-btn-md" style="display:inline-flex;align-items:center;gap:8px" disabled>
                                <span class="vui-spinner vui-spinner-sm" role="status" aria-label="Loading" style="width:14px;height:14px;border-width:2px;--spinner-color:currentColor"></span>
                                Processing
                            </button>
                        </div>',
                        'code'    => '<x-button variant="primary" :loading="true">Saving...</x-button>
<x-button variant="secondary" :loading="true">Processing</x-button>',
                    ],
                    [
                        'title'   => 'Disabled State',
                        'desc'    => 'Prevents interaction and renders at reduced opacity.',
                        'preview' => '<div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;padding:8px 0">
                            <button type="button" class="vui-btn vui-btn-primary vui-btn-md" disabled aria-disabled="true" style="opacity:0.5;cursor:not-allowed">Disabled Primary</button>
                            <button type="button" class="vui-btn vui-btn-secondary vui-btn-md" disabled aria-disabled="true" style="opacity:0.5;cursor:not-allowed">Disabled Secondary</button>
                        </div>',
                        'code'    => '<x-button variant="primary" :disabled="true">Disabled Primary</x-button>
<x-button variant="secondary" :disabled="true">Disabled Secondary</x-button>',
                    ],
                ],
            ],

            // ────────────────────────────────────────────────────────────────
            // INPUT
            // ────────────────────────────────────────────────────────────────
            'input' => [
                'id'       => 'input',
                'name'     => 'Input',
                'desc'     => 'Text input field with label, helper text, icons, validation states, password toggle, and grouped addons.',
                'cli'      => 'php veldora add input',
                'category' => 'forms',
                'variations' => [
                    [
                        'title'   => 'Basic Input',
                        'desc'    => 'Standard text input with label and helper text.',
                        'preview' => '<div style="max-width:360px;display:flex;flex-direction:column;gap:16px">
                            <div class="vui-field">
                                <label class="vui-label" for="d-in-1">Full Name <span class="vui-required" aria-hidden="true">*</span></label>
                                <input id="d-in-1" type="text" placeholder="Jane Doe" class="vui-input" required>
                                <p class="vui-field-helper">Enter your official full name</p>
                            </div>
                            <div class="vui-field">
                                <label class="vui-label" for="d-in-2">Email</label>
                                <input id="d-in-2" type="email" placeholder="you@example.com" class="vui-input">
                            </div>
                        </div>',
                        'code'    => '<x-input name="name" label="Full Name" placeholder="Jane Doe" :required="true" helper="Enter your official full name" />
<x-input name="email" label="Email" type="email" placeholder="you@example.com" />',
                    ],
                    [
                        'title'   => 'Error & Validation State',
                        'desc'    => 'Show validation feedback with the <code>error</code> prop.',
                        'preview' => '<div style="max-width:360px;display:flex;flex-direction:column;gap:16px">
                            <div class="vui-field">
                                <label class="vui-label" for="d-in-err">Email Address</label>
                                <input id="d-in-err" type="email" value="invalid-email" class="vui-input vui-input-error" aria-invalid="true">
                                <p class="vui-field-error" role="alert">Please enter a valid email address.</p>
                            </div>
                            <div class="vui-field">
                                <label class="vui-label" for="d-in-ok">Username</label>
                                <input id="d-in-ok" type="text" value="veldora_dev" class="vui-input vui-input-success">
                                <p class="vui-field-helper" style="color:var(--success)">Username is available ✓</p>
                            </div>
                        </div>',
                        'code'    => '<x-input name="email" label="Email Address" type="email" value="invalid-email" error="Please enter a valid email address." />
<x-input name="username" label="Username" value="veldora_dev" helper="Username is available ✓" />',
                    ],
                    [
                        'title'   => 'With Prefix & Suffix Icons',
                        'desc'    => 'Decorative or functional icons inside the input boundary.',
                        'preview' => '<div style="max-width:360px;display:flex;flex-direction:column;gap:16px">
                            <div class="vui-field">
                                <label class="vui-label" for="d-search">Search</label>
                                <div style="position:relative">
                                    <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-dim)"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
                                    <input id="d-search" type="search" placeholder="Search components…" class="vui-input" style="padding-left:36px">
                                </div>
                            </div>
                            <div class="vui-field">
                                <label class="vui-label" for="d-url">Website</label>
                                <div style="display:flex;align-items:center;background:var(--surface-2);border:1px solid var(--border);border-radius:8px;overflow:hidden">
                                    <span style="padding:0 12px;color:var(--text-dim);font-size:13px;border-right:1px solid var(--border);white-space:nowrap">https://</span>
                                    <input id="d-url" type="text" placeholder="yoursite.com" style="background:transparent;border:none;outline:none;padding:10px 12px;color:var(--text);font-size:13.5px;width:100%">
                                </div>
                            </div>
                        </div>',
                        'code'    => '<x-input name="search" label="Search" icon-left="search" placeholder="Search components…" />
<x-input name="url" label="Website" prefix="https://" placeholder="yoursite.com" />',
                    ],
                    [
                        'title'   => 'Password with Toggle',
                        'desc'    => 'Password input with an eye icon to reveal or mask the value.',
                        'preview' => '<div style="max-width:360px">
                            <div class="vui-field">
                                <label class="vui-label" for="d-pw">Password</label>
                                <div style="position:relative">
                                    <input id="d-pw" type="password" value="s3cr3tP@ssw0rd" class="vui-input" style="padding-right:44px">
                                    <button type="button" onclick="var i=document.getElementById(\'d-pw\');i.type=i.type===\'password\'?\'text\':\'password\'" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text-dim);display:flex;align-items:center" aria-label="Toggle password">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>',
                        'code'    => '<x-input name="password" label="Password" type="password" :toggle-visible="true" />',
                    ],
                    [
                        'title'   => 'Disabled State',
                        'desc'    => 'Non-interactive input for read-only display.',
                        'preview' => '<div style="max-width:360px;display:flex;flex-direction:column;gap:14px">
                            <div class="vui-field">
                                <label class="vui-label" for="d-dis">Account ID</label>
                                <input id="d-dis" type="text" value="ACC-88291" class="vui-input" disabled style="opacity:0.5;cursor:not-allowed">
                            </div>
                        </div>',
                        'code'    => '<x-input name="account_id" label="Account ID" value="ACC-88291" :disabled="true" />',
                    ],
                ],
            ],

            // ────────────────────────────────────────────────────────────────
            // SPINNER
            // ────────────────────────────────────────────────────────────────
            'spinner' => [
                'id'       => 'spinner',
                'name'     => 'Spinner',
                'desc'     => '12 animated loading indicators — pure CSS, zero dependencies. All converted to native Veldora with no Tailwind or external packages.',
                'cli'      => 'php veldora add spinner',
                'category' => 'feedback',
                'variations' => [
                    [
                        'title'   => 'Classic Spinner',
                        'desc'    => 'A single circular arc that rotates continuously. The most familiar loading indicator.',
                        'preview' => '<div style="display:flex;align-items:center;gap:28px;padding:16px 0;flex-wrap:wrap">
                            <div style="text-align:center">
                                <span class="vui-spinner vui-spinner-sm" role="status" aria-label="Loading"><span class="vui-spinner-ring"></span></span>
                                <p style="margin-top:10px;font-size:11px;color:var(--text-dim)">sm</p>
                            </div>
                            <div style="text-align:center">
                                <span class="vui-spinner vui-spinner-md" role="status" aria-label="Loading"><span class="vui-spinner-ring"></span></span>
                                <p style="margin-top:10px;font-size:11px;color:var(--text-dim)">md</p>
                            </div>
                            <div style="text-align:center">
                                <span class="vui-spinner vui-spinner-lg" role="status" aria-label="Loading"><span class="vui-spinner-ring"></span></span>
                                <p style="margin-top:10px;font-size:11px;color:var(--text-dim)">lg</p>
                            </div>
                        </div>',
                        'code'    => '<x-spinner size="sm" />
<x-spinner size="md" />
<x-spinner size="lg" />',
                    ],
                    [
                        'title'   => 'Dual Ring',
                        'desc'    => 'Two concentric arcs that counter-rotate, creating a hypnotic orbit effect.',
                        'preview' => '<div style="display:flex;align-items:center;gap:28px;padding:16px 0;flex-wrap:wrap">
                            <div style="text-align:center">
                                <span class="vui-spinner-dual vui-spinner-sm" role="status" aria-label="Loading"><span></span><span></span></span>
                                <p style="margin-top:10px;font-size:11px;color:var(--text-dim)">sm</p>
                            </div>
                            <div style="text-align:center">
                                <span class="vui-spinner-dual vui-spinner-md" role="status" aria-label="Loading"><span></span><span></span></span>
                                <p style="margin-top:10px;font-size:11px;color:var(--text-dim)">md</p>
                            </div>
                            <div style="text-align:center">
                                <span class="vui-spinner-dual vui-spinner-lg" role="status" aria-label="Loading"><span></span><span></span></span>
                                <p style="margin-top:10px;font-size:11px;color:var(--text-dim)">lg</p>
                            </div>
                        </div>',
                        'code'    => '<x-spinner variant="dual-ring" size="sm" />
<x-spinner variant="dual-ring" size="md" />
<x-spinner variant="dual-ring" size="lg" />',
                    ],
                    [
                        'title'   => 'Bounce Dots',
                        'desc'    => 'Three dots that bounce in a staggered sequence — ideal for chat / typing indicators.',
                        'preview' => '<div style="display:flex;align-items:center;gap:32px;padding:16px 0;flex-wrap:wrap">
                            <div style="text-align:center">
                                <span class="vui-spinner-bounce" role="status" aria-label="Loading" style="--dot-size:6px"><span></span><span></span><span></span></span>
                                <p style="margin-top:14px;font-size:11px;color:var(--text-dim)">sm</p>
                            </div>
                            <div style="text-align:center">
                                <span class="vui-spinner-bounce" role="status" aria-label="Loading" style="--dot-size:9px"><span></span><span></span><span></span></span>
                                <p style="margin-top:14px;font-size:11px;color:var(--text-dim)">md</p>
                            </div>
                            <div style="text-align:center">
                                <span class="vui-spinner-bounce" role="status" aria-label="Loading" style="--dot-size:13px"><span></span><span></span><span></span></span>
                                <p style="margin-top:14px;font-size:11px;color:var(--text-dim)">lg</p>
                            </div>
                        </div>',
                        'code'    => '<x-spinner variant="bounce-dots" size="sm" />
<x-spinner variant="bounce-dots" size="md" />
<x-spinner variant="bounce-dots" size="lg" />',
                    ],
                    [
                        'title'   => 'Pulse',
                        'desc'    => 'A solid circle that rhythmically scales up and down with a soft glow. Perfect for status indicators.',
                        'preview' => '<div style="display:flex;align-items:center;gap:32px;padding:16px 0;flex-wrap:wrap">
                            <div style="text-align:center">
                                <span class="vui-spinner-pulse" role="status" aria-label="Loading" style="--pulse-size:10px"><span></span></span>
                                <p style="margin-top:14px;font-size:11px;color:var(--text-dim)">sm</p>
                            </div>
                            <div style="text-align:center">
                                <span class="vui-spinner-pulse" role="status" aria-label="Loading" style="--pulse-size:18px"><span></span></span>
                                <p style="margin-top:14px;font-size:11px;color:var(--text-dim)">md</p>
                            </div>
                            <div style="text-align:center">
                                <span class="vui-spinner-pulse" role="status" aria-label="Loading" style="--pulse-size:26px"><span></span></span>
                                <p style="margin-top:14px;font-size:11px;color:var(--text-dim)">lg</p>
                            </div>
                        </div>',
                        'code'    => '<x-spinner variant="pulse" size="sm" />
<x-spinner variant="pulse" size="md" />
<x-spinner variant="pulse" size="lg" />',
                    ],
                    [
                        'title'   => 'Ring Pulse (Radar)',
                        'desc'    => 'A solid core dot surrounded by an expanding ring wave — like a radar or sonar ping.',
                        'preview' => '<div style="display:flex;align-items:center;gap:36px;padding:16px 0;flex-wrap:wrap">
                            <div style="text-align:center">
                                <span class="vui-spinner-ring-pulse" role="status" aria-label="Loading" style="--rp-size:14px"><span></span></span>
                                <p style="margin-top:18px;font-size:11px;color:var(--text-dim)">sm</p>
                            </div>
                            <div style="text-align:center">
                                <span class="vui-spinner-ring-pulse" role="status" aria-label="Loading" style="--rp-size:22px"><span></span></span>
                                <p style="margin-top:20px;font-size:11px;color:var(--text-dim)">md</p>
                            </div>
                            <div style="text-align:center">
                                <span class="vui-spinner-ring-pulse" role="status" aria-label="Loading" style="--rp-size:32px"><span></span></span>
                                <p style="margin-top:24px;font-size:11px;color:var(--text-dim)">lg</p>
                            </div>
                        </div>',
                        'code'    => '<x-spinner variant="ring-pulse" size="sm" />
<x-spinner variant="ring-pulse" size="md" />
<x-spinner variant="ring-pulse" size="lg" />',
                    ],
                    [
                        'title'   => 'Wave Bars (Equalizer)',
                        'desc'    => 'Five vertical bars oscillating like an audio equalizer. Great for media or AI generation states.',
                        'preview' => '<div style="display:flex;align-items:center;gap:32px;padding:16px 0;flex-wrap:wrap">
                            <div style="text-align:center">
                                <span class="vui-spinner-wave" role="status" aria-label="Loading" style="--bar-h:16px;--bar-w:3px;gap:3px"><span></span><span></span><span></span><span></span><span></span></span>
                                <p style="margin-top:10px;font-size:11px;color:var(--text-dim)">sm</p>
                            </div>
                            <div style="text-align:center">
                                <span class="vui-spinner-wave" role="status" aria-label="Loading" style="--bar-h:28px;--bar-w:4px;gap:4px"><span></span><span></span><span></span><span></span><span></span></span>
                                <p style="margin-top:10px;font-size:11px;color:var(--text-dim)">md</p>
                            </div>
                            <div style="text-align:center">
                                <span class="vui-spinner-wave" role="status" aria-label="Loading" style="--bar-h:40px;--bar-w:5px;gap:5px"><span></span><span></span><span></span><span></span><span></span></span>
                                <p style="margin-top:10px;font-size:11px;color:var(--text-dim)">lg</p>
                            </div>
                        </div>',
                        'code'    => '<x-spinner variant="wave-bars" size="sm" />
<x-spinner variant="wave-bars" size="md" />
<x-spinner variant="wave-bars" size="lg" />',
                    ],
                    [
                        'title'   => 'Dot Grid',
                        'desc'    => 'A 3×3 matrix of micro-dots pulsating in a wave across the grid.',
                        'preview' => '<div style="display:flex;align-items:center;gap:32px;padding:16px 0;flex-wrap:wrap">
                            <div style="text-align:center">
                                <span class="vui-spinner-dot-grid" role="status" aria-label="Loading" style="--dg-dot:5px;--dg-gap:4px"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></span>
                                <p style="margin-top:10px;font-size:11px;color:var(--text-dim)">sm</p>
                            </div>
                            <div style="text-align:center">
                                <span class="vui-spinner-dot-grid" role="status" aria-label="Loading" style="--dg-dot:8px;--dg-gap:6px"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></span>
                                <p style="margin-top:10px;font-size:11px;color:var(--text-dim)">md</p>
                            </div>
                            <div style="text-align:center">
                                <span class="vui-spinner-dot-grid" role="status" aria-label="Loading" style="--dg-dot:11px;--dg-gap:8px"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></span>
                                <p style="margin-top:10px;font-size:11px;color:var(--text-dim)">lg</p>
                            </div>
                        </div>',
                        'code'    => '<x-spinner variant="dot-grid" size="sm" />
<x-spinner variant="dot-grid" size="md" />
<x-spinner variant="dot-grid" size="lg" />',
                    ],
                    [
                        'title'   => 'Spinning Bars (Radial)',
                        'desc'    => '8 radial lines that fade out in sequence like a clock hand sweep.',
                        'preview' => '<div style="display:flex;align-items:center;gap:32px;padding:16px 0;flex-wrap:wrap">
                            <div style="text-align:center">
                                <span class="vui-spinner-bars" role="status" aria-label="Loading" style="--sb-size:20px"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></span>
                                <p style="margin-top:10px;font-size:11px;color:var(--text-dim)">sm</p>
                            </div>
                            <div style="text-align:center">
                                <span class="vui-spinner-bars" role="status" aria-label="Loading" style="--sb-size:32px"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></span>
                                <p style="margin-top:10px;font-size:11px;color:var(--text-dim)">md</p>
                            </div>
                            <div style="text-align:center">
                                <span class="vui-spinner-bars" role="status" aria-label="Loading" style="--sb-size:46px"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></span>
                                <p style="margin-top:10px;font-size:11px;color:var(--text-dim)">lg</p>
                            </div>
                        </div>',
                        'code'    => '<x-spinner variant="spinning-bars" size="sm" />
<x-spinner variant="spinning-bars" size="md" />
<x-spinner variant="spinning-bars" size="lg" />',
                    ],
                    [
                        'title'   => 'Orbit',
                        'desc'    => 'A small satellite dot that orbits a central sphere in 3D perspective.',
                        'preview' => '<div style="display:flex;align-items:center;gap:32px;padding:16px 0;flex-wrap:wrap">
                            <div style="text-align:center">
                                <span class="vui-spinner-orbit" role="status" aria-label="Loading" style="--orb-size:20px"><span class="orb-core"></span><span class="orb-satellite"></span></span>
                                <p style="margin-top:10px;font-size:11px;color:var(--text-dim)">sm</p>
                            </div>
                            <div style="text-align:center">
                                <span class="vui-spinner-orbit" role="status" aria-label="Loading" style="--orb-size:32px"><span class="orb-core"></span><span class="orb-satellite"></span></span>
                                <p style="margin-top:10px;font-size:11px;color:var(--text-dim)">md</p>
                            </div>
                            <div style="text-align:center">
                                <span class="vui-spinner-orbit" role="status" aria-label="Loading" style="--orb-size:46px"><span class="orb-core"></span><span class="orb-satellite"></span></span>
                                <p style="margin-top:10px;font-size:11px;color:var(--text-dim)">lg</p>
                            </div>
                        </div>',
                        'code'    => '<x-spinner variant="orbit" size="sm" />
<x-spinner variant="orbit" size="md" />
<x-spinner variant="orbit" size="lg" />',
                    ],
                ],
            ],

            // ────────────────────────────────────────────────────────────────
            // BADGE
            // ────────────────────────────────────────────────────────────────
            'badge' => [
                'id'       => 'badge',
                'name'     => 'Badge',
                'desc'     => 'Small status label for highlighting counts, labels, and states.',
                'cli'      => 'php veldora add badge',
                'category' => 'feedback',
                'variations' => [
                    [
                        'title'   => 'Solid Colour',
                        'desc'    => 'Fully filled badge in semantic colours.',
                        'preview' => '<div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;padding:8px 0">
                            <span class="vui-badge vui-badge-primary">Primary</span>
                            <span class="vui-badge vui-badge-success">Success</span>
                            <span class="vui-badge vui-badge-danger">Danger</span>
                            <span class="vui-badge vui-badge-warning">Warning</span>
                            <span class="vui-badge vui-badge-secondary">Secondary</span>
                        </div>',
                        'code'    => '<x-badge color="primary">Primary</x-badge>
<x-badge color="success">Success</x-badge>
<x-badge color="danger">Danger</x-badge>',
                    ],
                    [
                        'title'   => 'Soft / Tint Style',
                        'desc'    => 'Light background tint with matching text — less visually heavy.',
                        'preview' => '<div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;padding:8px 0">
                            <span class="vui-badge" style="background:rgba(124,110,245,0.15);color:#a89cf7;border:1px solid rgba(124,110,245,0.25)">Primary</span>
                            <span class="vui-badge" style="background:rgba(34,197,94,0.12);color:#4ade80;border:1px solid rgba(34,197,94,0.25)">Success</span>
                            <span class="vui-badge" style="background:rgba(239,68,68,0.12);color:#f87171;border:1px solid rgba(239,68,68,0.25)">Danger</span>
                            <span class="vui-badge" style="background:rgba(245,158,11,0.12);color:#fbbf24;border:1px solid rgba(245,158,11,0.25)">Warning</span>
                        </div>',
                        'code'    => '<x-badge color="primary" variant="soft">Primary</x-badge>
<x-badge color="success" variant="soft">Success</x-badge>
<x-badge color="danger" variant="soft">Danger</x-badge>',
                    ],
                    [
                        'title'   => 'Outline Style',
                        'desc'    => 'Transparent background with only a border.',
                        'preview' => '<div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;padding:8px 0">
                            <span class="vui-badge" style="background:transparent;color:#a89cf7;border:1.5px solid #a89cf7">Primary</span>
                            <span class="vui-badge" style="background:transparent;color:#4ade80;border:1.5px solid #4ade80">Success</span>
                            <span class="vui-badge" style="background:transparent;color:#f87171;border:1.5px solid #f87171">Danger</span>
                        </div>',
                        'code'    => '<x-badge color="primary" variant="outline">Primary</x-badge>
<x-badge color="success" variant="outline">Success</x-badge>',
                    ],
                    [
                        'title'   => 'Status Dot',
                        'desc'    => 'A coloured dot indicator for presence or live status.',
                        'preview' => '<div style="display:flex;flex-wrap:wrap;gap:16px;align-items:center;padding:8px 0">
                            <span style="display:inline-flex;align-items:center;gap:7px;font-size:13px;color:var(--text)"><span style="width:8px;height:8px;border-radius:50%;background:#22c55e;display:inline-block"></span>Online</span>
                            <span style="display:inline-flex;align-items:center;gap:7px;font-size:13px;color:var(--text)"><span style="width:8px;height:8px;border-radius:50%;background:#f59e0b;display:inline-block"></span>Away</span>
                            <span style="display:inline-flex;align-items:center;gap:7px;font-size:13px;color:var(--text)"><span style="width:8px;height:8px;border-radius:50%;background:#ef4444;display:inline-block"></span>Offline</span>
                        </div>',
                        'code'    => '<x-badge variant="dot" color="success">Online</x-badge>
<x-badge variant="dot" color="warning">Away</x-badge>
<x-badge variant="dot" color="danger">Offline</x-badge>',
                    ],
                ],
            ],

            // ────────────────────────────────────────────────────────────────
            // ALERT
            // ────────────────────────────────────────────────────────────────
            'alert' => [
                'id'       => 'alert',
                'name'     => 'Alert',
                'desc'     => 'Contextual feedback message with optional icon, title, and dismissal.',
                'cli'      => 'php veldora add alert',
                'category' => 'feedback',
                'variations' => [
                    [
                        'title'   => 'Semantic Types',
                        'desc'    => 'Four semantic variants for success, info, warning, and danger states.',
                        'preview' => '<div style="display:flex;flex-direction:column;gap:10px">
                            <div class="vui-alert vui-alert-success">✓ Operation completed successfully.</div>
                            <div class="vui-alert vui-alert-info">ℹ Your session will expire in 10 minutes.</div>
                            <div class="vui-alert vui-alert-warning">⚠ Please review the items before saving.</div>
                            <div class="vui-alert vui-alert-danger">✕ Something went wrong. Please try again.</div>
                        </div>',
                        'code'    => '<x-alert type="success">Operation completed successfully.</x-alert>
<x-alert type="info">Your session will expire in 10 minutes.</x-alert>
<x-alert type="warning">Please review the items before saving.</x-alert>
<x-alert type="danger">Something went wrong. Please try again.</x-alert>',
                    ],
                    [
                        'title'   => 'With Title',
                        'desc'    => 'Add a bold heading above the message body.',
                        'preview' => '<div class="vui-alert vui-alert-info"><strong style="display:block;margin-bottom:4px">Heads up!</strong>This action is irreversible. Make sure to export your data first.</div>',
                        'code'    => '<x-alert type="info" title="Heads up!">This action is irreversible. Make sure to export your data first.</x-alert>',
                    ],
                ],
            ],

            // ────────────────────────────────────────────────────────────────
            // CARD
            // ────────────────────────────────────────────────────────────────
            'card' => [
                'id'       => 'card',
                'name'     => 'Card',
                'desc'     => 'Flexible container for content grouping with optional header, footer, and image support.',
                'cli'      => 'php veldora add card',
                'category' => 'display',
                'variations' => [
                    [
                        'title'   => 'Basic Card',
                        'desc'    => 'Simple bordered container with title and body content.',
                        'preview' => '<div class="vui-card" style="max-width:300px">
                            <div class="vui-card-header"><h3 class="vui-card-title">Veldora Framework</h3><p class="vui-card-subtitle">v0.5.1 · PHP 8.2+</p></div>
                            <div class="vui-card-body"><p style="color:var(--text-dim);font-size:13.5px;margin:0">A modern PHP framework you actually own. Zero vendor lock-in, expressive routing, and 41+ UI components.</p></div>
                            <div class="vui-card-footer" style="display:flex;gap:8px"><button class="vui-btn vui-btn-primary vui-btn-sm">Get Started</button><button class="vui-btn vui-btn-ghost vui-btn-sm">Docs</button></div>
                        </div>',
                        'code'    => '<x-card title="Veldora Framework" subtitle="v0.5.1 · PHP 8.2+">
    <x-slot name="body">
        A modern PHP framework you actually own.
    </x-slot>
    <x-slot name="footer">
        <x-button size="sm">Get Started</x-button>
        <x-button variant="ghost" size="sm">Docs</x-button>
    </x-slot>
</x-card>',
                    ],
                    [
                        'title'   => 'Stat Card',
                        'desc'    => 'Compact metric card for dashboards.',
                        'preview' => '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px;max-width:500px">
                            <div class="vui-card" style="padding:16px 20px">
                                <p style="color:var(--text-dim);font-size:11.5px;text-transform:uppercase;letter-spacing:.06em;margin:0 0 6px">Total Users</p>
                                <p style="font-size:26px;font-weight:700;color:var(--text);margin:0">12,489</p>
                                <p style="font-size:12px;color:#22c55e;margin:4px 0 0">↑ 8.2% this month</p>
                            </div>
                            <div class="vui-card" style="padding:16px 20px">
                                <p style="color:var(--text-dim);font-size:11.5px;text-transform:uppercase;letter-spacing:.06em;margin:0 0 6px">Revenue</p>
                                <p style="font-size:26px;font-weight:700;color:var(--text);margin:0">$4,821</p>
                                <p style="font-size:12px;color:#f59e0b;margin:4px 0 0">↓ 1.4% this month</p>
                            </div>
                        </div>',
                        'code'    => '<x-stat label="Total Users" value="12,489" change="+8.2%" trend="up" />
<x-stat label="Revenue" value="$4,821" change="-1.4%" trend="down" />',
                    ],
                ],
            ],

            // ────────────────────────────────────────────────────────────────
            // MODAL
            // ────────────────────────────────────────────────────────────────
            'modal' => [
                'id'       => 'modal',
                'name'     => 'Modal',
                'desc'     => 'Accessible dialog overlay. Supports standard, confirmation, and form-based modals.',
                'cli'      => 'php veldora add modal',
                'category' => 'layout',
                'variations' => [
                    [
                        'title'   => 'Standard Dialog',
                        'desc'    => 'The default modal. Click the trigger to open; close by clicking the backdrop, X button, or pressing Escape.',
                        'preview' => '<div>
                            <button class="vui-btn vui-btn-primary vui-btn-md" onclick="document.getElementById(\'demo-modal-1\').setAttribute(\'aria-hidden\',\'false\')">Open Modal</button>
                            <div id="demo-modal-1" class="vui-modal-overlay" role="dialog" aria-modal="true" aria-hidden="true" aria-labelledby="dmi1-title" onclick="if(event.target===this)this.setAttribute(\'aria-hidden\',\'true\')">
                                <div class="vui-modal-container vui-modal-md">
                                    <div class="vui-modal-header">
                                        <h2 id="dmi1-title" class="vui-modal-title">Confirm Action</h2>
                                        <button type="button" class="vui-modal-close" onclick="document.getElementById(\'demo-modal-1\').setAttribute(\'aria-hidden\',\'true\')" aria-label="Close" style="display:inline-flex;align-items:center;justify-content:center"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
                                    </div>
                                    <div class="vui-modal-body"><p style="color:var(--text-dim);font-size:14px">Are you sure you want to delete this item? This action cannot be undone.</p></div>
                                    <div class="vui-modal-footer" style="display:flex;gap:8px;justify-content:flex-end">
                                        <button class="vui-btn vui-btn-ghost vui-btn-md" onclick="document.getElementById(\'demo-modal-1\').setAttribute(\'aria-hidden\',\'true\')">Cancel</button>
                                        <button class="vui-btn vui-btn-danger vui-btn-md">Delete</button>
                                    </div>
                                </div>
                            </div>
                        </div>',
                        'code'    => '<x-button onclick="openModal(\'confirm-modal\')">Open Modal</x-button>

<x-modal id="confirm-modal" title="Confirm Action">
    <x-slot name="body">
        Are you sure you want to delete this item?
    </x-slot>
    <x-slot name="footer">
        <x-button variant="ghost" onclick="closeModal(\'confirm-modal\')">Cancel</x-button>
        <x-button variant="danger">Delete</x-button>
    </x-slot>
</x-modal>',
                    ],
                ],
            ],

            // ────────────────────────────────────────────────────────────────
            // TABS
            // ────────────────────────────────────────────────────────────────
            'tabs' => [
                'id'       => 'tabs',
                'name'     => 'Tabs',
                'desc'     => 'Tabbed content switcher with multiple style variants including underline, pill, and segmented.',
                'cli'      => 'php veldora add tabs',
                'category' => 'display',
                'variations' => [
                    [
                        'title'   => 'Underline Style',
                        'desc'    => 'Classic border-bottom indicator — clean and minimal.',
                        'preview' => '<div>
                            <div class="vui-tabs">
                                <button class="vui-tab active" onclick="switchVuiTab(this,\'ut\',\'t1\')">Overview</button>
                                <button class="vui-tab" onclick="switchVuiTab(this,\'ut\',\'t2\')">Features</button>
                                <button class="vui-tab" onclick="switchVuiTab(this,\'ut\',\'t3\')">Settings</button>
                            </div>
                            <div style="padding:16px 0;color:var(--text-dim);font-size:13.5px">
                                <div id="ut-t1">Overview panel content — general description and summary.</div>
                                <div id="ut-t2" style="display:none">Features panel — list of capabilities and benefits.</div>
                                <div id="ut-t3" style="display:none">Settings panel — configuration options.</div>
                            </div>
                        </div>',
                        'code'    => '<x-tabs>
    <x-tab label="Overview">Overview panel content.</x-tab>
    <x-tab label="Features">Features panel content.</x-tab>
    <x-tab label="Settings">Settings panel content.</x-tab>
</x-tabs>',
                    ],
                    [
                        'title'   => 'Pill Style',
                        'desc'    => 'Rounded pill tabs — softer, more modern appearance.',
                        'preview' => '<div>
                            <div style="display:flex;gap:4px;background:var(--surface-2);padding:4px;border-radius:10px;width:fit-content">
                                <button onclick="switchVuiTab(this,\'pt\',\'p1\')" style="padding:7px 16px;border-radius:7px;border:none;cursor:pointer;font-size:13px;background:var(--accent);color:#fff;font-weight:600">Profile</button>
                                <button onclick="switchVuiTab(this,\'pt\',\'p2\')" style="padding:7px 16px;border-radius:7px;border:none;cursor:pointer;font-size:13px;background:transparent;color:var(--text-dim)">Billing</button>
                                <button onclick="switchVuiTab(this,\'pt\',\'p3\')" style="padding:7px 16px;border-radius:7px;border:none;cursor:pointer;font-size:13px;background:transparent;color:var(--text-dim)">Security</button>
                            </div>
                            <div style="padding:16px 0;color:var(--text-dim);font-size:13.5px">
                                <div id="pt-p1">Profile settings and personal information.</div>
                                <div id="pt-p2" style="display:none">Billing history and payment methods.</div>
                                <div id="pt-p3" style="display:none">Password and two-factor authentication.</div>
                            </div>
                        </div>',
                        'code'    => '<x-tabs variant="pill">
    <x-tab label="Profile">Profile settings.</x-tab>
    <x-tab label="Billing">Billing history.</x-tab>
    <x-tab label="Security">Security settings.</x-tab>
</x-tabs>',
                    ],
                ],
            ],

            // ────────────────────────────────────────────────────────────────
            // TOAST
            // ────────────────────────────────────────────────────────────────
            'toast' => [
                'id'       => 'toast',
                'name'     => 'Toast',
                'desc'     => 'Lightweight notification snackbar with auto-dismiss, semantic variants, and smooth slide-in animation.',
                'cli'      => 'php veldora add toast',
                'category' => 'feedback',
                'variations' => [
                    [
                        'title'   => 'Semantic Variants',
                        'desc'    => 'Trigger any toast variant from JavaScript using <code>showToast(message, type)</code>.',
                        'preview' => '<div style="display:flex;flex-wrap:wrap;gap:10px;padding:8px 0">
                            <button class="vui-btn vui-btn-primary vui-btn-sm" onclick="showToast(\'Changes saved successfully!\',\'success\')">Success Toast</button>
                            <button class="vui-btn vui-btn-secondary vui-btn-sm" onclick="showToast(\'Your session will expire soon.\',\'info\')">Info Toast</button>
                            <button class="vui-btn vui-btn-warning vui-btn-sm" onclick="showToast(\'This action cannot be undone.\',\'warning\')">Warning Toast</button>
                            <button class="vui-btn vui-btn-danger vui-btn-sm" onclick="showToast(\'Failed to save changes.\',\'error\')">Error Toast</button>
                        </div>',
                        'code'    => '{{-- Trigger from JavaScript --}}
<script>
    showToast("Changes saved!", "success");
    showToast("Session expiring soon.", "info");
    showToast("This cannot be undone.", "warning");
    showToast("Failed to save.", "error");
</script>

{{-- Or from PHP --}}
<x-toast id="my-toast" type="success">Changes saved!</x-toast>',
                    ],
                ],
            ],

            // ────────────────────────────────────────────────────────────────
            // ACCORDION
            // ────────────────────────────────────────────────────────────────
            'accordion' => [
                'id'       => 'accordion',
                'name'     => 'Accordion',
                'desc'     => 'Collapsible content sections for FAQs, settings, and progressive disclosure.',
                'cli'      => 'php veldora add accordion',
                'category' => 'display',
                'variations' => [
                    [
                        'title'   => 'Default Accordion',
                        'desc'    => 'One section open at a time. Click a header to expand.',
                        'preview' => '<div class="vui-accordion" style="max-width:480px">
                            <div class="vui-accordion-item">
                                <button class="vui-accordion-trigger" onclick="this.parentElement.classList.toggle(\'open\')" aria-expanded="false">What is Veldora?<span class="vui-accordion-icon"></span></button>
                                <div class="vui-accordion-content"><p style="color:var(--text-dim);font-size:13.5px;margin:0">Veldora is a modern PHP framework that you fully own — no SaaS lock-in, no cloud dependencies. Just clean MVC architecture.</p></div>
                            </div>
                            <div class="vui-accordion-item">
                                <button class="vui-accordion-trigger" onclick="this.parentElement.classList.toggle(\'open\')" aria-expanded="false">How do I install it?<span class="vui-accordion-icon"></span></button>
                                <div class="vui-accordion-content"><p style="color:var(--text-dim);font-size:13.5px;margin:0">Run <code>composer create-project veldora/veldora my-app</code> or <code>npx create-veldora-app my-app</code>.</p></div>
                            </div>
                            <div class="vui-accordion-item">
                                <button class="vui-accordion-trigger" onclick="this.parentElement.classList.toggle(\'open\')" aria-expanded="false">Is it production ready?<span class="vui-accordion-icon"></span></button>
                                <div class="vui-accordion-content"><p style="color:var(--text-dim);font-size:13.5px;margin:0">Yes. Veldora is built for production PHP 8.2+ environments with full SQLite, MySQL, and PostgreSQL support.</p></div>
                            </div>
                        </div>',
                        'code'    => '<x-accordion>
    <x-accordion-item title="What is Veldora?">
        A modern PHP framework you fully own.
    </x-accordion-item>
    <x-accordion-item title="How do I install it?">
        composer create-project veldora/veldora my-app
    </x-accordion-item>
    <x-accordion-item title="Is it production ready?">
        Yes, built for PHP 8.2+ with full DB support.
    </x-accordion-item>
</x-accordion>',
                    ],
                ],
            ],

            // ────────────────────────────────────────────────────────────────
            // AVATAR
            // ────────────────────────────────────────────────────────────────
            'avatar' => [
                'id'       => 'avatar',
                'name'     => 'Avatar',
                'desc'     => 'User avatar component with image support, initials fallback, status indicators, and avatar groups.',
                'cli'      => 'php veldora add avatar',
                'category' => 'display',
                'variations' => [
                    [
                        'title'   => 'Sizes',
                        'desc'    => 'From XS to XL avatar sizes.',
                        'preview' => '<div style="display:flex;flex-wrap:wrap;gap:14px;align-items:center;padding:8px 0">
                            <span class="vui-avatar" style="width:24px;height:24px;font-size:10px;background:var(--accent)">JD</span>
                            <span class="vui-avatar" style="width:32px;height:32px;font-size:12px;background:#7c6ef5">AB</span>
                            <span class="vui-avatar vui-avatar-md" style="background:#0ea5e9">VL</span>
                            <span class="vui-avatar" style="width:48px;height:48px;font-size:16px;background:#f59e0b">MK</span>
                            <span class="vui-avatar" style="width:60px;height:60px;font-size:20px;background:#22c55e">SN</span>
                        </div>',
                        'code'    => '<x-avatar name="Jane Doe" size="xs" />
<x-avatar name="Alex B" size="sm" />
<x-avatar name="Veldora" size="md" />
<x-avatar name="M K" size="lg" />
<x-avatar name="S N" size="xl" />',
                    ],
                    [
                        'title'   => 'With Status Indicator',
                        'desc'    => 'Show online/away/offline status with a dot badge.',
                        'preview' => '<div style="display:flex;flex-wrap:wrap;gap:18px;align-items:center;padding:8px 0">
                            <span style="position:relative;display:inline-block"><span class="vui-avatar vui-avatar-md" style="background:#7c6ef5">JD</span><span style="position:absolute;bottom:2px;right:2px;width:10px;height:10px;border-radius:50%;background:#22c55e;border:2px solid var(--bg)"></span></span>
                            <span style="position:relative;display:inline-block"><span class="vui-avatar vui-avatar-md" style="background:#0ea5e9">AB</span><span style="position:absolute;bottom:2px;right:2px;width:10px;height:10px;border-radius:50%;background:#f59e0b;border:2px solid var(--bg)"></span></span>
                            <span style="position:relative;display:inline-block"><span class="vui-avatar vui-avatar-md" style="background:#64748b">MK</span><span style="position:absolute;bottom:2px;right:2px;width:10px;height:10px;border-radius:50%;background:#64748b;border:2px solid var(--bg)"></span></span>
                        </div>',
                        'code'    => '<x-avatar name="Jane Doe" status="online" />
<x-avatar name="Alex B" status="away" />
<x-avatar name="M K" status="offline" />',
                    ],
                ],
            ],

            // ────────────────────────────────────────────────────────────────
            // PROGRESS
            // ────────────────────────────────────────────────────────────────
            'progress' => [
                'id'       => 'progress',
                'name'     => 'Progress',
                'desc'     => 'Linear progress bar with percentage, colours, stripes, and animated variants.',
                'cli'      => 'php veldora add progress',
                'category' => 'feedback',
                'variations' => [
                    [
                        'title'   => 'Basic Progress',
                        'desc'    => 'Simple linear progress with labelled percentage.',
                        'preview' => '<div style="display:flex;flex-direction:column;gap:14px;max-width:420px">
                            <div>
                                <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:12px;color:var(--text-dim)"><span>Profile Completion</span><span>72%</span></div>
                                <div class="vui-progress"><div class="vui-progress-bar" style="width:72%;background:var(--accent)"></div></div>
                            </div>
                            <div>
                                <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:12px;color:var(--text-dim)"><span>Upload Progress</span><span>45%</span></div>
                                <div class="vui-progress"><div class="vui-progress-bar" style="width:45%;background:#22c55e"></div></div>
                            </div>
                            <div>
                                <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:12px;color:var(--text-dim)"><span>Storage Used</span><span>88%</span></div>
                                <div class="vui-progress"><div class="vui-progress-bar" style="width:88%;background:#ef4444"></div></div>
                            </div>
                        </div>',
                        'code'    => '<x-progress label="Profile Completion" :value="72" />
<x-progress label="Upload Progress" :value="45" color="success" />
<x-progress label="Storage Used" :value="88" color="danger" />',
                    ],
                ],
            ],

            // ────────────────────────────────────────────────────────────────
            // DROPDOWN
            // ────────────────────────────────────────────────────────────────
            'dropdown' => [
                'id'       => 'dropdown',
                'name'     => 'Dropdown',
                'desc'     => 'Menu overlay anchored to a trigger element with keyboard navigation support.',
                'cli'      => 'php veldora add dropdown',
                'category' => 'actions',
                'variations' => [
                    [
                        'title'   => 'Basic Dropdown',
                        'desc'    => 'Click the trigger button to open a positioned menu.',
                        'preview' => '<div style="position:relative;display:inline-block" id="dd-demo">
                            <button class="vui-btn vui-btn-secondary vui-btn-md" onclick="var d=document.getElementById(\'ddm\');d.style.display=d.style.display===\'block\'?\'none\':\'block\'" style="display:inline-flex;align-items:center;gap:7px">
                                Options
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                            </button>
                            <div id="ddm" style="display:none;position:absolute;top:calc(100% + 6px);left:0;z-index:50;min-width:160px;background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:6px;box-shadow:0 10px 30px rgba(0,0,0,0.3)">
                                <a href="#" style="display:block;padding:8px 12px;border-radius:7px;color:var(--text);font-size:13.5px;text-decoration:none" onmouseover="this.style.background=\'var(--surface-2)\'" onmouseout="this.style.background=\'transparent\'">Edit Profile</a>
                                <a href="#" style="display:block;padding:8px 12px;border-radius:7px;color:var(--text);font-size:13.5px;text-decoration:none" onmouseover="this.style.background=\'var(--surface-2)\'" onmouseout="this.style.background=\'transparent\'">Settings</a>
                                <hr style="border:none;border-top:1px solid var(--border);margin:4px 0">
                                <a href="#" style="display:block;padding:8px 12px;border-radius:7px;color:#ef4444;font-size:13.5px;text-decoration:none" onmouseover="this.style.background=\'rgba(239,68,68,0.1)\'" onmouseout="this.style.background=\'transparent\'">Sign Out</a>
                            </div>
                        </div>',
                        'code'    => '<x-dropdown label="Options">
    <x-dropdown-item href="/profile">Edit Profile</x-dropdown-item>
    <x-dropdown-item href="/settings">Settings</x-dropdown-item>
    <x-dropdown-divider />
    <x-dropdown-item href="/logout" class="text-danger">Sign Out</x-dropdown-item>
</x-dropdown>',
                    ],
                ],
            ],

            // ────────────────────────────────────────────────────────────────
            // SWITCH
            // ────────────────────────────────────────────────────────────────
            'switch' => [
                'id'       => 'switch',
                'name'     => 'Switch',
                'desc'     => 'Toggle switch for boolean on/off settings with label and disabled state.',
                'cli'      => 'php veldora add switch',
                'category' => 'actions',
                'variations' => [
                    [
                        'title'   => 'Toggle Switches',
                        'desc'    => 'Binary on/off controls for settings and preferences.',
                        'preview' => '<div style="display:flex;flex-direction:column;gap:14px;max-width:320px">
                            <label class="vui-switch-wrap" style="display:flex;align-items:center;justify-content:space-between;cursor:pointer">
                                <span style="color:var(--text);font-size:13.5px">Email Notifications</span>
                                <span class="vui-switch"><input type="checkbox" checked><span class="vui-switch-slider"></span></span>
                            </label>
                            <label class="vui-switch-wrap" style="display:flex;align-items:center;justify-content:space-between;cursor:pointer">
                                <span style="color:var(--text);font-size:13.5px">Dark Mode</span>
                                <span class="vui-switch"><input type="checkbox"><span class="vui-switch-slider"></span></span>
                            </label>
                            <label class="vui-switch-wrap" style="display:flex;align-items:center;justify-content:space-between;cursor:not-allowed;opacity:0.5">
                                <span style="color:var(--text);font-size:13.5px">Two-Factor Auth (Disabled)</span>
                                <span class="vui-switch"><input type="checkbox" disabled><span class="vui-switch-slider"></span></span>
                            </label>
                        </div>',
                        'code'    => '<x-switch name="email_notifications" label="Email Notifications" :checked="true" />
<x-switch name="dark_mode" label="Dark Mode" />
<x-switch name="two_factor" label="Two-Factor Auth" :disabled="true" />',
                    ],
                ],
            ],

            // ────────────────────────────────────────────────────────────────
            // SKELETON
            // ────────────────────────────────────────────────────────────────
            'skeleton' => [
                'id'       => 'skeleton',
                'name'     => 'Skeleton',
                'desc'     => 'Animated placeholder content blocks for loading states.',
                'cli'      => 'php veldora add skeleton',
                'category' => 'feedback',
                'variations' => [
                    [
                        'title'   => 'Content Skeleton',
                        'desc'    => 'Mimics the shape of real content while data is loading.',
                        'preview' => '<div style="max-width:360px;display:flex;flex-direction:column;gap:10px;padding:8px 0">
                            <div class="vui-skeleton" style="height:16px;width:45%;border-radius:6px"></div>
                            <div class="vui-skeleton" style="height:12px;width:90%;border-radius:6px"></div>
                            <div class="vui-skeleton" style="height:12px;width:75%;border-radius:6px"></div>
                            <div class="vui-skeleton" style="height:12px;width:82%;border-radius:6px"></div>
                            <div style="display:flex;gap:10px;margin-top:6px">
                                <div class="vui-skeleton" style="height:32px;width:90px;border-radius:8px"></div>
                                <div class="vui-skeleton" style="height:32px;width:70px;border-radius:8px"></div>
                            </div>
                        </div>',
                        'code'    => '<x-skeleton lines="3" width="90%" />
<div style="display:flex;gap:8px;margin-top:8px">
    <x-skeleton width="90px" height="32px" />
    <x-skeleton width="70px" height="32px" />
</div>',
                    ],
                    [
                        'title'   => 'Card Skeleton',
                        'desc'    => 'Full card-shaped loading placeholder.',
                        'preview' => '<div class="vui-card" style="max-width:280px;padding:16px;display:flex;flex-direction:column;gap:10px">
                            <div style="display:flex;align-items:center;gap:12px">
                                <div class="vui-skeleton" style="width:40px;height:40px;border-radius:50%;flex-shrink:0"></div>
                                <div style="flex:1;display:flex;flex-direction:column;gap:6px">
                                    <div class="vui-skeleton" style="height:13px;width:60%;border-radius:5px"></div>
                                    <div class="vui-skeleton" style="height:11px;width:40%;border-radius:5px"></div>
                                </div>
                            </div>
                            <div class="vui-skeleton" style="height:80px;border-radius:8px"></div>
                            <div class="vui-skeleton" style="height:12px;width:80%;border-radius:5px"></div>
                            <div class="vui-skeleton" style="height:12px;width:65%;border-radius:5px"></div>
                        </div>',
                        'code'    => '<x-skeleton variant="card" />',
                    ],
                ],
            ],

        ]; // end map
    }


    /**
     * @return array<int, array{id: string, name: string, desc: string, cli: string, preview: string, code: string}>
     */
    private function getComponentsData(): array
    {
        return [
            // ── 1. Button ─────────────────────────────────────────────────────────────
            [
                'id'      => 'button',
                'name'    => 'Button',
                'desc'    => 'Clickable button with variants (primary, secondary, ghost, danger), sizes (sm, md, lg), and disabled state.',
                'cli'     => 'php veldora add button',
                'preview' => <<<'HTML'
<div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;">
    <button type="button" class="vui-btn vui-btn-primary vui-btn-md">Primary</button>
    <button type="button" class="vui-btn vui-btn-secondary vui-btn-md">Secondary</button>
    <button type="button" class="vui-btn vui-btn-ghost vui-btn-md">Ghost</button>
    <button type="button" class="vui-btn vui-btn-danger vui-btn-md">Danger</button>
    <button type="button" class="vui-btn vui-btn-primary vui-btn-sm">Small</button>
    <button type="button" class="vui-btn vui-btn-primary vui-btn-lg">Large</button>
    <button type="button" class="vui-btn vui-btn-secondary vui-btn-md" disabled aria-disabled="true">Disabled</button>
</div>
HTML,
                'code'    => <<<'CODE'
{{-- Variants --}}
<x-button variant="primary">Primary</x-button>
<x-button variant="secondary">Secondary</x-button>
<x-button variant="ghost">Ghost</x-button>
<x-button variant="danger">Danger</x-button>

{{-- Sizes: sm | md | lg --}}
<x-button variant="primary" size="sm">Small</x-button>
<x-button variant="primary" size="lg">Large</x-button>

{{-- Disabled State --}}
<x-button variant="secondary" :disabled="true">Disabled</x-button>
CODE,
            ],

            // ── 2. Input ──────────────────────────────────────────────────────────────
            [
                'id'      => 'input',
                'name'    => 'Input',
                'desc'    => 'Form text field supporting label, placeholder, required marker, helper text, and validation error state.',
                'cli'     => 'php veldora add input',
                'preview' => <<<'HTML'
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px;width:100%;">
    <div class="vui-field">
        <label class="vui-label" for="demo-in-1">Full Name <span class="vui-required" aria-hidden="true">*</span></label>
        <input id="demo-in-1" type="text" placeholder="Jane Doe" class="vui-input" required>
        <p class="vui-field-helper">Enter your official full name</p>
    </div>
    <div class="vui-field">
        <label class="vui-label" for="demo-in-2">Email Address</label>
        <input id="demo-in-2" type="email" value="invalid-email" class="vui-input vui-input-error" aria-invalid="true">
        <p class="vui-field-error" role="alert">Please enter a valid email address.</p>
    </div>
</div>
HTML,
                'code'    => <<<'CODE'
{{-- Standard Input with helper text --}}
<x-input name="name" label="Full Name" placeholder="Jane Doe" :required="true" helper="Enter your official full name" />

{{-- Input with validation error --}}
<x-input name="email" label="Email Address" type="email" value="invalid-email" error="Please enter a valid email address." />
CODE,
            ],

            // ── 3. Textarea ───────────────────────────────────────────────────────────
            [
                'id'      => 'textarea',
                'name'    => 'Textarea',
                'desc'    => 'Multi-line text area with label, rows configuration, and error state handling.',
                'cli'     => 'php veldora add textarea',
                'preview' => <<<'HTML'
<div style="width:100%;max-width:560px;">
    <div class="vui-field">
        <label class="vui-label" for="demo-txt-1">Project Description</label>
        <textarea id="demo-txt-1" rows="3" placeholder="Tell us a little about your project..." class="vui-textarea"></textarea>
        <p class="vui-field-helper">Maximum 500 characters allowed.</p>
    </div>
</div>
HTML,
                'code'    => <<<'CODE'
<x-textarea name="description" label="Project Description" rows="4" placeholder="Tell us a little about your project..." helper="Maximum 500 characters allowed." />
CODE,
            ],

            // ── 4. Select ─────────────────────────────────────────────────────────────
            [
                'id'      => 'select',
                'name'    => 'Select',
                'desc'    => 'Form dropdown select accepting associative or indexed arrays with error support.',
                'cli'     => 'php veldora add select',
                'preview' => <<<'HTML'
<div style="width:100%;max-width:380px;">
    <div class="vui-field">
        <label class="vui-label" for="demo-sel-1">Select Framework <span class="vui-required" aria-hidden="true">*</span></label>
        <select id="demo-sel-1" class="vui-select" required>
            <option value="veldora" selected>Veldora Framework (Recommended)</option>
            <option value="laravel">Laravel</option>
            <option value="symfony">Symfony</option>
        </select>
    </div>
</div>
HTML,
                'code'    => <<<'CODE'
<x-select name="framework" label="Select Framework" :options="['veldora' => 'Veldora Framework', 'laravel' => 'Laravel', 'symfony' => 'Symfony']" selected="veldora" :required="true" />
CODE,
            ],

            // ── 5. Checkbox ───────────────────────────────────────────────────────────
            [
                'id'      => 'checkbox',
                'name'    => 'Checkbox',
                'desc'    => 'Custom-styled checkbox input with accessible label wrapper and error validation.',
                'cli'     => 'php veldora add checkbox',
                'preview' => <<<'HTML'
<div style="display:flex;flex-direction:column;gap:10px;">
    <div class="vui-checkbox-wrap">
        <input type="checkbox" id="demo-chk-1" class="vui-checkbox" checked>
        <label class="vui-checkbox-label" for="demo-chk-1">Enable real-time notification alerts</label>
    </div>
    <div class="vui-checkbox-wrap">
        <input type="checkbox" id="demo-chk-2" class="vui-checkbox">
        <label class="vui-checkbox-label" for="demo-chk-2">Remember credentials on this browser</label>
    </div>
</div>
HTML,
                'code'    => <<<'CODE'
<x-checkbox name="notify" label="Enable real-time notification alerts" :checked="true" />
<x-checkbox name="remember" label="Remember credentials on this browser" />
CODE,
            ],

            // ── 6. Radio ──────────────────────────────────────────────────────────────
            [
                'id'      => 'radio',
                'name'    => 'Radio',
                'desc'    => 'Groupable radio input with custom styled focus state and label wrapping.',
                'cli'     => 'php veldora add radio',
                'preview' => <<<'HTML'
<div style="display:flex;gap:20px;flex-wrap:wrap;">
    <div class="vui-radio-wrap">
        <input type="radio" id="demo-rad-1" name="demo_plan" class="vui-radio" checked>
        <label class="vui-radio-label" for="demo-rad-1">Developer Plan (Free)</label>
    </div>
    <div class="vui-radio-wrap">
        <input type="radio" id="demo-rad-2" name="demo_plan" class="vui-radio">
        <label class="vui-radio-label" for="demo-rad-2">Enterprise Plan ($29/mo)</label>
    </div>
</div>
HTML,
                'code'    => <<<'CODE'
<x-radio name="plan" value="free" label="Developer Plan (Free)" :checked="true" />
<x-radio name="plan" value="pro" label="Enterprise Plan ($29/mo)" />
CODE,
            ],

            // ── 7. Badge ──────────────────────────────────────────────────────────────
            [
                'id'      => 'badge',
                'name'    => 'Badge',
                'desc'    => 'Status and label badges with 6 semantic variants and optional glowing dot indicator.',
                'cli'     => 'php veldora add badge',
                'preview' => <<<'HTML'
<div style="display:flex;flex-wrap:wrap;gap:8px;align-items:center;">
    <span class="vui-badge vui-badge-default">Default</span>
    <span class="vui-badge vui-badge-success"><span class="vui-badge-dot" aria-hidden="true"></span>Active</span>
    <span class="vui-badge vui-badge-warning"><span class="vui-badge-dot" aria-hidden="true"></span>Pending</span>
    <span class="vui-badge vui-badge-danger">Rejected</span>
    <span class="vui-badge vui-badge-info">Information</span>
    <span class="vui-badge vui-badge-purple">v0.4.1 Release</span>
</div>
HTML,
                'code'    => <<<'CODE'
<x-badge variant="default">Default</x-badge>
<x-badge variant="success" :dot="true">Active</x-badge>
<x-badge variant="warning" :dot="true">Pending</x-badge>
<x-badge variant="danger">Rejected</x-badge>
<x-badge variant="info">Information</x-badge>
<x-badge variant="purple">v0.4.1 Release</x-badge>
CODE,
            ],

            // ── 8. Alert ──────────────────────────────────────────────────────────────
            [
                'id'      => 'alert',
                'name'    => 'Alert',
                'desc'    => 'Contextual callout box with icon, optional title, and dismiss button support.',
                'cli'     => 'php veldora add alert',
                'preview' => <<<'HTML'
<div style="display:flex;flex-direction:column;gap:12px;width:100%;">
    <div class="vui-alert vui-alert-success" role="alert">
        <span class="vui-alert-icon" aria-hidden="true">✓</span>
        <div class="vui-alert-body">
            <p class="vui-alert-title">Migration Complete</p>
            <p class="vui-alert-message">Database tables migrated successfully in 12ms.</p>
        </div>
        <button type="button" class="vui-alert-close" onclick="this.closest('.vui-alert').remove()" aria-label="Dismiss">✕</button>
    </div>
    <div class="vui-alert vui-alert-warning" role="alert">
        <span class="vui-alert-icon" aria-hidden="true">⚠</span>
        <div class="vui-alert-body">
            <p class="vui-alert-title">Session Notice</p>
            <p class="vui-alert-message">Your authentication token will refresh in 10 minutes.</p>
        </div>
    </div>
    <div class="vui-alert vui-alert-danger" role="alert">
        <span class="vui-alert-icon" aria-hidden="true">✕</span>
        <div class="vui-alert-body">
            <p class="vui-alert-title">Connection Timeout</p>
            <p class="vui-alert-message">Could not reach the database replica server.</p>
        </div>
    </div>
    <div class="vui-alert vui-alert-info" role="alert">
        <span class="vui-alert-icon" aria-hidden="true">ℹ</span>
        <div class="vui-alert-body">
            <p class="vui-alert-title">New Features</p>
            <p class="vui-alert-message">Veldora UI component registry is now integrated.</p>
        </div>
    </div>
</div>
HTML,
                'code'    => <<<'CODE'
<x-alert variant="success" title="Migration Complete" :dismissible="true">
    Database tables migrated successfully in 12ms.
</x-alert>

<x-alert variant="warning" title="Session Notice">
    Your authentication token will refresh in 10 minutes.
</x-alert>

<x-alert variant="danger" title="Connection Timeout">
    Could not reach the database replica server.
</x-alert>

<x-alert variant="info" title="New Features">
    Veldora UI component registry is now integrated.
</x-alert>
CODE,
            ],

            // ── 9. Card ───────────────────────────────────────────────────────────────
            [
                'id'      => 'card',
                'name'    => 'Card',
                'desc'    => 'Content card container with optional header, footer, and customizable inner padding.',
                'cli'     => 'php veldora add card',
                'preview' => <<<'HTML'
<div style="width:100%;max-width:440px;">
    <div class="vui-card">
        <div class="vui-card-header">
            <h3 class="vui-card-title">Production Server</h3>
            <p class="vui-card-subtitle">AWS ap-southeast-1 &bull; Linux PHP 8.2</p>
        </div>
        <div class="vui-card-body">
            <p style="color:var(--vui-text-muted);font-size:0.88rem;margin:0 0 12px 0;">All 8 worker processes active. CPU load is normal at 14%.</p>
            <div style="display:flex;gap:8px;">
                <span class="vui-badge vui-badge-success"><span class="vui-badge-dot"></span>Healthy</span>
                <span class="vui-badge vui-badge-default">HTTP/2</span>
            </div>
        </div>
        <div class="vui-card-footer" style="display:flex;justify-content:flex-end;gap:8px;">
            <button type="button" class="vui-btn vui-btn-ghost vui-btn-sm" onclick="window.showToast('Server logs loaded')">Logs</button>
            <button type="button" class="vui-btn vui-btn-primary vui-btn-sm" onclick="window.showToast('Server restarting...')">Restart</button>
        </div>
    </div>
</div>
HTML,
                'code'    => <<<'CODE'
<x-card title="Production Server" subtitle="AWS ap-southeast-1 • Linux PHP 8.2">
    <p>All 8 worker processes active. CPU load is normal at 14%.</p>
    <x-badge variant="success" :dot="true">Healthy</x-badge>

    <x-slot name="footer">
        <x-button variant="ghost" size="sm">Logs</x-button>
        <x-button variant="primary" size="sm">Restart</x-button>
    </x-slot>
</x-card>
CODE,
            ],

            // ── 10. Modal ─────────────────────────────────────────────────────────────
            [
                'id'      => 'modal',
                'name'    => 'Modal',
                'desc'    => 'Accessible dialog overlay with Esc key support, click-outside dismiss, and header/body/footer structure.',
                'cli'     => 'php veldora add modal',
                'preview' => <<<'HTML'
<div>
    <button type="button" class="vui-btn vui-btn-primary vui-btn-md" onclick="document.getElementById('demo-vui-modal').setAttribute('aria-hidden','false')">
        Open Demo Modal
    </button>

    <div id="demo-vui-modal" class="vui-modal-overlay" role="dialog" aria-modal="true" aria-hidden="true" aria-labelledby="demo-vui-modal-title">
        <div class="vui-modal-container vui-modal-md">
            <div class="vui-modal-header">
                <h2 id="demo-vui-modal-title" class="vui-modal-title">Confirm Deployment</h2>
                <button type="button" class="vui-modal-close" onclick="document.getElementById('demo-vui-modal').setAttribute('aria-hidden','true')" aria-label="Close" style="display:inline-flex;align-items:center;justify-content:center;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div class="vui-modal-body">
                <p style="color:var(--vui-text);margin:0 0 8px 0;">Deploy latest changes to the production branch?</p>
                <p style="color:var(--vui-text-muted);font-size:0.85rem;margin:0;">This will execute pending database migrations and warm the view cache.</p>
            </div>
            <div class="vui-modal-footer" style="display:flex;justify-content:flex-end;gap:8px;">
                <button type="button" class="vui-btn vui-btn-ghost vui-btn-sm" onclick="document.getElementById('demo-vui-modal').setAttribute('aria-hidden','true')">Cancel</button>
                <button type="button" class="vui-btn vui-btn-primary vui-btn-sm" onclick="document.getElementById('demo-vui-modal').setAttribute('aria-hidden','true');window.showToast('Deployment queued!');">Deploy Now</button>
            </div>
        </div>
    </div>
</div>
HTML,
                'code'    => <<<'CODE'
{{-- Trigger Button --}}
<x-button onclick="document.getElementById('deploy-modal').setAttribute('aria-hidden','false')">
    Open Modal
</x-button>

{{-- Modal Component --}}
<x-modal id="deploy-modal" title="Confirm Deployment" size="md">
    <p>Deploy latest changes to the production branch?</p>

    <x-slot name="footer">
        <x-button variant="ghost" size="sm" onclick="document.getElementById('deploy-modal').setAttribute('aria-hidden','true')">Cancel</x-button>
        <x-button variant="primary" size="sm">Deploy Now</x-button>
    </x-slot>
</x-modal>
CODE,
            ],

            // ── 11. Spinner ───────────────────────────────────────────────────────────
            [
                'id'      => 'spinner',
                'name'    => 'Spinner',
                'desc'    => 'CSS animated loading indicator ring with size variants (sm, md, lg) and accessibility labels.',
                'cli'     => 'php veldora add spinner',
                'preview' => <<<'HTML'
<div style="display:flex;align-items:center;gap:24px;flex-wrap:wrap;">
    <span class="vui-spinner vui-spinner-sm" role="status" aria-label="Loading small"><span class="vui-spinner-ring"></span></span>
    <span class="vui-spinner vui-spinner-md" role="status" aria-label="Loading medium"><span class="vui-spinner-ring"></span></span>
    <span class="vui-spinner vui-spinner-lg" role="status" aria-label="Loading large"><span class="vui-spinner-ring"></span></span>
    <div style="display:flex;align-items:center;gap:8px;">
        <span class="vui-spinner vui-spinner-sm" role="status"><span class="vui-spinner-ring"></span></span>
        <span style="color:var(--vui-text-muted);font-size:0.85rem;">Processing request...</span>
    </div>
</div>
HTML,
                'code'    => <<<'CODE'
<x-spinner size="sm" label="Loading..." />
<x-spinner size="md" label="Loading data..." />
<x-spinner size="lg" label="Processing..." />
CODE,
            ],

            // ── 12. Avatar ────────────────────────────────────────────────────────────
            [
                'id'      => 'avatar',
                'name'    => 'Avatar',
                'desc'    => 'User profile avatar with initials fallback, size variants (xs to xl), and circle/square shapes.',
                'cli'     => 'php veldora add avatar',
                'preview' => <<<'HTML'
<div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
    <span class="vui-avatar vui-avatar-sm vui-avatar-circle" aria-label="Jane Doe"><span class="vui-avatar-initials">JD</span></span>
    <span class="vui-avatar vui-avatar-md vui-avatar-circle" aria-label="Veldora Framework" style="background:var(--vui-accent);color:#fff;"><span class="vui-avatar-initials">VF</span></span>
    <span class="vui-avatar vui-avatar-lg vui-avatar-circle" aria-label="Antigravity Agent"><span class="vui-avatar-initials">AA</span></span>
    <span class="vui-avatar vui-avatar-md vui-avatar-square" aria-label="Square Avatar"><span class="vui-avatar-initials">SQ</span></span>
</div>
HTML,
                'code'    => <<<'CODE'
{{-- Initials fallback avatars --}}
<x-avatar name="Jane Doe" size="sm" />
<x-avatar name="Veldora Framework" size="md" />
<x-avatar name="Antigravity Agent" size="lg" />

{{-- Square shape --}}
<x-avatar name="Square Avatar" size="md" shape="square" />

{{-- Image avatar --}}
<x-avatar src="/images/avatar.jpg" name="Alex Smith" size="md" />
CODE,
            ],

            // ── 13. Dropdown ──────────────────────────────────────────────────────────
            [
                'id'      => 'dropdown',
                'name'    => 'Dropdown',
                'desc'    => 'Toggleable floating menu with custom items, icons, keyboard and click-outside dismissal.',
                'cli'     => 'php veldora add dropdown',
                'preview' => <<<'HTML'
<div style="display:flex;gap:20px;flex-wrap:wrap;align-items:flex-start;min-height:220px;">
    <!-- Project Settings Dropdown -->
    <div class="vui-dropdown" id="demo-dd-left">
        <button type="button" class="vui-dropdown-trigger" aria-haspopup="true" aria-expanded="false" onclick="toggleDropdown(this)">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            Project Settings
            <span class="vui-dropdown-caret" aria-hidden="true" style="display:inline-flex;align-items:center;">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
            </span>
        </button>
        <ul class="vui-dropdown-menu" role="menu">
            <li>
                <a href="javascript:void(0)" onclick="window.showToast('Configuration opened')">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px;vertical-align:-2px;"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                    Edit Configuration
                </a>
            </li>
            <li>
                <a href="javascript:void(0)" onclick="window.showToast('Environment variables opened')">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px;vertical-align:-2px;"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"/><rect x="2" y="14" width="20" height="8" rx="2" ry="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>
                    Environment
                </a>
            </li>
            <li role="separator" style="border-top:1px solid var(--vui-border);margin:4px 0;"></li>
            <li>
                <a href="javascript:void(0)" style="color:#ef4444;" onclick="window.showToast('Delete action triggered', 'error')">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px;vertical-align:-2px;"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                    Delete Project
                </a>
            </li>
        </ul>
    </div>

    <!-- Account Menu Dropdown -->
    <div class="vui-dropdown" id="demo-dd-user">
        <button type="button" class="vui-dropdown-trigger" aria-haspopup="true" aria-expanded="false" onclick="toggleDropdown(this)">
            <span class="vui-avatar vui-avatar-xs vui-avatar-circle" style="background:var(--vui-accent);color:#fff;margin-right:4px;">
                <span class="vui-avatar-initials">AK</span>
            </span>
            Account Menu
            <span class="vui-dropdown-caret" aria-hidden="true" style="display:inline-flex;align-items:center;">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
            </span>
        </button>
        <ul class="vui-dropdown-menu" role="menu">
            <li><a href="javascript:void(0)" onclick="window.showToast('My Profile opened')">My Profile</a></li>
            <li><a href="javascript:void(0)" onclick="window.showToast('API Keys opened')">API Keys</a></li>
            <li role="separator" style="border-top:1px solid var(--vui-border);margin:4px 0;"></li>
            <li><a href="javascript:void(0)" style="color:#ef4444;" onclick="window.showToast('Sign out clicked')">Sign Out</a></li>
        </ul>
    </div>
</div>
HTML,
                'code'    => <<<'CODE'
{{-- Standard Dropdown --}}
<x-dropdown label="Project Settings">
    <li><a href="/settings">Edit Configuration</a></li>
    <li><a href="/env">Environment</a></li>
    <li role="separator"></li>
    <li><a href="/delete" class="text-danger">Delete Project</a></li>
</x-dropdown>

{{-- Right Aligned Dropdown --}}
<x-dropdown label="Account Menu" align="right">
    <li><a href="/profile">My Profile</a></li>
    <li><a href="/tokens">API Keys</a></li>
    <li role="separator"></li>
    <li><a href="/logout">Sign Out</a></li>
</x-dropdown>
CODE,
            ],

            // ── 14. Navbar ────────────────────────────────────────────────────────────
            [
                'id'      => 'navbar',
                'name'    => 'Navbar',
                'desc'    => 'Full responsive top navigation bar with brand icon, navigation links, and mobile burger toggle.',
                'cli'     => 'php veldora add navbar',
                'preview' => <<<'HTML'
<div class="vui-navbar-preview-wrap" style="width:100%;background:var(--vui-surface);border:1px solid var(--vui-border);border-radius:var(--vui-radius-lg);position:relative;">
    <nav class="vui-navbar" role="navigation" aria-label="Demo Navbar" style="border:none;background:transparent;">
        <div class="vui-navbar-inner">
            <a href="javascript:void(0)" class="vui-navbar-brand" style="display:flex;align-items:center;gap:8px;text-decoration:none;">
                <span style="width:26px;height:26px;background:var(--vui-accent);border-radius:6px;display:flex;align-items:center;justify-content:center;color:#fff;box-shadow:0 0 10px rgba(124,110,245,0.4);flex-shrink:0;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="white" aria-hidden="true"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                </span>
                <span style="font-weight:700;letter-spacing:-0.02em;color:var(--vui-text);white-space:nowrap;">Veldora App</span>
            </a>

            <!-- Desktop / Mobile Collapsible Menu -->
            <div class="vui-navbar-menu" id="demo-nav-menu">
                <a href="javascript:void(0)" class="active" onclick="window.showToast('Dashboard clicked')">Dashboard</a>
                <a href="javascript:void(0)" onclick="window.showToast('Documentation clicked')">Documentation</a>
                <a href="javascript:void(0)" onclick="window.showToast('Components clicked')">Components</a>
                <a href="javascript:void(0)" onclick="window.showToast('Releases clicked')">Releases</a>
            </div>

            <!-- Action buttons & toggle -->
            <div class="vui-navbar-actions" style="display:flex;align-items:center;gap:8px;">
                <button type="button" class="vui-btn vui-btn-primary vui-btn-sm" onclick="window.showToast('New project created!')" style="font-size:12px;padding:5px 10px;display:inline-flex;align-items:center;gap:5px;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    <span>New App</span>
                </button>
                <!-- Mobile burger toggle -->
                <button type="button" class="vui-navbar-toggle" aria-label="Toggle navigation" aria-expanded="false"
                        onclick="(function(btn){
                            var menu = document.getElementById('demo-nav-menu');
                            var isOpen = menu.classList.toggle('vui-navbar-open');
                            btn.classList.toggle('vui-toggle-active', isOpen);
                            btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                        })(this)">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <line x1="3" y1="12" x2="21" y2="12"/>
                        <line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
            </div>
        </div>
    </nav>
</div>
HTML,
                'code'    => <<<'CODE'
{{-- Responsive Navbar --}}
<x-navbar brand="Veldora App" brandHref="/" :sticky="false">
    <a href="/dashboard" class="active">Dashboard</a>
    <a href="/docs">Documentation</a>
    <a href="/components">Components</a>
    <a href="/releases">Releases</a>

    <x-slot name="actions">
        <x-button variant="primary" size="sm" icon="plus">New App</x-button>
    </x-slot>
</x-navbar>
CODE,
            ],

            // ── 15. Toast ─────────────────────────────────────────────────────────────
            [
                'id'      => 'toast',
                'name'    => 'Toast',
                'desc'    => 'Auto-dismissing pop-up notification with duration timer and variant styles.',
                'cli'     => 'php veldora add toast',
                'preview' => <<<'HTML'
<div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;">
    <button type="button" class="vui-btn vui-btn-primary vui-btn-sm" onclick="window.showToast('Action completed successfully!')">
        Trigger Success Toast
    </button>
    <button type="button" class="vui-btn vui-btn-danger vui-btn-sm" onclick="window.showToast('Something went wrong. Please retry.', 'error')">
        Trigger Error Toast
    </button>
</div>
HTML,
                'code'    => <<<'CODE'
{{-- Server-rendered toast --}}
<x-toast id="welcome-toast" variant="success" message="Welcome back to Veldora!" :duration="4000" />

{{-- Dynamic toast via global JavaScript helper --}}
<x-button onclick="showToast('Profile updated!')">Save Changes</x-button>
CODE,
            ],

            // ── 16. Tabs ──────────────────────────────────────────────────────────────
            [
                'id'      => 'tabs',
                'name'    => 'Tabs',
                'desc'    => 'Interactive tabbed navigation with animated active indicators and content panels.',
                'cli'     => 'php veldora add tabs',
                'preview' => <<<'HTML'
<div class="vui-tabs-container" id="demo-showcase-tabs" style="width:100%;">
    <div class="vui-tabs-list" role="tablist">
        <button type="button" role="tab" class="vui-tab-btn vui-tab-active" aria-selected="true"
                onclick="(function(btn){
                    var root = document.getElementById('demo-showcase-tabs');
                    root.querySelectorAll('.vui-tab-btn').forEach(function(b){ b.classList.remove('vui-tab-active'); });
                    root.querySelectorAll('.vui-tab-pane').forEach(function(p){ p.classList.remove('vui-tab-pane-active'); });
                    btn.classList.add('vui-tab-active');
                    document.getElementById('demo-pane-general').classList.add('vui-tab-pane-active');
                })(this)">
            General
        </button>
        <button type="button" role="tab" class="vui-tab-btn" aria-selected="false"
                onclick="(function(btn){
                    var root = document.getElementById('demo-showcase-tabs');
                    root.querySelectorAll('.vui-tab-btn').forEach(function(b){ b.classList.remove('vui-tab-active'); });
                    root.querySelectorAll('.vui-tab-pane').forEach(function(p){ p.classList.remove('vui-tab-pane-active'); });
                    btn.classList.add('vui-tab-active');
                    document.getElementById('demo-pane-security').classList.add('vui-tab-pane-active');
                })(this)">
            Security &amp; Keys
        </button>
        <button type="button" role="tab" class="vui-tab-btn" aria-selected="false"
                onclick="(function(btn){
                    var root = document.getElementById('demo-showcase-tabs');
                    root.querySelectorAll('.vui-tab-btn').forEach(function(b){ b.classList.remove('vui-tab-active'); });
                    root.querySelectorAll('.vui-tab-pane').forEach(function(p){ p.classList.remove('vui-tab-pane-active'); });
                    btn.classList.add('vui-tab-active');
                    document.getElementById('demo-pane-billing').classList.add('vui-tab-pane-active');
                })(this)">
            Billing
        </button>
    </div>
    <div class="vui-tabs-content">
        <div class="vui-tab-pane vui-tab-pane-active" id="demo-pane-general">
            <h4 style="margin:0 0 6px 0;font-size:15px;color:var(--vui-text);">General Application Settings</h4>
            <p style="margin:0;font-size:13.5px;color:var(--vui-text-muted);">Configure your app name, environment variables, and timezone settings.</p>
        </div>
        <div class="vui-tab-pane" id="demo-pane-security">
            <h4 style="margin:0 0 6px 0;font-size:15px;color:var(--vui-text);">Security &amp; API Keys</h4>
            <p style="margin:0;font-size:13.5px;color:var(--vui-text-muted);">Manage two-factor authentication and rotate production API tokens.</p>
        </div>
        <div class="vui-tab-pane" id="demo-pane-billing">
            <h4 style="margin:0 0 6px 0;font-size:15px;color:var(--vui-text);">Billing &amp; Subscription</h4>
            <p style="margin:0;font-size:13.5px;color:var(--vui-text-muted);">View current plan, download invoices, and manage payment methods.</p>
        </div>
    </div>
</div>
HTML,
                'code'    => <<<'CODE'
<x-tabs id="app-settings" :tabs="['general' => 'General', 'security' => 'Security', 'billing' => 'Billing']" active="general">
    <div class="vui-tab-pane vui-tab-pane-active" id="tab-pane-app-settings-general">
        <h4>General Settings</h4>
        <p>Manage application settings and preferences.</p>
    </div>
    <div class="vui-tab-pane" id="tab-pane-app-settings-security">
        <h4>Security</h4>
        <p>Update credentials and API tokens.</p>
    </div>
    <div class="vui-tab-pane" id="tab-pane-app-settings-billing">
        <h4>Billing</h4>
        <p>Review invoices and payment methods.</p>
    </div>
</x-tabs>
CODE,
            ],

            // ── 17. Accordion ─────────────────────────────────────────────────────────
            [
                'id'      => 'accordion',
                'name'    => 'Accordion',
                'desc'    => 'Smooth collapsible disclosure component with chevron indicator.',
                'cli'     => 'php veldora add accordion',
                'preview' => <<<'HTML'
<div style="width:100%;display:flex;flex-direction:column;gap:8px;">
    <div class="vui-accordion vui-accordion-open" id="demo-acc-1">
        <button type="button" class="vui-accordion-header" aria-expanded="true"
                onclick="(function(btn){
                    var item = document.getElementById('demo-acc-1');
                    var open = item.classList.toggle('vui-accordion-open');
                    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
                })(this)">
            <span class="vui-accordion-title">What is Veldora UI?</span>
            <svg class="vui-accordion-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div class="vui-accordion-body">
            <div class="vui-accordion-inner">
                Veldora UI is an open-source, modern component library built natively for Veldora Blade templates. It gives you 21+ prebuilt, accessible components with zero bloat.
            </div>
        </div>
    </div>
    <div class="vui-accordion" id="demo-acc-2">
        <button type="button" class="vui-accordion-header" aria-expanded="false"
                onclick="(function(btn){
                    var item = document.getElementById('demo-acc-2');
                    var open = item.classList.toggle('vui-accordion-open');
                    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
                })(this)">
            <span class="vui-accordion-title">How do I install individual components?</span>
            <svg class="vui-accordion-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div class="vui-accordion-body">
            <div class="vui-accordion-inner">
                Run <code>php veldora add [component-name]</code> in your project terminal to copy the component template directly into your <code>resources/views/components</code> folder.
            </div>
        </div>
    </div>
</div>
HTML,
                'code'    => <<<'CODE'
<x-accordion title="What is Veldora UI?" :open="true">
    Veldora UI provides accessible, beautifully designed components natively compiled into your views.
</x-accordion>

<x-accordion title="How do I scaffold a component?">
    Run <code>php veldora add accordion</code> to add this component to your project.
</x-accordion>
CODE,
            ],

            // ── 18. Progress ──────────────────────────────────────────────────────────
            [
                'id'      => 'progress',
                'name'    => 'Progress',
                'desc'    => 'Linear progress bar with striped gradients, animated states, and percentage badges.',
                'cli'     => 'php veldora add progress',
                'preview' => <<<'HTML'
<div style="width:100%;display:flex;flex-direction:column;gap:14px;">
    <div>
        <div style="display:flex;justify-content:space-between;margin-bottom:5px;font-size:12px;color:var(--vui-text-muted);">
            <span>Database Migration</span>
            <span style="font-weight:600;color:var(--vui-text);">75%</span>
        </div>
        <div class="vui-progress vui-progress-md" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">
            <div class="vui-progress-bar vui-progress-primary vui-progress-striped vui-progress-animated" style="width:75%;"></div>
        </div>
    </div>
    <div>
        <div style="display:flex;justify-content:space-between;margin-bottom:5px;font-size:12px;color:var(--vui-text-muted);">
            <span>Backup Completed</span>
            <span style="font-weight:600;color:var(--vui-success);">100%</span>
        </div>
        <div class="vui-progress vui-progress-md" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
            <div class="vui-progress-bar vui-progress-success" style="width:100%;"></div>
        </div>
    </div>
</div>
HTML,
                'code'    => <<<'CODE'
{{-- Primary animated striped progress --}}
<x-progress :value="75" variant="primary" :striped="true" :animated="true" size="md" />

{{-- Success completed progress with label --}}
<x-progress :value="100" variant="success" size="md" :showLabel="true" />
CODE,
            ],

            // ── 19. Tooltip ───────────────────────────────────────────────────────────
            [
                'id'      => 'tooltip',
                'name'    => 'Tooltip',
                'desc'    => 'Contextual hover bubble for actions, icons, and helper descriptions.',
                'cli'     => 'php veldora add tooltip',
                'preview' => <<<'HTML'
<div style="display:flex;flex-wrap:wrap;gap:16px;align-items:center;">
    <span class="vui-tooltip-wrapper vui-tooltip-top">
        <button type="button" class="vui-btn vui-btn-secondary vui-btn-sm">Tooltip Top</button>
        <span class="vui-tooltip-bubble" role="tooltip">Top tooltip helper</span>
    </span>
    <span class="vui-tooltip-wrapper vui-tooltip-bottom">
        <button type="button" class="vui-btn vui-btn-secondary vui-btn-sm">Tooltip Bottom</button>
        <span class="vui-tooltip-bubble" role="tooltip">Bottom tooltip helper</span>
    </span>
    <span class="vui-tooltip-wrapper vui-tooltip-right">
        <button type="button" class="vui-btn vui-btn-primary vui-btn-sm">Tooltip Right</button>
        <span class="vui-tooltip-bubble" role="tooltip">Right tooltip helper</span>
    </span>
</div>
HTML,
                'code'    => <<<'CODE'
<x-tooltip text="Click to copy secret key" position="top">
    <x-button variant="secondary" size="sm">Copy Token</x-button>
</x-tooltip>
CODE,
            ],

            // ── 20. Breadcrumb ────────────────────────────────────────────────────────
            [
                'id'      => 'breadcrumb',
                'name'    => 'Breadcrumb',
                'desc'    => 'Hierarchical trail navigation with vector SVG chevron separators.',
                'cli'     => 'php veldora add breadcrumb',
                'preview' => <<<'HTML'
<nav class="vui-breadcrumb" aria-label="Breadcrumb">
    <ol class="vui-breadcrumb-list">
        <li class="vui-breadcrumb-item">
            <a href="javascript:void(0)" class="vui-breadcrumb-link" onclick="window.showToast('Home clicked')">Home</a>
            <svg class="vui-breadcrumb-sep" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
        </li>
        <li class="vui-breadcrumb-item">
            <a href="javascript:void(0)" class="vui-breadcrumb-link" onclick="window.showToast('Docs clicked')">Documentation</a>
            <svg class="vui-breadcrumb-sep" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
        </li>
        <li class="vui-breadcrumb-item vui-breadcrumb-active">
            <span aria-current="page">UI Components</span>
        </li>
    </ol>
</nav>
HTML,
                'code'    => <<<'CODE'
<x-breadcrumb :items="[
    ['label' => 'Home', 'href' => '/'],
    ['label' => 'Documentation', 'href' => '/docs'],
{{ ... }}
    ['label' => 'UI Components']
]" />
CODE,
            ],

            // ── 21. Table ─────────────────────────────────────────────────────────────
            [
                'id'      => 'table',
                'name'    => 'Table',
                'desc'    => 'Responsive styled data table with hover elevation and zebra striping.',
                'cli'     => 'php veldora add table',
                'preview' => <<<'HTML'
<div class="vui-table-responsive" style="width:100%;">
    <table class="vui-table vui-table-hover vui-table-striped">
        <thead>
            <tr>
                <th>Package Name</th>
                <th>Version</th>
                <th>Status</th>
                <th style="text-align:right;">Action</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="font-weight:600;color:var(--vui-text);">veldora/framework</td>
                <td><span class="vui-badge vui-badge-secondary vui-badge-sm">v0.5.0</span></td>
                <td><span class="vui-badge vui-badge-success vui-badge-sm">Stable</span></td>
                <td style="text-align:right;"><button type="button" class="vui-btn vui-btn-ghost vui-btn-sm" onclick="window.showToast('Updating framework...')">Update</button></td>
            </tr>
            <tr>
                <td style="font-weight:600;color:var(--vui-text);">veldora/ui</td>
                <td><span class="vui-badge vui-badge-secondary vui-badge-sm">v0.5.0</span></td>
                <td><span class="vui-badge vui-badge-success vui-badge-sm">Stable</span></td>
                <td style="text-align:right;"><button type="button" class="vui-btn vui-btn-ghost vui-btn-sm" onclick="window.showToast('Viewing components...')">View</button></td>
            </tr>
            <tr>
                <td style="font-weight:600;color:var(--vui-text);">veldora-vscode</td>
                <td><span class="vui-badge vui-badge-secondary vui-badge-sm">v0.5.0</span></td>
                <td><span class="vui-badge vui-badge-info vui-badge-sm">Active</span></td>
                <td style="text-align:right;"><button type="button" class="vui-btn vui-btn-ghost vui-btn-sm" onclick="window.showToast('Extension downloaded')">Install</button></td>
            </tr>
        </tbody>
    </table>
</div>
HTML,
                'code'    => <<<'CODE'
<x-table :striped="true" :hover="true">
    <thead>
        <tr>
            <th>Name</th>
            <th>Role</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Jane Doe</td>
            <td>Lead Engineer</td>
            <td><x-badge variant="success">Active</x-badge></td>
        </tr>
    </tbody>
</x-table>
CODE,
            ],

            // ── 22. Switch ────────────────────────────────────────────────────────────
            [
                'id'      => 'switch',
                'name'    => 'Switch',
                'desc'    => 'Toggle switch for boolean states, features, and custom dark/light theme switching with animated icons.',
                'cli'     => 'php veldora add switch',
                'preview' => <<<'HTML'
<div style="display:flex;flex-direction:column;gap:18px;width:100%;max-width:440px;margin:0 auto;">
    
    <!-- Custom Live Dark / Light Theme Toggle Card -->
    <div id="demo-theme-card" style="padding:18px 20px;background:var(--vui-surface-2);border:1px solid var(--vui-border);border-radius:12px;display:flex;align-items:center;justify-content:space-between;transition:all 0.3s ease;">
        <div style="display:flex;align-items:center;gap:12px;">
            <div id="demo-theme-badge" style="width:40px;height:40px;border-radius:10px;background:rgba(124,110,245,0.15);color:var(--accent);display:flex;align-items:center;justify-content:center;transition:all 0.3s ease;">
                <svg id="demo-theme-moon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                <svg id="demo-theme-sun" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" style="display:none;"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
            </div>
            <div>
                <p id="demo-theme-label" style="margin:0 0 2px;font-size:13.5px;font-weight:600;color:var(--vui-text);">Dark Mode Active</p>
                <p id="demo-theme-sub" style="margin:0;font-size:12px;color:var(--vui-text-muted);">Click to toggle canvas theme</p>
            </div>
        </div>

        <!-- Custom Animated Sun/Moon Pill Switch -->
        <button type="button" id="demo-theme-btn" onclick="vuiToggleDemoTheme()"
                style="position:relative;width:60px;height:32px;border-radius:9999px;background:#18181b;border:1px solid #3f3f46;cursor:pointer;padding:3px;display:flex;align-items:center;transition:all 0.25s ease;"
                aria-label="Toggle theme">
            <span id="demo-theme-thumb" style="width:24px;height:24px;border-radius:50%;background:linear-gradient(135deg, #7c6ef5, #6366f1);display:flex;align-items:center;justify-content:center;color:#fff;box-shadow:0 2px 6px rgba(0,0,0,0.3);transform:translateX(28px);transition:transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), background 0.25s ease;">
                <svg id="demo-thumb-moon" width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                <svg id="demo-thumb-sun" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="display:none;"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="M2 12h2"/><path d="M20 12h2"/></svg>
            </span>
        </button>
    </div>

    <!-- Standard Form Switches -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
        <label class="vui-switch-wrapper" style="background:var(--vui-surface);padding:10px 14px;border:1px solid var(--vui-border);border-radius:8px;cursor:pointer;display:flex;align-items:center;gap:10px;">
            <input type="checkbox" class="vui-switch-input" role="switch" checked>
            <span class="vui-switch-track vui-switch-md"><span class="vui-switch-thumb"></span></span>
            <span class="vui-switch-label" style="font-size:12.5px;">Email Alerts</span>
        </label>
        <label class="vui-switch-wrapper" style="background:var(--vui-surface);padding:10px 14px;border:1px solid var(--vui-border);border-radius:8px;cursor:pointer;display:flex;align-items:center;gap:10px;">
            <input type="checkbox" class="vui-switch-input" role="switch">
            <span class="vui-switch-track vui-switch-md"><span class="vui-switch-thumb"></span></span>
            <span class="vui-switch-label" style="font-size:12.5px;">Auto Sync</span>
        </label>
    </div>
</div>
<script>
window.vuiDemoIsDark = true;
window.vuiToggleDemoTheme = function() {
    window.vuiDemoIsDark = !window.vuiDemoIsDark;
    const isDark = window.vuiDemoIsDark;
    const card = document.getElementById('demo-theme-card');
    const thumb = document.getElementById('demo-theme-thumb');
    const btn = document.getElementById('demo-theme-btn');
    const badge = document.getElementById('demo-theme-badge');
    const sunSvg = document.getElementById('demo-theme-sun');
    const moonSvg = document.getElementById('demo-theme-moon');
    const thumbSun = document.getElementById('demo-thumb-sun');
    const thumbMoon = document.getElementById('demo-thumb-moon');
    const label = document.getElementById('demo-theme-label');
    const sub = document.getElementById('demo-theme-sub');

    if (isDark) {
        card.style.background = 'var(--vui-surface-2)';
        card.style.borderColor = 'var(--vui-border)';
        btn.style.background = '#18181b';
        btn.style.borderColor = '#3f3f46';
        thumb.style.transform = 'translateX(28px)';
        thumb.style.background = 'linear-gradient(135deg, #7c6ef5, #6366f1)';
        badge.style.background = 'rgba(124,110,245,0.15)';
        badge.style.color = 'var(--accent)';
        sunSvg.style.display = 'none';
        moonSvg.style.display = 'block';
        thumbSun.style.display = 'none';
        thumbMoon.style.display = 'block';
        label.textContent = 'Dark Mode Active';
        label.style.color = 'var(--vui-text)';
        sub.textContent = 'Click to switch to Light mode';
        if (window.showToast) window.showToast('Theme switched to Dark Mode');
    } else {
        card.style.background = '#ffffff';
        card.style.borderColor = '#e4e4e7';
        btn.style.background = '#f4f4f5';
        btn.style.borderColor = '#d4d4d8';
        thumb.style.transform = 'translateX(0px)';
        thumb.style.background = 'linear-gradient(135deg, #f59e0b, #fbbf24)';
        badge.style.background = 'rgba(245,158,11,0.15)';
        badge.style.color = '#f59e0b';
        sunSvg.style.display = 'block';
        moonSvg.style.display = 'none';
        thumbSun.style.display = 'block';
        thumbMoon.style.display = 'none';
        label.textContent = 'Light Mode Active';
        label.style.color = '#09090b';
        sub.textContent = 'Click to switch to Dark mode';
        if (window.showToast) window.showToast('Theme switched to Light Mode');
    }
};
</script>
HTML,
                'code'    => <<<'CODE'
{{-- Standard boolean switch --}}
<x-switch name="notifications" label="Notifications" :checked="true" />

{{-- Theme toggle --}}
<x-switch name="theme" label="Dark Mode" :checked="true" />
CODE,
            ],

            // ── 23. Pagination ────────────────────────────────────────────────────────
            [
                'id'      => 'pagination',
                'name'    => 'Pagination',
                'desc'    => 'Page navigation bar with prev/next arrows, numbered pages, ellipsis for large ranges, and active state.',
                'cli'     => 'php veldora add pagination',
                'preview' => <<<'HTML'
<nav class="vui-pagination" aria-label="Pagination">
    <a class="vui-page-btn vui-page-disabled" aria-disabled="true" aria-label="Previous">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <a class="vui-page-btn">1</a>
    <a class="vui-page-btn vui-page-active" aria-current="page">2</a>
    <a class="vui-page-btn">3</a>
    <span class="vui-page-ellipsis">&hellip;</span>
    <a class="vui-page-btn">10</a>
    <a class="vui-page-btn" aria-label="Next">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
    </a>
</nav>
HTML,
                'code'    => <<<'CODE'
<x-pagination :current="2" :total="10" url="/posts?page=" />
CODE,
            ],

            // ── 24. Skeleton ──────────────────────────────────────────────────────────
            [
                'id'      => 'skeleton',
                'name'    => 'Skeleton',
                'desc'    => 'Pulsing placeholder loading cards, avatars, and text bars for seamless async state UI.',
                'cli'     => 'php veldora add skeleton',
                'preview' => <<<'HTML'
<div style="display:flex;flex-direction:column;gap:12px;width:100%;max-width:320px;">
    <div style="display:flex;gap:12px;align-items:center;">
        <div class="vui-skeleton vui-skeleton-avatar"></div>
        <div style="flex:1;display:flex;flex-direction:column;gap:6px;">
            <div class="vui-skeleton vui-skeleton-title" style="width:60%;"></div>
            <div class="vui-skeleton vui-skeleton-text" style="width:90%;"></div>
        </div>
    </div>
    <div class="vui-skeleton vui-skeleton-card" style="height:70px;"></div>
</div>
HTML,
                'code'    => <<<'CODE'
<x-skeleton type="avatar" />
<x-skeleton type="title" width="60%" />
<x-skeleton type="text" />
<x-skeleton type="card" height="120px" />
CODE,
            ],

            // ── 25. Empty ─────────────────────────────────────────────────────────────
            [
                'id'      => 'empty',
                'name'    => 'Empty',
                'desc'    => 'Empty state illustration with icon, title, message, and call-to-action button for blank views.',
                'cli'     => 'php veldora add empty',
                'preview' => <<<'HTML'
<div class="vui-empty" style="padding:28px 20px;">
    <div class="vui-empty-icon">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
            <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
            <line x1="12" y1="22.08" x2="12" y2="12"/>
        </svg>
    </div>
    <p class="vui-empty-title">No Projects Found</p>
    <p class="vui-empty-desc">You haven't created any projects yet. Get started by creating your first one.</p>
    <button type="button" class="vui-btn vui-btn-primary vui-btn-sm" onclick="window.showToast('New project modal')">+ Create Project</button>
</div>
HTML,
                'code'    => <<<'CODE'
<x-empty title="No Projects Found" message="Get started by creating your first one.">
    <x-button variant="primary" size="sm">+ Create Project</x-button>
</x-empty>
CODE,
            ],

            // ── 26. Divider ───────────────────────────────────────────────────────────
            [
                'id'      => 'divider',
                'name'    => 'Divider',
                'desc'    => 'Horizontal or vertical separator with optional text label, badge, or icon in the center.',
                'cli'     => 'php veldora add divider',
                'preview' => <<<'HTML'
<div style="width:100%;max-width:380px;display:flex;flex-direction:column;gap:16px;">
    <div class="vui-divider"></div>
    <div class="vui-divider vui-divider-labeled">
        <span class="vui-divider-label">OR CONTINUE WITH</span>
    </div>
    <div class="vui-divider"></div>
</div>
HTML,
                'code'    => <<<'CODE'
{{-- Plain --}}
<x-divider />

{{-- Labeled --}}
<x-divider label="OR" />

{{-- Vertical --}}
<x-divider orientation="vertical" />
CODE,
            ],

            // ── 27. Drawer ────────────────────────────────────────────────────────────
            [
                'id'      => 'drawer',
                'name'    => 'Drawer',
                'desc'    => 'Slide-in panel from left, right, top, or bottom. Includes overlay backdrop and close button.',
                'cli'     => 'php veldora add drawer',
                'preview' => <<<'HTML'
<div style="display:flex;gap:10px;flex-wrap:wrap;">
    <button class="vui-btn vui-btn-primary vui-btn-sm" onclick="vui.openDrawer('demo-drawer-right')">Open Right Drawer</button>
</div>
<div id="demo-drawer-right" class="vui-drawer-backdrop" role="dialog" aria-modal="true" aria-hidden="true"
     onclick="if(event.target===this)this.setAttribute('aria-hidden','true')">
    <div class="vui-drawer vui-drawer-right" style="max-width:320px;">
        <div class="vui-drawer-header">
            <h2 class="vui-drawer-title">Settings</h2>
            <button class="vui-drawer-close" aria-label="Close" onclick="vui.closeDrawer('demo-drawer-right')">&times;</button>
        </div>
        <div class="vui-drawer-body">
            <p style="color:var(--text-muted);">Drawer content goes here. This can contain forms, menus, or any content.</p>
        </div>
    </div>
</div>
<script>window.vui=window.vui||{};vui.openDrawer=function(id){document.getElementById(id).setAttribute('aria-hidden','false');};vui.closeDrawer=function(id){document.getElementById(id).setAttribute('aria-hidden','true');};</script>
HTML,
                'code'    => <<<'CODE'
{{-- Trigger --}}
<button onclick="vui.openDrawer('my-drawer')">Open Drawer</button>

{{-- Drawer Component --}}
<x-drawer id="my-drawer" position="right" title="Settings">
    <p>Drawer content goes here.</p>
</x-drawer>
CODE,
            ],

            // ── 28. Popover ───────────────────────────────────────────────────────────
            [
                'id'      => 'popover',
                'name'    => 'Popover',
                'desc'    => 'Floating info panel anchored to a trigger button. Supports top/bottom/left/right placement.',
                'cli'     => 'php veldora add popover',
                'preview' => <<<'HTML'
<div style="width:100%;max-width:440px;margin:0 auto;display:flex;flex-direction:column;align-items:center;gap:18px;">
    <!-- Open Popover Card Demo -->
    <div style="width:100%;background:var(--vui-surface-2);border:1px solid var(--vui-border);border-radius:12px;padding:16px 18px;box-shadow:0 12px 30px rgba(0,0,0,0.4);position:relative;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
            <div style="display:flex;align-items:center;gap:8px;">
                <span style="width:24px;height:24px;border-radius:6px;background:rgba(124,110,245,0.15);color:var(--accent);display:flex;align-items:center;justify-content:center;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                </span>
                <h4 style="margin:0;font-size:13.5px;font-weight:600;color:var(--vui-text);">Two-Factor Authentication</h4>
            </div>
            <span class="vui-badge vui-badge-warning" style="font-size:10px;padding:2px 6px;">Recommended</span>
        </div>
        <p style="font-size:12.5px;color:var(--vui-text-muted);line-height:1.6;margin:0 0 12px;">
            Protect your Veldora account with an extra security layer. A verification code will be sent on every sign-in.
        </p>
        <div style="display:flex;align-items:center;justify-content:flex-end;gap:8px;border-top:1px solid var(--vui-border);padding-top:10px;">
            <button type="button" class="vui-btn vui-btn-ghost vui-btn-sm" onclick="window.showToast('Dismissed')">Dismiss</button>
            <button type="button" class="vui-btn vui-btn-primary vui-btn-sm" onclick="window.showToast('Enabling 2FA...')">Enable Now</button>
        </div>
        <div style="position:absolute;bottom:-6px;left:40px;width:12px;height:12px;background:var(--vui-surface-2);border-right:1px solid var(--vui-border);border-bottom:1px solid var(--vui-border);transform:rotate(45deg);"></div>
    </div>
    <div style="display:flex;align-items:center;gap:10px;">
        <button type="button" class="vui-btn vui-btn-secondary vui-btn-sm" onclick="window.showToast('Popover attached to anchor button')">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            Security Notice (Trigger)
        </button>
    </div>
</div>
HTML,
                'code'    => <<<'CODE'
<x-popover trigger="Security Notice" title="Two-Factor Authentication" placement="top">
    Protect your Veldora account with an extra security layer.
</x-popover>
CODE,
            ],

            // ── 29. Confirm ───────────────────────────────────────────────────────────
            [
                'id'      => 'confirm',
                'name'    => 'Confirm',
                'desc'    => 'Accessible confirmation dialog for destructive actions with warning indicator, action details, and cancel/confirm buttons.',
                'cli'     => 'php veldora add confirm',
                'preview' => <<<'HTML'
<div style="width:100%;max-width:440px;margin:0 auto;display:flex;flex-direction:column;gap:14px;">
    <!-- Confirm Dialog Card Preview -->
    <div style="background:var(--vui-surface);border:1px solid rgba(239,68,68,0.3);border-radius:12px;padding:20px;box-shadow:0 12px 30px rgba(0,0,0,0.5);display:flex;flex-direction:column;gap:14px;">
        <div style="display:flex;gap:14px;align-items:flex-start;">
            <div style="width:40px;height:40px;border-radius:10px;background:rgba(239,68,68,0.12);color:#ef4444;display:flex;align-items:center;justify-content:center;flex-shrink:0;border:1px solid rgba(239,68,68,0.25);">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
            <div style="flex:1;">
                <h3 style="margin:0 0 4px;font-size:15px;font-weight:700;color:var(--vui-text);">Delete Production Database?</h3>
                <p style="margin:0;font-size:13px;color:var(--vui-text-muted);line-height:1.5;">
                    This will permanently delete the <strong>production-db-01</strong> instance and all 42 tables. This action cannot be undone.
                </p>
            </div>
        </div>
        <div style="display:flex;justify-content:flex-end;gap:10px;padding-top:12px;border-top:1px solid var(--vui-border);">
            <button type="button" class="vui-btn vui-btn-secondary vui-btn-sm" onclick="window.showToast('Cancelled')">Cancel</button>
            <button type="button" class="vui-btn vui-btn-danger vui-btn-sm" onclick="window.showToast('Database deleted successfully!')">Yes, Delete</button>
        </div>
    </div>
</div>
HTML,
                'code'    => <<<'CODE'
{{-- Trigger --}}
<button onclick="vui.confirm('del-confirm')">Delete Database</button>

{{-- Dialog --}}
<x-confirm
    id="del-confirm"
    title="Delete Production Database?"
    message="This action cannot be undone."
    action="/databases/1"
    method="DELETE"
    confirm-label="Yes, Delete"
    :danger="true"
/>
CODE,
            ],

            // ── 30. DatePicker ────────────────────────────────────────────────────────
            [
                'id'      => 'datepicker',
                'name'    => 'DatePicker',
                'desc'    => 'Styled native date input with label, min/max constraints, required marker, and helper text.',
                'cli'     => 'php veldora add datepicker',
                'preview' => <<<'HTML'
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;width:100%;">
    <div class="vui-field">
        <label for="demo-dp1" class="vui-label">Date of Birth <span class="vui-required" aria-hidden="true">*</span></label>
        <input type="date" id="demo-dp1" class="vui-input vui-datepicker" required>
    </div>
    <div class="vui-field">
        <label for="demo-dp2" class="vui-label">Event Date</label>
        <input type="date" id="demo-dp2" class="vui-input vui-datepicker" min="2026-01-01">
        <p class="vui-helper">Must be in 2026 or later</p>
    </div>
</div>
HTML,
                'code'    => <<<'CODE'
<x-datepicker name="dob" label="Date of Birth" :required="true" />
<x-datepicker name="event_date" label="Event Date" min="2026-01-01" helper="Must be in 2026 or later" />
CODE,
            ],

            // ── 31. FileUpload ────────────────────────────────────────────────────────
            [
                'id'      => 'fileupload',
                'name'    => 'FileUpload',
                'desc'    => 'Styled drag-and-drop file upload zone with file type filters, upload progress, and file management.',
                'cli'     => 'php veldora add fileupload',
                'preview' => <<<'HTML'
<div class="vui-fileupload-wrap" style="width:100%;max-width:440px;display:flex;flex-direction:column;gap:12px;">
    <div class="vui-fileupload-zone" onclick="document.getElementById('demo-fu1').click()">
        <div class="vui-fileupload-icon-wrap">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="17 8 12 3 7 8"/>
                <line x1="12" y1="3" x2="12" y2="15"/>
            </svg>
        </div>
        <div>
            <p class="vui-fileupload-text" style="margin:0 0 4px;font-weight:600;">Drag &amp; drop files here, or <span style="color:var(--accent);text-decoration:underline;">browse</span></p>
            <p class="vui-fileupload-hint" style="margin:0;">PNG, JPG, PDF, or ZIP &bull; Max 15MB each</p>
        </div>
        <input type="file" id="demo-fu1" class="vui-fileupload-input" onchange="window.showToast('File selected: ' + (this.files[0]?.name || 'example.pdf'))">
    </div>

    <!-- Active Uploaded File Item Preview -->
    <div class="vui-file-item" style="display:flex;align-items:center;gap:12px;padding:12px 14px;background:var(--vui-surface-2);border:1px solid var(--vui-border);border-radius:8px;">
        <div class="vui-file-item-icon" style="width:36px;height:36px;border-radius:8px;background:rgba(124,110,245,0.12);color:var(--accent);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        </div>
        <div class="vui-file-item-info" style="flex:1;min-width:0;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:3px;">
                <span class="vui-file-item-name" style="font-size:13px;font-weight:600;color:var(--vui-text);">brand_identity_v2.pdf</span>
                <span style="font-size:11px;color:#22c55e;font-weight:600;display:flex;align-items:center;gap:4px;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                    Ready
                </span>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;font-size:11.5px;color:var(--vui-text-muted);">
                <span>2.4 MB &bull; Upload complete</span>
                <span>100%</span>
            </div>
            <div class="vui-file-item-progress" style="width:100%;height:3px;background:var(--vui-border);border-radius:2px;margin-top:6px;overflow:hidden;">
                <div class="vui-file-item-bar" style="width:100%;height:100%;background:#22c55e;"></div>
            </div>
        </div>
        <button type="button" class="vui-btn vui-btn-ghost vui-btn-sm" style="padding:4px 8px;font-size:12px;color:var(--vui-text-muted);" onclick="this.closest('.vui-file-item').remove();window.showToast('File removed');" title="Remove file">
            &times;
        </button>
    </div>
</div>
HTML,
                'code'    => <<<'CODE'
<x-fileupload name="avatar" label="Profile Picture" accept="image/*" max-size="2MB" />

{{-- Multiple files --}}
<x-fileupload name="docs[]" label="Documents" accept=".pdf,.docx" :multiple="true" />
CODE,
            ],

            // ── 32. Combobox ──────────────────────────────────────────────────────────
            [
                'id'      => 'combobox',
                'name'    => 'Combobox',
                'desc'    => 'Searchable select dropdown with autocomplete filtering. Keyboard-friendly and fully accessible.',
                'cli'     => 'php veldora add combobox',
                'preview' => <<<'HTML'
<div class="vui-combobox-wrap" id="demo-cb1" style="width:100%;max-width:320px;margin:0 auto;">
    <label class="vui-label" for="demo-cb1-input">Select Destination</label>
    <div class="vui-combobox">
        <div style="position:relative;display:flex;align-items:center;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position:absolute;left:10px;opacity:.5;pointer-events:none;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" id="demo-cb1-input" class="vui-input vui-combobox-input" placeholder="Search country..." autocomplete="off" value="Japan"
                   oninput="vuiCbFilter('demo-cb1')" onfocus="vuiCbOpen('demo-cb1')" style="padding-left:32px;padding-right:32px;">
            <button type="button" onclick="document.getElementById('demo-cb1-input').value='';vuiCbFilter('demo-cb1');" style="position:absolute;right:8px;background:none;border:none;color:var(--text-muted);cursor:pointer;padding:2px 6px;display:flex;align-items:center;justify-content:center;" aria-label="Clear selection">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <input type="hidden" name="country" value="jp">
        <ul class="vui-combobox-list" id="demo-cb1-list" role="listbox" style="position:static;margin-top:8px;box-shadow:none;border-color:var(--vui-border);">
            <li class="vui-combobox-option" role="option" onclick="vuiCbSelect('demo-cb1','bd',this.textContent.trim())">
                <span style="display:flex;align-items:center;gap:8px;">
                    <span style="font-size:10.5px;font-family:var(--font-mono);background:var(--surface-3);padding:1px 6px;border-radius:4px;color:var(--text-muted);">BD</span>
                    <span>Bangladesh</span>
                </span>
            </li>
            <li class="vui-combobox-option vui-selected" role="option" onclick="vuiCbSelect('demo-cb1','jp',this.textContent.trim())">
                <span style="display:flex;align-items:center;gap:8px;">
                    <span style="font-size:10.5px;font-family:var(--font-mono);background:var(--accent-dim);padding:1px 6px;border-radius:4px;color:var(--accent);font-weight:600;">JP</span>
                    <span>Japan</span>
                </span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            </li>
            <li class="vui-combobox-option" role="option" onclick="vuiCbSelect('demo-cb1','us',this.textContent.trim())">
                <span style="display:flex;align-items:center;gap:8px;">
                    <span style="font-size:10.5px;font-family:var(--font-mono);background:var(--surface-3);padding:1px 6px;border-radius:4px;color:var(--text-muted);">US</span>
                    <span>United States</span>
                </span>
            </li>
            <li class="vui-combobox-option" role="option" onclick="vuiCbSelect('demo-cb1','gb',this.textContent.trim())">
                <span style="display:flex;align-items:center;gap:8px;">
                    <span style="font-size:10.5px;font-family:var(--font-mono);background:var(--surface-3);padding:1px 6px;border-radius:4px;color:var(--text-muted);">GB</span>
                    <span>United Kingdom</span>
                </span>
            </li>
            <li class="vui-combobox-option" role="option" onclick="vuiCbSelect('demo-cb1','ca',this.textContent.trim())">
                <span style="display:flex;align-items:center;gap:8px;">
                    <span style="font-size:10.5px;font-family:var(--font-mono);background:var(--surface-3);padding:1px 6px;border-radius:4px;color:var(--text-muted);">CA</span>
                    <span>Canada</span>
                </span>
            </li>
        </ul>
    </div>
</div>
<script>
function vuiCbOpen(u){var el=document.getElementById(u+'-list');if(el)el.style.display='';}
function vuiCbFilter(u){var q=document.querySelector('#'+u+' .vui-combobox-input').value.toLowerCase();document.querySelectorAll('#'+u+'-list .vui-combobox-option').forEach(function(o){o.style.display=o.textContent.toLowerCase().includes(q)?'':'none';});}
function vuiCbSelect(u,val,lbl){document.querySelector('#'+u+' .vui-combobox-input').value=lbl.replace(/[^\w\s]/gi,'').trim();document.querySelectorAll('#'+u+'-list .vui-combobox-option').forEach(function(o){o.classList.remove('vui-selected');});event.currentTarget.classList.add('vui-selected');if(window.showToast)window.showToast('Selected: '+lbl.trim());}
</script>
HTML,
                'code'    => <<<'CODE'
<?php
$countries = ['bd' => 'Bangladesh', 'us' => 'United States', 'gb' => 'United Kingdom'];
?>
<x-combobox name="country" label="Country" :options="$countries" placeholder="Search country..." />
CODE,
            ],

            // ── 33. InputGroup ────────────────────────────────────────────────────────
            [
                'id'      => 'inputgroup',
                'name'    => 'InputGroup',
                'desc'    => 'Input field with attached prefix and/or suffix addons (text, currency symbols, units, icons).',
                'cli'     => 'php veldora add inputgroup',
                'preview' => <<<'HTML'
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;width:100%;">
    <div class="vui-field">
        <label class="vui-label">Price</label>
        <div class="vui-input-group">
            <span class="vui-input-addon vui-input-prefix">$</span>
            <input type="number" class="vui-input" placeholder="0.00">
            <span class="vui-input-addon vui-input-suffix">USD</span>
        </div>
    </div>
    <div class="vui-field">
        <label class="vui-label">Website</label>
        <div class="vui-input-group">
            <span class="vui-input-addon vui-input-prefix">https://</span>
            <input type="text" class="vui-input" placeholder="example.com">
        </div>
    </div>
</div>
HTML,
                'code'    => <<<'CODE'
<x-inputgroup name="price" label="Price" prefix="$" suffix="USD" type="number" placeholder="0.00" />
<x-inputgroup name="website" label="Website" prefix="https://" placeholder="example.com" />
CODE,
            ],

            // ── 34. Stat ──────────────────────────────────────────────────────────────
            [
                'id'      => 'stat',
                'name'    => 'Stat',
                'desc'    => 'Metric display card with label, value, optional icon, and trend indicator (up/down with percentage).',
                'cli'     => 'php veldora add stat',
                'preview' => <<<'HTML'
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;width:100%;">
    <div class="vui-stat">
        <div class="vui-stat-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div class="vui-stat-body">
            <p class="vui-stat-label">Total Users</p>
            <p class="vui-stat-value">12,403</p>
            <span class="vui-stat-trend vui-trend-up">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"/></svg>
                +8.2% vs last month
            </span>
        </div>
    </div>
    <div class="vui-stat">
        <div class="vui-stat-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
        <div class="vui-stat-body">
            <p class="vui-stat-label">Total Revenue</p>
            <p class="vui-stat-value">$48,290</p>
            <span class="vui-stat-trend vui-trend-up">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"/></svg>
                +12.4% vs last month
            </span>
        </div>
    </div>
    <div class="vui-stat">
        <div class="vui-stat-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
        </div>
        <div class="vui-stat-body">
            <p class="vui-stat-label">Bounce Rate</p>
            <p class="vui-stat-value">2.4%</p>
            <span class="vui-stat-trend vui-trend-down">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                -0.8% decrease
            </span>
        </div>
    </div>
</div>
HTML,
                'code'    => <<<'CODE'
<x-stat label="Total Users" value="12,403" trend="+8.2%" :trend-up="true" />
<x-stat label="Revenue" value="48,290" prefix="$" trend="+12.4%" :trend-up="true" />
<x-stat label="Churn Rate" value="2.4" suffix="%" trend="-0.8%" :trend-up="false" />
CODE,
            ],

            // ── 35. DataTable ─────────────────────────────────────────────────────────
            [
                'id'      => 'datatable',
                'name'    => 'DataTable',
                'desc'    => 'Interactive table with client-side search, column sort, and pagination. No server round-trips needed.',
                'cli'     => 'php veldora add datatable',
                'preview' => <<<'HTML'
<div class="vui-datatable-wrap" id="demo-dt1">
    <div class="vui-datatable-toolbar" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;flex-wrap:wrap;gap:8px;">
        <div style="position:relative;width:240px;">
            <input type="search" class="vui-input vui-datatable-search" placeholder="Search members..." oninput="vuiDtSearch('demo-dt1', this.value)" style="padding-left:32px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);opacity:.5;pointer-events:none;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </div>
        <span style="font-size:12px;color:var(--text-muted);" id="demo-dt1-count">Showing 4 members</span>
    </div>
    <div class="vui-table-responsive" style="overflow-x:auto;">
        <table class="vui-table vui-table-hover vui-table-striped" style="width:100%;">
            <thead><tr>
                <th onclick="vuiDtSort('demo-dt1', 0, this)" class="vui-sortable" style="cursor:pointer;user-select:none;">
                    <div style="display:flex;align-items:center;gap:6px;">
                        <span>Name</span>
                        <svg class="vui-sort-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m7 15 5 5 5-5"/><path d="m7 9 5-5 5 5"/></svg>
                    </div>
                </th>
                <th onclick="vuiDtSort('demo-dt1', 1, this)" class="vui-sortable" style="cursor:pointer;user-select:none;">
                    <div style="display:flex;align-items:center;gap:6px;">
                        <span>Role</span>
                        <svg class="vui-sort-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m7 15 5 5 5-5"/><path d="m7 9 5-5 5 5"/></svg>
                    </div>
                </th>
                <th onclick="vuiDtSort('demo-dt1', 2, this)" class="vui-sortable" style="cursor:pointer;user-select:none;">
                    <div style="display:flex;align-items:center;gap:6px;">
                        <span>Department</span>
                        <svg class="vui-sort-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m7 15 5 5 5-5"/><path d="m7 9 5-5 5 5"/></svg>
                    </div>
                </th>
                <th onclick="vuiDtSort('demo-dt1', 3, this)" class="vui-sortable" style="cursor:pointer;user-select:none;">
                    <div style="display:flex;align-items:center;gap:6px;">
                        <span>Status</span>
                        <svg class="vui-sort-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m7 15 5 5 5-5"/><path d="m7 9 5-5 5 5"/></svg>
                    </div>
                </th>
            </tr></thead>
            <tbody id="demo-dt1-tbody">
                <tr>
                    <td style="font-weight:600;color:var(--vui-text);">Alice Smith</td>
                    <td>Principal Engineer</td>
                    <td>Engineering</td>
                    <td><span class="vui-badge vui-badge-success">Active</span></td>
                </tr>
                <tr>
                    <td style="font-weight:600;color:var(--vui-text);">Bob Martinez</td>
                    <td>Product Designer</td>
                    <td>Design</td>
                    <td><span class="vui-badge vui-badge-warning">Away</span></td>
                </tr>
                <tr>
                    <td style="font-weight:600;color:var(--vui-text);">Carol White</td>
                    <td>Engineering Lead</td>
                    <td>Engineering</td>
                    <td><span class="vui-badge vui-badge-success">Active</span></td>
                </tr>
                <tr>
                    <td style="font-weight:600;color:var(--vui-text);">Dave Johnson</td>
                    <td>QA Architect</td>
                    <td>Quality</td>
                    <td><span class="vui-badge vui-badge-danger">Offline</span></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:12px;flex-wrap:wrap;gap:10px;">
        <span style="font-size:12.5px;color:var(--text-muted);">Page 1 of 2</span>
        <div class="vui-pagination">
            <button type="button" class="vui-page-btn vui-page-active">1</button>
            <button type="button" class="vui-page-btn" onclick="window.showToast('Showing page 2')">2</button>
            <button type="button" class="vui-page-btn" onclick="window.showToast('Next page')" aria-label="Next">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
        </div>
    </div>
</div>
HTML,
                'code'    => <<<'CODE'
<?php
$columns = ['name' => 'Name', 'role' => 'Role', 'dept' => 'Department', 'status' => 'Status'];
$rows    = [
    ['name' => 'Alice Smith',  'role' => 'Principal Engineer', 'dept' => 'Engineering', 'status' => 'Active'],
    ['name' => 'Bob Martinez', 'role' => 'Product Designer',   'dept' => 'Design',      'status' => 'Away'],
];
?>
<x-datatable :columns="$columns" :rows="$rows" :searchable="true" :per-page="10" />
CODE,
            ],

            // ── 36. Timeline ──────────────────────────────────────────────────────────
            [
                'id'      => 'timeline',
                'name'    => 'Timeline',
                'desc'    => 'Vertical timeline for activity logs, changelogs, or order tracking with icons, descriptions, and timestamps.',
                'cli'     => 'php veldora add timeline',
                'preview' => <<<'HTML'
<ol class="vui-timeline">
    <li class="vui-timeline-item">
        <div class="vui-timeline-marker" style="background:rgba(34,197,94,0.15);border-color:#22c55e;color:#22c55e;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <div class="vui-timeline-content">
            <p class="vui-timeline-title">Order Confirmed</p>
            <p class="vui-timeline-desc">Payment of $149.00 processed via Stripe.</p>
            <time class="vui-timeline-time">Aug 23, 2026 &middot; 10:04 AM</time>
        </div>
    </li>
    <li class="vui-timeline-item">
        <div class="vui-timeline-marker" style="background:rgba(59,130,246,0.15);border-color:#3b82f6;color:#3b82f6;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
        </div>
        <div class="vui-timeline-content">
            <p class="vui-timeline-title">Dispatched with DHL Express</p>
            <p class="vui-timeline-desc">Tracking #DHL-94820491-BD in transit.</p>
            <time class="vui-timeline-time">Aug 23, 2026 &middot; 02:30 PM</time>
        </div>
    </li>
    <li class="vui-timeline-item">
        <div class="vui-timeline-marker" style="background:rgba(167,139,250,0.15);border-color:#a78bfa;color:#a78bfa;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div class="vui-timeline-content">
            <p class="vui-timeline-title">Out for Delivery</p>
            <p class="vui-timeline-desc">Courier is en route to destination address.</p>
            <time class="vui-timeline-time">Expected Aug 24, 2026 &middot; By 05:00 PM</time>
        </div>
    </li>
</ol>
HTML,
                'code'    => <<<'CODE'
<?php
$events = [
    ['title' => 'Order Confirmed', 'description' => 'Payment processed.', 'time' => 'Aug 23, 2026', 'color' => '#22c55e'],
    ['title' => 'Dispatched',      'description' => 'Via DHL Express.',   'time' => 'Aug 23, 2026', 'color' => '#3b82f6'],
    ['title' => 'Out for Delivery','description' => 'En route.',          'time' => 'Aug 24, 2026', 'color' => '#a78bfa'],
];
?>
<x-timeline :items="$events" />
CODE,
            ],

            // ── 37. Stepper ───────────────────────────────────────────────────────────
            [
                'id'      => 'stepper',
                'name'    => 'Stepper',
                'desc'    => 'Multi-step wizard progress indicator. Completed steps show a checkmark; active step is highlighted.',
                'cli'     => 'php veldora add stepper',
                'preview' => <<<'HTML'
<div style="width:100%;max-width:540px;margin:0 auto;padding:12px 0;">
    <ol class="vui-stepper" aria-label="Checkout Progress">
        <li class="vui-stepper-step vui-step-done">
            <span class="vui-step-circle">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
            </span>
            <span class="vui-step-label">1. Account</span>
            <span class="vui-step-line" aria-hidden="true"></span>
        </li>
        <li class="vui-stepper-step vui-step-active" aria-current="step">
            <span class="vui-step-circle">2</span>
            <span class="vui-step-label">2. Shipping</span>
            <span class="vui-step-line" aria-hidden="true"></span>
        </li>
        <li class="vui-stepper-step vui-step-pending">
            <span class="vui-step-circle">3</span>
            <span class="vui-step-label">3. Payment</span>
            <span class="vui-step-line" aria-hidden="true"></span>
        </li>
        <li class="vui-stepper-step vui-step-pending">
            <span class="vui-step-circle">4</span>
            <span class="vui-step-label">4. Review</span>
        </li>
    </ol>
</div>
HTML,
                'code'    => <<<'CODE'
<x-stepper :steps="['Account', 'Shipping', 'Payment', 'Review']" :current="2" />
CODE,
            ],

            // ── 38. Sidebar ───────────────────────────────────────────────────────────
            [
                'id'      => 'sidebar',
                'name'    => 'Sidebar',
                'desc'    => 'Application navigation sidebar with logo, nav links, active state, icons, and collapsible sub-menus.',
                'cli'     => 'php veldora add sidebar',
                'preview' => <<<'HTML'
<div class="vui-sidebar-preview-card" style="width:100%;max-width:280px;margin:0 auto;background:var(--vui-surface);border:1px solid var(--vui-border);border-radius:12px;overflow:hidden;box-shadow:0 8px 24px rgba(0,0,0,0.35);">
    <aside class="vui-sidebar" role="navigation" style="width:100%;border:none;min-height:auto;background:transparent;">
        <div class="vui-sidebar-header" style="display:flex;align-items:center;gap:10px;padding:14px 16px;border-bottom:1px solid var(--vui-border);">
            <div style="width:28px;height:28px;border-radius:6px;background:var(--accent);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:14px;flex-shrink:0;">V</div>
            <span class="vui-sidebar-logo" style="font-size:15px;font-weight:700;letter-spacing:-.02em;color:var(--vui-text);">Veldora App</span>
        </div>
        <nav style="padding:10px 8px;">
            <ul class="vui-sidebar-nav" style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:3px;">
                <li class="vui-nav-item vui-nav-active">
                    <a href="javascript:void(0)" class="vui-nav-link" onclick="window.showToast('Dashboard selected')">
                        <span class="vui-nav-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                        </span>
                        <span class="vui-nav-label">Dashboard</span>
                    </a>
                </li>
                <li class="vui-nav-item">
                    <a href="javascript:void(0)" class="vui-nav-link" onclick="window.showToast('Team selected')">
                        <span class="vui-nav-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </span>
                        <span class="vui-nav-label">Team Members</span>
                    </a>
                </li>
                <li class="vui-nav-item">
                    <a href="javascript:void(0)" class="vui-nav-link" onclick="window.showToast('Settings selected')">
                        <span class="vui-nav-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        </span>
                        <span class="vui-nav-label">Settings</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>
</div>
HTML,
                'code'    => <<<'CODE'
<?php
$navItems = [
    ['label' => 'Dashboard', 'href' => '/dashboard', 'icon' => 'dashboard', 'active' => true],
    ['label' => 'Team',      'href' => '/team',      'icon' => 'users'],
    ['label' => 'Settings',  'href' => '/settings',  'icon' => 'settings'],
];
?>
<x-sidebar :items="$navItems" logo="Veldora App" />
CODE,
            ],

            // ── 39. Container ─────────────────────────────────────────────────────────
            [
                'id'      => 'container',
                'name'    => 'Container',
                'desc'    => 'Responsive max-width layout wrapper that automatically centers page content with balanced side margins.',
                'cli'     => 'php veldora add container',
                'preview' => <<<'HTML'
<div style="width:100%;display:flex;flex-direction:column;gap:16px;">
    
    <!-- Browser Window Simulation Canvas -->
    <div style="width:100%;background:#09090b;border:1px solid var(--vui-border);border-radius:12px;overflow:hidden;box-shadow:0 12px 30px rgba(0,0,0,0.5);">
        
        <!-- Browser Chrome Header -->
        <div style="background:var(--vui-surface-2);padding:10px 16px;border-bottom:1px solid var(--vui-border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
            <div style="display:flex;align-items:center;gap:6px;">
                <span style="width:10px;height:10px;border-radius:50%;background:#ef4444;display:inline-block;"></span>
                <span style="width:10px;height:10px;border-radius:50%;background:#f59e0b;display:inline-block;"></span>
                <span style="width:10px;height:10px;border-radius:50%;background:#22c55e;display:inline-block;"></span>
                <span style="font-size:11.5px;color:var(--text-muted);margin-left:8px;font-family:var(--font-mono);">Full Page Viewport (100% Canvas Width)</span>
            </div>
            <div style="font-size:11px;color:var(--accent);font-family:var(--font-mono);background:var(--accent-dim);padding:2px 8px;border-radius:4px;border:1px solid var(--accent-glow);">
                margin: 0 auto &bull; Auto-Centering Gutters
            </div>
        </div>

        <!-- Viewport Canvas Body -->
        <div style="padding:22px 16px;background:repeating-linear-gradient(45deg, rgba(255,255,255,0.012), rgba(255,255,255,0.012) 10px, transparent 10px, transparent 20px);display:flex;flex-direction:column;gap:14px;">
            
            <!-- Large Container (1024px) -->
            <div style="width:92%;margin:0 auto;background:var(--vui-surface);border:2px dashed var(--accent);border-radius:10px;padding:14px 18px;position:relative;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;flex-wrap:wrap;gap:6px;">
                    <span style="font-size:11.5px;font-family:var(--font-mono);background:var(--accent);color:#fff;padding:2px 8px;border-radius:4px;font-weight:600;">
                        &lt;x-container size="lg"&gt;
                    </span>
                    <span style="font-size:11px;color:var(--text-muted);font-family:var(--font-mono);background:var(--surface-3);padding:2px 8px;border-radius:4px;">
                        max-width: 1024px (Standard Page Content)
                    </span>
                </div>
                <div style="background:var(--vui-surface-2);border:1px solid var(--vui-border);border-radius:6px;padding:12px 16px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
                    <div style="font-size:13px;font-weight:600;color:var(--vui-text);">Hero Section / Main Application Dashboard</div>
                    <span class="vui-badge vui-badge-primary">Page Body</span>
                </div>
            </div>

            <!-- Medium Container (768px) -->
            <div style="width:72%;margin:0 auto;background:var(--vui-surface);border:2px dashed #3b82f6;border-radius:10px;padding:12px 16px;position:relative;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;flex-wrap:wrap;gap:6px;">
                    <span style="font-size:11px;font-family:var(--font-mono);background:#3b82f6;color:#fff;padding:2px 8px;border-radius:4px;font-weight:600;">
                        &lt;x-container size="md"&gt;
                    </span>
                    <span style="font-size:11px;color:var(--text-muted);font-family:var(--font-mono);background:var(--surface-3);padding:2px 6px;border-radius:4px;">
                        max-width: 768px (Article / Blog Content)
                    </span>
                </div>
                <div style="background:var(--vui-surface-2);border:1px solid var(--vui-border);border-radius:6px;padding:10px 14px;">
                    <p style="font-size:12.5px;color:var(--vui-text-muted);margin:0;">Optimized single-column reading width with balanced left &amp; right margin gutters.</p>
                </div>
            </div>

            <!-- Small Container (640px) -->
            <div style="width:52%;margin:0 auto;background:var(--vui-surface);border:2px dashed #22c55e;border-radius:10px;padding:10px 14px;position:relative;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;flex-wrap:wrap;gap:6px;">
                    <span style="font-size:11px;font-family:var(--font-mono);background:#22c55e;color:#fff;padding:2px 8px;border-radius:4px;font-weight:600;">
                        &lt;x-container size="sm"&gt;
                    </span>
                    <span style="font-size:11px;color:var(--text-muted);font-family:var(--font-mono);background:var(--surface-3);padding:2px 6px;border-radius:4px;">
                        max-width: 640px (Auth Card / Form)
                    </span>
                </div>
                <div style="background:var(--vui-surface-2);border:1px solid var(--vui-border);border-radius:6px;padding:10px 12px;text-align:center;">
                    <p style="font-size:12px;color:var(--vui-text-muted);margin:0;">Centered narrow card for Login, Register &amp; Checkout Modals</p>
                </div>
            </div>

        </div>
    </div>
</div>
HTML,
                'code'    => <<<'CODE'
{{-- Small centered container --}}
<x-container size="sm">
    <p>Narrow content area (640px max)</p>
</x-container>

{{-- Default large container --}}
<x-container size="lg">
    <p>Standard page content (1024px max)</p>
</x-container>

{{-- Full width --}}
<x-container size="full" :center="false">
    <p>Edge-to-edge layout</p>
</x-container>
CODE,
            ],
            [
                'id'      => 'footer',
                'name'    => 'Footer',
                'desc'    => 'Responsive site footer with branding, nav links, and legal notice.',
                'cli'     => 'php veldora add footer',
                'preview' => <<<'HTML'
<div style="background:var(--vui-surface,#18181b);border:1px solid var(--vui-border,#27272a);border-radius:10px;padding:24px 20px;color:var(--vui-muted,#a1a1aa);">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;margin-bottom:16px;">
        <div>
            <h3 style="margin:0 0 4px;font-size:16px;font-weight:700;color:var(--accent,#7c6ef5);">Veldora UI</h3>
            <p style="margin:0;font-size:12.5px;color:var(--text-dim);">The PHP framework you actually own.</p>
        </div>
        <div style="display:flex;gap:14px;font-size:13px;">
            <a href="#" style="color:var(--text-dim);text-decoration:none;">Docs</a>
            <a href="#" style="color:var(--text-dim);text-decoration:none;">Components</a>
            <a href="#" style="color:var(--text-dim);text-decoration:none;">GitHub</a>
        </div>
    </div>
    <div style="border-top:1px solid var(--vui-border,#27272a);padding-top:12px;text-align:center;font-size:12px;color:var(--text-dim);">
        &copy; 2026 Veldora. All rights reserved.
    </div>
</div>
HTML,
                'code'    => <<<'CODE'
<x-footer brand="My App" tagline="Built with Veldora" />
CODE,
            ],
            [
                'id'      => 'rating',
                'name'    => 'Rating',
                'desc'    => 'Interactive star rating component with half-star and read-only support.',
                'cli'     => 'php veldora add rating',
                'preview' => <<<'HTML'
<div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
    <div style="display:flex;gap:4px;color:#f59e0b;font-size:20px;">
        <span>★</span><span>★</span><span>★</span><span>★</span><span style="color:var(--border);">★</span>
    </div>
    <span style="font-size:13.5px;color:var(--text-dim);">(4.0 out of 5 stars)</span>
</div>
HTML,
                'code'    => <<<'CODE'
<x-rating :value="4" :max="5" />
<x-rating :value="4.5" :readonly="true" />
CODE,
            ],
        ];
    }
}