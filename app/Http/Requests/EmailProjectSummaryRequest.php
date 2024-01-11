<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class EmailProjectSummaryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'id'      => ['required', 'int'],
            'start'   => ['required', 'date'],
            'end'     => ['required', 'date'],
            'email'   => ['nullable', 'array'],
            'email.*' => ['email:rfc']
        ];
    }
}
