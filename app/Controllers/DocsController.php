<?php

declare(strict_types=1);

namespace App\Controllers;

use Veldora\Framework\Http\Request;
use Veldora\Framework\Http\Response;
use Veldora\Framework\View\Engine;
use App\Helpers\DocsParser;

class DocsController
{
    private DocsParser $parser;

    public function __construct(protected Engine $view)
    {
        $this->parser = new DocsParser();
    }

    public function index(Request $request): Response
    {
        $sections = $this->parser->getSections();
        $nav      = $this->parser->getNav();

        // Redirect to first section
        foreach ($sections as $s) {
            if ($s['level'] === 2) {
                return new Response('', 302, ['Location' => '/docs/' . $s['slug']]);
            }
        }

        $html = $this->view->render('pages.docs', [
            'nav'     => $nav,
            'section' => null,
            'prev'    => null,
            'next'    => null,
        ]);
        return new Response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    /** Route: /docs/{section} — $section injected by Router via param name match */
    public function section(string $section): Response
    {
        $data    = $this->parser->getSection($section);
        $nav     = $this->parser->getNav();
        $adj     = $this->parser->getAdjacentSections($section);

        if (!$data) {
            $html = $this->view->render('pages.docs', [
                'nav'     => $nav,
                'section' => null,
                'prev'    => null,
                'next'    => null,
                'error'   => "Section \"{$section}\" not found.",
            ]);
            return new Response($html, 404, ['Content-Type' => 'text/html; charset=UTF-8']);
        }

        // Convert markdown to HTML
        $html = $this->parseMarkdown($data['content']);

        $body = $this->view->render('pages.docs', [
            'nav'     => $nav,
            'section' => array_merge($data, ['html' => $html]),
            'prev'    => $adj['prev'],
            'next'    => $adj['next'],
            'current' => $section,
        ]);

        return new Response($body, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    private function parseMarkdown(string $content): string
    {
        // Convert CRLF to LF
        $content = str_replace("\r\n", "\n", $content);

        // Extract code blocks first to protect them from being parsed or escaped
        $codeBlocks = [];
        $content = preg_replace_callback('/```(\w*)\n([\s\S]*?)\n```/', function ($matches) use (&$codeBlocks) {
            $lang = $matches[1] ?: 'php';
            $code = $matches[2];
            $placeholder = '__CODE_BLOCK_PLACEHOLDER_' . count($codeBlocks) . '__';
            $codeBlocks[] = [
                'lang' => $lang,
                'code' => htmlspecialchars($code, ENT_QUOTES, 'UTF-8')
            ];
            return $placeholder;
        }, $content) ?? $content;

        // Escape HTML tags to protect against XSS, but preserve placeholders
        $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');

        // Split into block lines / elements
        $lines = explode("\n", $content);
        $html = [];
        $inList = false;
        $listType = ''; // 'ul' or 'ol'
        $inTable = false;
        $tableRows = [];
        $inBlockquote = false;
        $blockquoteLines = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);

            // Handle Tables (pipe-separated)
            if (str_starts_with($trimmed, '|')) {
                // If this is the separator line (e.g. |---|---|), skip it
                if (preg_match('/^\|[\s|:-]+\|$/', $trimmed)) {
                    continue;
                }
                // Parse table cells
                $cells = array_map('trim', explode('|', trim($trimmed, '|')));
                $tableRows[] = $cells;
                $inTable = true;
                continue;
            } else {
                if ($inTable) {
                    // Render the table
                    $tableHtml = '<table><thead>';
                    foreach ($tableRows as $rowIndex => $row) {
                        $tag = ($rowIndex === 0) ? 'th' : 'td';
                        if ($rowIndex === 1 && count($tableRows) > 1 && $tableRows[0] === $row) {
                            continue; // skip duplicate separator
                        }
                        $tableHtml .= '<tr>';
                        foreach ($row as $cell) {
                            $tableHtml .= "<{$tag}>" . $this->parseInline($cell) . "</{$tag}>";
                        }
                        $tableHtml .= '</tr>';
                        if ($rowIndex === 0) {
                            $tableHtml .= '</thead><tbody>';
                        }
                    }
                    $tableHtml .= '</tbody></table>';
                    $html[] = $tableHtml;
                    $tableRows = [];
                    $inTable = false;
                }
            }

            // Handle List items
            $isUlItem = preg_match('/^[-*+]\s+(.+)$/', $line, $m);
            $isOlItem = preg_match('/^\d+\.\s+(.+)$/', $line, $m2);

            if ($isUlItem || $isOlItem) {
                $itemContent = $isUlItem ? $m[1] : $m2[1];
                $currentType = $isUlItem ? 'ul' : 'ol';

                if (!$inList) {
                    $inList = true;
                    $listType = $currentType;
                    $html[] = "<{$listType}>";
                } elseif ($listType !== $currentType) {
                    $html[] = "</{$listType}>";
                    $listType = $currentType;
                    $html[] = "<{$listType}>";
                }
                $html[] = '<li>' . $this->parseInline($itemContent) . '</li>';
                continue;
            } else {
                if ($inList) {
                    $html[] = "</{$listType}>";
                    $inList = false;
                }
            }

            // Handle Blockquotes (escaped > by htmlspecialchars is &gt;)
            if (str_starts_with($trimmed, '&gt;')) {
                $bqContent = preg_replace('/^&gt;\s?/', '', $trimmed);
                $blockquoteLines[] = $this->parseInline($bqContent);
                $inBlockquote = true;
                continue;
            } else {
                if ($inBlockquote) {
                    $html[] = '<blockquote>' . implode('<br>', $blockquoteLines) . '</blockquote>';
                    $blockquoteLines = [];
                    $inBlockquote = false;
                }
            }

            // Handle Headers
            if (preg_match('/^(#{1,6})\s+(.+)$/', $line, $m)) {
                $level = strlen($m[1]);
                $title = $this->parseInline(trim($m[2]));
                $id = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $title));
                $html[] = "<h{$level} id=\"{$id}\">{$title}</h{$level}>";
                continue;
            }

            // Horizontal Rule
            if (preg_match('/^([-*_])\1{2,}$/', $trimmed)) {
                $html[] = '<hr>';
                continue;
            }

            // Empty line
            if ($trimmed === '') {
                continue;
            }

            // Standard Paragraph
            $html[] = '<p>' . $this->parseInline($line) . '</p>';
        }

        // Clean up unclosed states
        if ($inTable) {
            $tableHtml = '<table><thead>';
            foreach ($tableRows as $rowIndex => $row) {
                $tag = ($rowIndex === 0) ? 'th' : 'td';
                $tableHtml .= '<tr>';
                foreach ($row as $cell) {
                    $tableHtml .= "<{$tag}>" . $this->parseInline($cell) . "</{$tag}>";
                }
                $tableHtml .= '</tr>';
                if ($rowIndex === 0) {
                    $tableHtml .= '</thead><tbody>';
                }
            }
            $tableHtml .= '</tbody></table>';
            $html[] = $tableHtml;
        }
        if ($inList) {
            $html[] = "</{$listType}>";
        }
        if ($inBlockquote) {
            $html[] = '<blockquote>' . implode('<br>', $blockquoteLines) . '</blockquote>';
        }

        $parsedHtml = implode("\n", $html);

        // Put back protected code blocks with proper formatting and Copy button
        foreach ($codeBlocks as $index => $block) {
            $placeholder = '__CODE_BLOCK_PLACEHOLDER_' . $index . '__';
            $codeMarkup = '<div class="code-block-wrapper">';
            $codeMarkup .= '<div class="code-block-header">';
            $codeMarkup .= '<span class="code-block-lang">' . htmlspecialchars(strtoupper($block['lang']), ENT_QUOTES, 'UTF-8') . '</span>';
            $codeMarkup .= '<button class="code-copy-btn" onclick="copyCode(this)">';
            $codeMarkup .= '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>';
            $codeMarkup .= ' Copy</button>';
            $codeMarkup .= '</div>';
            $lang = !empty($block['lang']) ? $block['lang'] : 'bash';
            $codeMarkup .= '<pre class="code-block language-' . htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') . '"><code class="language-' . htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') . '">' . $block['code'] . '</code></pre>';
            $codeMarkup .= '</div>';

            $parsedHtml = str_replace($placeholder, $codeMarkup, $parsedHtml);
        }

        return $parsedHtml;
    }

    private function parseInline(string $text): string
    {
        // Inline code `code`
        $text = preg_replace('/`([^`]+)`/', '<code>$1</code>', $text) ?? $text;

        // Bold **text**
        $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text) ?? $text;

        // Italic *text*
        $text = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $text) ?? $text;

        // Links [text](url)
        $text = (string) preg_replace_callback('/\[([^\]]+)\]\(([^)]+)\)/', function (array $m) {
            $label = $m[1];
            $url   = trim($m[2]);

            // Convert hash anchors (e.g. #1-project-structure) to /docs/1-project-structure
            if (str_starts_with($url, '#')) {
                $rawSlug = ltrim($url, '#');
                $slug = strtolower((string) preg_replace('/[\s_-]+/', '-', $rawSlug));
                $slug = trim($slug, '-');
                return '<a href="/docs/' . htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') . '">' . $label . '</a>';
            }

            // External links
            if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
                return '<a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" target="_blank" rel="noopener noreferrer">' . $label . '</a>';
            }

            return '<a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '">' . $label . '</a>';
        }, $text);

        return $text;
    }
}