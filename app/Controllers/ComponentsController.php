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

        $html = $this->view->render('pages.components', [
            'nav'        => $nav,
            'components' => $components,
        ]);

        return new Response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
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
                <button type="button" class="vui-modal-close" onclick="document.getElementById('demo-vui-modal').setAttribute('aria-hidden','true')" aria-label="Close">✕</button>
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
            <span class="vui-dropdown-caret" aria-hidden="true">▾</span>
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
            <span class="vui-dropdown-caret" aria-hidden="true">▾</span>
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
<div style="width:100%;background:var(--vui-surface);border:1px solid var(--vui-border);border-radius:var(--radius-lg);overflow:hidden;">
    <nav class="vui-navbar" role="navigation" aria-label="Demo Navbar" style="border:none;">
        <div class="vui-navbar-inner">
            <a href="javascript:void(0)" class="vui-navbar-brand" style="display:flex;align-items:center;gap:8px;text-decoration:none;">
                <span style="width:26px;height:26px;background:var(--vui-accent);border-radius:6px;display:flex;align-items:center;justify-content:center;color:#fff;box-shadow:0 0 10px rgba(124,110,245,0.4);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="white" aria-hidden="true"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                </span>
                <span style="font-weight:700;letter-spacing:-0.02em;color:var(--vui-text);">Veldora App</span>
            </a>

            <!-- Desktop Links -->
            <div class="vui-navbar-menu" id="demo-nav-menu" style="display:flex;gap:4px;align-items:center;">
                <a href="javascript:void(0)" class="active" onclick="window.showToast('Dashboard clicked')">Dashboard</a>
                <a href="javascript:void(0)" onclick="window.showToast('Documentation clicked')">Documentation</a>
                <a href="javascript:void(0)" onclick="window.showToast('Components clicked')">Components</a>
                <a href="javascript:void(0)" onclick="window.showToast('Releases clicked')">Releases</a>
            </div>

            <!-- Action buttons -->
            <div style="display:flex;align-items:center;gap:8px;">
                <button type="button" class="vui-btn vui-btn-primary vui-btn-sm" onclick="window.showToast('New project created!')">
                    + New App
                </button>
                <!-- Mobile burger toggle -->
                <button type="button" class="vui-navbar-toggle" aria-label="Toggle navigation"
                        onclick="(function(btn){
                            var menu = document.getElementById('demo-nav-menu');
                            if (menu.style.display === 'none' || !menu.classList.contains('vui-navbar-open')) {
                                menu.classList.add('vui-navbar-open');
                                menu.style.display = 'flex';
                            } else {
                                menu.classList.remove('vui-navbar-open');
                                menu.style.display = '';
                            }
                        })(this)">
                    <span class="vui-navbar-burger"></span>
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
        <x-button variant="primary" size="sm">+ New App</x-button>
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
                <td><span class="vui-badge vui-badge-secondary vui-badge-sm">v0.4.0</span></td>
                <td><span class="vui-badge vui-badge-success vui-badge-sm">Stable</span></td>
                <td style="text-align:right;"><button type="button" class="vui-btn vui-btn-ghost vui-btn-sm" onclick="window.showToast('Updating framework...')">Update</button></td>
            </tr>
            <tr>
                <td style="font-weight:600;color:var(--vui-text);">veldora/ui</td>
                <td><span class="vui-badge vui-badge-secondary vui-badge-sm">v0.4.0</span></td>
                <td><span class="vui-badge vui-badge-success vui-badge-sm">Stable</span></td>
                <td style="text-align:right;"><button type="button" class="vui-btn vui-btn-ghost vui-btn-sm" onclick="window.showToast('Viewing components...')">View</button></td>
            </tr>
            <tr>
                <td style="font-weight:600;color:var(--vui-text);">veldora-vscode</td>
                <td><span class="vui-badge vui-badge-secondary vui-badge-sm">v0.4.0</span></td>
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
        ];
    }
}