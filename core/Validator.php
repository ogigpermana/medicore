<?php

namespace Core;

/**
 * Validator Class
 * Handles input validation
 */

class Validator
{
    private array $errors = [];

    /**
     * Validate data against rules
     */
    public function validate(array $data, array $rules): array
    {
        $this->errors = [];

        foreach ($rules as $field => $fieldRules) {
            $ruleArray = explode('|', $fieldRules);
            $value = $data[$field] ?? null;

            foreach ($ruleArray as $rule) {
                $this->validateRule($field, $value, $rule, $data);
            }
        }

        return [
            'valid' => empty($this->errors),
            'errors' => $this->errors
        ];
    }

    /**
     * Validate a single rule
     */
    private function validateRule(string $field, mixed $value, string $rule, array $data): void
    {
        // Handle rules with parameters (e.g., min:3)
        if (str_contains($rule, ':')) {
            [$ruleName, $parameter] = explode(':', $rule, 2);
        } else {
            $ruleName = $rule;
            $parameter = null;
        }

        switch ($ruleName) {
            case 'required':
                if (empty($value)) {
                    $this->errors[$field][] = "{$field} is required";
                }
                break;

            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field][] = "{$field} must be a valid email";
                }
                break;

            case 'min':
                if (!empty($value) && strlen($value) < $parameter) {
                    $this->errors[$field][] = "{$field} must be at least {$parameter} characters";
                }
                break;

            case 'max':
                if (!empty($value) && strlen($value) > $parameter) {
                    $this->errors[$field][] = "{$field} must not exceed {$parameter} characters";
                }
                break;

            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->errors[$field][] = "{$field} must be numeric";
                }
                break;

            case 'confirmed':
                if (!empty($value) && $value !== ($data[$field . '_confirmation'] ?? '')) {
                    $this->errors[$field][] = "{$field} confirmation does not match";
                }
                break;

            case 'same':
                if (!empty($value) && $value !== ($data[$parameter] ?? '')) {
                    $this->errors[$field][] = "{$field} must match {$parameter}";
                }
                break;
        }
    }

    /**
     * Get validation errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Check if validation passed
     */
    public function passes(): bool
    {
        return empty($this->errors);
    }

    /**
     * Check if validation failed
     */
    public function fails(): bool
    {
        return !$this->passes();
    }
}