<?php

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Orchestra\Testbench\Concerns\CreatesApplication;
use PhpBench\Attributes\Iterations;
use PhpBench\Attributes\ParamProviders;
use PhpBench\Attributes\Revs;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\LaravelDataServiceProvider;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Tests\Fakes\ComplicatedData;
use Spatie\LaravelData\Tests\Fakes\MultiNestedData;
use Spatie\LaravelData\Tests\Fakes\NestedData;
use Spatie\LaravelData\Tests\Fakes\SimpleData;

class DataCollectionBench
{
    use CreatesApplication;

    public function __construct()
    {
        $this->createApplication();
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelDataServiceProvider::class,
        ];
    }

    #[Revs(10), Iterations(20), ParamProviders(['provideItemCount'])]
    public function benchDataCollectionCreation(array $params)
    {
        $collection = Collection::times(
            $params['itemCount'],
            fn () => [
                'withoutType' => 42,
                'int' => 42,
                'bool' => true,
                'float' => 3.14,
                'string' => 'Hello world',
                'array' => [1, 1, 2, 3, 5, 8],
                'nullable' => null,
                'mixed' => 42,
                'explicitCast' => '16-06-1994',
                'defaultCast' => '1994-05-16T12:00:00+01:00',
                'nestedData' => [
                    'string' => 'hello',
                ],
                'nestedCollection' => [
                    ['string' => 'never'],
                    ['string' => 'gonna'],
                    ['string' => 'give'],
                    ['string' => 'you'],
                    ['string' => 'up'],
                ],
            ]
        )->all();

        ComplicatedData::collection($collection);
    }

    #[Revs(10), Iterations(20), ParamProviders(['provideItemCount'])]
    public function benchDataArrayCollectionCreation(array $params)
    {
        Collection::times(
            $params['itemCount'],
            fn () => [
                'withoutType' => 42,
                'int' => 42,
                'bool' => true,
                'float' => 3.14,
                'string' => 'Hello world',
                'array' => [1, 1, 2, 3, 5, 8],
                'nullable' => null,
                'mixed' => 42,
                'explicitCast' => '16-06-1994',
                'defaultCast' => '1994-05-16T12:00:00+01:00',
                'nestedData' => [
                    'string' => 'hello',
                ],
                'nestedCollection' => [
                    ['string' => 'never'],
                    ['string' => 'gonna'],
                    ['string' => 'give'],
                    ['string' => 'you'],
                    ['string' => 'up'],
                ],
            ]
        )->map(fn (array $data) => ComplicatedData::from($data))->all();
    }

    #[Revs(10), Iterations(20), ParamProviders(['provideItemCount'])]
    public function benchPlainOldPHPDataCollectionCreation(array $params)
    {
        $collection = Collection::times(
            $params['itemCount'],
            fn () => new ComplicatedData(
                42,
                42,
                true,
                3.14,
                'Hello World',
                [1, 1, 2, 3, 5, 8],
                null,
                Optional::create(),
                42,
                CarbonImmutable::create(1994, 05, 16),
                new DateTime('1994-05-16T12:00:00+01:00'),
                new SimpleData('hello'),
                new DataCollection(NestedData::class, [
                    new NestedData(new SimpleData('never')),
                    new NestedData(new SimpleData('gonna')),
                    new NestedData(new SimpleData('give')),
                    new NestedData(new SimpleData('you')),
                    new NestedData(new SimpleData('up')),
                ])
            )
        )->all();

        new DataCollection(ComplicatedData::class, $collection);
    }

    #[Revs(10), Iterations(20), ParamProviders(['provideItemCount'])]
    public function benchDataCollectionTransformation(array $params)
    {
        $collection = Collection::times(
            $params['itemCount'],
            fn () => new ComplicatedData(
                42,
                42,
                true,
                3.14,
                'Hello World',
                [1, 1, 2, 3, 5, 8],
                null,
                Optional::create(),
                42,
                CarbonImmutable::create(1994, 05, 16),
                new DateTime('1994-05-16T12:00:00+01:00'),
                new SimpleData('hello'),
                new DataCollection(NestedData::class, [
                    new NestedData(new SimpleData('I')),
                    new NestedData(new SimpleData('am')),
                    new NestedData(new SimpleData('groot')),
                ])
            )
        )->all();

        $collection = ComplicatedData::collection($collection);

        $collection->toArray();
    }

    #[Revs(10), Iterations(20), ParamProviders(['provideItemCount'])]
    public function benchDataArrayTransformation(array $params)
    {
        Collection::times(
            $params['itemCount'],
            fn () => new ComplicatedData(
                42,
                42,
                true,
                3.14,
                'Hello World',
                [1, 1, 2, 3, 5, 8],
                null,
                Optional::create(),
                42,
                CarbonImmutable::create(1994, 05, 16),
                new DateTime('1994-05-16T12:00:00+01:00'),
                new SimpleData('hello'),
                new DataCollection(NestedData::class, [
                    new NestedData(new SimpleData('I')),
                    new NestedData(new SimpleData('am')),
                    new NestedData(new SimpleData('groot')),
                ])
            )
        )->toArray();
    }

    #[Revs(50), Iterations(40), ParamProviders(['provideItemCount'])]
    public function benchPlainOldPHPArrayTransformation(array $params)
    {
        Collection::times(
            $params['itemCount'],
            fn () => new ComplicatedData(
                42,
                42,
                true,
                3.14,
                'Hello World',
                [1, 1, 2, 3, 5, 8],
                null,
                Optional::create(),
                42,
                CarbonImmutable::create(1994, 05, 16),
                new DateTime('1994-05-16T12:00:00+01:00'),
                new SimpleData('hello'),
                new DataCollection(NestedData::class, [
                    new NestedData(new SimpleData('I')),
                    new NestedData(new SimpleData('am')),
                    new NestedData(new SimpleData('groot')),
                ])
            ))->map(function (ComplicatedData $data) {
            return [
                'withoutType' => $data->withoutType,
                'int' => $data->int,
                'bool' => $data->bool,
                'float' => $data->float,
                'string' => $data->string,
                'array' => $data->array,
                'nullable' => $data->nullable,
                'undefinable' => $data->undefinable,
                'mixed' => $data->mixed,
                'explicitCast' => $data->explicitCast,
                'defaultCast' => $data->defaultCast,
                'nestedData' => [
                    'string' => $data->nestedData->string,
                ],
                'nestedCollection' => $data->nestedCollection->toCollection()->map(function (NestedData $data) {
                    return [
                        'string' => $data->simple->string,
                    ];
                })->all(),
            ];
        });
    }

    function provideItemCount(): Generator
    {
        yield '10 items' => ['itemCount' => 10];
        yield '100 items' => ['itemCount' => 100];
        yield '1000 items' => ['itemCount' => 1000];
    }
}
