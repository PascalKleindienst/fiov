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

expect()->extend('toBeHexColor', function (): \Pest\Expectation {
    /** @var \Pest\Expectation $this */
    $value = $this->value;

    // Regex for hex colors: #RGB, #RRGGBB, #RRGGBBAA
    $isValid = is_string($value) && preg_match('/^#(?:[0-9a-fA-F]{3}){1,2}(?:[0-9a-fA-F]{2})?$/', $value);

    if (! $isValid) {
        test()->fail(sprintf("Failed asserting that '%s' is a valid hex color.", $value));
    }

    return expect($value)->toBeString();
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
function withProLicense(): void
{
    \App\Models\License::create(['key' => 'test-123', 'status' => 'activated']);
    Http::fake([
        'api.lemonsqueezy.com/v1/licenses/validate' => Http::response([
            'valid' => true,
            'instance' => [
                'id' => 'instance-123',
                'name' => 'Test Instance',
                'created_at' => now()->toDateTimeString(),
            ],
            'license_key' => [
                'id' => 1,
                'key' => 'test-license-key',
                'status' => 'active',
                'activation_limit' => 1,
                'activation_usage' => 1,
                'created_at' => now()->toDateTimeString(),
                'expires_at' => now()->addYear()->toDateTimeString(),
            ],
            'meta' => [
                'store_id' => 12345,
                'customer_id' => 1,
                'customer_name' => 'Test User',
                'customer_email' => 'test@example.com',
            ],
        ]
        ),
    ]);
}
