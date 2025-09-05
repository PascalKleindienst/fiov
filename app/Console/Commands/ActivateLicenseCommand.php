<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Facades\LicenseService;
use Exception;
use Illuminate\Console\Command;

use function sprintf;

final class ActivateLicenseCommand extends Command
{
    protected $signature = 'fiov:license:activate {key : The license key to activate. }';

    protected $description = 'Activate a fiov license';

    public function handle(): int
    {
        $this->components->info('Activating license...');

        try {
            $license = LicenseService::activate($this->argument('key'));
        } catch (Exception $exception) {
            $this->components->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->components->info('License activated successfully.');
        $this->components->twoColumnDetail('License Key', $license->short_key);

        $this->components->twoColumnDetail(
            'Registered To',
            sprintf('%s <%s>', $license->meta?->customerName, $license->meta?->customerEmail)
        );

        $this->components->twoColumnDetail('Expires On', $license->expires_at?->toDateString() ?? 'Heat death of the universe');

        return self::SUCCESS;
    }
}
