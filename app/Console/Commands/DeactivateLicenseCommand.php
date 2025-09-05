<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Facades\LicenseService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Throwable;

use function Laravel\Prompts\confirm;

#[AsCommand(name: 'fiov:license:deactivate', description: 'Deactivate a fiov license')]
final class DeactivateLicenseCommand extends Command
{
    public function handle(): int
    {
        $status = LicenseService::status(false);

        if ($status->hasNoLicense()) {
            $this->components->warn('No active license found.');

            return self::SUCCESS;
        }

        if (! confirm('Do you want to deactivate your fiov license?')) {
            $this->components->warn('License was not deactivated.');

            return self::SUCCESS;
        }

        $this->components->info('Deactivating license...');

        try {
            LicenseService::deactivate();
            $this->components->info('License has been deactivated. Premium features are now disabled.');

            return self::SUCCESS;
        } catch (Throwable $throwable) {
            $this->components->error('Failed to deactivate license: '.$throwable->getMessage());

            return self::FAILURE;
        }
    }
}
