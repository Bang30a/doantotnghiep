<?php

namespace App\Support;

use Illuminate\Support\HtmlString;

class RichTextSanitizer
{
    private const ALLOWED_TAGS = '<p><div><span><br><strong><b><em><i><u><ul><ol><li><font>';

    public static function sanitize(?string $html): string
    {
        $html = trim((string) $html);

        if ($html === '') {
            return '';
        }

        $html = preg_replace('/<!--.*?-->/s', '', $html) ?? '';
        $html = preg_replace('/<(script|style|iframe|object|embed|form|input|button|textarea|select|option|meta|link)[^>]*>.*?<\/\1>/is', '', $html) ?? '';
        $html = preg_replace('/<(script|style|iframe|object|embed|form|input|button|textarea|select|option|meta|link)[^>]*\/?>/is', '', $html) ?? '';

        $html = strip_tags($html, self::ALLOWED_TAGS);

        $html = preg_replace_callback('/<([a-z][a-z0-9]*)([^>]*)>/i', function ($matches) {
            $tag = strtolower($matches[1]);
            $attributes = $matches[2] ?? '';
            $safeAttributes = [];

            if (in_array($tag, ['p', 'div', 'span', 'font', 'li'], true)) {
                $style = self::extractAttribute($attributes, 'style');
                $safeStyle = self::sanitizeStyle($style);

                if ($safeStyle !== '') {
                    $safeAttributes[] = 'style="' . e($safeStyle) . '"';
                }
            }

            if (in_array($tag, ['p', 'div'], true)) {
                $align = strtolower(self::extractAttribute($attributes, 'align'));

                if (in_array($align, ['left', 'center', 'right', 'justify'], true)) {
                    $safeAttributes[] = 'align="' . $align . '"';
                }
            }

            if ($tag === 'font') {
                $size = self::extractAttribute($attributes, 'size');

                if (preg_match('/^[1-7]$/', $size)) {
                    $safeAttributes[] = 'size="' . $size . '"';
                }
            }

            return '<' . $tag . (count($safeAttributes) ? ' ' . implode(' ', $safeAttributes) : '') . '>';
        }, $html) ?? '';

        return trim($html);
    }

    public static function isBlank(?string $html): bool
    {
        $clean = self::sanitize($html);
        $text = html_entity_decode(strip_tags(str_replace('&nbsp;', ' ', $clean)), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace('/\s+/u', ' ', $text) ?? '') === '';
    }

    public static function render(?string $html, string $emptyText = ''): HtmlString
    {
        $raw = trim((string) $html);

        if ($raw === '' || self::isBlank($raw)) {
            return new HtmlString(e($emptyText));
        }

        if ($raw === strip_tags($raw)) {
            return new HtmlString(nl2br(e($raw)));
        }

        return new HtmlString(self::sanitize($raw));
    }

    private static function extractAttribute(string $attributes, string $name): string
    {
        $pattern = '/\s' . preg_quote($name, '/') . '\s*=\s*("([^"]*)"|\'([^\']*)\'|([^\s>]+))/i';

        if (!preg_match($pattern, $attributes, $matches)) {
            return '';
        }

        return html_entity_decode($matches[2] ?? $matches[3] ?? $matches[4] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    private static function sanitizeStyle(string $style): string
    {
        if ($style === '') {
            return '';
        }

        $safe = [];

        foreach (explode(';', $style) as $rule) {
            [$property, $value] = array_pad(explode(':', $rule, 2), 2, '');
            $property = strtolower(trim($property));
            $value = strtolower(trim($value));

            if ($property === 'text-align' && in_array($value, ['left', 'center', 'right', 'justify'], true)) {
                $safe[] = 'text-align: ' . $value;
            }

            if ($property === 'font-size' && preg_match('/^([1-2]?[0-9]|30)(px|pt)$/', $value)) {
                $safe[] = 'font-size: ' . $value;
            }

            if ($property === 'font-size' && in_array($value, ['small', 'medium', 'large', 'x-large', 'xx-large'], true)) {
                $safe[] = 'font-size: ' . $value;
            }

            if ($property === 'font-weight' && preg_match('/^(bold|[6-9]00)$/', $value)) {
                $safe[] = 'font-weight: ' . $value;
            }

            if ($property === 'font-style' && $value === 'italic') {
                $safe[] = 'font-style: italic';
            }

            if ($property === 'text-decoration' && str_contains($value, 'underline')) {
                $safe[] = 'text-decoration: underline';
            }
        }

        return implode('; ', array_unique($safe));
    }
}
