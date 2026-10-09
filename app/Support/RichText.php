<?php

namespace App\Support;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

/**
 * Article bodies written in the admin's rich-text editor (Trix).
 * HTML is cleaned against a strict allowlist on save AND on display, so no script,
 * style, event handler or javascript: link can ever reach a visitor's browser.
 * Older plain-text articles are converted to safe HTML on display.
 */
class RichText
{
    private const MAX_INPUT = 100_000;

    private static ?HtmlSanitizer $sanitizer = null;

    public static function sanitize(string $html): string
    {
        return self::sanitizer()->sanitize($html);
    }

    /** Safe HTML for any stored article body (rich HTML or legacy plain text). */
    public static function toHtml(?string $content): string
    {
        $content = (string) $content;

        return self::isHtml($content) ? self::sanitize($content) : self::fromPlainText($content);
    }

    public static function isHtml(string $content): bool
    {
        return (bool) preg_match('~<(div|p|br|h[1-6]|ul|ol|li|blockquote|pre|strong|b|em|i|a|del|s)\b~i', $content);
    }

    /** True when the editor produced no actual text (e.g. "<div><br></div>"). */
    public static function isBlank(?string $html): bool
    {
        return self::plainText($html) === '';
    }

    public static function plainText(?string $html): string
    {
        $text = html_entity_decode(strip_tags(preg_replace('~<(br|/div|/p|/h\d|/li|/blockquote)>~i', ' $0', (string) $html)), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace('/[\s\x{00A0}]+/u', ' ', $text));
    }

    /**
     * Legacy format: blank lines separate paragraphs, "## " starts a heading and "> " a quote.
     * Every piece of text is escaped.
     */
    public static function fromPlainText(string $text): string
    {
        $blocks = preg_split("/\R{2,}/", trim($text), -1, PREG_SPLIT_NO_EMPTY);

        return collect($blocks)->map(function (string $block) {
            $block = trim($block);

            return match (true) {
                str_starts_with($block, '## ') => '<h2>' . e(substr($block, 3)) . '</h2>',
                str_starts_with($block, '> ') => '<blockquote>' . e(substr($block, 2)) . '</blockquote>',
                default => '<p>' . nl2br(e($block), false) . '</p>',
            };
        })->implode("\n");
    }

    private static function sanitizer(): HtmlSanitizer
    {
        if (self::$sanitizer) {
            return self::$sanitizer;
        }

        $config = (new HtmlSanitizerConfig())
            ->allowLinkSchemes(['https', 'http', 'mailto'])
            ->allowRelativeLinks()
            ->forceAttribute('a', 'rel', 'noopener noreferrer nofollow')
            ->withMaxInputLength(self::MAX_INPUT);

        // Text formatting produced by the editor toolbar, and nothing else.
        foreach (['div', 'p', 'br', 'strong', 'b', 'em', 'i', 'del', 's', 'h1', 'h2', 'h3', 'blockquote', 'pre', 'code', 'ul', 'ol', 'li'] as $element) {
            $config = $config->allowElement($element);
        }
        $config = $config->allowElement('a', ['href']);

        return self::$sanitizer = new HtmlSanitizer($config);
    }
}
