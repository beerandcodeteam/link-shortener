<?php

declare(strict_types=1);

namespace App\Forms;

use App\Rules\ReservedShortCode;
use Illuminate\Support\Facades\Validator;

/**
 * Validates and normalizes data for the link store operation.
 */
final class LinkStoreFormObject
{
    /** Errors keyed by field name; populated during construction. */
    private array $errors = [];

    public function __construct(
        public readonly string $originalUrl,
        public readonly ?string $customCode,
    ) {
        $this->validate();
    }

    /** Returns true when validation passed for all fields. */
    public function isValid(): bool
    {
        return empty($this->errors);
    }

    /** Check if a specific field has validation errors. */
    public function hasError(string $field): bool
    {
        return isset($this->errors[$field]) && count($this->errors[$field]) > 0;
    }

    /** Get all errors for a specific field. */
    public function getErrorsFor(string $field): array
    {
        return $this->errors[$field] ?? [];
    }

    /** Get all errors as a flat list of strings. */
    public function getErrors(): array
    {
        return array_merge(...array_values($this->errors));
    }

    /** Validate both fields in dependency order and populate $this->errors. */
    private function validate(): void
    {
        $this->validateOriginalUrl();

        if ($this->customCode !== null && $this->customCode !== '') {
            $this->validateCustomCode();
        }
    }

    /** Validate original_url: required, well-formed, http/https only, max length. */
    private function validateOriginalUrl(): void
    {
        $value = trim($this->originalUrl);

        if ($value === '') {
            $this->addError('original_url', 'The original url field is required.');
            return;
        }

        // Length check first — independent of scheme.
        $maxUrlLength = config('link-shortener.max_original_url_length', 2048);

        if (strlen($value) > $maxUrlLength) {
            $this->addError(
                'original_url',
                sprintf('The original url field must not exceed %d characters.', $maxUrlLength),
            );
            return;
        }

        // General URL shape check.
        $urlValidator = Validator::make(
            ['original_url' => $value],
            ['original_url' => 'url'],
        );

        if (! $urlValidator->passes()) {
            $this->addError('original_url', 'The original url field must be a valid URL.');
            return;
        }

        // Scheme enforcement: http or https only.
        $parsedUrl = parse_url($value);

        if ($parsedUrl === false) {
            $this->addError('original_url', 'The original url field must be a valid URL.');
            return;
        }

        /** @var string|false $scheme */
        $scheme = $parsedUrl['scheme'] ?? false;

        if (! in_array(strtolower((string) $scheme), ['http', 'https'], true)) {
            $this->addError('original_url', 'The original url field must use http or https.');
            return;
        }
    }

    /** Validate custom_code: charset, max length, reserved words, uniqueness. */
    private function validateCustomCode(): void
    {
        $codeValue = trim($this->customCode);

        // Allowed charset: alphanumeric + hyphen only.
        if (! preg_match('/^[a-zA-Z0-9\-]+$/', $codeValue)) {
            $this->addError('custom_code', 'The custom code field may only contain letters, numbers, and hyphens.');
            return;
        }

        $maxCodeLength = config('link-shortener.max_custom_code_length', 64);

        if (strlen($codeValue) > $maxCodeLength) {
            $this->addError(
                'custom_code',
                sprintf('The custom code field must not exceed %d characters.', $maxCodeLength),
            );
            return;
        }

        // Reserved word check (case-insensitive).
        if (! (new ReservedShortCode())->passes('custom_code', $codeValue)) {
            $this->addError('custom_code', 'The custom code field is a reserved short code and cannot be used.');
            return;
        }

        // Uniqueness: check against existing links (no user scope).
        if (\App\Models\Link::where('short_code', $codeValue)->exists()) {
            $this->addError('custom_code', 'The custom code field has already been taken.');
        }
    }

    /** Add a single validation error to the given field. */
    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    /** Resolve the short code: trimmed custom code or null for auto-generation. */
    public function resolvedCode(): ?string
    {
        if ($this->customCode !== null && $this->customCode !== '') {
            return trim($this->customCode);
        }

        // Return null to signal auto-generation should be used downstream.
        // The caller (e.g., controller) dispatches ShortCodeGenerator::generateUnique().
        return null;
    }
}
