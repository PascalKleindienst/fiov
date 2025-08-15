<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

use Illuminate\Database\Eloquent\Model;

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('databaseToHaveEncrypted', function (array $data) {
    /** @var class-string<Model> $model */
    $model = new $this->value();
    expect($model)->toBeInstanceOf(Model::class);

    $entity = $model::query()->latest()->get();

    if ($entity->isEmpty()) {
        test()->fail('No entity found');
    }

    $exists = $entity->filter(function (Model $item) use ($data): bool {
        foreach ($data as $key => $value) {
            if ($item->getAttribute($key) != $value) {
                return false;
            }
        }

        return true;
    });

    if ($exists->isEmpty()) {
        test()->fail('Failed asserting that a row in the table ['.$model->getTable().'] matches the attributes '.json_encode($data, JSON_PRETTY_PRINT));
    }

    return $this;
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/
