<?php

namespace Spatie\LaravelData\Tests\TestSupport;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

use function PHPUnit\Framework\assertTrue;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Rules\InitialPropertyRule;
use Spatie\LaravelData\Support\DataConfig;

/**
 * @property class-string<Data::class> $dataClass
 */
class DataPreValidationAsserter
{
    private readonly string $dataClass;

    public static function for(
        string|object $dataClass
    ): self {
        return new self($dataClass);
    }

    public function __construct(
        string|object $dataClass,
    ) {
        $this->dataClass = is_object($dataClass)
            ? $dataClass::class
            : $dataClass;
    }

    public function assertOk(array $payload): self
    {
        $this->executeValidation($payload);

        expect(true)->toBeTrue();

        return $this;
    }

    public function assertErrors(
        array $payload,
        ?array $errors = null
    ): self {
        try {
            $this->executeValidation($payload);
        } catch (ValidationException $exception) {
            expect(true)->toBeTrue();

            if ($errors !== null) {
                expect($exception->errors())->toBe($errors);
            }

            return $this;
        }

        assertTrue(false, 'No validation errors');

        return $this;
    }

    private function executeValidation(
        array $payload
    ): void {
        $dataClass = app(DataConfig::class)->getDataClass($this->dataClass);

        $rules = [];

        foreach ($dataClass->properties as $property) {
            $rules[$property->name] = new InitialPropertyRule($property);
        }

        Validator::make($payload, $rules)->validate();
    }
}
