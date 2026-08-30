<?php

declare(strict_types=1);

namespace App\Controllers;

use Veldora\Framework\Http\Request;
use Veldora\Framework\Http\Response;
use Veldora\Framework\View\Engine;

class ChangelogController
{
    public function __construct(protected Engine $view) {}

    public function index(Request $request): Response
    {
        $changelogs = $this->buildChangelogs();

        // Inline markdown renderer: escape HTML first, then apply backtick→<code> and **bold**
        $renderInlineMarkdown = static function (string $text): string {
            $safe = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
            // `code` → <code>
            $safe = (string) preg_replace('/`([^`]+)`/', '<code class="cl-code">$1</code>', $safe);
            // **bold**
            $safe = (string) preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $safe);
            return $safe;
        };

        $html = $this->view->render('pages.changelog', [
            'changelogs'           => $changelogs,
            'renderInlineMarkdown' => $renderInlineMarkdown,
        ]);

        return new Response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    private function buildChangelogs(): array
    {
        return [
            // ── veldora/framework ────────────────────────────────────────────────
            [
                'repo'    => 'veldora/framework',
                'label'   => 'Core',
                'github'  => 'https://github.com/veldorahq/veldora-core',
                'color'   => 'accent',
                'icon'    => 'core',
                'releases' => [
                    [
                        'version' => '0.5.7',
                        'date'    => '2026-08-30',
                        'tag'     => 'latest',
                        'added'   => [
                            '**48 Built-in CLI Commands**: Complete standalone and Symfony Console dual-mode runner with all 48 commands properly wired',
                            '**`executeDirect()` on all commands**: `MakeControllerCommand`, `MakeModelCommand`, `MakeMigrationCommand`, `MakeSeederCommand`, `MakeMiddlewareCommand`, `MakeRequestCommand`, `MakeResourceCommand`, `MakeFactoryCommand`, `DownCommand`, `UpCommand` callable with zero dependencies',
                            '**Maintenance Mode (`down` / `up`)**: `php veldora down --secret=token` and `php veldora up` fully registered in CLI runner',
                            '**Queue Management**: `queue:work`, `queue:failed`, `queue:retry`, `queue:clear` commands added to standalone CLI runner',
                        ],
                        'fixed'   => [
                            '`make:migration` generates anonymous `return new class extends Migration {}` format for reliable schema loading',
                            '`make:model -m` correctly creates companion migration in standalone mode without requiring Symfony Application',
                            '`make:auth` callable directly via `executeDirect()` with zero-dependency execution',
                            'Friendly check when `composer install` has not been run',
                        ],
                    ],
                    [
                        'version' => '0.5.6',
                        'date'    => '2026-08-30',
                        'tag'     => null,
                        'added'   => [
                            'Single-source-of-truth runtime versioning via `Application::VERSION` across all CLI banners, exception handlers, and layout views',
                            'Automated PHPUnit test suites and PHPStan static analysis workflow in GitHub Actions',
                            'Automated application boot and health check diagnostics across PHP 8.2 and PHP 8.3',
                        ],
                        'fixed'   => [
                            'Eliminated hardcoded version strings across all exception handlers and runtime headers',
                        ],
                    ],
                    [
                        'version' => '0.5.5',
                        'date'    => '2026-08-30',
                        'tag'     => null,
                        'added'   => [
                            'Shipped full 41+ command CLI binary with `serve`, `doctor`, `about`, `key:generate`, `migrate`, `db:seed`, and `add` support',
                            'Clean pre-seeded starter SQLite database distribution',
                        ],
                        'fixed'   => [
                            'Synchronized CLI binary with framework core distribution',
                        ],
                    ],
                    [
                        'version' => '0.5.4',
                        'date'    => '2026-08-30',
                        'tag'     => null,
                        'added'   => [
                            'Export ignore rules in `.gitattributes` for optimized lightweight production archive downloads',
                        ],
                        'fixed'   => [],
                    ],
                    [
                        'version' => '0.5.2',
                        'date'    => '2026-08-30',
                        'tag'     => null,
                        'added'   => [
                            'Composer Packagist VCS release validation synchronization — version tags unified across all packages',
                            'Enhanced `executeDirect()` handlers for zero-dependency CLI execution',
                        ],
                        'fixed'   => [
                            'VCS driver tag version mismatch resolution in composer metadata',
                        ],
                    ],
                    [
                        'version' => '0.5.1',
                        'date'    => '2026-08-28',
                        'tag'     => null,
                        'added'   => [
                            'Enhanced routing pipeline with strict pattern constraint matching',
                            'Optimized template rendering engine cache layer',
                        ],
                        'fixed'   => [
                            'Nested view partial parameter inheritance resolution',
                        ],
                    ],
                    [
                        'version' => '0.5.0',
                        'date'    => '2026-08-25',
                        'tag'     => null,
                        'added'   => [
                            'DB Facade — `statement()`, `select()`, `selectOne()`, `insert()`, `update()`, `delete()`, `transaction()`',
                            'SoftDeletes Trait — `deleted_at` auto-management, `withTrashed()`, `onlyTrashed()`, `restore()`, `forceDelete()`',
                            'Model Lifecycle Events — `creating`, `created`, `updating`, `updated`, `deleting`, `deleted` hooks',
                            'Named Route URLs — `route(\'name\', [\'id\' => 1])` global helper with parameter substitution',
                            'ThrottleRequests Middleware — Token-bucket rate limiter, `429 Too Many Requests` response',
                            'CheckForMaintenanceMode Middleware — `storage/framework/.down` status + bypass `?secret=`',
                            'Complete Auth Scaffold — `php veldora make:auth` generates Login, Register, ForgotPassword, ResetPassword, Profile, EmailVerify views',
                            'PasswordBroker — HMAC token-based password reset with expiry validation',
                            'Console Polyfill — Zero-dependency Symfony\\Console shim (`src/Console/Polyfill.php`)',
                            'Anonymous Migration Classes — prevents duplicate class-name collisions on re-run',
                            '`executeDirect()` on MigrateCommand, RollbackCommand, FreshCommand, ListComponentsCommand, AddComponentCommand',
                            'View Compiler: `@method(\'PUT\')` / `@method(\'DELETE\')` directive to hidden `<input>` field',
                            '`Request::create()` factory method and `query()` getter',
                            '`Response::setHeader()` / `getHeader()` utilities',
                            '`Engine::renderFile()` and `renderString()` standalone methods',
                        ],
                        'fixed'   => [
                            '`Blueprint::boolean()` default value compiled as `0`/`1` instead of empty `DEFAULT ,` SQL',
                            '`QueryBuilder::get()` and `first()` auto-hydrate rows into Model instances when `modelClass` is set',
                            '`Model::find()` and `all()` handle pre-hydrated instances correctly',
                            '`ThrottleRequests` and `CheckForMaintenanceMode` `\\Closure $next` type hints',
                        ],
                    ],
                    [
                        'version' => '0.4.0',
                        'date'    => '2026-07-15',
                        'tag'     => null,
                        'added'   => [
                            'Session auth guards — `Auth::attempt()`, `Auth::logout()`, `auth()` helper',
                            'Middleware pipeline with `web`, `auth`, `guest`, `throttle` groups',
                            '`make:controller`, `make:model`, `make:migration`, `make:middleware`, `make:mail` commands',
                            'Queue system — `dispatch()`, workers, failed jobs table',
                            'Mail system — `mailer()->to()->send()` and Mailable classes',
                            'Cache system — file/array drivers and `cache()->remember()`',
                            'HTTP Client — `Http::get()`, `Http::post()`, `withToken()`',
                            'Event / Listener system',
                            'Storage system — `storage(\'disk\')->put/get/delete/url`',
                        ],
                        'fixed'   => [],
                    ],
                    [
                        'version' => '0.3.0',
                        'date'    => '2026-07-13',
                        'tag'     => null,
                        'added'   => [
                            'Initial public release',
                            'MVC architecture — Router, Service Container, View Compiler',
                            'Database QueryBuilder, Migrator, Schema Blueprint',
                            '`php veldora serve`, `php veldora migrate`, `php veldora make:*` commands',
                        ],
                        'fixed'   => [],
                    ],
                ],
            ],

            // ── veldora/ui ───────────────────────────────────────────────────────
            [
                'repo'    => 'veldora/ui',
                'label'   => 'UI',
                'github'  => 'https://github.com/veldorahq/veldora-ui',
                'color'   => 'green',
                'icon'    => 'ui',
                'releases' => [
                    [
                        'version' => '0.5.7',
                        'date'    => '2026-08-30',
                        'tag'     => 'latest',
                        'added'   => [
                            'Synchronized component registry metadata with framework core v0.5.7 release',
                            '41+ UI components verified and tested across all style variants (Skeuomorphic, Neumorphic, Flat, Glassmorphic)',
                        ],
                        'fixed'   => [],
                    ],
                    [
                        'version' => '0.5.6',
                        'date'    => '2026-08-30',
                        'tag'     => null,
                        'added'   => [
                            'Synchronized component registry metadata with framework core v0.5.6 release',
                            'Enhanced CSS custom property tokens and component layout polish',
                        ],
                        'fixed'   => [],
                    ],
                    [
                        'version' => '0.5.5',
                        'date'    => '2026-08-30',
                        'tag'     => null,
                        'added'   => [
                            'Optimized distribution archives and component export rules',
                        ],
                        'fixed'   => [],
                    ],
                    [
                        'version' => '0.5.2',
                        'date'    => '2026-08-30',
                        'tag'     => null,
                        'added'   => [
                            '**Skeuomorphic 3D Aesthetics**: Physically realistic beveled and embossed metallic variants for `Radio`, `Checkbox`, `Switch`, and `Button` (`.vui-radio-skeuo`, `.vui-checkbox-skeuo`, `.vui-switch-skeuo`, `.vui-btn-skeuo`)',
                            '**Flat Minimalist 2D Aesthetics**: High-contrast geometric solid variants with zero shadow blur for `Radio`, `Checkbox`, `Switch`, and `Button` (`.vui-radio-flat`, `.vui-checkbox-flat`, `.vui-switch-flat`, `.vui-btn-flat`)',
                            '**Neumorphic Soft UI Aesthetics**: Extruded dual-shadow soft plate with tactile sunken depression for `Radio`, `Checkbox`, `Switch`, and `Button` (`.vui-radio-neumorphic`, `.vui-checkbox-neumorphic`, `.vui-switch-neumorphic`, `.vui-btn-neumorphic`)',
                            '**Glassmorphism Button**: Frosted translucent acrylic button with backdrop-blur (`.vui-btn-glass`)',
                            '**Modern SaaS Application Sidebar**: Workspace Switcher, Quick Search (⌘K), categorized sections, active state highlight, badge pills, and User Profile footer',
                            '**Interactive Toast Engine**: Ambient notification system with `window.showToast(message, type, duration)` API and copy action feedback',
                            '**ComponentRegistry Multi-Aesthetic Support**: Template generators updated for `<x-button>`, `<x-checkbox>`, `<x-switch>`, and `<x-radio>` with `variant="skeuomorphic"`, `variant="flat"`, `variant="neumorphic"`, and `variant="glass"`',
                        ],
                        'fixed'   => [
                            'Fixed horizontal inline-flex layout for custom radio & checkbox controls so the disc/box icon and title text always sit cleanly on the same line',
                            'Packagist composer.json version alignment with git release tags',
                        ],
                    ],
                    [
                        'version' => '0.5.1',
                        'date'    => '2026-08-28',
                        'tag'     => null,
                        'added'   => [
                            'Enhanced CSS custom property tokens for dark mode palette',
                            'Accessibility ARIA attributes for custom interactive controls',
                        ],
                        'fixed'   => [
                            'Component registry CLI installer directory path resolution',
                        ],
                    ],
                    [
                        'version' => '0.5.0',
                        'date'    => '2026-08-25',
                        'tag'     => null,
                        'added'   => [
                            '`footer` — Responsive site footer with branding, nav links, and legal text',
                            '`rating` — Interactive star rating with half-star precision and read-only mode',
                            '`switch` — Toggle switch with label and checked state binding',
                            '`pagination` — Pagination bar with prev/next links',
                            '`skeleton` — Animated placeholder skeleton for loading states',
                            '`empty` — Empty state with illustration, title, description, and action slot',
                            '`divider` — Horizontal or vertical separator with optional label',
                            '`drawer` — Slide-in panel (left/right/top/bottom) with overlay',
                            '`popover` — Floating content panel anchored to a trigger',
                            '`confirm` — Confirm dialog with confirm/cancel for destructive operations',
                            '`datepicker` — Native date input with Veldora styling',
                            '`fileupload` — Drag-and-drop file upload zone',
                            '`combobox` — Searchable select with autocomplete dropdown',
                            '`inputgroup` — Input with prefix/suffix addons',
                            '`stat` — Metric stat card with value, label, icon, and trend indicator',
                            '`datatable` — Interactive table with search, sort, and pagination',
                            '`timeline` — Vertical event list with icon, title, and timestamp',
                            '`stepper` — Multi-step wizard indicator',
                            '`sidebar` — Navigation sidebar with logo and collapsible sub-menus',
                            '`container` — Responsive max-width wrapper',
                            'Total: **41+ components** available via `php veldora ui:list`',
                        ],
                        'fixed'   => [
                            '`php veldora ui:list` and `php veldora add <name>` now use `executeDirect()` — zero-dependency environments supported',
                        ],
                    ],
                    [
                        'version' => '0.4.0',
                        'date'    => '2026-07-15',
                        'tag'     => null,
                        'added'   => [
                            'Initial 21 components: button, input, textarea, select, checkbox, radio, badge, alert, card, modal, spinner, avatar, dropdown, navbar, toast, tabs, accordion, progress, tooltip, breadcrumb, table',
                            '`veldora-ui.css` base stylesheet with `--vui-*` CSS custom properties',
                            'ComponentRegistry class with template definitions',
                        ],
                        'fixed'   => [],
                    ],
                ],
            ],

            // ── create-veldora-app ───────────────────────────────────────────────
            [
                'repo'    => 'create-veldora-app',
                'label'   => 'CLI',
                'github'  => 'https://github.com/veldorahq/create-veldora-app',
                'color'   => 'purple',
                'icon'    => 'cli',
                'releases' => [
                    [
                        'version' => '0.5.7',
                        'date'    => '2026-08-30',
                        'tag'     => 'latest',
                        'added'   => [
                            'Template updated with `veldora/framework: ^0.5.7` and `veldora/ui: ^0.5.7`',
                            'Clean starter skeleton without `src/` directory — pure standard PSR-4 MVC structure',
                            'Updated CLI runner with 48 working commands and friendly composer installation notices',
                        ],
                        'fixed'   => [],
                    ],
                    [
                        'version' => '0.5.6',
                        'date'    => '2026-08-30',
                        'tag'     => null,
                        'added'   => [
                            'Dynamic package version resolution from `package.json` at runtime',
                            'Package lockfile generation and resilient release workflow',
                            'Updated starter app template with unified `v0.5.6` core and UI components',
                        ],
                        'fixed'   => [],
                    ],
                    [
                        'version' => '0.5.5',
                        'date'    => '2026-08-30',
                        'tag'     => null,
                        'added'   => [
                            'Ships complete 41+ command CLI binary and clean starter SQLite database in every newly scaffolded project',
                        ],
                        'fixed'   => [],
                    ],
                    [
                        'version' => '0.5.2',
                        'date'    => '2026-08-30',
                        'tag'     => null,
                        'added'   => [
                            'Updated scaffold templates with Veldora v0.5.2 core and UI components',
                            'Pre-configured Skeuomorphic, Flat, and Neumorphic design assets in starter apps',
                        ],
                        'fixed'   => [],
                    ],
                    [
                        'version' => '0.5.1',
                        'date'    => '2026-08-28',
                        'tag'     => null,
                        'added'   => [
                            'Improved interactive terminal prompts and color-coded output',
                        ],
                        'fixed'   => [],
                    ],
                    [
                        'version' => '0.5.0',
                        'date'    => '2026-08-25',
                        'tag'     => null,
                        'added'   => [
                            'Template ships zero Symfony/Console dependency — CLI works out-of-the-box with built-in Polyfill shim',
                            '`bootstrap/autoload.php` auto-loads `Polyfill.php` when Symfony/Console is absent',
                            'All `php veldora` switch-cases call `executeDirect()` directly',
                            'Anonymous migration classes in template (prevents duplicate class-name collisions)',
                            '`php veldora make:auth` generates full auth layer with **100% native `.veldora.php` views** — zero raw PHP, zero CDN CSS',
                        ],
                        'fixed'   => [],
                    ],
                    [
                        'version' => '0.4.0',
                        'date'    => '2026-07-15',
                        'tag'     => null,
                        'added'   => [
                            'Interactive npx scaffolder with project name prompt',
                            'Composer dependency install, APP_KEY generation, and storage setup',
                            'Template with routes, layout, home view, sample controller/model/migration',
                        ],
                        'fixed'   => [],
                    ],
                ],
            ],

            // ── veldora-vscode ───────────────────────────────────────────────────
            [
                'repo'    => 'veldora-vscode',
                'label'   => 'Extension',
                'github'  => 'https://github.com/veldorahq/veldora-vscode',
                'color'   => 'cyan',
                'icon'    => 'vscode',
                'releases' => [
                    [
                        'version' => '0.5.6',
                        'date'    => '2026-08-30',
                        'tag'     => 'latest',
                        'added'   => [
                            '**Multi-Aesthetic Design System Snippets**: Dedicated IntelliSense snippets for Skeuomorphic 3D (`vc-*-skeuo`), Neumorphic (`vc-*-neumorphic`), Glassmorphic (`vc-*-glass`), and Flat Minimalist (`vc-*-flat`) variants across buttons, radios, checkboxes, and switches',
                            '**Modern Template Directives Snippets**: Added `v-props`, `v-slot`, `v-push`, `v-stack`, `v-once`, and `v-error` snippets',
                            'Synchronized version with Veldora Core and UI registry',
                        ],
                        'fixed'   => [],
                    ],
                    [
                        'version' => '0.5.5',
                        'date'    => '2026-08-30',
                        'tag'     => null,
                        'added'   => [
                            'Custom purple Veldora brand folder icons for `veldora-core`, `veldora-ui`, `veldora-vscode`, `create-veldora-app`, `docs-site`, and `playground`',
                            'Enhanced folder icon coverage for `storage/`, `framework/`, `sessions/`, `bootstrap/`, and `cache/`',
                        ],
                        'fixed'   => [],
                    ],
                    [
                        'version' => '0.5.3',
                        'date'    => '2026-08-29',
                        'tag'     => null,
                        'added'   => [
                            'Built-in 900+ Material Design file & folder icon theme with custom Veldora branding',
                            'Custom icons for `.veldora.php` templates, `veldora` binary, `.env` files, and route definition files',
                        ],
                        'fixed'   => [],
                    ],
                    [
                        'version' => '0.5.0',
                        'date'    => '2026-08-25',
                        'tag'     => null,
                        'added'   => [
                            'Initial TextMate grammar syntax highlighting for `.veldora.php` and `.veldora` files',
                            '32 core template directive & UI component snippets',
                            'Auto-closing tags, bracket matching, and code folding configuration',
                        ],
                        'fixed'   => [],
                    ],
                ],
            ],
        ];
    }
}
