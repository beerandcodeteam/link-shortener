<?php

namespace App\Livewire\Forms;

use App\Models\Link;
use App\Rules\ReservedShortCode;
use Illuminate\Validation\Rule;
use Livewire\Form;

class LinkForm extends Form
{
    public ?Link $link = null;

    public string $original_url = '';

    public ?string $custom_code = '';

    /**
     * Validation rules for the form.
     *
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return static::validationRules($this->link);
    }

    /**
     * Custom validation messages for the form.
     *
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return static::validationMessages();
    }

    /**
     * The canonical link creation rules, reusable outside a component context.
     *
     * @param  Link|null  $ignore  An existing link to exclude from the uniqueness check (edit flow).
     * @return array<string, array<int, mixed>>
     */
    public static function validationRules(?Link $ignore = null): array
    {
        return [
            'original_url' => [
                'required',
                'string',
                'max:'.config('links.original_url.max_length'),
                'url:http,https',
            ],
            'custom_code' => [
                'nullable',
                'string',
                'min:'.config('links.custom_code.min_length'),
                'max:'.config('links.custom_code.max_length'),
                'regex:'.config('links.custom_code.pattern'),
                new ReservedShortCode,
                Rule::unique('links', 'short_code')->ignore($ignore),
            ],
        ];
    }

    /**
     * The canonical link creation validation messages.
     *
     * @return array<string, string>
     */
    public static function validationMessages(): array
    {
        return [
            'original_url.required' => 'Please enter a URL to shorten.',
            'original_url.url' => 'Enter a valid http or https URL.',
            'original_url.max' => 'The URL may not be longer than :max characters.',
            'custom_code.unique' => 'That short code is already taken. Please choose another.',
            'custom_code.regex' => 'Short codes may only contain letters, numbers, hyphens and underscores.',
            'custom_code.min' => 'Short codes must be at least :min characters.',
            'custom_code.max' => 'Short codes may not be longer than :max characters.',
        ];
    }
}
