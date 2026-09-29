<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Http\Validation;

use StarterKit\Mvc\Exceptions\ValidationException;
use StarterKit\Mvc\Rules\Rule;

final class Validator
{
    /** @param array<string, list<string|Rule>> $rules */
    public function validate(array $input, array $rules): array
    {
        $errors = [];
        $valid = [];
        foreach ($rules as $field => $fieldRules) {
            $present = array_key_exists($field, $input);
            $value = $input[$field] ?? null;
            foreach ($fieldRules as $rule) {
                if ($rule instanceof Rule) {
                    if ($present && !$rule->passes($value)) {
                        $errors[$field][] = $rule->message();
                        break;
                    }
                    continue;
                }
                if ($rule === 'required' && (!$present || $value === null || $value === '')) {
                    $errors[$field][] = __('This field is required.', 'wordpress-mvc-starter');
                    break;
                }
                if ((!$present || $value === null || $value === '') && in_array('nullable', $fieldRules, true)) {
                    break;
                }
                if (!$present) {
                    break;
                }
                [$name, $argument] = array_pad(explode(':', $rule, 2), 2, null);
                $ok = match ($name) {
                    'required', 'nullable' => true,
                    'string' => is_string($value),
                    'integer' => filter_var($value, FILTER_VALIDATE_INT) !== false,
                    'numeric' => is_numeric($value),
                    'boolean' => is_bool($value) || in_array($value, [0, 1, '0', '1'], true),
                    'array' => is_array($value),
                    'email' => is_string($value) && filter_var($value, FILTER_VALIDATE_EMAIL) !== false,
                    'url' => is_string($value) && filter_var($value, FILTER_VALIDATE_URL) !== false,
                    'min' => is_numeric($argument) && (is_string($value) ? $this->length($value) >= (int) $argument : (is_numeric($value) && $value >= (float) $argument)),
                    'max' => is_numeric($argument) && (is_string($value) ? $this->length($value) <= (int) $argument : (is_numeric($value) && $value <= (float) $argument)),
                    'in' => in_array((string) $value, explode(',', (string) $argument), true),
                    'notIn' => !in_array((string) $value, explode(',', (string) $argument), true),
                    'date' => is_string($value) && strtotime($value) !== false,
                    'regex' => is_string($value) && is_string($argument) && $this->matchesRegex($argument, $value),
                    default => throw new \InvalidArgumentException("Unknown validation rule: {$name}"),
                };
                if (!$ok) {
                    /* translators: %s: validation rule name. */
                    $errors[$field][] = sprintf(__('The %s rule failed.', 'wordpress-mvc-starter'), $name);
                    break;
                }
            }
            if ($present && !isset($errors[$field])) {
                $valid[$field] = $value;
            }
        }
        if ($errors !== []) {
            throw new ValidationException($errors);
        }
        return $valid;
    }

    private function length(string $value): int
    {
        if (function_exists('mb_strlen')) {
            return mb_strlen($value, 'UTF-8');
        }
        $count = preg_match_all('/./us', $value);
        return $count === false ? strlen($value) : $count;
    }

    private function matchesRegex(string $pattern, string $value): bool
    {
        $result = preg_match($pattern, $value);
        if ($result === false) {
            throw new \InvalidArgumentException('Invalid validation regex pattern.');
        }
        return $result === 1;
    }
}
