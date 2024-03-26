<?php

namespace framework\core;

abstract class Model
{
    public const RULE_REQUIRED = 'required';
    public const RULE_EMAIL = 'email';
    public const RULE_MIN = 'min';
    public const RULE_MAX = 'max';
    public const RULE_UNIQUE = 'table_name';
    public const RULE_MATCH = 'match';

    public array $errors = [];


    abstract public function labels(): array;
    abstract public function rules(): array;


    public function loadData(array $data): void
    {
        foreach($data as $key => $value) {
            if(property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }

    }


    public function validate(): bool
    {
        foreach($this->rules() as $attribute => $rules) {
            $value = $this->{$attribute};
            foreach($rules as $rule) {
                $ruleName = $rule;
                if(!is_string($ruleName)) {
                    $ruleName = $rule[0];
                }

                if($ruleName === self::RULE_REQUIRED && !$value) {
                    $this->addErrorForRule($attribute, self::RULE_REQUIRED);
                }

                if($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addErrorForRule($attribute, self::RULE_EMAIL);
                }

                if($ruleName === self::RULE_MIN && strlen($value) < $rule['min']) {
                    $this->addErrorForRule($attribute, self::RULE_MIN, $rule);
                }

                if($ruleName === self::RULE_MAX && strlen($value) > $rule['max']) {
                    $this->addErrorForRule($attribute, self::RULE_MAX, $rule);
                }

                if($ruleName === self::RULE_MATCH && $value !== $this->{$rule['match']}) {
                    $this->addErrorForRule($attribute, self::RULE_MATCH, ['match' => $this->getLabel($rule['match'])]);
                }

                if($ruleName === self::RULE_UNIQUE) {
                    $className = $rule['class'];
                    $uniqueAttr = $rule['attribute'] ?? $attribute;
                    $tableName = $className::tableName();

                    $stm = Application::$app->db->prepare("SELECT * FROM $tableName 
                            WHERE $uniqueAttr = :$uniqueAttr");

                    $stm->bindValue(":$uniqueAttr", $value);
                    $stm->execute();
                    $record = $stm->fetchObject();
                    if($record) {
                        $this->addErrorForRule($attribute, self::RULE_UNIQUE, ['field' => $this->getLabel($attribute)]);
                    }
                }
            }
        }

        return empty($this->errors);
    }


    private function addErrorForRule(string $attribute, string $rule, $params = []): void
    {
        $message = $this->errorMessages()[$rule] ?? '';

        if($params) {
            foreach($params as $key => $value) {
                $message = str_replace("{{$key}}", $value, $message);
            }
        }
        $this->errors[$attribute][] = str_replace("{field}", $this->getlabel($attribute), $message);
    }


    public function addError(string $attribute, string $message): void
    {
        $this->errors[$attribute][] = $message;
    }


    public function hasError($attribute): bool
    {
        return !empty($this->errors[$attribute]);
    }


    public function getLabel(string $attr): string
    {
        return $this->labels()[$attr] ?? $attr;
    }


    public function getFirstError($attribute): string
    {
        return $this->errors[$attribute][0] ?? '';
    }


    public function errorMessages(): array
    {
        return [
            self::RULE_REQUIRED => "{field} is required",
            self::RULE_EMAIL => "{field} must be a valid email address",
            self::RULE_MIN => "{field} must be at least {min} characters long",
            self::RULE_MAX => "{field} can not be more than {max} characters",
            self::RULE_UNIQUE => "{field} already exists",
            self::RULE_MATCH => "{field} and {match} do not match"
        ];
    }

}