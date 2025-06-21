<?php

use App\Concerns\WithBreadcrumbs;
use App\Data\BreadcrumbItemData;

it('sets shares the breadcrumbs with the view', function () {
    $class = new class {
        use WithBreadcrumbs;

        public function doSomething(): void
        {
            $this->withBreadcrumbs(new BreadcrumbItemData('foo', '#foo'), new BreadcrumbItemData('bar'));
        }
    };

    $class->doSomething();
    expect(View::shared('breadcrumbs'))->toEqual(
        [
            new BreadcrumbItemData('foo', '#foo'),
            new BreadcrumbItemData('bar')
        ]
    );
});
