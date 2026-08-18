<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SecureText implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            $fail('Format :attribute tidak valid.');
            return;
        }

        /*
         * HTML tag yang diizinkan oleh Tiptap.
         */
        $allowedTags = [
            'p',
            'br',

            // Text formatting
            'strong',
            'b',
            'em',
            'i',
            'u',
            's',
            'strike',
            'del',
            'mark',

            // Text style
            'span',

            // Heading
            'h1',
            'h2',
            'h3',
            'h4',
            'h5',
            'h6',

            // List
            'ul',
            'ol',
            'li',

            // Other
            'blockquote',
            'code',
            'pre',

            // Link
            'a',
        ];

        /*
         * Cari semua HTML tag.
         */
        preg_match_all(
            '/<\s*\/?\s*([a-zA-Z0-9]+)(?:\s[^>]*)?>/i',
            $value,
            $matches
        );

        /*
         * Pastikan hanya tag yang diizinkan yang digunakan.
         */
        foreach ($matches[1] as $tag) {
            if (!in_array(strtolower($tag), $allowedTags, true)) {
                $fail(':attribute mengandung karakter atau kode yang tidak diizinkan.');
                return;
            }
        }

        /*
         * Blokir tag / kode berbahaya.
         */
        $dangerousPatterns = [
            '/<\s*script\b/i',
            '/<\s*\/\s*script\s*>/i',

            '/<\s*style\b/i',
            '/<\s*\/\s*style\s*>/i',

            '/<\s*iframe\b/i',
            '/<\s*object\b/i',
            '/<\s*embed\b/i',
            '/<\s*form\b/i',
            '/<\s*meta\b/i',
            '/<\s*link\b/i',
            '/<\s*svg\b/i',

            '/<\s*img\b/i',
            '/<\s*video\b/i',
            '/<\s*audio\b/i',

            '/<\s*input\b/i',
            '/<\s*button\b/i',
            '/<\s*textarea\b/i',
            '/<\s*select\b/i',

            /*
             * Event handler:
             * onclick=
             * onerror=
             * onload=
             */
            '/\bon[a-z]+\s*=/i',

            /*
             * JavaScript / VBScript.
             */
            '/javascript\s*:/i',
            '/vbscript\s*:/i',

            /*
             * Data URI HTML.
             */
            '/data\s*:\s*text\/html/i',

            /*
             * CSS expression.
             */
            '/expression\s*\(/i',
            '/url\s*\(\s*javascript\s*:/i',

            /*
             * PHP.
             */
            '/<\?php/i',
            '/<\?=/i',
            '/<\?xml/i',
            '/\?>/i',

            /*
             * Blade.
             */
            '/\{\{\s*.*\}\}/i',
            '/\{!!.*!!\}/i',
        ];

        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $value)) {
                $fail(':attribute mengandung karakter atau kode yang tidak diizinkan.');
                return;
            }
        }

        /*
         * Validasi atribut style.
         *
         * Tiptap TextStyle dapat menghasilkan:
         *
         * <span style="color: red">
         * <span style="background-color: yellow">
         *
         * Kita izinkan style tertentu saja.
         */
        preg_match_all(
            '/<span\b([^>]*)>/i',
            $value,
            $spanMatches
        );

        foreach ($spanMatches[1] as $attributes) {

            /*
             * Jika span memiliki style.
             */
            if (preg_match('/\bstyle\s*=\s*["\']([^"\']*)["\']/i', $attributes, $styleMatch)) {

                $style = strtolower(trim($styleMatch[1]));

                /*
                 * Style berbahaya.
                 */
                $dangerousStylePatterns = [
                    '/expression\s*\(/i',
                    '/javascript\s*:/i',
                    '/vbscript\s*:/i',
                    '/url\s*\(/i',
                    '/@import/i',
                    '/behavior\s*:/i',
                    '/-moz-binding/i',
                ];

                foreach ($dangerousStylePatterns as $pattern) {
                    if (preg_match($pattern, $style)) {
                        $fail(':attribute mengandung style yang tidak diizinkan.');
                        return;
                    }
                }

                /*
                 * Hanya property style yang diperbolehkan.
                 */
                $allowedStyleProperties = [
                    'color',
                    'background-color',
                    'font-size',
                    'font-family',
                    'font-weight',
                    'font-style',
                    'text-decoration',
                ];

                preg_match_all(
                    '/([a-zA-Z-]+)\s*:/',
                    $style,
                    $propertyMatches
                );

                foreach ($propertyMatches[1] as $property) {
                    if (!in_array(strtolower($property), $allowedStyleProperties, true)) {
                        $fail(':attribute mengandung style yang tidak diizinkan.');
                        return;
                    }
                }
            }
        }

        /*
         * Validasi attribute pada link.
         */
        preg_match_all(
            '/<a\b([^>]*)>/i',
            $value,
            $linkMatches
        );

        foreach ($linkMatches[1] as $attributes) {

            /*
             * href harus aman.
             */
            if (preg_match('/\bhref\s*=\s*["\']([^"\']*)["\']/i', $attributes, $hrefMatch)) {

                $href = strtolower(trim($hrefMatch[1]));

                if (
                    str_starts_with($href, 'javascript:') ||
                    str_starts_with($href, 'vbscript:') ||
                    str_starts_with($href, 'data:')
                ) {
                    $fail(':attribute mengandung link yang tidak diizinkan.');
                    return;
                }
            }
        }

        /*
         * Decode HTML entity lalu lakukan pengecekan ulang.
         */
        $decoded = html_entity_decode(
            $value,
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );

        if ($decoded !== $value) {

            preg_match_all(
                '/<\s*\/?\s*([a-zA-Z0-9]+)(?:\s[^>]*)?>/i',
                $decoded,
                $decodedTags
            );

            foreach ($decodedTags[1] as $tag) {
                if (!in_array(strtolower($tag), $allowedTags, true)) {
                    $fail(':attribute mengandung kode yang tidak diizinkan.');
                    return;
                }
            }

            foreach ($dangerousPatterns as $pattern) {
                if (preg_match($pattern, $decoded)) {
                    $fail(':attribute mengandung kode yang tidak diizinkan.');
                    return;
                }
            }
        }

        /*
         * Blokir control characters.
         */
        if (preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $value)) {
            $fail(':attribute mengandung karakter yang tidak valid.');
        }
    }
}