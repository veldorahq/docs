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

            // ── 22. Switch ────────────────────────────────────────────────────────────
            [
                'id'      => 'switch',
                'name'    => 'Switch',
                'desc'    => 'Toggle switch for boolean on/off states. Supports label, checked, disabled, and sm/md/lg sizes.',
                'cli'     => 'php veldora add switch',
                'preview' => <<<'HTML'
<div style="display:flex;flex-direction:column;gap:14px;">
    <label class="vui-switch-wrapper">
        <input type="checkbox" class="vui-switch-input" role="switch" aria-checked="false">
        <span class="vui-switch-track vui-switch-md"><span class="vui-switch-thumb"></span></span>
        <span class="vui-switch-label">Notifications</span>
    </label>
    <label class="vui-switch-wrapper">
        <input type="checkbox" class="vui-switch-input" role="switch" aria-checked="true" checked>
        <span class="vui-switch-track vui-switch-md"><span class="vui-switch-thumb"></span></span>
        <span class="vui-switch-label">Dark Mode (on)</span>
    </label>
    <label class="vui-switch-wrapper vui-switch-disabled">
        <input type="checkbox" class="vui-switch-input" role="switch" disabled>
        <span class="vui-switch-track vui-switch-md"><span class="vui-switch-thumb"></span></span>
        <span class="vui-switch-label">Disabled</span>
    </label>
</div>
HTML,
                'code'    => <<<'CODE'
<x-switch name="notifications" label="Notifications" />
<x-switch name="dark_mode" label="Dark Mode" :checked="true" />
<x-switch name="feature" label="Disabled" :disabled="true" />
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
    <a class="vui-page-btn vui-page-disabled" aria-disabled="true">&#8592;</a>
    <a class="vui-page-btn">1</a>
    <a class="vui-page-btn vui-page-active" aria-current="page">2</a>
    <a class="vui-page-btn">3</a>
    <span class="vui-page-ellipsis">&hellip;</span>
    <a class="vui-page-btn">10</a>
    <a class="vui-page-btn">&#8594;</a>
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
                'desc'    => 'Animated shimmer placeholder for loading states. Supports text lines, circle avatar, and rect block modes.',
                'cli'     => 'php veldora add skeleton',
                'preview' => <<<'HTML'
<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;width:100%;">
    <div class="vui-skeleton-wrap" aria-busy="true" aria-label="Loading...">
        <div class="vui-skeleton vui-skeleton-text" style="width:100%"></div>
        <div class="vui-skeleton vui-skeleton-text" style="width:100%"></div>
        <div class="vui-skeleton vui-skeleton-text" style="width:70%"></div>
    </div>
    <div class="vui-skeleton-wrap" style="display:flex;gap:12px;align-items:center;" aria-busy="true">
        <div class="vui-skeleton vui-skeleton-circle" style="width:2.5rem;height:2.5rem;flex-shrink:0"></div>
        <div style="flex:1">
            <div class="vui-skeleton vui-skeleton-text" style="width:100%"></div>
            <div class="vui-skeleton vui-skeleton-text" style="width:60%"></div>
        </div>
    </div>
</div>
HTML,
                'code'    => <<<'CODE'
{{-- Text lines --}}
<x-skeleton lines="3" />

{{-- With avatar --}}
<x-skeleton lines="2" :avatar="true" />

{{-- Rect block --}}
<x-skeleton type="rect" width="100%" height="120px" />
CODE,
            ],

            // ── 25. Empty ─────────────────────────────────────────────────────────────
            [
                'id'      => 'empty',
                'name'    => 'Empty',
                'desc'    => 'Zero-data state component with icon, title, description, and an optional action slot.',
                'cli'     => 'php veldora add empty',
                'preview' => <<<'HTML'
<div class="vui-empty" style="padding:2rem 0;">
    <div class="vui-empty-icon">
        <svg width="64" height="64" viewBox="0 0 64 64" fill="none" aria-hidden="true">
            <circle cx="32" cy="32" r="30" stroke="currentColor" stroke-width="2" stroke-dasharray="6 4"/>
            <path d="M22 32h20M32 22v20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
    </div>
    <h3 class="vui-empty-title">No results found</h3>
    <p class="vui-empty-desc">Try adjusting your search or filters.</p>
    <div class="vui-empty-action">
        <button class="vui-btn vui-btn-primary vui-btn-sm">Clear Filters</button>
    </div>
</div>
HTML,
                'code'    => <<<'CODE'
<x-empty title="No results found" description="Try adjusting your search or filters.">
    <x-button variant="primary" size="sm">Clear Filters</x-button>
</x-empty>
CODE,
            ],

            // ── 26. Divider ───────────────────────────────────────────────────────────
            [
                'id'      => 'divider',
                'name'    => 'Divider',
                'desc'    => 'Horizontal or vertical separator. Supports an optional centered text label (e.g. "OR").',
                'cli'     => 'php veldora add divider',
                'preview' => <<<'HTML'
<div style="display:flex;flex-direction:column;gap:20px;width:100%;">
    <hr class="vui-divider">
    <div class="vui-divider-labeled">
        <span class="vui-divider-line"></span>
        <span class="vui-divider-label">OR</span>
        <span class="vui-divider-line"></span>
    </div>
    <div style="display:flex;align-items:center;height:40px;gap:16px;">
        <span>Left</span>
        <div class="vui-divider-vertical" style="height:100%;"></div>
        <span>Right</span>
    </div>
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
<div style="display:flex;gap:16px;flex-wrap:wrap;padding:20px 0;">
    <div class="vui-popover-wrap">
        <button class="vui-btn vui-btn-secondary vui-btn-sm" type="button"
                onclick="const p=document.getElementById('demo-pop1');p.hidden=!p.hidden">More Info</button>
        <div id="demo-pop1" class="vui-popover vui-popover-bottom" hidden role="tooltip">
            <div class="vui-popover-title">Did you know?</div>
            <div class="vui-popover-body">Popovers can contain rich content including links and buttons.</div>
        </div>
    </div>
</div>
HTML,
                'code'    => <<<'CODE'
<x-popover trigger="More Info" title="Did you know?" placement="bottom">
    Popovers can contain rich content including links and buttons.
</x-popover>
CODE,
            ],

            // ── 29. Confirm ───────────────────────────────────────────────────────────
            [
                'id'      => 'confirm',
                'name'    => 'Confirm',
                'desc'    => 'Accessible confirmation dialog for destructive actions. Includes cancel/confirm buttons with form POST support.',
                'cli'     => 'php veldora add confirm',
                'preview' => <<<'HTML'
<button class="vui-btn vui-btn-danger vui-btn-sm" onclick="vui.confirm('demo-confirm')">Delete Item</button>
<div id="demo-confirm" class="vui-modal-backdrop" role="alertdialog" aria-modal="true" aria-hidden="true" aria-labelledby="demo-confirm-title">
    <div class="vui-modal vui-confirm-dialog">
        <div class="vui-modal-header"><h3 id="demo-confirm-title" class="vui-modal-title">Delete item?</h3></div>
        <div class="vui-modal-body"><p>This action cannot be undone.</p></div>
        <div class="vui-modal-footer">
            <button type="button" class="vui-btn vui-btn-secondary" onclick="document.getElementById('demo-confirm').setAttribute('aria-hidden','true')">Cancel</button>
            <button type="button" class="vui-btn vui-btn-danger">Delete</button>
        </div>
    </div>
</div>
<script>window.vui=window.vui||{};vui.confirm=function(id){document.getElementById(id).setAttribute('aria-hidden','false');};</script>
HTML,
                'code'    => <<<'CODE'
{{-- Trigger --}}
<button onclick="vui.confirm('del-confirm')">Delete Item</button>

{{-- Dialog --}}
<x-confirm
    id="del-confirm"
    title="Delete item?"
    message="This action cannot be undone."
    action="/items/1"
    method="DELETE"
    confirm-label="Delete"
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
                'desc'    => 'Styled drag-and-drop file upload zone with file type filters, multiple selection, and size hint.',
                'cli'     => 'php veldora add fileupload',
                'preview' => <<<'HTML'
<div style="max-width:400px;">
    <label for="demo-fu1" class="vui-label">Profile Picture</label>
    <label for="demo-fu1" class="vui-fileupload-zone">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
        </svg>
        <span class="vui-fileupload-text">Drag &amp; drop or <strong>browse</strong></span>
        <span class="vui-fileupload-hint">Max 2MB &mdash; JPG, PNG, WebP</span>
        <input type="file" id="demo-fu1" accept="image/*" class="vui-fileupload-input">
    </label>
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
<div style="max-width:300px;">
    <div class="vui-field vui-combobox-wrap" id="demo-cb1">
        <label class="vui-label" for="demo-cb1-input">Country</label>
        <div class="vui-combobox">
            <input type="text" id="demo-cb1-input" class="vui-input vui-combobox-input" placeholder="Search country..." autocomplete="off"
                   oninput="vuiCbFilter('demo-cb1')" onfocus="vuiCbOpen('demo-cb1')">
            <input type="hidden" name="country" value="">
            <ul class="vui-combobox-list" id="demo-cb1-list" role="listbox" hidden>
                <li class="vui-combobox-option" role="option" onclick="vuiCbSelect('demo-cb1','bd',this.textContent.trim())">Bangladesh</li>
                <li class="vui-combobox-option" role="option" onclick="vuiCbSelect('demo-cb1','us',this.textContent.trim())">United States</li>
                <li class="vui-combobox-option" role="option" onclick="vuiCbSelect('demo-cb1','gb',this.textContent.trim())">United Kingdom</li>
                <li class="vui-combobox-option" role="option" onclick="vuiCbSelect('demo-cb1','ca',this.textContent.trim())">Canada</li>
                <li class="vui-combobox-option" role="option" onclick="vuiCbSelect('demo-cb1','au',this.textContent.trim())">Australia</li>
            </ul>
        </div>
    </div>
</div>
<script>
function vuiCbOpen(u){document.getElementById(u+'-list').hidden=false;}
function vuiCbFilter(u){var q=document.querySelector('#'+u+' .vui-combobox-input').value.toLowerCase();document.querySelectorAll('#'+u+'-list .vui-combobox-option').forEach(function(o){o.hidden=!o.textContent.toLowerCase().includes(q);});document.getElementById(u+'-list').hidden=false;}
function vuiCbSelect(u,val,lbl){document.querySelector('#'+u+' .vui-combobox-input').value=lbl;document.querySelector('#'+u+' input[type=hidden]').value=val;document.getElementById(u+'-list').hidden=true;}
document.addEventListener('click',function(e){document.querySelectorAll('.vui-combobox-list').forEach(function(l){if(!l.closest('.vui-combobox-wrap').contains(e.target))l.hidden=true;});});
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
            <button type="button" class="vui-page-btn" onclick="window.showToast('Next page')">&rarr;</button>
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
<ol class="vui-stepper" aria-label="Progress">
    <li class="vui-stepper-step vui-step-done">
        <span class="vui-step-circle"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>
        <span class="vui-step-label">Account</span>
        <span class="vui-step-line" aria-hidden="true"></span>
    </li>
    <li class="vui-stepper-step vui-step-active" aria-current="step">
        <span class="vui-step-circle">2</span>
        <span class="vui-step-label">Profile</span>
        <span class="vui-step-line" aria-hidden="true"></span>
    </li>
    <li class="vui-stepper-step vui-step-pending">
        <span class="vui-step-circle">3</span>
        <span class="vui-step-label">Confirm</span>
    </li>
</ol>
HTML,
                'code'    => <<<'CODE'
<x-stepper :steps="['Account', 'Profile', 'Confirm']" :current="2" />
CODE,
            ],

            // ── 38. Sidebar ───────────────────────────────────────────────────────────
            [
                'id'      => 'sidebar',
                'name'    => 'Sidebar',
                'desc'    => 'Application navigation sidebar with logo, nav links, active state, icons, and collapsible sub-menus.',
                'cli'     => 'php veldora add sidebar',
                'preview' => <<<'HTML'
<aside class="vui-sidebar" role="navigation" style="width:100%;max-width:240px;border-radius:12px;border:1px solid var(--vui-border);background:var(--vui-surface);overflow:hidden;">
    <div class="vui-sidebar-header" style="display:flex;align-items:center;gap:10px;padding:16px 18px;border-bottom:1px solid var(--vui-border);">
        <div style="width:28px;height:28px;border-radius:6px;background:var(--accent);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:14px;">V</div>
        <span class="vui-sidebar-logo" style="font-size:15px;font-weight:700;letter-spacing:-.02em;">Veldora App</span>
    </div>
    <nav style="padding:10px 8px;"><ul class="vui-sidebar-nav" style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:3px;">
        <li class="vui-nav-item vui-nav-active">
            <a href="javascript:void(0)" class="vui-nav-link" style="text-decoration:none !important;" onclick="window.showToast('Dashboard selected')">
                <span class="vui-nav-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                </span>
                <span class="vui-nav-label">Dashboard</span>
            </a>
        </li>
        <li class="vui-nav-item">
            <a href="javascript:void(0)" class="vui-nav-link" style="text-decoration:none !important;" onclick="window.showToast('Team selected')">
                <span class="vui-nav-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </span>
                <span class="vui-nav-label">Team Members</span>
            </a>
        </li>
        <li class="vui-nav-item">
            <a href="javascript:void(0)" class="vui-nav-link" style="text-decoration:none !important;" onclick="window.showToast('Settings selected')">
                <span class="vui-nav-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                </span>
                <span class="vui-nav-label">Settings</span>
            </a>
        </li>
    </ul></nav>
</aside>
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
                'desc'    => 'Responsive max-width wrapper with configurable size (sm/md/lg/xl/full) and auto-centering.',
                'cli'     => 'php veldora add container',
                'preview' => <<<'HTML'
<div style="width:100%;background:var(--vui-bg);border:1px solid var(--vui-border);border-radius:12px;padding:20px 16px;box-sizing:border-box;">
    <div style="display:flex;flex-direction:column;gap:18px;width:100%;">
        
        <!-- Large Container (1024px) -->
        <div style="border:2px dashed var(--accent);border-radius:10px;padding:12px;background:rgba(124,110,245,0.05);position:relative;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;flex-wrap:wrap;gap:6px;">
                <span style="font-size:11px;font-family:var(--font-mono);background:var(--accent);color:#fff;padding:2px 8px;border-radius:4px;font-weight:600;">&lt;x-container size="lg"&gt; &mdash; max-width: 1024px</span>
                <span style="font-size:11px;color:var(--text-muted);font-family:var(--font-mono);">Auto Centered (margin: 0 auto)</span>
            </div>
            <div style="background:var(--surface-2);border:1px solid var(--border);border-radius:6px;padding:14px 18px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
                <div style="font-size:13.5px;font-weight:600;color:var(--text);">Hero Section / Main Page Content Area</div>
                <button type="button" class="vui-btn vui-btn-primary vui-btn-sm" style="pointer-events:none;">Action Button</button>
            </div>
        </div>

        <!-- Medium Container (768px) -->
        <div style="width:80%;margin:0 auto;border:2px dashed #3b82f6;border-radius:10px;padding:12px;background:rgba(59,130,246,0.05);position:relative;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;flex-wrap:wrap;gap:6px;">
                <span style="font-size:11px;font-family:var(--font-mono);background:#3b82f6;color:#fff;padding:2px 8px;border-radius:4px;font-weight:600;">&lt;x-container size="md"&gt; &mdash; max-width: 768px</span>
                <span style="font-size:11px;color:var(--text-muted);font-family:var(--font-mono);">Centered Blog / Article</span>
            </div>
            <div style="background:var(--surface-2);border:1px solid var(--border);border-radius:6px;padding:12px 16px;">
                <p style="font-size:13px;color:var(--text-muted);margin:0;">Optimized reading width with automatic left and right gutters.</p>
            </div>
        </div>

        <!-- Small Container (640px) -->
        <div style="width:60%;margin:0 auto;border:2px dashed #22c55e;border-radius:10px;padding:12px;background:rgba(34,197,94,0.05);position:relative;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;flex-wrap:wrap;gap:6px;">
                <span style="font-size:11px;font-family:var(--font-mono);background:#22c55e;color:#fff;padding:2px 8px;border-radius:4px;font-weight:600;">&lt;x-container size="sm"&gt; &mdash; max-width: 640px</span>
                <span style="font-size:11px;color:var(--text-muted);font-family:var(--font-mono);">Login / Form Card</span>
            </div>
            <div style="background:var(--surface-2);border:1px solid var(--border);border-radius:6px;padding:12px 16px;text-align:center;">
                <p style="font-size:13px;color:var(--text-muted);margin:0;">Focused narrow form / auth card wrapper</p>
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
        ];
    }
}