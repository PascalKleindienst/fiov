<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\RequiredVersion;
use Illuminate\Contracts\Support\MessageBag as MessageBagContract;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\MessageBag;
use Symfony\Component\Uid\Ulid;
use Throwable;

final class StatusCheckService
{
    public const string MIN_PHP_VERSION = '8.3.0';

    public const string MIN_NODE_VERSION = '22.0.0';

    public const string MIN_NPM_VERSION = '10.0.0';

    private readonly MessageBagContract $errors;

    private bool $valid = true;

    public function __construct()
    {
        $this->errors = new MessageBag();
    }

    public function errors(): MessageBagContract
    {
        return $this->errors;
    }

    public function isValid(): bool
    {
        return $this->valid && ! $this->hasErrors();
    }

    public function hasErrors(): bool
    {
        return $this->errors->isNotEmpty();
    }

    /**
     * @return array{
     *     permissions: array<string, array{'valid': bool, 'path': string}>,
     *     mail_configuration: bool|int,
     *     php_version: RequiredVersion,
     *     npm_version: RequiredVersion,
     *     node_version: RequiredVersion
     * }
     */
    public function check(): array
    {
        return [
            'permissions' => $this->checkDirectoryPermissions(),
            'mail_configuration' => $this->checkMailConfiguration(),
            'php_version' => $this->checkPhp(),
            'npm_version' => $this->checkNpm(),
            'node_version' => $this->checkNode(),
        ];
    }

    /**
     * @return array<string, array{'valid': bool, 'path': string}>
     */
    public function checkDirectoryPermissions(): array
    {
        $directories = [
            'Session' => base_path('storage/framework/sessions'),
            'Cache' => base_path('storage/framework/cache'),
            'Logs' => base_path('storage/logs'),
        ];

        return array_map(function (string $path): array {
            $valid = File::isReadable($path) && File::isWritable($path);
            $this->valid = $valid && $this->valid;

            return [
                'valid' => $valid,
                'path' => $path,
            ];
        }, $directories);
    }

    public function checkMailConfiguration(): int|bool
    {
        if (! config('mail.default') || config('mail.default') === 'log') {
            // TODO Throw exception?
            return -1;
        }

        $recipient = Ulid::generate().'@mailinator.com';

        try {
            Mail::raw('This is a test email.', static fn (Message $message) => $message->to($recipient));

            return true;
        } catch (Throwable $throwable) {
            $this->valid = false;
            $this->errors->add(__METHOD__, $throwable->getMessage());

            return false;
        }
    }

    public function checkPhp(): RequiredVersion
    {
        $requirement = new RequiredVersion(
            required: self::MIN_PHP_VERSION,
            version: PHP_VERSION,
        );

        $this->valid = $requirement->valid && $this->valid;

        return $requirement;
    }

    public function checkNpm(): RequiredVersion
    {
        try {
            return new RequiredVersion(
                required: self::MIN_NPM_VERSION,
                version: trim(Process::run('npm -v')->output()),
            );
        } catch (Throwable $throwable) {
            $this->errors->add(__METHOD__, $throwable->getMessage());
        }

        return new RequiredVersion(required: self::MIN_NPM_VERSION);
    }

    public function checkNode(): RequiredVersion
    {
        try {
            return new RequiredVersion(
                required: self::MIN_NODE_VERSION,
                version: trim(str_replace('v', '', Process::run('node -v')->output())),
            );
        } catch (Throwable $throwable) {
            $this->errors->add(__METHOD__, $throwable->getMessage());
        }

        return new RequiredVersion(required: self::MIN_NODE_VERSION);
    }
}
