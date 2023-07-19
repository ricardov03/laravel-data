<?php

namespace Spatie\LaravelData\Support;

use Spatie\LaravelData\Casts\Cast;

class TransientProperty
{
    public function __construct(
        public mixed $value,
        public DataProperty $property,
        public ?Cast $cast = null, // Pre determined cast -> see idea to set this in stone on the data property
        public bool $isExactValue = false, // Whether an object is exaxctly what the property needed,
        public bool $noCastFound = false, // Whether no cast was found for the property
        public ?DataMethod $magicMethod = null, // A determined magic method that can be used to create the value
    ) {
    }
}
