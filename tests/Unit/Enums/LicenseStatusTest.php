<?php

declare(strict_types=1);

use App\Enums\LicenseStatus;

it('checks if a license is valid', function (): void {
    expect(LicenseStatus::Valid->isValid())->toBeTrue();
    expect(LicenseStatus::No_License->isValid())->toBeFalse();
});

it('checks if a license is invalid', function (): void {
    expect(LicenseStatus::Invalid->isInvalid())->toBeTrue();
    expect(LicenseStatus::No_License->isInvalid())->toBeFalse();
});

it('checks if a license is unknown', function (): void {
    expect(LicenseStatus::Unknown->isUnknown())->toBeTrue();
    expect(LicenseStatus::No_License->isUnknown())->toBeFalse();
});

it('checks if there is no license', function (): void {
    expect(LicenseStatus::No_License->hasNoLicense())->toBeTrue();
    expect(LicenseStatus::Valid->hasNoLicense())->toBeFalse();
});
