<?php

declare(strict_types=1);

use App\Concerns\WithModel;

beforeEach(function (): void {
    $this->form = new class
    {
        use WithModel;

        public array $data = [];

        public function fill(mixed $value): void
        {
            $this->data = $value;
        }
    };

    $this->model = new class extends \Illuminate\Database\Eloquent\Model {};
    $this->model->foo = 'bar';
});

it('sets and fills the model', function (): void {
    expect($this->form->model)->toBeNull();
    $this->form->setModel($this->model);

    expect($this->form->model)->toBeInstanceOf(\Illuminate\Database\Eloquent\Model::class)
        ->and($this->form->model)->not->toBeInstanceOf(\Illuminate\Database\Eloquent\Builder::class)
        ->and($this->form->data)->toBe(['foo' => 'bar']);
});

it('does nothing if the model is not an instance of Illuminate\Database\Eloquent\Model', function (): void {
    $this->form->setModel(null);

    expect($this->form->model)->toBeNull()
        ->and($this->form->data)->toBe([]);
});
