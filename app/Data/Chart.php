<?php

declare(strict_types=1);

namespace App\Data;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use JsonException;
use JsonSerializable;

/**
 * @implements Arrayable<string, mixed>
 */
final readonly class Chart implements Arrayable, Jsonable, JsonSerializable
{
    /**
     * @var Collection<string, array{name: string, data: array{float|int}|array<int, array{x: string|float, y: float}>}>
     */
    private Collection $series;

    /**
     * @var Collection<int, string>
     */
    private Collection $colors;

    /**
     * @param  array<int, array{x: string|float, y: float, color?: string}>  $data
     */
    public function __construct(
        public string $name,
        public string $currency,
        array $data = [],
        public float|int $previousTotal = 0,
        /** @var Collection<string, mixed> $options */
        public Collection $options = new Collection()
    ) {
        $this->series = new Collection();
        $this->colors = new Collection();

        foreach ($data as $dataPoint) {
            $this->addDataPoint($dataPoint['x'], $dataPoint['y']);

            if (isset($dataPoint['color'])) {
                $this->addColor($dataPoint['color']);
            }
        }
    }

    public function addDataPoint(string|float $x, float $y): self
    {
        $this->addSeries($this->name, [
            [
                'x' => $x,
                'y' => $y,
            ],
        ]);

        return $this;
    }

    /**
     * @param  array{float|int}|array<int, array{x: string|float, y: float}>  $data
     */
    public function addSeries(string $name, array $data): self
    {
        if (! $this->series->has($name)) {
            $this->series->put($name, [
                'name' => $name, 'data' => $data,
            ]);

            return $this;
        }

        $this->series->put($name, [
            'name' => $name,
            'data' => [...Arr::get($this->series->get($name, []), 'data', []), ...$data],
        ]);

        return $this;
    }

    public function addColor(string $color): self
    {
        $this->colors->add($color);

        return $this;
    }

    public function addOption(string $key, mixed $value): self
    {
        $this->options->put($key, $value);

        return $this;
    }

    public function growth(): float
    {
        if ($this->previousTotal === 0) {
            return 100;
        }

        return (($this->total() - $this->previousTotal) / $this->previousTotal) * 100;
    }

    public function total(): float|int
    {
        return $this->series->sum(function (array $series): float|int {
            $sum = array_sum(array_column($series['data'], 'y')); // sum data with [x, y] coords

            return $sum !== 0 ? $sum : array_sum($series['data']); // sum data with values only
        });
    }

    /**
     * @param  int  $options
     *
     * @throws JsonException
     */
    public function toJson($options = 0): string // @pest-ignore-type
    {
        return json_encode($this, $options | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            'series' => $this->series->values()->toArray(),
            'colors' => $this->colors,
            ...$this->options,
        ];
    }
}
