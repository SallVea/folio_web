<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class HttpUrl implements ValidationRule
{
    /**
     * Pastikan nilai berupa URL valid dengan skema http/https saja.
     * Mencegah XSS via skema seperti javascript:, data:, vbscript:.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || $value === '') {
            return;
        }

        if (! preg_match('#^https?://#i', $value) || filter_var($value, FILTER_VALIDATE_URL) === false) {
            $fail('Kolom :attribute harus berupa URL http/https yang valid.');
        }
    }
}
