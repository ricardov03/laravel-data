<?php

use Spatie\LaravelData\Data;

it('can handle multiple cases', function () {
    $stringable = new class () implements Stringable {
        public function __toString()
        {
            return 'stringable';
        }
    };

    class DataTestBreakableValidation extends Data
    {
        // strict types are not a problem since this all happens in the data context which is not strict
        public function __construct(
            // Takes everything
            public mixed $mixed,
            // Takes string, bool, float, int, class implementing Stringable
            public string $string,
            // Takes string, bool, float, int
            public bool $bool,
            // Takes bool, float, int
            public int $int,
            // Takes bool, float, int
            public float $float,
            // Takes array
            public array $array,
            // Takes string, bool, float, int, class implementing Stringable, null
            public ?string $nullable,
        ) {
        }
    }


    $data = new DataTestBreakableValidation(
        'mixed',
        'string',
        '1d',
        '12',
        3.14,
        [],
        null,
    );

    expect($data)->toBeInstanceOf(DataTestBreakableValidation::class);
});
