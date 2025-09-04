<?php

declare(strict_types=1);

use App\Enums\UserLevel;

it("checks if it's admin", function (): void {
    expect(UserLevel::Admin->isAdmin())->toBeTrue()
        ->and(UserLevel::User->isAdmin())->toBeFalse();
});
