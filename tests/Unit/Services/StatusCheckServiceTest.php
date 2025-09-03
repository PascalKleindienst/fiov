<?php

declare(strict_types=1);

use App\Facades\StatusCheckService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Process;

beforeEach(function (): void {
    // Mock File facade
    File::shouldReceive('isReadable')
        ->andReturn(true)
        ->byDefault();

    File::shouldReceive('isWritable')
        ->andReturn(true)
        ->byDefault();

    // Mock Mail facade
    Mail::fake();

    // Set default mail config
    Config::set('mail.default', 'smtp');
});

it('checks directory permissions', function (): void {
    $result = StatusCheckService::checkDirectoryPermissions();

    expect($result)->toBeArray()
        ->and($result)->toHaveKeys(['Session', 'Cache', 'Logs'])
        ->and($result['Session']['valid'])->toBeTrue()
        ->and($result['Cache']['valid'])->toBeTrue()
        ->and($result['Logs']['valid'])->toBeTrue()
        ->and(StatusCheckService::isValid())->toBeTrue();
});

it('handles invalid directory permissions', function (): void {
    File::shouldReceive('isReadable')
        ->with(base_path('storage/framework/sessions'))
        ->andReturn(false);

    $result = StatusCheckService::checkDirectoryPermissions();

    expect($result['Session']['valid'])->toBeFalse()
        ->and(StatusCheckService::isValid())->toBeFalse();
});

it('checks PHP version', function (): void {
    $result = StatusCheckService::checkPhp();

    expect($result->valid)->toBeTrue()
        ->and($result->version)->toBe(PHP_VERSION)
        ->and($result->required)->toBe(\App\Services\StatusCheckService::MIN_PHP_VERSION)
        ->and(StatusCheckService::isValid())->toBeTrue();
});

it('checks Node.js version', function (): void {
    Process::shouldReceive('run')
        ->andReturnUsing(function () {
            $processResult = mock(\Illuminate\Contracts\Process\ProcessResult::class);
            $processResult->shouldReceive('output')
                ->andReturn("v22.1.0\n");

            return $processResult;
        });

    $result = StatusCheckService::checkNode();

    expect($result->valid)->toBeTrue()
        ->and($result->version)->toBe('22.1.0')
        ->and($result->required)->toBe(\App\Services\StatusCheckService::MIN_NODE_VERSION)
        ->and(StatusCheckService::isValid())->toBeTrue();
});

it('checks NPM version', function (): void {
    Process::shouldReceive('run')
        ->andReturnUsing(function () {
            $processResult = mock(\Illuminate\Contracts\Process\ProcessResult::class);
            $processResult->shouldReceive('output')
                ->andReturn("10.0.0\n");

            return $processResult;
        });

    $result = StatusCheckService::checkNpm();

    expect($result->valid)->toBeTrue()
        ->and($result->version)->toBe('10.0.0')
        ->and($result->required)->toBe(\App\Services\StatusCheckService::MIN_NPM_VERSION)
        ->and(StatusCheckService::isValid())->toBeTrue();
});

it('handles node process execution errors', function (): void {
    Process::shouldReceive('run')
        ->andThrow(new RuntimeException('Command not found'), code: 1);

    $result = StatusCheckService::checkNode();

    expect($result->valid)->toBeFalse()
        ->and(StatusCheckService::hasErrors())->toBeTrue()
        ->and(StatusCheckService::isValid())->toBeFalse()
        ->and(StatusCheckService::errors())->toHaveCount(1);
});

it('handles npm process execution errors', function (): void {
    Process::shouldReceive('run')
        ->andThrow(new RuntimeException('Command not found'), code: 1);

    $result = StatusCheckService::checkNpm();

    expect($result->valid)->toBeFalse()
        ->and(StatusCheckService::hasErrors())->toBeTrue()
        ->and(StatusCheckService::isValid())->toBeFalse()
        ->and(StatusCheckService::errors())->toHaveCount(1);
});

it('checks mail configuration', function (): void {
    $result = StatusCheckService::checkMailConfiguration();

    expect($result)->toBeTrue()
        ->and(StatusCheckService::isValid())->toBeTrue();
});

it('handles mail configuration with log driver', function (): void {
    Config::set('mail.default', 'log');

    $result = StatusCheckService::checkMailConfiguration();

    expect($result)->toBe(-1)
        ->and(StatusCheckService::isValid())->toBeTrue();
});

it('handles mail sending errors', function (): void {
    Mail::shouldReceive('raw')
        ->once()
        ->andThrow(new Exception('Mail server error'));

    $result = StatusCheckService::checkMailConfiguration();

    expect($result)->toBeFalse()
        ->and(StatusCheckService::hasErrors())->toBeTrue()
        ->and(StatusCheckService::isValid())->toBeFalse()
        ->and(StatusCheckService::errors())->toHaveCount(1);
});

it('performs a full check', function (): void {
    $result = StatusCheckService::check();

    expect($result)->toBeArray()
        ->and($result)->toHaveKeys([
            'permissions',
            'mail_configuration',
            'php_version',
            'npm_version',
            'node_version',
        ])
        ->and(StatusCheckService::isValid())->toBeTrue();
});
