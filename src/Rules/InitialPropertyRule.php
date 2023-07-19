<?php

namespace Spatie\LaravelData\Rules;

use Closure;
use Exception;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Spatie\LaravelData\Support\DataProperty;
use Spatie\LaravelData\Support\Types\MultiType;
use Spatie\LaravelData\Support\Types\PartialType;
use Spatie\LaravelData\Support\Types\SingleType;
use Spatie\LaravelData\Support\Types\UndefinedType;
use Stringable;

class InitialPropertyRule implements ValidationRule
{
    public function __construct(
        protected DataProperty $property
    ) {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $dataType = $this->property->type;
        $type = $dataType->type;

        if ($dataType->isMixed() || $type instanceof UndefinedType) {
            return;
        }

        if (! $dataType->isNullable() && $value === null) {
            $fail(__('validation.required', ['attribute' => $attribute]));
        }

        if ($dataType->isNullable() && $value === null) {
            return;
        }

        if ($dataType->type instanceof SingleType) {
            $this->validateSingleType($dataType->type, $attribute, $value, $fail);

            return;
        }

        if ($dataType->type instanceof MultiType) {
            $this->validateMultiType($dataType->type, $attribute, $value, $fail);

            return;
        }

        throw new Exception('Unexpected path, we should never get here');
    }

    protected function validateSingleType(
        SingleType $singleType,
        string $attribute,
        mixed $value,
        Closure $fail,
    ): void {
        $validity = $this->isPartialTypeValid($singleType->type, $attribute, $value);

        if ($validity === true) {
            return;
        }

        $fail($validity);
    }

    protected function validateMultiType(
        MultiType $multiType,
        string $attribute,
        mixed $value,
        Closure $fail,
    ): void {
        $invalidMessages = [];

        foreach ($multiType->types as $type) {
            $validity = $this->isPartialTypeValid($type, $attribute, $value);

            if ($validity === true) {
                return;
            }

            $invalidMessages[] = $validity;
        }

        $fail(implode(' or ', $invalidMessages));
    }

    protected function isPartialTypeValid(
        PartialType $type,
        string $attribute,
        mixed $value
    ): true|string {
        // TODO: casts always have precedence over here
        if ($this->property->cast) {
            return Validator::make(
                [$attribute => $value],
                [$attribute => $this->property->cast->preValidationRules($this->property, $value)]
            )->validate();
        }

        if ($type->name === 'string' && ! $this->isValidString($value)) {
            return __('validation.string', ['attribute' => $attribute]);
        }

        if ($type->name === 'bool' && ! $this->isValidBool($value)) {
            return __('validation.boolean', ['attribute' => $attribute]);
        }

        if ($type->name === 'int' && ! $this->isValidIntOrFloat($value)) {
            return __('validation.integer', ['attribute' => $attribute]);
        }

        if ($type->name === 'float' && ! $this->isValidIntOrFloat($value)) {
            return __('validation.float', ['attribute' => $attribute]);
        }

        if ($type->name === 'array' && ! $this->isValidArray($value)) {
            return __('validation.array', ['attribute' => $attribute]);
        }

        // We're having an object over here, this should basically be catched by the casts
        // Unless it is a data object
        // Data objects can be created using magic methods, so we need check these types and count them all up
        // Data objects also can be created using from which accepts all values based upon their normalizer
        // Then there are datacollectables, these we can also automatically create
        // Again magic collect methods which can take any type
        // + some default types
        // For each item in such a collection we need to, again check the data objects

        return true;
    }

    protected function isValidString(mixed $value): bool
    {
        return is_scalar($value) || $value instanceof Stringable;
    }

    protected function isValidBool(mixed $value): bool
    {
        return is_scalar($value);
    }

    protected function isValidIntOrFloat(mixed $value): bool
    {
        return is_numeric($value) || is_bool($value);
    }

    protected function isValidArray(mixed $value): bool
    {
        return is_array($value); // TODO what if we have an arrayble? A) cast before validation, B) split casting and decide a cast before validation
        // Follow up -> add a new pipe, auto infer casts, which automatically adds global casts
        // We'll add support for a collection global cast from array which solves our problem
    }
}
