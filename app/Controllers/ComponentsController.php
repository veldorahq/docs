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
        // Shared CSS helpers (Veldora design tokens, no hardcoded RGB)
        $s = 'var(--vui-surface)';        // dark surface
        $s2 = 'var(--vui-surface-2)';     // slightly lighter
        $b = 'var(--vui-border)';         // border color
        $t = 'var(--vui-text)';           // primary text
        $tm = 'var(--vui-text-muted)';    // muted text
        $acc = 'var(--vui-accent)';       // violet accent

        return [

            // ────────────────────────────────────────────────────────────────
            // BUTTON
            // ────────────────────────────────────────────────────────────────
            'button' => [
                'id'       => 'button',
                'name'     => 'Button',
                'desc'     => 'Flexible button component with semantic variants, sizes, outline styles, icon support, loading state, and button groups.',
                'cli'      => 'php veldora add button',
                'category' => 'actions',
                'variations' => [
                    [
                        'title' => 'Solid Semantic Variants',
                        'desc'  => 'Use <code>variant</code> to pick the role. All variants use Veldora design tokens — no hardcoded colours.',
                        'preview' => '<div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;">
                            <button class="vui-btn vui-btn-primary vui-btn-md">Primary</button>
                            <button class="vui-btn vui-btn-secondary vui-btn-md">Secondary</button>
                            <button class="vui-btn vui-btn-success vui-btn-md">Success</button>
                            <button class="vui-btn vui-btn-danger vui-btn-md">Danger</button>
                            <button class="vui-btn vui-btn-warning vui-btn-md">Warning</button>
                            <button class="vui-btn vui-btn-ghost vui-btn-md">Ghost</button>
                        </div>',
                        'code' => '<x-button variant="primary">Primary</x-button>
<x-button variant="secondary">Secondary</x-button>
<x-button variant="success">Success</x-button>
<x-button variant="danger">Danger</x-button>
<x-button variant="warning">Warning</x-button>
<x-button variant="ghost">Ghost</x-button>',
                    ],
                    [
                        'title' => 'Outline Variants',
                        'desc'  => 'Transparent background with coloured border. Ideal for secondary actions alongside a solid primary button.',
                        'preview' => '<div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;">
                            <button class="vui-btn vui-btn-outline-primary vui-btn-md">Outline Primary</button>
                            <button class="vui-btn vui-btn-outline-secondary vui-btn-md">Outline Neutral</button>
                            <button class="vui-btn vui-btn-outline-danger vui-btn-md">Outline Danger</button>
                        </div>',
                        'code' => '<x-button variant="outline-primary">Outline Primary</x-button>
<x-button variant="outline-secondary">Outline Neutral</x-button>
<x-button variant="outline-danger">Outline Danger</x-button>',
                    ],
                    [
                        'title' => 'Sizes',
                        'desc'  => 'Three built-in sizes: <code>sm</code>, <code>md</code> (default), and <code>lg</code>.',
                        'preview' => '<div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;">
                            <button class="vui-btn vui-btn-primary vui-btn-sm">Small</button>
                            <button class="vui-btn vui-btn-primary vui-btn-md">Medium</button>
                            <button class="vui-btn vui-btn-primary vui-btn-lg">Large</button>
                        </div>',
                        'code' => '<x-button variant="primary" size="sm">Small</x-button>
<x-button variant="primary" size="md">Medium</x-button>
<x-button variant="primary" size="lg">Large</x-button>',
                    ],
                    [
                        'title' => 'With Icons',
                        'desc'  => 'Inline SVG icons sit flush with the label thanks to the flex gap built into <code>.vui-btn</code>.',
                        'preview' => '<div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;">
                            <button class="vui-btn vui-btn-primary vui-btn-md">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                Download
                            </button>
                            <button class="vui-btn vui-btn-secondary vui-btn-md">
                                Settings
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
                            </button>
                            <button class="vui-btn vui-btn-danger vui-btn-md">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                Delete
                            </button>
                        </div>',
                        'code' => '<x-button variant="primary" icon-left="download">Download</x-button>
<x-button variant="secondary" icon-right="settings">Settings</x-button>
<x-button variant="danger" icon-left="trash">Delete</x-button>',
                    ],
                    [
                        'title' => 'Loading & Disabled States',
                        'desc'  => 'Show feedback during async operations or lock unavailable actions.',
                        'preview' => '<div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;">
                            <button class="vui-btn vui-btn-primary vui-btn-md" disabled>
                                <span class="vui-spinner-ring" style="width:13px;height:13px;border-width:2px;border-top-color:#fff;flex-shrink:0;"></span>
                                Saving...
                            </button>
                            <button class="vui-btn vui-btn-secondary vui-btn-md" disabled aria-disabled="true">Unavailable</button>
                        </div>',
                        'code' => '<x-button variant="primary" :loading="true">Saving...</x-button>
<x-button variant="secondary" :disabled="true">Unavailable</x-button>',
                    ],
                    [
                        'title' => 'Button Group',
                        'desc'  => 'Attach buttons into a segmented control using <code>.vui-btn-group</code>.',
                        'preview' => '<div style="display:flex;flex-wrap:wrap;gap:16px;align-items:center;">
                            <div class="vui-btn-group">
                                <button class="vui-btn vui-btn-primary vui-btn-sm">Daily</button>
                                <button class="vui-btn vui-btn-secondary vui-btn-sm">Weekly</button>
                                <button class="vui-btn vui-btn-secondary vui-btn-sm">Monthly</button>
                            </div>
                        </div>',
                        'code' => '<x-button-group>
    <x-button variant="primary" size="sm">Daily</x-button>
    <x-button variant="secondary" size="sm">Weekly</x-button>
    <x-button variant="secondary" size="sm">Monthly</x-button>
</x-button-group>',
                    ],
                    [
                        'title' => 'Skeuomorphic — Beveled Embossed Button',
                        'desc'  => '3D physical press simulation with specular highlight highlight, inset depth on active, and multi-layer box-shadow bevel.',
                        'preview' => '<div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;padding:10px 0;">
                            <button class="vui-btn-skeuo">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
                                Deploy Now
                            </button>
                            <button class="vui-btn-skeuo vui-btn-skeuo-neutral">
                                Settings
                            </button>
                            <button class="vui-btn-skeuo vui-btn-skeuo-danger">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                Delete
                            </button>
                        </div>',
                        'code' => '<button class="vui-btn-skeuo">Deploy Now</button>
<button class="vui-btn-skeuo vui-btn-skeuo-neutral">Settings</button>
<button class="vui-btn-skeuo vui-btn-skeuo-danger">Delete</button>',
                    ],
                    [
                        'title' => 'Flat Minimal 2D Button',
                        'desc'  => 'Pure flat design — no shadows, no gradients. Bold colour fill with strict 2D border-radius. Outline variant also available.',
                        'preview' => '<div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;padding:10px 0;">
                            <button class="vui-btn-flat">Publish</button>
                            <button class="vui-btn-flat vui-btn-flat-outline">Edit Draft</button>
                            <button class="vui-btn-flat vui-btn-flat-neutral">Cancel</button>
                        </div>',
                        'code' => '<button class="vui-btn-flat">Publish</button>
<button class="vui-btn-flat vui-btn-flat-outline">Edit Draft</button>
<button class="vui-btn-flat vui-btn-flat-neutral">Cancel</button>',
                    ],
                    [
                        'title' => 'Neumorphic Soft UI Button',
                        'desc'  => 'Extruded dual-shadow soft plate that physically recesses inward on click — pure CSS depth illusion.',
                        'preview' => '<div style="display:flex;flex-wrap:wrap;gap:16px;align-items:center;padding:10px 0;">
                            <button class="vui-btn-neumorphic">Default</button>
                            <button class="vui-btn-neumorphic vui-btn-neumorphic-accent">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                                New Project
                            </button>
                        </div>',
                        'code' => '<button class="vui-btn-neumorphic">Default</button>
<button class="vui-btn-neumorphic vui-btn-neumorphic-accent">New Project</button>',
                    ],
                    [
                        'title' => 'Glassmorphism Button',
                        'desc'  => 'Frosted-glass effect with backdrop-blur, translucent tinted fill, and soft outer glow. Perfect for hero sections over dark imagery.',
                        'preview' => '<div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;padding:10px 0;">
                            <button class="vui-btn-glass">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                                Get Started
                            </button>
                            <button class="vui-btn-glass vui-btn-glass-neutral">Learn More</button>
                        </div>',
                        'code' => '<button class="vui-btn-glass">Get Started</button>
<button class="vui-btn-glass vui-btn-glass-neutral">Learn More</button>',
                    ],
                ],
            ],

            // ────────────────────────────────────────────────────────────────
            'spinner' => [
                'id'       => 'spinner',
                'name'     => 'Spinner',
                'desc'     => '12 pure CSS loading animations — no Tailwind, no external dependencies. GPU-accelerated and zero JS.',
                'cli'      => 'php veldora add spinner',
                'category' => 'feedback',
                'variations' => [
                    [
                        'title' => 'Classic Ring — Sizes',
                        'desc'  => 'Smooth rotating arc available in <code>sm</code>, <code>md</code>, and <code>lg</code>.',
                        'preview' => '<div style="display:flex;align-items:center;gap:28px;padding:12px 0;flex-wrap:wrap;">
                            <div style="text-align:center;">
                                <span class="vui-spinner vui-spinner-sm" role="status"><span class="vui-spinner-ring"></span></span>
                                <p style="margin-top:8px;font-size:11px;color:var(--vui-text-muted);">sm</p>
                            </div>
                            <div style="text-align:center;">
                                <span class="vui-spinner vui-spinner-md" role="status"><span class="vui-spinner-ring"></span></span>
                                <p style="margin-top:8px;font-size:11px;color:var(--vui-text-muted);">md</p>
                            </div>
                            <div style="text-align:center;">
                                <span class="vui-spinner vui-spinner-lg" role="status"><span class="vui-spinner-ring"></span></span>
                                <p style="margin-top:8px;font-size:11px;color:var(--vui-text-muted);">lg</p>
                            </div>
                        </div>',
                        'code' => '<x-spinner size="sm" />
<x-spinner size="md" />
<x-spinner size="lg" />',
                    ],
                    [
                        'title' => 'Dual Ring Counter-Spin',
                        'desc'  => 'Two concentric rings spinning in opposite directions.',
                        'preview' => '<div style="display:flex;align-items:center;gap:28px;padding:12px 0;">
                            <span class="vui-spinner-dual vui-spinner-sm"><span></span><span></span></span>
                            <span class="vui-spinner-dual vui-spinner-md"><span></span><span></span></span>
                            <span class="vui-spinner-dual vui-spinner-lg"><span></span><span></span></span>
                        </div>',
                        'code' => '<x-spinner variant="dual-ring" size="md" />',
                    ],
                    [
                        'title' => 'Bounce Dots',
                        'desc'  => 'Three staggered bouncing dots — ideal for chat / AI typing indicators.',
                        'preview' => '<div style="display:flex;align-items:center;gap:28px;padding:12px 0;">
                            <span class="vui-spinner-bounce vui-spinner-sm"><span></span><span></span><span></span></span>
                            <span class="vui-spinner-bounce vui-spinner-md"><span></span><span></span><span></span></span>
                            <span class="vui-spinner-bounce vui-spinner-lg"><span></span><span></span><span></span></span>
                        </div>',
                        'code' => '<x-spinner variant="bounce-dots" size="md" />',
                    ],
                    [
                        'title' => 'Wave Bars (Equalizer)',
                        'desc'  => 'Five oscillating bars great for media players and audio UIs.',
                        'preview' => '<div style="display:flex;align-items:center;gap:28px;padding:12px 0;">
                            <span class="vui-spinner-wave vui-spinner-sm"><span></span><span></span><span></span><span></span><span></span></span>
                            <span class="vui-spinner-wave vui-spinner-md"><span></span><span></span><span></span><span></span><span></span></span>
                            <span class="vui-spinner-wave vui-spinner-lg"><span></span><span></span><span></span><span></span><span></span></span>
                        </div>',
                        'code' => '<x-spinner variant="wave-bars" size="md" />',
                    ],
                    [
                        'title' => 'Dot Grid',
                        'desc'  => '3×3 pulsating dot matrix with progressive wave delay.',
                        'preview' => '<div style="display:flex;align-items:center;gap:28px;padding:12px 0;">
                            <span class="vui-spinner-dot-grid vui-spinner-sm"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></span>
                            <span class="vui-spinner-dot-grid vui-spinner-md"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></span>
                        </div>',
                        'code' => '<x-spinner variant="dot-grid" size="md" />',
                    ],
                    [
                        'title' => 'Pulse Scale',
                        'desc'  => 'Rhythmically scaling circle with ambient glow — good for status indicators.',
                        'preview' => '<div style="display:flex;align-items:center;gap:28px;padding:12px 0;">
                            <span class="vui-spinner-pulse vui-spinner-sm"><span></span></span>
                            <span class="vui-spinner-pulse vui-spinner-md"><span></span></span>
                            <span class="vui-spinner-pulse vui-spinner-lg"><span></span></span>
                        </div>',
                        'code' => '<x-spinner variant="pulse" size="md" />',
                    ],
                ],
            ],


            // ────────────────────────────────────────────────────────────────
            // SWITCH (Skeuomorphic, Flat, Neumorphic)
            // ────────────────────────────────────────────────────────────────
            'switch' => [
                'id'       => 'switch',
                'name'     => 'Switch',
                'desc'     => 'Toggle switch with multiple aesthetic variants: native pill toggle, Skeuomorphic 3D embossed rocker, Flat 2D, and Neumorphic soft rocker.',
                'cli'      => 'php veldora add switch',
                'category' => 'actions',
                'variations' => [
                    [
                        'title' => 'Native Pill Switch',
                        'desc'  => 'Default Veldora switch using CSS accent-color. Animates thumb smoothly between off/on states.',
                        'preview' => '<div style="display:flex;flex-direction:column;gap:14px;padding:8px 0;max-width:280px;">
                            <label class="vui-switch-wrapper" style="background:var(--vui-surface);padding:12px 16px;border:1px solid var(--vui-border);border-radius:10px;cursor:pointer;display:flex;align-items:center;gap:12px;">
                                <input type="checkbox" class="vui-switch-input" role="switch" checked>
                                <span class="vui-switch-track vui-switch-md"><span class="vui-switch-thumb"></span></span>
                                <span class="vui-switch-label">Email Notifications</span>
                            </label>
                            <label class="vui-switch-wrapper" style="background:var(--vui-surface);padding:12px 16px;border:1px solid var(--vui-border);border-radius:10px;cursor:pointer;display:flex;align-items:center;gap:12px;">
                                <input type="checkbox" class="vui-switch-input" role="switch">
                                <span class="vui-switch-track vui-switch-md"><span class="vui-switch-thumb"></span></span>
                                <span class="vui-switch-label">Auto Sync</span>
                            </label>
                        </div>',
                        'code' => '<x-switch name="notifications" label="Email Notifications" :checked="true" />
<x-switch name="sync" label="Auto Sync" />',
                    ],
                    [
                        'title' => 'Skeuomorphic 3D Embossed Toggle',
                        'desc'  => 'Physically realistic switch with inset groove, reflective thumb, and active violet accent glow.',
                        'preview' => '<div style="display:flex;flex-direction:column;gap:16px;padding:10px 0;max-width:320px;">
                            <label class="vui-switch-custom vui-switch-skeuo">
                                <input type="checkbox" name="skeuo_s1" checked>
                                <span class="vui-switch-track"><span class="vui-switch-thumb"></span></span>
                                <span style="font-size:14px;font-weight:500;color:var(--vui-text);">Enable 2FA Authentication</span>
                            </label>
                            <label class="vui-switch-custom vui-switch-skeuo">
                                <input type="checkbox" name="skeuo_s2">
                                <span class="vui-switch-track"><span class="vui-switch-thumb"></span></span>
                                <span style="font-size:14px;font-weight:500;color:var(--vui-text);">Dark Mode Preference</span>
                            </label>
                            <label class="vui-switch-custom vui-switch-skeuo">
                                <input type="checkbox" name="skeuo_s3" checked>
                                <span class="vui-switch-track"><span class="vui-switch-thumb"></span></span>
                                <span style="font-size:14px;font-weight:500;color:var(--vui-text);">Background Sync</span>
                            </label>
                        </div>',
                        'code' => '<x-switch variant="skeuomorphic" name="twofa" label="Enable 2FA Authentication" :checked="true" />
<x-switch variant="skeuomorphic" name="darkmode" label="Dark Mode Preference" />',
                    ],
                    [
                        'title' => 'Flat 2D Toggle',
                        'desc'  => 'No gradients, no shadows — just clean geometry. Accent fill on active, muted grey on off.',
                        'preview' => '<div style="display:flex;flex-direction:column;gap:16px;padding:10px 0;max-width:320px;">
                            <label class="vui-switch-custom vui-switch-flat">
                                <input type="checkbox" name="flat_s1" checked>
                                <span class="vui-switch-track"><span class="vui-switch-thumb"></span></span>
                                <span style="font-size:14px;font-weight:500;color:var(--vui-text);">Analytics Collection</span>
                            </label>
                            <label class="vui-switch-custom vui-switch-flat">
                                <input type="checkbox" name="flat_s2">
                                <span class="vui-switch-track"><span class="vui-switch-thumb"></span></span>
                                <span style="font-size:14px;font-weight:500;color:var(--vui-text);">Marketing Emails</span>
                            </label>
                        </div>',
                        'code' => '<x-switch variant="flat" name="analytics" label="Analytics Collection" :checked="true" />
<x-switch variant="flat" name="marketing" label="Marketing Emails" />',
                    ],
                    [
                        'title' => 'Neumorphic Soft Rocker',
                        'desc'  => 'Carved inset track with extruded thumb that lifts and glows violet when activated.',
                        'preview' => '<div style="display:flex;flex-direction:column;gap:20px;padding:12px 0;max-width:320px;">
                            <label class="vui-switch-custom vui-switch-neumorphic">
                                <input type="checkbox" name="neu_s1" checked>
                                <span class="vui-switch-track"><span class="vui-switch-thumb"></span></span>
                                <span style="font-size:14px;font-weight:600;color:var(--vui-text);">Biometric Login</span>
                            </label>
                            <label class="vui-switch-custom vui-switch-neumorphic">
                                <input type="checkbox" name="neu_s2">
                                <span class="vui-switch-track"><span class="vui-switch-thumb"></span></span>
                                <span style="font-size:14px;font-weight:600;color:var(--vui-text);">Auto Backup</span>
                            </label>
                        </div>',
                        'code' => '<x-switch variant="neumorphic" name="biometric" label="Biometric Login" :checked="true" />
<x-switch variant="neumorphic" name="backup" label="Auto Backup" />',
                    ],
                ],
            ],

            // ────────────────────────────────────────────────────────────────
            // CHECKBOX (Skeuomorphic, Flat, Neumorphic)
            // ────────────────────────────────────────────────────────────────
            'checkbox' => [
                'id'       => 'checkbox',
                'name'     => 'Checkbox',
                'desc'     => 'Boolean selection control with multiple aesthetic variants: native browser, Skeuomorphic 3D engraved metallic, Flat Minimal 2D, and Neumorphic Soft UI.',
                'cli'      => 'php veldora add checkbox',
                'category' => 'forms',
                'variations' => [
                    [
                        'title' => 'Native Checkbox',
                        'desc'  => 'Default Veldora checkbox using accent-color CSS variable. Fully accessible with visible focus ring.',
                        'preview' => '<div style="display:flex;flex-direction:column;gap:12px;padding:10px 0;">
                            <div class="vui-checkbox-wrap">
                                <input type="checkbox" id="demo-chk-1" class="vui-checkbox" checked>
                                <label class="vui-checkbox-label" for="demo-chk-1">Enable real-time notification alerts</label>
                            </div>
                            <div class="vui-checkbox-wrap">
                                <input type="checkbox" id="demo-chk-2" class="vui-checkbox">
                                <label class="vui-checkbox-label" for="demo-chk-2">Receive weekly digest emails</label>
                            </div>
                            <div class="vui-checkbox-wrap">
                                <input type="checkbox" id="demo-chk-3" class="vui-checkbox" disabled>
                                <label class="vui-checkbox-label" for="demo-chk-3">SMS alerts (requires verified phone)</label>
                            </div>
                        </div>',
                        'code' => '<x-checkbox name="notify" label="Enable real-time notification alerts" :checked="true" />
<x-checkbox name="digest" label="Receive weekly digest emails" />
<x-checkbox name="sms" label="SMS alerts" :disabled="true" />',
                    ],
                    [
                        'title' => 'Skeuomorphic 3D Engraved Checkbox',
                        'desc'  => 'Physically realistic metallic recess with jewel-glow checkmark that springs in on selection.',
                        'preview' => '<div style="display:flex;flex-direction:column;gap:14px;padding:12px 0;">
                            <label class="vui-checkbox-custom vui-checkbox-skeuo">
                                <input type="checkbox" name="skeuo_c1" checked>
                                <span class="vui-checkbox-box">
                                    <svg class="vui-checkbox-check" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                </span>
                                <span style="font-size:14px;color:var(--vui-text);font-weight:500;">Enable real-time notifications</span>
                            </label>
                            <label class="vui-checkbox-custom vui-checkbox-skeuo">
                                <input type="checkbox" name="skeuo_c2">
                                <span class="vui-checkbox-box">
                                    <svg class="vui-checkbox-check" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                </span>
                                <span style="font-size:14px;color:var(--vui-text);font-weight:500;">Remember credentials on device</span>
                            </label>
                            <label class="vui-checkbox-custom vui-checkbox-skeuo">
                                <input type="checkbox" name="skeuo_c3" checked>
                                <span class="vui-checkbox-box">
                                    <svg class="vui-checkbox-check" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                </span>
                                <span style="font-size:14px;color:var(--vui-text);font-weight:500;">Agree to terms of service</span>
                            </label>
                        </div>',
                        'code' => '<x-checkbox variant="skeuomorphic" name="notify" label="Enable real-time notifications" :checked="true" />
<x-checkbox variant="skeuomorphic" name="remember" label="Remember credentials on device" />',
                    ],
                    [
                        'title' => 'Flat 2D Minimal Checkbox',
                        'desc'  => 'High-contrast geometric 2D style — no shadows, no gradients, just crisp solid accent fill on selection.',
                        'preview' => '<div style="display:flex;flex-direction:column;gap:14px;padding:12px 0;">
                            <label class="vui-checkbox-custom vui-checkbox-flat">
                                <input type="checkbox" name="flat_c1" checked>
                                <span class="vui-checkbox-box">
                                    <svg class="vui-checkbox-check" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                </span>
                                <span style="font-size:14px;color:var(--vui-text);font-weight:500;">Deploy to production</span>
                            </label>
                            <label class="vui-checkbox-custom vui-checkbox-flat">
                                <input type="checkbox" name="flat_c2">
                                <span class="vui-checkbox-box">
                                    <svg class="vui-checkbox-check" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                </span>
                                <span style="font-size:14px;color:var(--vui-text);font-weight:500;">Run database migrations</span>
                            </label>
                            <label class="vui-checkbox-custom vui-checkbox-flat">
                                <input type="checkbox" name="flat_c3" checked>
                                <span class="vui-checkbox-box">
                                    <svg class="vui-checkbox-check" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                </span>
                                <span style="font-size:14px;color:var(--vui-text);font-weight:500;">Invalidate CDN cache</span>
                            </label>
                        </div>',
                        'code' => '<x-checkbox variant="flat" name="deploy" label="Deploy to production" :checked="true" />
<x-checkbox variant="flat" name="migrate" label="Run database migrations" />',
                    ],
                    [
                        'title' => 'Neumorphic Soft UI Checkbox',
                        'desc'  => 'Extruded dual-shadow plate that recesses inward when checked, with ambient violet glow as the check indicator.',
                        'preview' => '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;padding:14px 0;">
                            <label class="vui-checkbox-custom vui-checkbox-neumorphic">
                                <input type="checkbox" name="neu_c1" checked>
                                <span class="vui-checkbox-box">
                                    <svg class="vui-checkbox-check" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                </span>
                                <span style="font-size:13.5px;color:var(--vui-text);font-weight:600;">Cache Layer</span>
                            </label>
                            <label class="vui-checkbox-custom vui-checkbox-neumorphic">
                                <input type="checkbox" name="neu_c2">
                                <span class="vui-checkbox-box">
                                    <svg class="vui-checkbox-check" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                </span>
                                <span style="font-size:13.5px;color:var(--vui-text);font-weight:600;">Email Queue</span>
                            </label>
                            <label class="vui-checkbox-custom vui-checkbox-neumorphic">
                                <input type="checkbox" name="neu_c3" checked>
                                <span class="vui-checkbox-box">
                                    <svg class="vui-checkbox-check" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                </span>
                                <span style="font-size:13.5px;color:var(--vui-text);font-weight:600;">Audit Logs</span>
                            </label>
                        </div>',
                        'code' => '<x-checkbox variant="neumorphic" name="cache" label="Cache Layer" :checked="true" />
<x-checkbox variant="neumorphic" name="queue" label="Email Queue" />
<x-checkbox variant="neumorphic" name="audit" label="Audit Logs" :checked="true" />',
                    ],
                ],
            ],


            'input' => [
                'id'       => 'input',
                'name'     => 'Input',
                'desc'     => 'Accessible form text field with label, helper, prefix/suffix addons, password toggle, and validation states.',
                'cli'      => 'php veldora add input',
                'category' => 'forms',
                'variations' => [
                    [
                        'title' => 'Standard Input with Helper',
                        'desc'  => 'Label + input + optional helper text. Uses <code>.vui-field</code> wrapper for consistent spacing.',
                        'preview' => '<div style="max-width:380px;display:flex;flex-direction:column;gap:16px;">
                            <div class="vui-field">
                                <label class="vui-label" for="inp-1">Full Name <span class="vui-required">*</span></label>
                                <input id="inp-1" type="text" placeholder="Jane Doe" class="vui-input">
                                <p class="vui-field-helper">Enter your official name as it appears on your ID.</p>
                            </div>
                        </div>',
                        'code' => '<x-input name="name" label="Full Name" placeholder="Jane Doe" :required="true"
    helper="Enter your official name as it appears on your ID." />',
                    ],
                    [
                        'title' => 'Validation Error State',
                        'desc'  => 'Adds <code>.vui-input-error</code> border and a red helper message for failed validation.',
                        'preview' => '<div style="max-width:380px;">
                            <div class="vui-field">
                                <label class="vui-label">Email Address</label>
                                <input type="email" value="not-an-email" class="vui-input vui-input-error" aria-invalid="true">
                                <p class="vui-field-error">Please enter a valid email address.</p>
                            </div>
                        </div>',
                        'code' => '<x-input name="email" type="email" label="Email Address"
    value="not-an-email" error="Please enter a valid email address." />',
                    ],
                    [
                        'title' => 'URL Prefix Addon',
                        'desc'  => 'Attach a static text prefix (e.g. a domain) flush to the input field.',
                        'preview' => '<div style="max-width:400px;">
                            <div class="vui-field">
                                <label class="vui-label">Repository URL</label>
                                <div style="display:flex;border:1px solid var(--vui-border);border-radius:var(--vui-radius);overflow:hidden;background:var(--vui-surface);">
                                    <span style="padding:8px 12px;background:var(--vui-surface-2);border-right:1px solid var(--vui-border);color:var(--vui-text-muted);font-size:13px;white-space:nowrap;display:flex;align-items:center;">github.com/</span>
                                    <input type="text" placeholder="username/repo" style="flex:1;background:transparent;border:none;outline:none;padding:8px 12px;color:var(--vui-text);font-size:14px;font-family:var(--vui-font);">
                                </div>
                            </div>
                        </div>',
                        'code' => '<x-input name="repo" label="Repository URL" prefix="github.com/" placeholder="username/repo" />',
                    ],
                    [
                        'title' => 'Password with Reveal Toggle',
                        'desc'  => 'Eye-icon button to show/hide plain text. Zero JS library required.',
                        'preview' => '<div style="max-width:380px;">
                            <div class="vui-field">
                                <label class="vui-label">Password</label>
                                <div style="position:relative;">
                                    <input id="pw-toggle" type="password" value="s3cur3P@ss!" class="vui-input" style="padding-right:42px;">
                                    <button type="button" onclick="var e=document.getElementById(\'pw-toggle\');e.type=e.type===\'password\'?\'text\':\'password\'" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--vui-text-muted);cursor:pointer;display:flex;padding:0;" title="Toggle visibility">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>',
                        'code' => '<x-input name="password" type="password" label="Password" :toggle-visible="true" />',
                    ],
                ],
            ],

            // ────────────────────────────────────────────────────────────────
            // DROPDOWN
            // ────────────────────────────────────────────────────────────────
                        // ────────────────────────────────────────────────────────────────
            // RADIO (Skeuomorphic, Flat, Neumorphic, Cards, Segmented)
            // ────────────────────────────────────────────────────────────────
            'radio' => [
                'id'       => 'radio',
                'name'     => 'Radio',
                'desc'     => 'Single-choice selection control with multiple aesthetic design styles: Skeuomorphism (3D tactile depth), Flat Minimal 2D, Neumorphism (Soft UI), Interactive Plan Cards, and Segmented Pills.',
                'cli'      => 'php veldora add radio',
                'category' => 'forms',
                'variations' => [
                    [
                        'title' => 'Skeuomorphic 3D Tactile Radio',
                        'desc'  => 'Realistic physical feel with beveled metallic outer rim, convex gradient depth, and radiant jewel glow indicator.',
                        'preview' => '<div style="display:flex;flex-direction:column;gap:14px;padding:12px 0;">
                            <label class="vui-radio-custom vui-radio-skeuo">
                                <input type="radio" name="demo_skeuo_plan" value="starter" checked>
                                <span class="vui-radio-disc"><span class="vui-radio-dot"></span></span>
                                <span class="vui-radio-label" style="font-size:14px;color:var(--vui-text);font-weight:500;">Starter Engine &middot; Free Tier</span>
                            </label>
                            <label class="vui-radio-custom vui-radio-skeuo">
                                <input type="radio" name="demo_skeuo_plan" value="pro">
                                <span class="vui-radio-disc"><span class="vui-radio-dot"></span></span>
                                <span class="vui-radio-label" style="font-size:14px;color:var(--vui-text);font-weight:500;">Pro Developer &middot; $29 / month</span>
                            </label>
                            <label class="vui-radio-custom vui-radio-skeuo">
                                <input type="radio" name="demo_skeuo_plan" value="enterprise">
                                <span class="vui-radio-disc"><span class="vui-radio-dot"></span></span>
                                <span class="vui-radio-label" style="font-size:14px;color:var(--vui-text);font-weight:500;">Enterprise Cluster &middot; Custom Scale</span>
                            </label>
                        </div>',
                        'code' => '<x-radio variant="skeuomorphic" name="plan" value="starter" label="Starter Engine" :checked="true" />
<x-radio variant="skeuomorphic" name="plan" value="pro" label="Pro Developer ($29/mo)" />
<x-radio variant="skeuomorphic" name="plan" value="enterprise" label="Enterprise Cluster" />',
                    ],
                    [
                        'title' => 'Flat 2D Minimalist Radio',
                        'desc'  => 'Modern high-contrast 2D geometric style with clean bold outlines, zero shadow blur, and sharp solid dot.',
                        'preview' => '<div style="display:flex;flex-direction:column;gap:14px;padding:12px 0;">
                            <label class="vui-radio-custom vui-radio-flat">
                                <input type="radio" name="demo_flat_env" value="production" checked>
                                <span class="vui-radio-disc"><span class="vui-radio-dot"></span></span>
                                <span class="vui-radio-label" style="font-size:14px;color:var(--vui-text);font-weight:500;">Production (ap-southeast-1)</span>
                            </label>
                            <label class="vui-radio-custom vui-radio-flat">
                                <input type="radio" name="demo_flat_env" value="staging">
                                <span class="vui-radio-disc"><span class="vui-radio-dot"></span></span>
                                <span class="vui-radio-label" style="font-size:14px;color:var(--vui-text);font-weight:500;">Staging Preview (us-east-1)</span>
                            </label>
                            <label class="vui-radio-custom vui-radio-flat">
                                <input type="radio" name="demo_flat_env" value="local">
                                <span class="vui-radio-disc"><span class="vui-radio-dot"></span></span>
                                <span class="vui-radio-label" style="font-size:14px;color:var(--vui-text);font-weight:500;">Local Environment (127.0.0.1)</span>
                            </label>
                        </div>',
                        'code' => '<x-radio variant="flat" name="env" value="production" label="Production" :checked="true" />
<x-radio variant="flat" name="env" value="staging" label="Staging Preview" />
<x-radio variant="flat" name="env" value="local" label="Local Environment" />',
                    ],
                    [
                        'title' => 'Neumorphic Soft UI Radio',
                        'desc'  => 'Extruded dual light/dark shadows with tactile sunken depression when checked and ambient purple center glow.',
                        'preview' => '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;padding:14px 0;">
                            <label class="vui-radio-custom vui-radio-neumorphic">
                                <input type="radio" name="demo_neu_theme" value="dark" checked>
                                <span class="vui-radio-disc"><span class="vui-radio-dot"></span></span>
                                <span class="vui-radio-label" style="font-size:13.5px;color:var(--vui-text);font-weight:600;">Dark Obsidian</span>
                            </label>
                            <label class="vui-radio-custom vui-radio-neumorphic">
                                <input type="radio" name="demo_neu_theme" value="midnight">
                                <span class="vui-radio-disc"><span class="vui-radio-dot"></span></span>
                                <span class="vui-radio-label" style="font-size:13.5px;color:var(--vui-text);font-weight:600;">Midnight Blue</span>
                            </label>
                            <label class="vui-radio-custom vui-radio-neumorphic">
                                <input type="radio" name="demo_neu_theme" value="cyber">
                                <span class="vui-radio-disc"><span class="vui-radio-dot"></span></span>
                                <span class="vui-radio-label" style="font-size:13.5px;color:var(--vui-text);font-weight:600;">Cyber Violet</span>
                            </label>
                        </div>',
                        'code' => '<x-radio variant="neumorphic" name="theme" value="dark" label="Dark Obsidian" :checked="true" />
<x-radio variant="neumorphic" name="theme" value="midnight" label="Midnight Blue" />
<x-radio variant="neumorphic" name="theme" value="cyber" label="Cyber Violet" />',
                    ],
                    [
                        'title' => 'Interactive Plan Selection Cards',
                        'desc'  => 'Full clickable radio cards with icon, title, description, price badge, and active border highlight.',
                        'preview' => '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:14px;width:100%;">
                            <label class="vui-radio-card">
                                <input type="radio" name="demo_card_plan" value="hobby" checked>
                                <span class="vui-radio-card-disc"><span class="vui-radio-card-dot"></span></span>
                                <div style="flex:1;">
                                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                                        <strong style="font-size:14px;color:var(--vui-text);">Hobby</strong>
                                        <span style="font-size:12px;font-weight:700;color:var(--vui-text-muted);">Free</span>
                                    </div>
                                    <p style="margin:0;font-size:12px;color:var(--vui-text-muted);line-height:1.45;">Up to 3 projects, SQLite database, community support.</p>
                                </div>
                            </label>

                            <label class="vui-radio-card">
                                <input type="radio" name="demo_card_plan" value="pro">
                                <span class="vui-radio-card-disc"><span class="vui-radio-card-dot"></span></span>
                                <div style="flex:1;">
                                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                                        <strong style="font-size:14px;color:var(--vui-text);">Pro Developer</strong>
                                        <span style="font-size:12px;font-weight:700;color:var(--vui-accent);">$19/mo</span>
                                    </div>
                                    <p style="margin:0;font-size:12px;color:var(--vui-text-muted);line-height:1.45;">Unlimited projects, MySQL ORM, priority support.</p>
                                </div>
                            </label>
                        </div>',
                        'code' => '<x-radio-card name="plan" value="hobby" title="Hobby" price="Free" :checked="true">
    Up to 3 projects, SQLite database, community support.
</x-radio-card>

<x-radio-card name="plan" value="pro" title="Pro Developer" price="$19/mo">
    Unlimited projects, MySQL ORM, priority support.
</x-radio-card>',
                    ],
                    [
                        'title' => 'Segmented Radio Group (Pill Switcher)',
                        'desc'  => 'Grouped radio buttons with sliding pill indicator — ideal for billing intervals or filter states.',
                        'preview' => '<div style="display:flex;justify-content:center;padding:12px 0;">
                            <div class="vui-radio-segmented-group">
                                <label class="vui-radio-segmented-item">
                                    <input type="radio" name="demo_billing_cycle" value="monthly" checked>
                                    <span class="vui-radio-segmented-label">Monthly Billing</span>
                                </label>
                                <label class="vui-radio-segmented-item">
                                    <input type="radio" name="demo_billing_cycle" value="annual">
                                    <span class="vui-radio-segmented-label">
                                        Annual Billing
                                        <span style="font-size:10px;padding:1px 5px;border-radius:4px;background:#22c55e;color:#000;font-weight:800;">-20%</span>
                                    </span>
                                </label>
                                <label class="vui-radio-segmented-item">
                                    <input type="radio" name="demo_billing_cycle" value="lifetime">
                                    <span class="vui-radio-segmented-label">Lifetime Deal</span>
                                </label>
                            </div>
                        </div>',
                        'code' => '<x-radio-group variant="segmented" name="billing_cycle">
    <x-radio-segmented value="monthly" label="Monthly Billing" :checked="true" />
    <x-radio-segmented value="annual" label="Annual Billing" badge="-20%" />
    <x-radio-segmented value="lifetime" label="Lifetime Deal" />
</x-radio-group>',
                    ],
                ],
            ],

            'dropdown' => [
                'id'       => 'dropdown',
                'name'     => 'Dropdown',
                'desc'     => 'Accessible click-anchored menu overlay with keyboard navigation, dividers, and icon support.',
                'cli'      => 'php veldora add dropdown',
                'category' => 'actions',
                'variations' => [
                    [
                        'title' => 'Action Menu with Shortcuts',
                        'desc'  => 'Standard contextual menu with icons, keyboard shortcut hints, and a destructive divider zone.',
                        'preview' => '<div style="min-height:200px;padding:12px 0;display:flex;align-items:flex-start;">
                            <div style="position:relative;display:inline-block;">
                                <button class="vui-btn vui-btn-secondary vui-btn-md" onclick="var d=document.getElementById(\'dd1\');d.style.display=d.style.display===\'block\'?\'none\':\'block\'" style="gap:8px;">
                                    Actions
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                                </button>
                                <div id="dd1" style="display:block;position:absolute;top:calc(100% + 6px);left:0;z-index:50;min-width:200px;background:var(--vui-surface);border:1px solid var(--vui-border);border-radius:var(--vui-radius-lg);padding:5px;box-shadow:var(--vui-shadow-lg);">
                                    <a href="#" style="display:flex;align-items:center;justify-content:space-between;padding:7px 11px;border-radius:var(--vui-radius);color:var(--vui-text);font-size:13px;text-decoration:none;" onmouseover="this.style.background=\'var(--vui-surface-2)\'" onmouseout="this.style.background=\'transparent\'">
                                        <span style="display:inline-flex;align-items:center;gap:8px;"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Edit</span>
                                        <kbd style="font-family:var(--vui-font-mono);font-size:10px;background:var(--vui-surface-2);border:1px solid var(--vui-border);padding:1px 5px;border-radius:4px;color:var(--vui-text-muted);">⌘E</kbd>
                                    </a>
                                    <a href="#" style="display:flex;align-items:center;gap:8px;padding:7px 11px;border-radius:var(--vui-radius);color:var(--vui-text);font-size:13px;text-decoration:none;" onmouseover="this.style.background=\'var(--vui-surface-2)\'" onmouseout="this.style.background=\'transparent\'">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg> Duplicate
                                    </a>
                                    <hr style="border:none;border-top:1px solid var(--vui-border);margin:4px 0;">
                                    <a href="#" style="display:flex;align-items:center;gap:8px;padding:7px 11px;border-radius:var(--vui-radius);color:var(--vui-danger);font-size:13px;text-decoration:none;" onmouseover="this.style.background=\'var(--vui-danger-bg)\'" onmouseout="this.style.background=\'transparent\'">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg> Delete
                                    </a>
                                </div>
                            </div>
                        </div>',
                        'code' => '<x-dropdown label="Actions">
    <x-dropdown-item icon="edit" shortcut="⌘E">Edit</x-dropdown-item>
    <x-dropdown-item icon="copy">Duplicate</x-dropdown-item>
    <x-dropdown-divider />
    <x-dropdown-item icon="trash" class="text-danger">Delete</x-dropdown-item>
</x-dropdown>',
                    ],
                ],
            ],

            // ────────────────────────────────────────────────────────────────
            // CARD
            // ────────────────────────────────────────────────────────────────
            'card' => [
                'id'       => 'card',
                'name'     => 'Card',
                'desc'     => 'Content container with header, body, footer sections. Supports flush body, stats, pricing, and profile variants.',
                'cli'      => 'php veldora add card',
                'category' => 'display',
                'variations' => [
                    [
                        'title' => 'Standard Card with Header & Footer',
                        'desc'  => 'Base card anatomy: <code>.vui-card-header</code>, <code>.vui-card-body</code>, <code>.vui-card-footer</code>.',
                        'preview' => '<div style="max-width:340px;">
                            <div class="vui-card">
                                <div class="vui-card-header">
                                    <h3 class="vui-card-title">Project Summary</h3>
                                    <p class="vui-card-subtitle">Last updated 2 hours ago</p>
                                </div>
                                <div class="vui-card-body">
                                    <p style="font-size:13.5px;color:var(--vui-text-muted);line-height:1.6;margin:0;">Veldora UI ships zero-dependency pure CSS components designed to integrate cleanly into any PHP project.</p>
                                </div>
                                <div class="vui-card-footer" style="display:flex;gap:8px;justify-content:flex-end;">
                                    <button class="vui-btn vui-btn-ghost vui-btn-sm">Cancel</button>
                                    <button class="vui-btn vui-btn-primary vui-btn-sm">Save</button>
                                </div>
                            </div>
                        </div>',
                        'code' => '<x-card>
    <x-slot name="header">
        <x-card-title>Project Summary</x-card-title>
        <x-card-subtitle>Last updated 2 hours ago</x-card-subtitle>
    </x-slot>
    Veldora UI ships zero-dependency pure CSS components...
    <x-slot name="footer">
        <x-button variant="ghost" size="sm">Cancel</x-button>
        <x-button variant="primary" size="sm">Save</x-button>
    </x-slot>
</x-card>',
                    ],
                    [
                        'title' => 'Pricing Tier Card',
                        'desc'  => 'Highlighted card with accent border, price, feature checklist, and CTA button.',
                        'preview' => '<div style="max-width:300px;">
                            <div class="vui-card" style="border-color:var(--vui-accent);box-shadow:0 0 0 1px var(--vui-accent);">
                                <div class="vui-card-header" style="display:flex;align-items:center;justify-content:space-between;">
                                    <h3 class="vui-card-title">Pro</h3>
                                    <span class="vui-badge vui-badge-purple">Popular</span>
                                </div>
                                <div class="vui-card-body">
                                    <div style="font-size:28px;font-weight:800;color:var(--vui-text);margin-bottom:14px;">$29<span style="font-size:14px;font-weight:400;color:var(--vui-text-muted);"> / mo</span></div>
                                    <ul style="list-style:none;padding:0;margin:0 0 18px;display:flex;flex-direction:column;gap:7px;font-size:13px;color:var(--vui-text-muted);">
                                        <li style="display:flex;align-items:center;gap:7px;"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="var(--vui-success)" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>Unlimited components</li>
                                        <li style="display:flex;align-items:center;gap:7px;"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="var(--vui-success)" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>SQLite &amp; MySQL ORM</li>
                                        <li style="display:flex;align-items:center;gap:7px;"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="var(--vui-success)" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>Priority support</li>
                                    </ul>
                                    <button class="vui-btn vui-btn-primary vui-btn-md" style="width:100%;">Get Started</button>
                                </div>
                            </div>
                        </div>',
                        'code' => '<x-card variant="pricing" title="Pro" price="$29" :highlight="true">
    <x-feature>Unlimited components</x-feature>
    <x-feature>SQLite &amp; MySQL ORM</x-feature>
    <x-feature>Priority support</x-feature>
    <x-slot name="footer">
        <x-button variant="primary" :full="true">Get Started</x-button>
    </x-slot>
</x-card>',
                    ],
                    [
                        'title' => 'User Profile Card',
                        'desc'  => 'Avatar, display name, role, stat counters, and a follow CTA.',
                        'preview' => '<div style="max-width:280px;">
                            <div class="vui-card" style="text-align:center;">
                                <div class="vui-card-body">
                                    <div class="vui-avatar vui-avatar-lg" style="margin:0 auto 12px;background:var(--vui-accent);">VR</div>
                                    <h4 style="margin:0 0 2px;font-size:15px;font-weight:700;color:var(--vui-text);">Veldora Dev</h4>
                                    <p style="margin:0 0 14px;font-size:12.5px;color:var(--vui-text-muted);">Full-stack PHP Engineer</p>
                                    <div style="display:flex;justify-content:space-around;padding:12px 0;border-top:1px solid var(--vui-border);border-bottom:1px solid var(--vui-border);margin-bottom:14px;">
                                        <div><strong style="color:var(--vui-text);font-size:15px;display:block;">42</strong><span style="font-size:11px;color:var(--vui-text-muted);">Components</span></div>
                                        <div><strong style="color:var(--vui-text);font-size:15px;display:block;">1.4k</strong><span style="font-size:11px;color:var(--vui-text-muted);">Stars</span></div>
                                    </div>
                                    <button class="vui-btn vui-btn-outline-primary vui-btn-sm" style="width:100%;">Follow</button>
                                </div>
                            </div>
                        </div>',
                        'code' => '<x-card variant="profile" name="Veldora Dev" role="Full-stack PHP Engineer">
    <x-slot name="stats">
        <x-stat label="Components" value="42" />
        <x-stat label="Stars" value="1.4k" />
    </x-slot>
    <x-button variant="outline-primary" size="sm" :full="true">Follow</x-button>
</x-card>',
                    ],
                ],
            ],

            // ────────────────────────────────────────────────────────────────
            // TOAST
            // ────────────────────────────────────────────────────────────────
                        // ────────────────────────────────────────────────────────────────
            // TOAST & NOTIFICATIONS
            // ────────────────────────────────────────────────────────────────
            'toast' => [
                'id'       => 'toast',
                'name'     => 'Toast',
                'desc'     => 'Non-blocking floating notification toasts with distinct semantic color badges, glowing accent borders, auto-dismiss timers, action buttons, and soundless micro-animations.',
                'cli'      => 'php veldora add toast',
                'category' => 'feedback',
                'variations' => [
                    [
                        'title' => 'Semantic Accent Stripe Toasts',
                        'desc'  => 'Each semantic variant has an independent color badge, high-contrast left border, distinctive icon, clear white title, and custom glow.',
                        'preview' => '<div style="display:flex;flex-direction:column;gap:14px;width:100%;max-width:460px;margin:0 auto;">
                            <!-- 1. Success Toast -->
                            <div class="vui-toast vui-toast-success" style="width:100%;">
                                <div class="vui-toast-icon-wrap">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                </div>
                                <div class="vui-toast-body">
                                    <span class="vui-toast-title">Changes Saved</span>
                                    <span class="vui-toast-msg">Your project settings have been synchronized.</span>
                                </div>
                                <button type="button" class="vui-toast-close" title="Dismiss">✕</button>
                                <div class="vui-toast-progress" style="width:70%;"></div>
                            </div>

                            <!-- 2. Danger / Error Toast -->
                            <div class="vui-toast vui-toast-danger" style="width:100%;">
                                <div class="vui-toast-icon-wrap">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                </div>
                                <div class="vui-toast-body">
                                    <span class="vui-toast-title">Payment Failed</span>
                                    <span class="vui-toast-msg">Card authentication failed. Please update payment method.</span>
                                </div>
                                <button type="button" class="vui-toast-close" title="Dismiss">✕</button>
                                <div class="vui-toast-progress" style="width:45%;"></div>
                            </div>

                            <!-- 3. Warning Toast -->
                            <div class="vui-toast vui-toast-warning" style="width:100%;">
                                <div class="vui-toast-icon-wrap">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                                </div>
                                <div class="vui-toast-body">
                                    <span class="vui-toast-title">Storage Limit Warning</span>
                                    <span class="vui-toast-msg">You have reached 88% of your monthly database capacity.</span>
                                </div>
                                <button type="button" class="vui-toast-close" title="Dismiss">✕</button>
                                <div class="vui-toast-progress" style="width:85%;"></div>
                            </div>

                            <!-- 4. Info Toast -->
                            <div class="vui-toast vui-toast-info" style="width:100%;">
                                <div class="vui-toast-icon-wrap">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                                </div>
                                <div class="vui-toast-body">
                                    <span class="vui-toast-title">Framework Update</span>
                                    <span class="vui-toast-msg">Veldora UI v0.5.2 is available with enhanced animations.</span>
                                </div>
                                <button type="button" class="vui-toast-close" title="Dismiss">✕</button>
                                <div class="vui-toast-progress" style="width:60%;"></div>
                            </div>

                            <!-- 5. Purple / Accent Toast -->
                            <div class="vui-toast vui-toast-purple" style="width:100%;">
                                <div class="vui-toast-icon-wrap">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                </div>
                                <div class="vui-toast-body">
                                    <span class="vui-toast-title">New Feature Unlocked</span>
                                    <span class="vui-toast-msg">You now have access to all 28 pure-CSS UI components.</span>
                                </div>
                                <button type="button" class="vui-toast-close" title="Dismiss">✕</button>
                                <div class="vui-toast-progress" style="width:40%;"></div>
                            </div>
                        </div>',
                        'code' => '{{-- Standard Template Usage --}}
<x-toast type="success" title="Changes Saved" message="Your project settings have been synchronized." />
<x-toast type="danger"  title="Payment Failed" message="Card authentication failed. Please retry." />
<x-toast type="warning" title="Storage Limit"  message="You have reached 88% capacity." />
<x-toast type="info"    title="Update Ready"   message="Veldora UI v0.5.2 is available." />
<x-toast type="purple"  title="Pro Features"   message="Unlocked 28 pure-CSS components." />',
                    ],
                    [
                        'title' => 'Toasts with Interactive Action Buttons',
                        'desc'  => 'Attach contextual inline action triggers like Undo, Retry, or Upgrade directly into the notification.',
                        'preview' => '<div style="display:flex;flex-direction:column;gap:14px;width:100%;max-width:460px;margin:0 auto;">
                            <!-- Action 1: Undo Delete -->
                            <div class="vui-toast vui-toast-danger" style="width:100%;">
                                <div class="vui-toast-icon-wrap">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                </div>
                                <div class="vui-toast-body">
                                    <span class="vui-toast-title">Project Deleted</span>
                                    <span class="vui-toast-msg">The repository "my-app" has been moved to trash.</span>
                                    <div>
                                        <button type="button" class="vui-toast-action" onclick="if(window.showToast) window.showToast(\'Restored project successfully!\', \'success\')">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                                            Undo Action
                                        </button>
                                    </div>
                                </div>
                                <button type="button" class="vui-toast-close" title="Dismiss">✕</button>
                            </div>

                            <!-- Action 2: Upgrade -->
                            <div class="vui-toast vui-toast-warning" style="width:100%;">
                                <div class="vui-toast-icon-wrap">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                                </div>
                                <div class="vui-toast-body">
                                    <span class="vui-toast-title">API Rate Limit</span>
                                    <span class="vui-toast-msg">95% of your 10,000 monthly API calls consumed.</span>
                                    <div>
                                        <button type="button" class="vui-toast-action" style="background:#f59e0b;color:#000;border-color:#f59e0b;font-weight:700;" onclick="if(window.showToast) window.showToast(\'Redirecting to billing...\', \'info\')">
                                            Upgrade Plan &rarr;
                                        </button>
                                    </div>
                                </div>
                                <button type="button" class="vui-toast-close" title="Dismiss">✕</button>
                            </div>
                        </div>',
                        'code' => '<x-toast type="danger" title="Project Deleted" message="Moved to trash.">
    <x-slot name="action">
        <x-button size="sm" variant="outline-danger" onclick="undoDelete()">Undo</x-button>
    </x-slot>
</x-toast>',
                    ],
                    [
                        'title' => 'Solid Vibrant Gradient Toasts',
                        'desc'  => 'Full-gradient high-impact cards designed for critical system alerts and milestone achievements.',
                        'preview' => '<div style="display:flex;flex-direction:column;gap:14px;width:100%;max-width:460px;margin:0 auto;">
                            <!-- Solid Success -->
                            <div class="vui-toast vui-toast-solid-success" style="width:100%;">
                                <div class="vui-toast-icon-wrap">✓</div>
                                <div class="vui-toast-body">
                                    <span class="vui-toast-title">Build Succeeded</span>
                                    <span class="vui-toast-msg">Production bundle compiled in 1.4s with 0 errors.</span>
                                </div>
                                <button type="button" class="vui-toast-close" style="color:#fff;" title="Dismiss">✕</button>
                            </div>

                            <!-- Solid Danger -->
                            <div class="vui-toast vui-toast-solid-danger" style="width:100%;">
                                <div class="vui-toast-icon-wrap">✕</div>
                                <div class="vui-toast-body">
                                    <span class="vui-toast-title">Critical Exception</span>
                                    <span class="vui-toast-msg">Database replication connection dropped on port 3306.</span>
                                </div>
                                <button type="button" class="vui-toast-close" style="color:#fff;" title="Dismiss">✕</button>
                            </div>

                            <!-- Solid Warning -->
                            <div class="vui-toast vui-toast-solid-warning" style="width:100%;">
                                <div class="vui-toast-icon-wrap">⚠</div>
                                <div class="vui-toast-body">
                                    <span class="vui-toast-title">High Memory Pressure</span>
                                    <span class="vui-toast-msg">Worker process 4 is using 94% allocated heap.</span>
                                </div>
                                <button type="button" class="vui-toast-close" style="color:#fff;" title="Dismiss">✕</button>
                            </div>
                        </div>',
                        'code' => '<x-toast variant="solid-success" title="Build Succeeded" message="Compiled in 1.4s." />
<x-toast variant="solid-danger"  title="Critical Exception" message="Connection dropped." />
<x-toast variant="solid-warning" title="High Memory" message="Heap load at 94%." />',
                    ],
                    [
                        'title' => 'Live Interactive Toast Playground',
                        'desc'  => 'Click below to fire real animated floating toasts into the viewport using JavaScript.',
                        'preview' => '<div style="padding:16px;background:var(--vui-surface-2);border:1px solid var(--vui-border);border-radius:12px;">
                            <p style="margin:0 0 14px;font-size:13px;color:var(--vui-text-muted);">Test all 5 semantic toast variations live in your browser:</p>
                            <div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;">
                                <button type="button" class="vui-btn vui-btn-success vui-btn-sm" onclick="window.showToast && window.showToast(\'Settings synchronized successfully!\', \'success\', \'Success\')">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                    Fire Success
                                </button>
                                <button type="button" class="vui-btn vui-btn-danger vui-btn-sm" onclick="window.showToast && window.showToast(\'Payment transaction failed (Code 402)\', \'danger\', \'Error\')">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                    Fire Danger
                                </button>
                                <button type="button" class="vui-btn vui-btn-warning vui-btn-sm" onclick="window.showToast && window.showToast(\'Storage approaching 90% threshold\', \'warning\', \'Warning\')">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                                    Fire Warning
                                </button>
                                <button type="button" class="vui-btn vui-btn-outline-primary vui-btn-sm" onclick="window.showToast && window.showToast(\'Veldora v0.5.2 released\', \'info\', \'Notice\')">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                                    Fire Info
                                </button>
                                <button type="button" class="vui-btn vui-btn-primary vui-btn-sm" onclick="window.showToast && window.showToast(\'All 28 pure-CSS UI components unlocked\', \'purple\', \'Veldora Pro\')">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                    Fire Purple
                                </button>
                            </div>
                        </div>',
                        'code' => '// Trigger toasts from any JavaScript script:
window.showToast("Settings synchronized successfully!", "success", "Success");
window.showToast("Payment transaction failed", "danger", "Error");
window.showToast("Storage approaching 90%", "warning", "Warning");
window.showToast("Veldora v0.5.2 released", "info", "Notice");
window.showToast("Pro features activated", "purple", "Veldora Pro");',
                    ],
                ],
            ],

            'navbar' => [
                'id'       => 'navbar',
                'name'     => 'Navbar',
                'desc'     => 'Responsive top navigation bar with brand, nav links, search, and action slot.',
                'cli'      => 'php veldora add navbar',
                'category' => 'navigation',
                'variations' => [
                    [
                        'title' => 'Standard Sticky Navbar',
                        'desc'  => 'Clean minimal navbar that uses Veldora surface tokens. Sticky via CSS, no JS needed.',
                        'preview' => '<div style="width:100%;border-radius:var(--vui-radius-lg);overflow:hidden;border:1px solid var(--vui-border);">
                            <nav style="display:flex;align-items:center;justify-content:space-between;padding:10px 18px;background:var(--vui-surface);border-bottom:1px solid var(--vui-border);">
                                <div style="display:flex;align-items:center;gap:20px;">
                                    <a href="#" style="display:flex;align-items:center;gap:8px;text-decoration:none;color:var(--vui-text);font-weight:700;font-size:14.5px;">
                                        <span style="width:22px;height:22px;background:var(--vui-accent);border-radius:5px;display:inline-flex;align-items:center;justify-content:center;color:#fff;font-size:11px;font-weight:800;">V</span>
                                        Veldora
                                    </a>
                                    <div style="display:flex;gap:14px;font-size:13px;">
                                        <a href="#" style="color:var(--vui-accent);text-decoration:none;font-weight:600;">Docs</a>
                                        <a href="#" style="color:var(--vui-text-muted);text-decoration:none;">Components</a>
                                        <a href="#" style="color:var(--vui-text-muted);text-decoration:none;">GitHub</a>
                                    </div>
                                </div>
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <button class="vui-btn vui-btn-ghost vui-btn-sm">Log in</button>
                                    <button class="vui-btn vui-btn-primary vui-btn-sm">Get Started</button>
                                </div>
                            </nav>
                        </div>',
                        'code' => '<x-navbar brand="Veldora">
    <x-navbar-link href="/docs" :active="true">Docs</x-navbar-link>
    <x-navbar-link href="/components">Components</x-navbar-link>
    <x-navbar-link href="https://github.com/veldorahq">GitHub</x-navbar-link>
    <x-slot name="actions">
        <x-button variant="ghost" size="sm">Log in</x-button>
        <x-button variant="primary" size="sm">Get Started</x-button>
    </x-slot>
</x-navbar>',
                    ],
                ],
            ],

            // ────────────────────────────────────────────────────────────────
            // FOOTER
            // ────────────────────────────────────────────────────────────────
            'footer' => [
                'id'       => 'footer',
                'name'     => 'Footer',
                'desc'     => 'Site footer with multi-column link sections, brand bio, and copyright line.',
                'cli'      => 'php veldora add footer',
                'category' => 'layout',
                'variations' => [
                    [
                        'title' => 'Multi-Column Footer',
                        'desc'  => 'Brand description + 3 link columns. Collapses to single column on mobile.',
                        'preview' => '<div style="background:var(--vui-surface);border:1px solid var(--vui-border);border-radius:var(--vui-radius-lg);padding:24px 20px 16px;width:100%;box-sizing:border-box;">
                            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:20px;margin-bottom:20px;">
                                <div>
                                    <h4 style="margin:0 0 8px;font-size:14px;color:var(--vui-text);font-weight:700;">Veldora UI</h4>
                                    <p style="font-size:12px;line-height:1.65;color:var(--vui-text-muted);margin:0;">Pure CSS components for PHP 8.2+.</p>
                                </div>
                                <div>
                                    <h5 style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--vui-text);margin:0 0 8px;letter-spacing:0.05em;">Product</h5>
                                    <div style="display:flex;flex-direction:column;gap:5px;font-size:12.5px;">
                                        <a href="#" style="color:var(--vui-text-muted);text-decoration:none;">Components</a>
                                        <a href="#" style="color:var(--vui-text-muted);text-decoration:none;">CLI Tools</a>
                                    </div>
                                </div>
                                <div>
                                    <h5 style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--vui-text);margin:0 0 8px;letter-spacing:0.05em;">Resources</h5>
                                    <div style="display:flex;flex-direction:column;gap:5px;font-size:12.5px;">
                                        <a href="#" style="color:var(--vui-text-muted);text-decoration:none;">Documentation</a>
                                        <a href="#" style="color:var(--vui-text-muted);text-decoration:none;">GitHub</a>
                                    </div>
                                </div>
                            </div>
                            <div style="border-top:1px solid var(--vui-border);padding-top:14px;text-align:center;font-size:11.5px;color:var(--vui-text-muted);">
                                &copy; 2026 Veldora HQ. MIT License.
                            </div>
                        </div>',
                        'code' => '<x-footer brand="Veldora UI" tagline="Pure CSS components for PHP 8.2+.">
    <x-footer-column title="Product">
        <x-footer-link href="/components">Components</x-footer-link>
        <x-footer-link href="/cli">CLI Tools</x-footer-link>
    </x-footer-column>
    <x-footer-column title="Resources">
        <x-footer-link href="/docs">Documentation</x-footer-link>
        <x-footer-link href="https://github.com/veldorahq">GitHub</x-footer-link>
    </x-footer-column>
</x-footer>',
                    ],
                ],
            ],

            // ────────────────────────────────────────────────────────────────
            // ALERT
            // ────────────────────────────────────────────────────────────────
            'alert' => [
                'id'       => 'alert',
                'name'     => 'Alert',
                'desc'     => 'Contextual inline notification with semantic colour, icon, title, message, and optional dismiss button.',
                'cli'      => 'php veldora add alert',
                'category' => 'feedback',
                'variations' => [
                    [
                        'title' => 'Semantic Alerts',
                        'desc'  => 'All four semantic states — colours inherit from Veldora design tokens.',
                        'preview' => '<div style="display:flex;flex-direction:column;gap:10px;width:100%;">
                            <div class="vui-alert vui-alert-success">
                                <span class="vui-alert-icon">✓</span>
                                <div class="vui-alert-body"><p class="vui-alert-title">Deployment Successful</p><p class="vui-alert-message">Your application v0.5.1 is live.</p></div>
                            </div>
                            <div class="vui-alert vui-alert-info">
                                <span class="vui-alert-icon">ℹ</span>
                                <div class="vui-alert-body"><p class="vui-alert-title">Framework Update</p><p class="vui-alert-message">Veldora v0.6 is available with new components.</p></div>
                            </div>
                            <div class="vui-alert vui-alert-warning">
                                <span class="vui-alert-icon">⚠</span>
                                <div class="vui-alert-body"><p class="vui-alert-title">Expiring Soon</p><p class="vui-alert-message">Your API key expires in 3 days.</p></div>
                            </div>
                            <div class="vui-alert vui-alert-danger">
                                <span class="vui-alert-icon">✕</span>
                                <div class="vui-alert-body"><p class="vui-alert-title">Connection Failed</p><p class="vui-alert-message">Could not reach database host 127.0.0.1.</p></div>
                            </div>
                        </div>',
                        'code' => '<x-alert type="success" title="Deployment Successful">Your application v0.5.1 is live.</x-alert>
<x-alert type="info"    title="Framework Update">Veldora v0.6 is available.</x-alert>
<x-alert type="warning" title="Expiring Soon">Your API key expires in 3 days.</x-alert>
<x-alert type="danger"  title="Connection Failed">Could not reach database.</x-alert>',
                    ],
                ],
            ],

            // ────────────────────────────────────────────────────────────────
            // BADGE
            // ────────────────────────────────────────────────────────────────
            'badge' => [
                'id'       => 'badge',
                'name'     => 'Badge',
                'desc'     => 'Compact pill for status labels, counters, category tags, and version strings.',
                'cli'      => 'php veldora add badge',
                'category' => 'feedback',
                'variations' => [
                    [
                        'title' => 'Semantic Badge Variants',
                        'desc'  => 'Uses <code>.vui-badge-{variant}</code> classes tied to Veldora semantic tokens.',
                        'preview' => '<div style="display:flex;flex-wrap:wrap;gap:8px;align-items:center;padding:8px 0;">
                            <span class="vui-badge vui-badge-default">Default</span>
                            <span class="vui-badge vui-badge-success">Active</span>
                            <span class="vui-badge vui-badge-warning">Pending</span>
                            <span class="vui-badge vui-badge-danger">Failed</span>
                            <span class="vui-badge vui-badge-info">Beta</span>
                            <span class="vui-badge vui-badge-purple">New</span>
                        </div>',
                        'code' => '<x-badge>Default</x-badge>
<x-badge color="success">Active</x-badge>
<x-badge color="warning">Pending</x-badge>
<x-badge color="danger">Failed</x-badge>
<x-badge color="info">Beta</x-badge>
<x-badge color="purple">New</x-badge>',
                    ],
                ],
            ],

            // ────────────────────────────────────────────────────────────────
            // SIDEBAR
            // ────────────────────────────────────────────────────────────────
                        // ────────────────────────────────────────────────────────────────
            // SIDEBAR
            // ────────────────────────────────────────────────────────────────
            'sidebar' => [
                'id'       => 'sidebar',
                'name'     => 'Sidebar',
                'desc'     => 'Modern SaaS application navigation with workspace switcher, search shortcut (⌘K), categorized navigation groups, active indicators, badge pills, collapsible tree, and user profile footer.',
                'cli'      => 'php veldora add sidebar',
                'category' => 'navigation',
                'variations' => [
                    [
                        'title' => 'Modern SaaS Application Sidebar',
                        'desc'  => 'Full-featured dashboard sidebar with Workspace Switcher, Quick Search (⌘K), categorized sections, active state highlight, and User Profile footer.',
                        'preview' => '<div style="display:flex;justify-content:center;width:100%;padding:10px 0;">
                            <div class="vui-sidebar" style="width:280px;height:560px;border:1px solid var(--vui-border);border-radius:14px;box-shadow:0 20px 40px -10px rgba(0,0,0,0.5);">
                                <!-- Workspace Header -->
                                <div class="vui-sidebar-header">
                                    <a href="#" class="vui-sidebar-brand" onclick="if(window.showToast) window.showToast(\'Switched workspace to Veldora HQ\', \'info\'); return false;">
                                        <div class="vui-sidebar-logo">V</div>
                                        <div style="display:flex;flex-direction:column;gap:2px;min-width:0;">
                                            <span class="vui-sidebar-brand-text">Veldora HQ</span>
                                            <span class="vui-sidebar-brand-badge">Pro Plan</span>
                                        </div>
                                        <span class="vui-sidebar-brand-caret">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                                        </span>
                                    </a>
                                </div>

                                <!-- Quick Search Button -->
                                <div class="vui-sidebar-search-box">
                                    <button type="button" class="vui-sidebar-search-btn" onclick="if(window.showToast) window.showToast(\'Command Palette opened (⌘K)\', \'info\')">
                                        <div class="vui-sidebar-search-left">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                            <span class="vui-sidebar-search-text">Search...</span>
                                        </div>
                                        <kbd class="vui-sidebar-search-kbd">⌘K</kbd>
                                    </button>
                                </div>

                                <!-- Navigation Body -->
                                <div class="vui-sidebar-body">
                                    <!-- Platform Group -->
                                    <div class="vui-sidebar-group">
                                        <p class="vui-sidebar-group-title">Platform</p>
                                        <ul class="vui-sidebar-nav">
                                            <li class="vui-sidebar-item">
                                                <a href="#" class="vui-sidebar-link active" onclick="return false;">
                                                    <span class="vui-sidebar-icon">
                                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                                                    </span>
                                                    <span class="vui-sidebar-label">Dashboard</span>
                                                </a>
                                            </li>
                                            <li class="vui-sidebar-item">
                                                <a href="#" class="vui-sidebar-link" onclick="return false;">
                                                    <span class="vui-sidebar-icon">
                                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                                                    </span>
                                                    <span class="vui-sidebar-label">Analytics</span>
                                                    <span class="vui-sidebar-badge vui-sidebar-badge-accent">Live</span>
                                                </a>
                                            </li>
                                            <li class="vui-sidebar-item">
                                                <a href="#" class="vui-sidebar-link" onclick="return false;">
                                                    <span class="vui-sidebar-icon">
                                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                                    </span>
                                                    <span class="vui-sidebar-label">Customers</span>
                                                    <span class="vui-sidebar-badge">1.4k</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>

                                    <!-- Manage Group -->
                                    <div class="vui-sidebar-group">
                                        <p class="vui-sidebar-group-title">Configuration</p>
                                        <ul class="vui-sidebar-nav">
                                            <li class="vui-sidebar-item vui-sidebar-open">
                                                <a href="#" class="vui-sidebar-link" onclick="return false;">
                                                    <span class="vui-sidebar-icon">
                                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                                                    </span>
                                                    <span class="vui-sidebar-label">Settings</span>
                                                    <span class="vui-sidebar-chevron">
                                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                                                    </span>
                                                </a>
                                                <ul class="vui-sidebar-sub">
                                                    <li><a href="#" class="vui-sidebar-sub-link active" onclick="return false;">Team Members</a></li>
                                                    <li><a href="#" class="vui-sidebar-sub-link" onclick="return false;">API Keys</a></li>
                                                    <li><a href="#" class="vui-sidebar-sub-link" onclick="return false;">Billing & Plans</a></li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- User Profile Footer -->
                                <div class="vui-sidebar-footer">
                                    <a href="#" class="vui-sidebar-user" onclick="if(window.showToast) window.showToast(\'Profile settings clicked\', \'info\'); return false;">
                                        <div class="vui-sidebar-user-avatar">
                                            AM
                                            <span class="vui-sidebar-user-status"></span>
                                        </div>
                                        <div class="vui-sidebar-user-info">
                                            <span class="vui-sidebar-user-name">Alex Mercer</span>
                                            <span class="vui-sidebar-user-role">alex@veldorahq.dev</span>
                                        </div>
                                        <span class="vui-sidebar-user-more">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="1"/><circle cx="12" cy="5" r="1"/><circle cx="12" cy="19" r="1"/></svg>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>',
                        'code' => '<x-sidebar
    :brand="[\'name\' => \'Veldora HQ\', \'plan\' => \'Pro Plan\', \'logo\' => \'V\']"
    :search="true"
    :groups="[
        [
            \'title\' => \'Platform\',
            \'items\' => [
                [\'label\' => \'Dashboard\', \'href\' => \'/dashboard\', \'icon\' => \'layout\', \'active\' => true],
                [\'label\' => \'Analytics\', \'href\' => \'/analytics\', \'icon\' => \'activity\', \'badge\' => \'Live\', \'badge_accent\' => true],
                [\'label\' => \'Customers\', \'href\' => \'/customers\', \'icon\' => \'users\', \'badge\' => \'1.4k\'],
            ]
        ],
        [
            \'title\' => \'Configuration\',
            \'items\' => [
                [
                    \'label\'    => \'Settings\',
                    \'icon\'     => \'settings\',
                    \'children\' => [
                        [\'label\' => \'Team Members\', \'href\' => \'/settings/team\', \'active\' => true],
                        [\'label\' => \'API Keys\', \'href\' => \'/settings/api\'],
                        [\'label\' => \'Billing\', \'href\' => \'/settings/billing\'],
                    ]
                ]
            ]
        ]
    ]"
    :user="[\'name\' => \'Alex Mercer\', \'role\' => \'alex@veldorahq.dev\', \'avatar\' => \'AM\']"
/>',
                    ],
                    [
                        'title' => 'Compact Icon-Only Collapsed Sidebar',
                        'desc'  => 'Space-saving 68px width mini sidebar with centered icons and clean profile footer.',
                        'preview' => '<div style="display:flex;justify-content:center;width:100%;padding:10px 0;">
                            <div class="vui-sidebar vui-sidebar-collapsed" style="height:480px;border:1px solid var(--vui-border);border-radius:14px;box-shadow:0 20px 40px -10px rgba(0,0,0,0.5);">
                                <div class="vui-sidebar-header">
                                    <a href="#" class="vui-sidebar-brand" onclick="return false;">
                                        <div class="vui-sidebar-logo">V</div>
                                    </a>
                                </div>
                                <div class="vui-sidebar-body">
                                    <ul class="vui-sidebar-nav">
                                        <li class="vui-sidebar-item">
                                            <a href="#" class="vui-sidebar-link active" title="Dashboard" onclick="return false;">
                                                <span class="vui-sidebar-icon">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                                                </span>
                                            </a>
                                        </li>
                                        <li class="vui-sidebar-item">
                                            <a href="#" class="vui-sidebar-link" title="Analytics" onclick="return false;">
                                                <span class="vui-sidebar-icon">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                                                </span>
                                            </a>
                                        </li>
                                        <li class="vui-sidebar-item">
                                            <a href="#" class="vui-sidebar-link" title="Customers" onclick="return false;">
                                                <span class="vui-sidebar-icon">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                                </span>
                                            </a>
                                        </li>
                                        <li class="vui-sidebar-item">
                                            <a href="#" class="vui-sidebar-link" title="Settings" onclick="return false;">
                                                <span class="vui-sidebar-icon">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                                                </span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="vui-sidebar-footer">
                                    <a href="#" class="vui-sidebar-user" title="Alex Mercer" onclick="return false;">
                                        <div class="vui-sidebar-user-avatar">
                                            AM
                                            <span class="vui-sidebar-user-status"></span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>',
                        'code' => '<x-sidebar :collapsed="true" :items="$navItems" :user="$user" />',
                    ],
                    [
                        'title' => 'Interactive Live Collapse & Expand Demo',
                        'desc'  => 'Toggle sidebar width dynamically between full 260px and compact 68px mode.',
                        'preview' => '<div style="display:flex;flex-direction:column;gap:14px;align-items:center;width:100%;">
                            <button type="button" class="vui-btn vui-btn-secondary vui-btn-sm" onclick="(function(){
                                var sb = document.getElementById(\'demo-interactive-sb\');
                                var isCol = sb.classList.toggle(\'vui-sidebar-collapsed\');
                                if(window.showToast) window.showToast(isCol ? \'Sidebar collapsed to icon mode\' : \'Sidebar expanded\', \'info\');
                            })()">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                                Toggle Sidebar Mode
                            </button>

                            <div id="demo-interactive-sb" class="vui-sidebar" style="height:480px;border:1px solid var(--vui-border);border-radius:14px;box-shadow:0 20px 40px -10px rgba(0,0,0,0.5);">
                                <div class="vui-sidebar-header">
                                    <a href="#" class="vui-sidebar-brand" onclick="return false;">
                                        <div class="vui-sidebar-logo">V</div>
                                        <div style="display:flex;flex-direction:column;gap:2px;min-width:0;">
                                            <span class="vui-sidebar-brand-text">Veldora Studio</span>
                                            <span class="vui-sidebar-brand-badge">Enterprise</span>
                                        </div>
                                    </a>
                                </div>
                                <div class="vui-sidebar-body">
                                    <div class="vui-sidebar-group">
                                        <p class="vui-sidebar-group-title">Main</p>
                                        <ul class="vui-sidebar-nav">
                                            <li class="vui-sidebar-item">
                                                <a href="#" class="vui-sidebar-link active" onclick="return false;">
                                                    <span class="vui-sidebar-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg></span>
                                                    <span class="vui-sidebar-label">Projects</span>
                                                    <span class="vui-sidebar-badge vui-sidebar-badge-accent">24</span>
                                                </a>
                                            </li>
                                            <li class="vui-sidebar-item">
                                                <a href="#" class="vui-sidebar-link" onclick="return false;">
                                                    <span class="vui-sidebar-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg></span>
                                                    <span class="vui-sidebar-label">Deployments</span>
                                                    <span class="vui-sidebar-badge vui-sidebar-badge-success">Ready</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="vui-sidebar-footer">
                                    <a href="#" class="vui-sidebar-user" onclick="return false;">
                                        <div class="vui-sidebar-user-avatar">
                                            VS
                                            <span class="vui-sidebar-user-status"></span>
                                        </div>
                                        <div class="vui-sidebar-user-info">
                                            <span class="vui-sidebar-user-name">Sarah Chen</span>
                                            <span class="vui-sidebar-user-role">sarah@studio.dev</span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>',
                        'code' => '<x-sidebar :brand="[\'name\' => \'Veldora Studio\', \'plan\' => \'Enterprise\']" :items="$navItems" />',
                    ],
                ],
            ],

            'table' => [
                'id'       => 'table',
                'name'     => 'Table',
                'desc'     => 'Sortable data table with status badges, action buttons, and hover row highlight.',
                'cli'      => 'php veldora add table',
                'category' => 'display',
                'variations' => [
                    [
                        'title' => 'Data Table with Badges',
                        'desc'  => 'Each row uses <code>.vui-badge</code> for status and a ghost button for inline actions.',
                        'preview' => '<div style="overflow-x:auto;width:100%;border:1px solid var(--vui-border);border-radius:var(--vui-radius-lg);">
                            <table class="vui-table" style="width:100%;border-collapse:collapse;font-size:13px;">
                                <thead>
                                    <tr style="background:var(--vui-surface-2);color:var(--vui-text-muted);border-bottom:1px solid var(--vui-border);">
                                        <th style="padding:10px 14px;text-align:left;font-weight:600;font-size:11.5px;text-transform:uppercase;letter-spacing:0.05em;">Name</th>
                                        <th style="padding:10px 14px;text-align:left;font-weight:600;font-size:11.5px;text-transform:uppercase;letter-spacing:0.05em;">Role</th>
                                        <th style="padding:10px 14px;text-align:left;font-weight:600;font-size:11.5px;text-transform:uppercase;letter-spacing:0.05em;">Status</th>
                                        <th style="padding:10px 14px;text-align:right;font-weight:600;font-size:11.5px;text-transform:uppercase;letter-spacing:0.05em;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="border-bottom:1px solid var(--vui-border);background:var(--vui-surface);" onmouseover="this.style.background=\'var(--vui-surface-2)\'" onmouseout="this.style.background=\'var(--vui-surface)\'">
                                        <td style="padding:11px 14px;color:var(--vui-text);font-weight:500;">Alex Mercer</td>
                                        <td style="padding:11px 14px;color:var(--vui-text-muted);">Lead Dev</td>
                                        <td style="padding:11px 14px;"><span class="vui-badge vui-badge-success">Active</span></td>
                                        <td style="padding:11px 14px;text-align:right;"><button class="vui-btn vui-btn-ghost vui-btn-sm">Edit</button></td>
                                    </tr>
                                    <tr style="background:var(--vui-surface);" onmouseover="this.style.background=\'var(--vui-surface-2)\'" onmouseout="this.style.background=\'var(--vui-surface)\'">
                                        <td style="padding:11px 14px;color:var(--vui-text);font-weight:500;">Elena Rostova</td>
                                        <td style="padding:11px 14px;color:var(--vui-text-muted);">Designer</td>
                                        <td style="padding:11px 14px;"><span class="vui-badge vui-badge-warning">Pending</span></td>
                                        <td style="padding:11px 14px;text-align:right;"><button class="vui-btn vui-btn-ghost vui-btn-sm">Edit</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>',
                        'code' => '<x-table :headers="[\'Name\', \'Role\', \'Status\', \'Action\']">
    <x-table-row>
        <x-table-cell font-weight="500">Alex Mercer</x-table-cell>
        <x-table-cell>Lead Dev</x-table-cell>
        <x-table-cell><x-badge color="success">Active</x-badge></x-table-cell>
        <x-table-cell align="right"><x-button variant="ghost" size="sm">Edit</x-button></x-table-cell>
    </x-table-row>
</x-table>',
                    ],
                ],
            ],

        ]; // end map
    }

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
                'desc'    => 'Single-choice selection control with multiple aesthetics: Skeuomorphism (3D tactile depth), Flat Minimal 2D, Neumorphic Soft UI, and Radio Cards.',
                'cli'     => 'php veldora add radio',
                'preview' => <<<'HTML'
<div style="display:flex;flex-direction:column;gap:14px;width:100%;">
    <!-- Skeuomorphic & Flat Demo Row -->
    <div style="display:flex;flex-wrap:wrap;gap:18px;align-items:center;">
        <label class="vui-radio-custom vui-radio-skeuo">
            <input type="radio" name="catalog_radio_skeuo" value="1" checked>
            <span class="vui-radio-disc"><span class="vui-radio-dot"></span></span>
            <span style="font-size:13.5px;color:var(--vui-text);font-weight:500;">Skeuomorphic 3D</span>
        </label>
        <label class="vui-radio-custom vui-radio-flat">
            <input type="radio" name="catalog_radio_flat" value="1" checked>
            <span class="vui-radio-disc"><span class="vui-radio-dot"></span></span>
            <span style="font-size:13.5px;color:var(--vui-text);font-weight:500;">Flat Minimal 2D</span>
        </label>
        <label class="vui-radio-custom vui-radio-neumorphic" style="padding:6px 12px;">
            <input type="radio" name="catalog_radio_neu" value="1" checked>
            <span class="vui-radio-disc"><span class="vui-radio-dot"></span></span>
            <span style="font-size:13.5px;color:var(--vui-text);font-weight:500;">Neumorphic Soft UI</span>
        </label>
    </div>
</div>
HTML,
                'code'    => <<<'CODE'
<x-radio variant="skeuomorphic" name="plan" value="pro" label="Skeuomorphic 3D" :checked="true" />
<x-radio variant="flat" name="env" value="prod" label="Flat Minimal 2D" :checked="true" />
<x-radio variant="neumorphic" name="mode" value="dark" label="Neumorphic Soft UI" :checked="true" />
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
                'desc'    => 'Modern SaaS application navigation with workspace switcher, search shortcut (⌘K), categorized groups, active states, badges, and user profile footer.',
                'cli'     => 'php veldora add sidebar',
                'preview' => <<<'HTML'
<div style="width:100%;max-width:280px;margin:0 auto;">
    <div class="vui-sidebar" style="height:440px;border:1px solid var(--vui-border);border-radius:12px;box-shadow:0 12px 30px -10px rgba(0,0,0,0.5);">
        <!-- Workspace Header -->
        <div class="vui-sidebar-header">
            <a href="javascript:void(0)" class="vui-sidebar-brand" onclick="window.showToast('Workspace switched', 'info')">
                <div class="vui-sidebar-logo">V</div>
                <div style="display:flex;flex-direction:column;gap:2px;min-width:0;">
                    <span class="vui-sidebar-brand-text">Veldora HQ</span>
                    <span class="vui-sidebar-brand-badge">Pro Plan</span>
                </div>
            </a>
        </div>
        <!-- Quick Search -->
        <div class="vui-sidebar-search-box">
            <button type="button" class="vui-sidebar-search-btn" onclick="window.showToast('Search triggered (⌘K)', 'info')">
                <div class="vui-sidebar-search-left">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <span class="vui-sidebar-search-text">Search...</span>
                </div>
                <kbd class="vui-sidebar-search-kbd">⌘K</kbd>
            </button>
        </div>
        <!-- Nav Body -->
        <div class="vui-sidebar-body">
            <div class="vui-sidebar-group">
                <p class="vui-sidebar-group-title">Platform</p>
                <ul class="vui-sidebar-nav">
                    <li class="vui-sidebar-item">
                        <a href="javascript:void(0)" class="vui-sidebar-link active" onclick="window.showToast('Dashboard clicked')">
                            <span class="vui-sidebar-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg></span>
                            <span class="vui-sidebar-label">Dashboard</span>
                        </a>
                    </li>
                    <li class="vui-sidebar-item">
                        <a href="javascript:void(0)" class="vui-sidebar-link" onclick="window.showToast('Analytics clicked')">
                            <span class="vui-sidebar-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></span>
                            <span class="vui-sidebar-label">Analytics</span>
                            <span class="vui-sidebar-badge vui-sidebar-badge-accent">Live</span>
                        </a>
                    </li>
                    <li class="vui-sidebar-item">
                        <a href="javascript:void(0)" class="vui-sidebar-link" onclick="window.showToast('Customers clicked')">
                            <span class="vui-sidebar-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></span>
                            <span class="vui-sidebar-label">Customers</span>
                            <span class="vui-sidebar-badge">1.4k</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Footer -->
        <div class="vui-sidebar-footer">
            <a href="javascript:void(0)" class="vui-sidebar-user" onclick="window.showToast('User profile settings')">
                <div class="vui-sidebar-user-avatar">
                    AM
                    <span class="vui-sidebar-user-status"></span>
                </div>
                <div class="vui-sidebar-user-info">
                    <span class="vui-sidebar-user-name">Alex Mercer</span>
                    <span class="vui-sidebar-user-role">alex@veldorahq.dev</span>
                </div>
            </a>
        </div>
    </div>
</div>
HTML,
                'code'    => <<<'CODE'
<x-sidebar
    :brand="['name' => 'Veldora HQ', 'plan' => 'Pro Plan', 'logo' => 'V']"
    :search="true"
    :groups="[
        [
            'title' => 'Platform',
            'items' => [
                ['label' => 'Dashboard', 'href' => '/dashboard', 'icon' => 'layout', 'active' => true],
                ['label' => 'Analytics', 'href' => '/analytics', 'icon' => 'activity', 'badge' => 'Live', 'badge_accent' => true],
                ['label' => 'Customers', 'href' => '/customers', 'icon' => 'users', 'badge' => '1.4k'],
            ]
        ]
    ]"
    :user="['name' => 'Alex Mercer', 'role' => 'alex@veldorahq.dev', 'avatar' => 'AM']"
/>
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