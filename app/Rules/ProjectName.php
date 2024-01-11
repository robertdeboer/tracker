<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ProjectName implements ValidationRule
{
    public const REGEX = '/^[a-z0-9 \-_]+$/i';
    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (preg_match(self::REGEX, $value) !== 1) {
            $fail('The :attribute may only contains letters, numbers, spaces, underscores, and dashes.');
        }
    }
}
