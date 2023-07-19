<?php

namespace Spatie\LaravelData\DataPipes;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Spatie\LaravelData\Rules\InitialPropertyRule;
use Spatie\LaravelData\Support\DataClass;

class InitialValidationDataPipe implements DataPipe
{
    public function handle(mixed $payload, DataClass $class, Collection $properties): Collection
    {
        $rules = [];

        foreach ($class->properties as $property) {
            $rules[$property->name] = new InitialPropertyRule($property);
        }

        Validator::make($properties->toArray(), $rules)->validate();

        return $properties;
    }
}
