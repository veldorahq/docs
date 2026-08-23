<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Parses docs.md into structured chapters and sections for the documentation site.
 */
class DocsParser
{
    private string $docsPath;

    /** @var array<int, array{id: string, title: string, level: int, content: string, slug: string}> */
    private array $sections = [];

    public function __construct()
    {
        $this->docsPath = dirname(__DIR__, 2) . '/../docs.md';
    }

    public function getRawContent(): string
    {
        if (!file_exists($this->docsPath)) {
            return '';
        }

        return file_get_contents($this->docsPath) ?: '';
    }

    /**
     * @return array<int, array{id: string, title: string, level: int, content: string, slug: string}>
     */
    public function getSections(): array
    {
        if (!empty($this->sections)) {
            return $this->sections;
        }

        $raw = $this->getRawContent();
        if (empty($raw)) {
            return [];
        }

        $lines       = explode("\n", $raw);
        $sections    = [];
        $current     = null;
        $buffer      = [];
        $inCodeBlock = false;

        foreach ($lines as $line) {
            $trimmed = trim($line);

            // Track code block fences
            if (str_starts_with($trimmed, '```')) {
                $inCodeBlock = !$inCodeBlock;
                if ($current !== null) {
                    $buffer[] = $line;
                }
                continue;
            }

            // Only match primary chapters (##) outside of code blocks
            if (!$inCodeBlock && preg_match('/^(#{2})\s+(.+)$/', $line, $m)) {
                $level = 2;
                $title = trim($m[2]);

                if (empty($title) || preg_match('/^[-_\s|:]+$/', $title)) {
                    if ($current !== null) {
                        $buffer[] = $line;
                    }
                    continue;
                }

                $slug = $this->slugify($title);

                // Save previous section
                if ($current !== null) {
                    $current['content'] = implode("\n", $buffer);
                    $sections[] = $current;
                }

                $current = [
                    'id'      => $slug,
                    'title'   => $title,
                    'level'   => $level,
                    'content' => '',
                    'slug'    => $slug,
                ];
                $buffer  = [];
            } else {
                if ($current !== null) {
                    $buffer[] = $line;
                }
            }
        }

        // Save last section
        if ($current !== null) {
            $current['content'] = implode("\n", $buffer);
            $sections[] = $current;
        }

        $this->sections = $sections;

        return $sections;
    }

    /**
     * @return array<int, array{id: string, title: string, level: int, slug: string}>
     */
    public function getNav(): array
    {
        $nav = [];
        foreach ($this->getSections() as $s) {
            $nav[] = [
                'id'    => $s['id'],
                'title' => $s['title'],
                'level' => $s['level'],
                'slug'  => $s['slug'],
            ];
        }

        return $nav;
    }

    /**
     * @return array{id: string, title: string, level: int, content: string, slug: string}|null
     */
    public function getSection(string $slug): ?array
    {
        $aliases = [
            'getting-started'        => '1-getting-started-installation',
            'installation'           => '1-getting-started-installation',
            'quickstart'             => '1-getting-started-installation',
            'routing'                => '2-routing-http-layer',
            'router'                 => '2-routing-http-layer',
            'http'                   => '2-routing-http-layer',
            'controllers'            => '3-controllers-requests',
            'requests'               => '3-controllers-requests',
            'templates'              => '4-blade-inspired-templates',
            'views'                  => '4-blade-inspired-templates',
            'compiler'               => '4-blade-inspired-templates',
            'database'               => '5-database-schema-migrations',
            'migrations'             => '5-database-schema-migrations',
            'schema'                 => '5-database-schema-migrations',
            'models'                 => '6-activerecord-models-query-builder',
            'activerecord'           => '6-activerecord-models-query-builder',
            'query-builder'          => '6-activerecord-models-query-builder',
            'relationships'          => '7-model-relationships',
            'relations'              => '7-model-relationships',
            'auth'                   => '8-authentication-system',
            'authentication'         => '8-authentication-system',
            'validation'             => '9-validation-form-requests',
            'form-requests'          => '9-validation-form-requests',
            'cli'                    => '10-cli-console-make-commands',
            'commands'               => '10-cli-console-make-commands',
            'scaffolding'            => '10-cli-console-make-commands',
            'events'                 => '11-events-listeners',
            'listeners'              => '11-events-listeners',
            'queue'                  => '12-background-queues-jobs',
            'jobs'                   => '12-background-queues-jobs',
            'queues'                 => '12-background-queues-jobs',
            'mail'                   => '13-mail-smtp-transport',
            'mailer'                 => '13-mail-smtp-transport',
            'smtp'                   => '13-mail-smtp-transport',
            'cache'                  => '14-cache-system',
            'storage'                => '15-file-storage-disks',
            'files'                  => '15-file-storage-disks',
            'logging'                => '16-psr-3-logging',
            'logs'                   => '16-psr-3-logging',
            'http-client'            => '17-http-client',
            'client'                 => '17-http-client',
            'resources'              => '18-api-json-resources',
            'api'                    => '18-api-json-resources',
            'testing'                => '19-testing-model-factories',
            'factories'              => '19-testing-model-factories',
            'components'             => '20-veldora-ui-21-components',
            'ui'                     => '20-veldora-ui-21-components',
            'veldora-ui'             => '20-veldora-ui-21-components',
            'vscode'                 => '21-vs-code-extension',
            'extension'              => '21-vs-code-extension',
            'ai'                     => '22-ai-context-prompt-ai-skills',
            'ai-prompt'              => '22-ai-context-prompt-ai-skills',
            'ai-context'             => '22-ai-context-prompt-ai-skills',
            'ai-context-prompt'      => '22-ai-context-prompt-ai-skills',
            'ai-skills'              => '22-ai-context-prompt-ai-skills',
        ];

        if (isset($aliases[$slug])) {
            $slug = $aliases[$slug];
        }

        $allSections = $this->getSections();

        // 1. Exact match
        foreach ($allSections as $s) {
            if ($s['slug'] === $slug) {
                return $s;
            }
        }

        // 2. Numbered match (e.g. "1" matches "1-getting-started-installation")
        if (is_numeric($slug)) {
            foreach ($allSections as $s) {
                if (str_starts_with($s['slug'], $slug . '-')) {
                    return $s;
                }
            }
        }

        // 3. Fuzzy substring match
        $cleanSlug = preg_replace('/^\d+-/', '', $slug) ?: $slug;
        foreach ($allSections as $s) {
            $cleanSectionSlug = preg_replace('/^\d+-/', '', $s['slug']) ?: $s['slug'];
            if ($cleanSectionSlug === $cleanSlug || str_contains($s['slug'], $cleanSlug)) {
                return $s;
            }
        }

        return $allSections[0] ?? null;
    }

    /**
     * @return array{prev: array{id: string, title: string, level: int, slug: string}|null, next: array{id: string, title: string, level: int, slug: string}|null}
     */
    public function getAdjacentSections(string $slug): array
    {
        $currentSection = $this->getSection($slug);
        $activeSlug = $currentSection['slug'] ?? $slug;

        $nav = $this->getNav();
        $currentIndex = -1;

        foreach ($nav as $i => $item) {
            if ($item['slug'] === $activeSlug || $item['id'] === $activeSlug) {
                $currentIndex = $i;
                break;
            }
        }

        return [
            'prev' => $currentIndex > 0 ? $nav[$currentIndex - 1] : null,
            'next' => ($currentIndex >= 0 && $currentIndex < count($nav) - 1) ? $nav[$currentIndex + 1] : null,
        ];
    }

    public function slugify(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9 -]/', '', $text) ?? $text;
        $text = preg_replace('/[\s-]+/', '-', $text) ?? $text;

        return trim($text, '-');
    }
}
