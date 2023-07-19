<?php

use Illuminate\Support\Facades\Validator;
use Spatie\LaravelData\Rules\InitialPropertyRule;
use Spatie\LaravelData\Support\DataConfig;
use Spatie\LaravelData\Tests\TestSupport\DataPreValidationAsserter;

it('can pre validate strings', function () {
    $class = new class () {
        public string $string;
    };

    DataPreValidationAsserter::for($class)
        ->assertOk(['string' => 'Hello World'])
        ->assertOk(['string' => 3.14])
        ->assertOk(['string' => 42])
        ->assertOk(['string' => true])
        ->assertErrors(['string' => ['array']], ['string' => [__('validation.string', ['attribute' => 'string'])]]);
});

it('can pre validate bools', function () {
    $class = new class () {
        public bool $bool;
    };

    DataPreValidationAsserter::for($class)
        ->assertOk(['bool' => '1'])
        ->assertOk(['bool' => 0])
        ->assertOk(['bool' => 1.0])
        ->assertOk(['bool' => true])
        ->assertErrors(['bool' => ['1']], ['bool' => [__('validation.boolean', ['attribute' => 'bool'])]]);
});

it('can pre validate ints', function () {
    $class = new class () {
        public int $int;
    };

    DataPreValidationAsserter::for($class)
        ->assertOk(['int' => '42'])
        ->assertOk(['int' => 3.14])
        ->assertOk(['int' => 42])
        ->assertOk(['int' => true])
        ->assertErrors(['int' => 'not an int'], ['int' => [__('validation.integer', ['attribute' => 'int'])]])
        ->assertErrors(['int' => ['array']], ['int' => [__('validation.integer', ['attribute' => 'int'])]]);
});

it('can pre validate floats', function () {
    $class = new class () {
        public float $float;
    };

    DataPreValidationAsserter::for($class)
        ->assertOk(['float' => '3.14'])
        ->assertOk(['float' => 3.14])
        ->assertOk(['float' => 42])
        ->assertOk(['float' => true])
        ->assertErrors(['float' => 'not an float'], ['float' => [__('validation.float', ['attribute' => 'float'])]])
        ->assertErrors(['float' => ['array']], ['float' => [__('validation.float', ['attribute' => 'float'])]]);
});

it('can pre validate arrays', function () {
    $class = new class () {
        public array $array;
    };

    DataPreValidationAsserter::for($class)
        ->assertOk(['array' => ['array']])
        ->assertErrors(['array' => 'not an array'], ['array' => [__('validation.array', ['attribute' => 'array'])]]);
});

it('can pre validate nullable types', function () {
    $class = new class () {
        public ?string $nullable;
    };

    DataPreValidationAsserter::for($class)
        ->assertOk(['nullable' => 'nullable'])
        ->assertOk(['nullable' => null])
        ->assertErrors(['nullable' => ['not a string']], ['nullable' => [__('validation.string', ['attribute' => 'nullable'])]]);
});

it('can pre validate union types', function () {
    $class = new class () {
        public string|array $union;
    };

    DataPreValidationAsserter::for($class)
        ->assertOk(['union' => 'Hello World'])
        ->assertOk(['union' => 3.14])
        ->assertOk(['union' => 42])
        ->assertOk(['union' => true])
        ->assertOk(['union' => ['array']])
        ->assertErrors(['union' => null], ['union' => [
            __('validation.required', ['attribute' => 'union']),
            __('validation.array', ['attribute' => 'union']) . ' or ' .  __('validation.string', ['attribute' => 'union'])
        ]]);
});

// Tests Required
// Test the casting functionality rules
