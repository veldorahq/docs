<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Parses docs.md into structured sections for the documentation site.
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

            // Track code block fences to avoid parsing comments or dashed lines inside code
            if (str_starts_with($trimmed, '```')) {
                $inCodeBlock = !$inCodeBlock;
                if ($current !== null) {
                    $buffer[] = $line;
                }
                continue;
            }

            // Only match headings (## or ###) outside of code blocks
            if (!$inCodeBlock && preg_match('/^(#{1,2})\s+(.+)$/', $line, $m)) {
                $level = strlen($m[1]);
                $title = trim($m[2]);

                // Filter out non-header artifacts like dashed lines or separator comments
                if (preg_match('/^[-_\s|:]+$/', $title) || empty($title)) {
                    if ($current !== null) {
                        $buffer[] = $line;
                    }
                    continue;
                }

                $slug = $this->slugify($title);
                if (empty($slug) || preg_match('/^[-]+$/', $slug)) {
                    if ($current !== null) {
                        $buffer[] = $line;
                    }
                    continue;
                }

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
            // Exclude the document title `# Veldora Framework — Developer Documentation`
            if ($s['slug'] === 'veldora-framework-developer-documentation') {
                continue;
            }

            // Only include primary sections (Table of Contents and numbered chapters 1-19)
            if ($s['level'] <= 2) {
                $nav[] = [
                    'id'    => $s['id'],
                    'title' => $s['title'],
                    'level' => $s['level'],
                    'slug'  => $s['slug'],
                ];
            }
        }

        return $nav;
    }

    /**
     * @return array{id: string, title: string, level: int, content: string, slug: string}|null
     */
    public function getSection(string $slug): ?array
    {
        $aliases = [
            'getting-started'        => 'project-structure',
            'quickstart'             => 'project-structure',
            'veldora-ui-components'  => 'ui-component-system-veldora-ui',
            'components'             => 'ui-component-system-veldora-ui',
            'ui'                     => 'ui-component-system-veldora-ui',
            'scaffolding'            => 'cli-console-make-commands',
            'cli'                    => 'cli-console-make-commands',
            'routing'                => 'http-layer-request-response-router',
            'router'                 => 'http-layer-request-response-router',
            'templates'              => 'template-compiler-engine',
            'compiler'               => 'template-compiler-engine',
            'views'                  => 'template-compiler-engine',
            'environment'            => 'environment-configuration',
            'configuration'          => 'environment-configuration',
            'env'                    => 'environment-configuration',
            'auth'                   => 'authentication-guards-auth-facade',
            'database'               => 'database-connection-query-builder',
            'models'                 => 'activerecord-models',
            'migrations'             => 'database-schema-migrations',
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

        // 2. Fuzzy match (e.g. numeric prefix "1" or "project-structure")
        $cleanSlug = preg_replace('/^\d+-/', '', $slug) ?: $slug;
        foreach ($allSections as $s) {
            $cleanSectionSlug = preg_replace('/^\d+-/', '', $s['slug']) ?: $s['slug'];
            if ($cleanSectionSlug === $cleanSlug || str_contains($s['slug'], $cleanSlug)) {
                return $s;
            }
        }

        return null;
    }

    /**
     * @return array{prev: array{id: string, title: string, level: int, slug: string}|null, next: array{id: string, title: string, level: int, slug: string}|null}
     */
    public function getAdjacentSections(string $slug): array
    {
        $nav = $this->getNav();
        $currentIndex = -1;

        foreach ($nav as $i => $item) {
            if ($item['slug'] === $slug || $item['id'] === $slug) {
                $currentIndex = $i;
                break;
            }
        }

        // Fuzzy match if not found directly
        if ($currentIndex === -1) {
            $cleanSlug = preg_replace('/^\d+-/', '', $slug) ?: $slug;
            foreach ($nav as $i => $item) {
                $cleanItemSlug = preg_replace('/^\d+-/', '', $item['slug']) ?: $item['slug'];
                if ($cleanItemSlug === $cleanSlug || str_contains($item['slug'], $cleanSlug)) {
                    $currentIndex = $i;
                    break;
                }
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
