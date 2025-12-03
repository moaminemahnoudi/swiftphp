<?php

declare(strict_types=1);

namespace SwiftPHP\Validation;

class Validator
{
    private array $data;
    private array $rules;
    private array $errors = [];
    private array $customMessages = [];

    public function __construct(array $data, array $rules, array $customMessages = [])
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->customMessages = $customMessages;
    }

    public static function make(array $data, array $rules, array $customMessages = []): self
    {
        return new self($data, $rules, $customMessages);
    }

    public function validate(): array
    {
        foreach ($this->rules as $field => $ruleSet) {
            $rules = is_string($ruleSet) ? explode('|', $ruleSet) : $ruleSet;
            $value = $this->data[$field] ?? null;

            foreach ($rules as $rule) {
                $this->validateRule($field, $value, $rule);
            }
        }

        if (!empty($this->errors)) {
            throw new ValidationException($this->errors);
        }

        return $this->data;
    }

    public function fails(): bool
    {
        try {
            $this->validate();
            return false;
        } catch (ValidationException $e) {
            return true;
        }
    }

    public function errors(): array
    {
        return $this->errors;
    }

    private function validateRule(string $field, $value, string $rule): void
    {
        // Parse rule with parameters (e.g., "min:8")
        $params = [];
        if (str_contains($rule, ':')) {
            [$rule, $paramString] = explode(':', $rule, 2);
            $params = explode(',', $paramString);
        }

        $method = 'validate' . ucfirst($rule);

        if (method_exists($this, $method)) {
            $this->$method($field, $value, $params);
        }
    }

    private function addError(string $field, string $message): void
    {
        if (isset($this->customMessages[$field])) {
            $this->errors[$field][] = $this->customMessages[$field];
        } else {
            $this->errors[$field][] = $message;
        }
    }

    // Validation Rules

    private function validateRequired(string $field, $value, array $params): void
    {
        if (empty($value) && $value !== '0') {
            $this->addError($field, "The {$field} field is required.");
        }
    }

    private function validateEmail(string $field, $value, array $params): void
    {
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, "The {$field} must be a valid email address.");
        }
    }

    private function validateMin(string $field, $value, array $params): void
    {
        $min = (int) $params[0];
        if (!empty($value) && strlen($value) < $min) {
            $this->addError($field, "The {$field} must be at least {$min} characters.");
        }
    }

    private function validateMax(string $field, $value, array $params): void
    {
        $max = (int) $params[0];
        if (!empty($value) && strlen($value) > $max) {
            $this->addError($field, "The {$field} may not be greater than {$max} characters.");
        }
    }

    private function validateNumeric(string $field, $value, array $params): void
    {
        if (!empty($value) && !is_numeric($value)) {
            $this->addError($field, "The {$field} must be a number.");
        }
    }

    private function validateInteger(string $field, $value, array $params): void
    {
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_INT)) {
            $this->addError($field, "The {$field} must be an integer.");
        }
    }

    private function validateUrl(string $field, $value, array $params): void
    {
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_URL)) {
            $this->addError($field, "The {$field} must be a valid URL.");
        }
    }

    private function validateIn(string $field, $value, array $params): void
    {
        if (!empty($value) && !in_array($value, $params)) {
            $allowed = implode(', ', $params);
            $this->addError($field, "The {$field} must be one of: {$allowed}.");
        }
    }

    private function validateConfirmed(string $field, $value, array $params): void
    {
        $confirmField = $field . '_confirmation';
        if (!empty($value) && (!isset($this->data[$confirmField]) || $value !== $this->data[$confirmField])) {
            $this->addError($field, "The {$field} confirmation does not match.");
        }
    }

    private function validateSame(string $field, $value, array $params): void
    {
        $otherField = $params[0];
        if (!empty($value) && (!isset($this->data[$otherField]) || $value !== $this->data[$otherField])) {
            $this->addError($field, "The {$field} and {$otherField} must match.");
        }
    }

    private function validateDifferent(string $field, $value, array $params): void
    {
        $otherField = $params[0];
        if (!empty($value) && isset($this->data[$otherField]) && $value === $this->data[$otherField]) {
            $this->addError($field, "The {$field} and {$otherField} must be different.");
        }
    }

    private function validateAlpha(string $field, $value, array $params): void
    {
        if (!empty($value) && !ctype_alpha($value)) {
            $this->addError($field, "The {$field} may only contain letters.");
        }
    }

    private function validateAlphaNum(string $field, $value, array $params): void
    {
        if (!empty($value) && !ctype_alnum($value)) {
            $this->addError($field, "The {$field} may only contain letters and numbers.");
        }
    }

    private function validateDate(string $field, $value, array $params): void
    {
        if (!empty($value) && !strtotime($value)) {
            $this->addError($field, "The {$field} is not a valid date.");
        }
    }

    private function validateBefore(string $field, $value, array $params): void
    {
        $beforeDate = strtotime($params[0]);
        $fieldDate = strtotime($value);

        if (!empty($value) && $fieldDate >= $beforeDate) {
            $this->addError($field, "The {$field} must be a date before {$params[0]}.");
        }
    }

    private function validateAfter(string $field, $value, array $params): void
    {
        $afterDate = strtotime($params[0]);
        $fieldDate = strtotime($value);

        if (!empty($value) && $fieldDate <= $afterDate) {
            $this->addError($field, "The {$field} must be a date after {$params[0]}.");
        }
    }

    private function validateRegex(string $field, $value, array $params): void
    {
        if (!empty($value) && !preg_match($params[0], $value)) {
            $this->addError($field, "The {$field} format is invalid.");
        }
    }
}
