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

        $patterns = [
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
            '/<\s*style\b/i',
            '/\bon[a-z]+\s*=/i',
            '/javascript\s*:/i',
            '/vbscript\s*:/i',
            '/data\s*:\s*text\/html/i',
            '/expression\s*\(/i',
            '/url\s*\(\s*javascript\s*:/i',
            '/<\?php/i',
            '/<\?=/i',
            '/<\?=/i',
            '/<\?xml/i',
            '/\?>/i',
            '/\{\{\s*.*\}\}/i',
            '/\{!!.*!!\}/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $value)) {
                $fail(':attribute mengandung karakter atau kode yang tidak diizinkan.');
                return;
            }
        }

        $decoded = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        if ($decoded !== $value) {
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $decoded)) {
                    $fail(':attribute mengandung kode yang tidak diizinkan.');
                    return;
                }
            }
        }

        if (preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $value)) {
            $fail(':attribute mengandung karakter yang tidak valid.');
        }
    }
}
