<?php

declare(strict_types=1);

namespace App\Core;

class Validator {
    private array $data;
    private array $rules;
    private array $errors = [];

    public function __construct(array $data, array $rules) {
        $this->data = $data;
        $this->rules = $rules;
    }

    public static function make(array $data, array $rules): self {
        $validator = new self($data, $rules);
        $validator->validate();
        return $validator;
    }

    public function validate(): bool {
        $this->errors = [];

        foreach ($this->rules as $field => $ruleString) {
            $fieldRules = is_array($ruleString) ? $ruleString : explode('|', $ruleString);
            $value = $this->data[$field] ?? null;

            foreach ($fieldRules as $rule) {
                $params = [];
                if (str_contains($rule, ':')) {
                    [$ruleName, $paramStr] = explode(':', $rule, 2);
                    $params = explode(',', $paramStr);
                } else {
                    $ruleName = $rule;
                }

                // If not required and value is empty, skip other rules
                if ($ruleName !== 'required' && ($value === null || $value === '')) {
                    continue;
                }

                switch ($ruleName) {
                    case 'required':
                        if ($value === null || trim((string)$value) === '') {
                            $this->addError($field, ucfirst(str_replace('_', ' ', $field)) . " is required.");
                        }
                        break;

                    case 'email':
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $this->addError($field, "Please enter a valid email address.");
                        }
                        break;

                    case 'phone':
                        // Bangladeshi phone or international format
                        $clean = preg_replace('/[^0-9+]/', '', (string)$value);
                        if (strlen($clean) < 11 || strlen($clean) > 15) {
                            $this->addError($field, "Please enter a valid mobile phone number.");
                        }
                        break;

                    case 'numeric':
                        if (!is_numeric($value)) {
                            $this->addError($field, ucfirst(str_replace('_', ' ', $field)) . " must be a number.");
                        }
                        break;

                    case 'integer':
                        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
                            $this->addError($field, ucfirst(str_replace('_', ' ', $field)) . " must be an integer.");
                        }
                        break;

                    case 'min':
                        $min = (int)($params[0] ?? 0);
                        if (is_numeric($value) && (float)$value < $min) {
                            $this->addError($field, ucfirst(str_replace('_', ' ', $field)) . " must be at least {$min}.");
                        } elseif (is_string($value) && mb_strlen($value) < $min) {
                            $this->addError($field, ucfirst(str_replace('_', ' ', $field)) . " must be at least {$min} characters.");
                        }
                        break;

                    case 'max':
                        $max = (int)($params[0] ?? 0);
                        if (is_numeric($value) && (float)$value > $max) {
                            $this->addError($field, ucfirst(str_replace('_', ' ', $field)) . " may not be greater than {$max}.");
                        } elseif (is_string($value) && mb_strlen($value) > $max) {
                            $this->addError($field, ucfirst(str_replace('_', ' ', $field)) . " may not be greater than {$max} characters.");
                        }
                        break;

                    case 'in':
                        if (!in_array((string)$value, $params, true)) {
                            $this->addError($field, "Selected value for " . str_replace('_', ' ', $field) . " is invalid.");
                        }
                        break;

                    case 'confirmed':
                        $confirmField = $field . '_confirmation';
                        $confirmVal = $this->data[$confirmField] ?? null;
                        if ($value !== $confirmVal) {
                            $this->addError($field, ucfirst(str_replace('_', ' ', $field)) . " confirmation does not match.");
                        }
                        break;

                    case 'unique':
                        $table = $params[0] ?? '';
                        $column = $params[1] ?? $field;
                        $ignoreId = $params[2] ?? null;

                        if ($table) {
                            $sql = "SELECT id FROM `{$table}` WHERE `{$column}` = :val";
                            $queryParams = ['val' => $value];
                            if ($ignoreId !== null && $ignoreId !== '') {
                                $sql .= " AND `id` != :ignore_id";
                                $queryParams['ignore_id'] = $ignoreId;
                            }
                            $exists = Database::fetch($sql, $queryParams);
                            if ($exists) {
                                $this->addError($field, ucfirst(str_replace('_', ' ', $field)) . " has already been taken.");
                            }
                        }
                        break;
                }

                if (isset($this->errors[$field])) {
                    break; // Stop evaluating further rules for this field on first failure
                }
            }
        }

        if ($this->fails()) {
            Session::setOldInput($this->data);
            Session::flash('errors', $this->errors);
        }

        return $this->passes();
    }

    public function passes(): bool {
        return empty($this->errors);
    }

    public function fails(): bool {
        return !empty($this->errors);
    }

    public function errors(): array {
        return $this->errors;
    }

    public function firstError(): ?string {
        foreach ($this->errors as $error) {
            return $error;
        }
        return null;
    }

    private function addError(string $field, string $message): void {
        $this->errors[$field] = $message;
    }
}
