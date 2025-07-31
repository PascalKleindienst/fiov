<?php

declare(strict_types=1);

use App\Data\Chart;

it('can be instantiated', function () {
    expect(new Chart('Test Chart', 'USD'))
        ->toBeInstanceOf(Chart::class)
        ->name->toBe('Test Chart')
        ->currency->toBe('USD');
});

it('can add data points', function () {
    // Arrange
    $chart = new Chart('Test Chart', 'USD');
    $chart->addDataPoint('Category 1', 100.50);
    $chart->addDataPoint('Category 2', 200.75);

    // Act & Assert
    $data = $chart->toArray();

    expect($data['series'])
        ->toHaveCount(1)
        ->and($data['series'][0]['data'])
        ->toHaveCount(2)
        ->and($data['series'][0]['data'][0])
        ->toMatchArray(['x' => 'Category 1', 'y' => 100.50])
        ->and($data['series'][0]['data'][1])
        ->toMatchArray(['x' => 'Category 2', 'y' => 200.75]);
});

it('can add series', function () {
    // Arrange
    $chart = new Chart('Test Chart', 'USD');

    $chart->addSeries('Series 1', [['x' => 'A', 'y' => 10]]);
    $chart->addSeries('Series 2', [['x' => 'B', 'y' => 20]]);

    // Act & Assert
    $data = $chart->toArray();

    expect($data['series'])
        ->toHaveCount(2)
        ->and($data['series'][0])
        ->toMatchArray(['name' => 'Series 1'])
        ->and($data['series'][1])
        ->toMatchArray(['name' => 'Series 2']);
});

it('can add colors', function () {
    // Arrange
    $chart = new Chart('Test Chart', 'USD');

    $chart->addColor('#FF0000');
    $chart->addColor('#00FF00');

    // Act & Assert
    $data = $chart->toArray();

    expect($data['colors'])
        ->toHaveCount(2)
        ->toContain('#FF0000', '#00FF00');
});

it('can add colors via the constructor', function () {
    // Arrange
    $chart = new Chart('Test Chart', 'USD', [['x' => 'A', 'y' => 10, 'color' => '#FF0000']]);

    // Act & Assert
    $data = $chart->toArray();

    expect($data['colors'])
        ->toHaveCount(1)
        ->toContain('#FF0000');
});

it('can add options', function () {
    // Arrange
    $chart = new Chart('Test Chart', 'USD');

    $chart->addOption('xaxis', ['type' => 'category']);
    $chart->addOption('yaxis', ['show' => true]);

    // Act & Assert
    expect($chart->options)
        ->toHaveCount(2)
        ->toHaveKey('xaxis')
        ->toHaveKey('yaxis');
});

it('calculates growth', function () {
    // Arrange
    $chart = new Chart('Test Chart', 'USD', previousTotal: 100);

    // Act & Assert
    $chart->addDataPoint('Category 1', 100);
    $chart->addDataPoint('Category 2', 50);

    expect($chart->growth())->toBe(50.0);
});

it('handles empty data when calculating growth', function () {
    // When previous total is 0 and current total is 0, growth should be 100
    $chart = new Chart('Test Chart', 'USD', previousTotal: 0);
    expect($chart->growth())->toBe(100.0);

    // When previous total is 0 and current total is positive, growth should be 100%
    $chartWithData = new Chart('Test Chart', 'USD', [['x' => 'Test', 'y' => 100]], previousTotal: 0);
    expect($chartWithData->growth())->toBe(100.0);
});

it('can be converted to json', function () {
    // Arrange
    $chart = new Chart('Test Chart', 'USD');
    $chart->addDataPoint('A', 10);

    // Act
    $json = $chart->toJson();
    $array = $chart->toArray();

    // Assert
    expect($json)
        ->toBeJson()
        ->and($array)
        ->toHaveKey('series')
        ->toHaveKey('colors');
});

it(' implements arrayable and jsonable', function () {
    $chart = new Chart('Test', 'USD');

    expect($chart)
        ->toBeInstanceOf(Illuminate\Contracts\Support\Arrayable::class)
        ->toBeInstanceOf(Illuminate\Contracts\Support\Jsonable::class)
        ->toBeInstanceOf(JsonSerializable::class);
});
