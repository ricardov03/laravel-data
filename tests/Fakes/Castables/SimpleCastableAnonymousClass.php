<?php
namespace Spatie\LaravelData\Tests\Fakes\Castables;

use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Castable;
use Spatie\LaravelData\Support\DataProperty;

class SimpleCastableAnonymousClass implements Castable
{
  public function __construct(public string $value) {

  }

  public static function castUsing(...$arguments)
  {
    return new class implements Cast {
        public function cast(DataProperty $property, mixed $value, array $context): mixed {
            return new SimpleCastableAnonymousClass($value);
        }
    };
  }
}
