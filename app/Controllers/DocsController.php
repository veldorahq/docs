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
        if (!empty($sections)) {
            return new Response('', 302, ['Location' => '/docs/' . $sections[0]['slug']]);
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
        $data = $this->parser->getSection($section);
        $nav  = $this->parser->getNav();

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

        $adj  = $this->parser->getAdjacentSections($data['slug']);
        $html = $this->parseMarkdown($data['content']);

        $body = $this->view->render('pages.docs', [
            'nav'     => $nav,
            'section' => array_merge($data, ['html' => $html]),
            'prev'    => $adj['prev'],
            'next'    => $adj['next'],
            'current' => $data['slug'],
        ]);

        return new Response($body, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    /** Route: /download/veldora-ai-prompt.md */
    public function downloadPrompt(): Response
    {
        $data = $this->parser->getSection('22-ai-context-prompt-ai-skills');
        $content = $data['content'] ?? '';

        // Extract pure master prompt if fenced
        if (preg_match('/```(?:\w*)\n([\s\S]+?)\n```/', $content, $m)) {
            $promptText = trim($m[1]);
        } else {
            $promptText = $content ?: '# Veldora AI Developer Master Prompt';
        }

        return new Response($promptText, 200, [
            'Content-Type'        => 'text/markdown; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="veldora-ai-master-prompt.md"',
            'Content-Length'      => (string) strlen($promptText),
        ]);
    }

    private function parseMarkdown(string $content): string
    {
        // Convert CRLF to LF
        $content = str_replace("\r\n", "\n", $content);

        // Extract code blocks first to protect them from being parsed or escaped
        $codeBlocks = [];
        $content = preg_replace_callback('/```(\w*)\n([\s\S]*?)\n```/', function ($matches) use (&$codeBlocks) {
            $lang = strtolower($matches[1] ?: 'php');
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
                if (preg_match('/^\|[\s|:-]+\|$/', $trimmed)) {
                    continue;
                }
                $cells = array_map('trim', explode('|', trim($trimmed, '|')));
                $tableRows[] = $cells;
                $inTable = true;
                continue;
            } else {
                if ($inTable) {
                    $tableHtml = '<div class="table-responsive"><table><thead>';
                    foreach ($tableRows as $rowIndex => $row) {
                        $tag = ($rowIndex === 0) ? 'th' : 'td';
                        if ($rowIndex === 1 && count($tableRows) > 1 && $tableRows[0] === $row) {
                            continue;
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
                    $tableHtml .= '</tbody></table></div>';
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

            // Handle Blockquotes
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

            // Handle Subheaders (h3, h4)
            if (preg_match('/^(#{3,6})\s+(.+)$/', $line, $m)) {
                $level = strlen($m[1]);
                $title = $this->parseInline(trim($m[2]));
                $id = strtolower(preg_replace('/[^a-z0-9]+/i', '-', strip_tags($title)));
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
            $tableHtml = '<div class="table-responsive"><table><thead>';
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
            $tableHtml .= '</tbody></table></div>';
            $html[] = $tableHtml;
        }
        if ($inList) {
            $html[] = "</{$listType}>";
        }
        if ($inBlockquote) {
            $html[] = '<blockquote>' . implode('<br>', $blockquoteLines) . '</blockquote>';
        }

        $parsedHtml = implode("\n", $html);

        // Put back protected code blocks with appropriate layout (Terminal mockup vs Standard code block)
        foreach ($codeBlocks as $index => $block) {
            $placeholder = '__CODE_BLOCK_PLACEHOLDER_' . $index . '__';
            $lang = $block['lang'];
            $isTerminal = ($lang === 'terminal' || $lang === 'cmd' || $lang === 'shell' || str_contains($block['code'], '▲ Veldora Framework'));

            if ($isTerminal) {
                $markup = '<div class="terminal-mockup">';
                $markup .= '<div class="terminal-mockup-header">';
                $markup .= '<div class="terminal-dots"><span class="dot-red"></span><span class="dot-yellow"></span><span class="dot-green"></span></div>';
                $markup .= '<span class="terminal-mockup-title">bash &bull; Interactive CLI Setup</span>';
                $markup .= '<button type="button" class="code-copy-btn" onclick="copyCode(this)" aria-label="Copy terminal text">';
                $markup .= '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg> Copy';
                $markup .= '</button>';
                $markup .= '</div>';
                $markup .= '<pre class="terminal-mockup-body code-block"><code>' . $block['code'] . '</code></pre>';
                $markup .= '</div>';
            } else {
                $markup = '<div class="code-block-wrapper">';
                $markup .= '<div class="code-block-header">';
                $markup .= '<span class="code-block-lang">' . htmlspecialchars(strtoupper($lang ?: 'PHP'), ENT_QUOTES, 'UTF-8') . '</span>';
                $markup .= '<button type="button" class="code-copy-btn" onclick="copyCode(this)" aria-label="Copy code">';
                $markup .= '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg> Copy';
                $markup .= '</button>';
                $markup .= '</div>';
                $markup .= '<pre class="code-block language-' . htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') . '"><code class="language-' . htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') . '">' . $block['code'] . '</code></pre>';
                $markup .= '</div>';
            }

            $parsedHtml = str_replace($placeholder, $markup, $parsedHtml);
        }

        return $parsedHtml;
    }

    private function parseInline(string $text): string
    {
        // Unescape entities for directives and tags
        $text = str_replace(['&amp;#64;', '&#64;'], '@', $text);
        $text = str_replace(['&amp;#123;', '&#123;'], '{', $text);
        $text = str_replace(['&amp;#125;', '&#125;'], '}', $text);

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

            // Convert hash anchors to /docs/...
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