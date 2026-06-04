<?php

namespace App\Forms;

use App\Models\Link;
use App\Rules\ReservedShortCode;
use App\Services\ShortCodeGenerator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

/**
 * Validation Form Object for creating a short link.
 *
 * - `original_url` is required, must be a valid URL, http/https only,
 *   and within the configured max length.
 * - `custom_code` is optional. When provided it must match the allowed
 *   charset/length, be unique in `links.short_code`, and not be a
 *   reserved word.
 *
 * On `validated()` the Form Object returns a payload containing
 * `original_url` and a resolved `short_code` (custom or auto-generated
 * and collision-checked via {@see ShortCodeGenerator}).
 */
class LinkForm extends FormRequest
{
    /**
     * Authorize the request — link creation is open in this phase and
     * will be gated behind auth in later phases.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $maxUrl = (int) config('shortener.url.max_length', 2048);
        $minCode = (int) config('shortener.custom_code.min_length', 3);
        $maxCode = (int) config('shortener.custom_code.max_length', 32);
        $alphabet = (string) config('shortener.custom_code.alphabet', 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_');

        $codeRegex = '/^['.preg_quote($alphabet, '/').']+$/';

        return [
            'original_url' => ['required', 'string', 'max:'.$maxUrl, 'url:http,https'],
            'custom_code' => [
                'nullable',
                'string',
                'min:'.$minCode,
                'max:'.$maxCode,
                'regex:'.$codeRegex,
                Rule::unique('links', 'short_code'),
                new ReservedShortCode,
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'original_url.required' => 'Please paste a link to shorten.',
            'original_url.url' => 'Please provide a valid URL.',
            'original_url.regex' => 'The URL must use the http or https scheme.',
            'original_url.max' => 'The URL is too long (max :max characters).',
            'custom_code.regex' => 'The custom code may only contain letters, numbers, dashes and underscores.',
            'custom_code.min' => 'The custom code must be at least :min characters.',
            'custom_code.max' => 'The custom code may not be longer than :max characters.',
            'custom_code.unique' => 'This custom code is already taken. Please choose another.',
        ];
    }

    /**
     * Resolve a validated payload containing both the URL and the
     * resolved short code (custom or freshly generated).
     *
     * @return array{original_url: string, short_code: string}
     */
    public function resolved(): array
    {
        $data = $this->validated();
        $generator = app(ShortCodeGenerator::class);

        $shortCode = $this->filled('custom_code')
            ? (string) $data['custom_code']
            : $generator->generate();

        return [
            'original_url' => (string) $data['original_url'],
            'short_code' => $shortCode,
        ];
    }
}
