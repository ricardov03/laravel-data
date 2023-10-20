<?php

use Orchestra\Testbench\Concerns\CreatesApplication;
use PhpBench\Attributes\Iterations;
use PhpBench\Attributes\Revs;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\LaravelDataServiceProvider;
use Spatie\LaravelData\Tests\Fakes\MultiNestedData;
use Spatie\LaravelData\Tests\Fakes\NestedData;
use Spatie\LaravelData\Tests\Fakes\SimpleData;

class DataBench
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

    #[Revs(500), Iterations(2)]
    public function benchDataCreation()
    {
        MultiNestedData::from([
            'nested' => ['simple' => 'Hello'],
            'nestedCollection' => [
                ['simple' => 'I'],
                ['simple' => 'am'],
                ['simple' => 'groot'],
            ],
        ]);
    }

    #[Revs(500), Iterations(2)]
    public function benchPlainOldPHPDataCreation()
    {
        new MultiNestedData(
            nested: new NestedData(new SimpleData('Hello')),
            nestedCollection: new DataCollection(SimpleData::class, [
                new SimpleData('I'),
                new SimpleData('am'),
                new SimpleData('groot'),
            ]),
        );
    }

    #[Revs(500), Iterations(2)]
    public function benchDataTransformation()
    {
        $data = new MultiNestedData(
            new NestedData(new SimpleData('Hello')),
            new DataCollection(NestedData::class, [
                new NestedData(new SimpleData('I')),
                new NestedData(new SimpleData('am')),
                new NestedData(new SimpleData('groot')),
            ])
        );

        $data->toArray();
    }

    #[Revs(500), Iterations(2)]
    public function benchPlainOldPHPDataTransformation()
    {
        $data = new MultiNestedData(
            new NestedData(new SimpleData('Hello')),
            new DataCollection(NestedData::class, [
                new NestedData(new SimpleData('I')),
                new NestedData(new SimpleData('am')),
                new NestedData(new SimpleData('groot')),
            ])
        );

        [
            'nested' => [
                'simple' => $data->nested->simple,
            ],
            'nestedCollection' => $data->nestedCollection->toCollection()->map(
                fn (NestedData $nestedData) => [
                    'simple' => $nestedData->simple,
                ]
            )->all(),
        ];
    }
}
