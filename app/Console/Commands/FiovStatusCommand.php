<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Facades\StatusCheckService;
use Closure;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Attribute\AsCommand;

use function sprintf;

#[AsCommand('fiov:status', 'Check Fiov setup status')]
final class FiovStatusCommand extends Command
{
    public function handle(): int
    {
        $this->components->alert('Checking Fiov setup...');
        $this->newLine();

        $this->checkDirectoryPermissions();
        $this->checkMailConfiguration();
        $this->checkPhp();
        $this->checkNpm();
        $this->checkNode();

        if (StatusCheckService::hasErrors()) {
            $this->components->error('There are errors in your Fiov setup. Fiov will not work properly.');

            if (File::isWritable(base_path('storage/logs/laravel.log'))) {
                foreach (StatusCheckService::errors()->all() as $method => $error) {
                    Log::error('[FIOV.STATUS] '.$error, ['method' => $method]);
                }

                $this->components->error('You can find more details in '.base_path('storage/logs/laravel.log'));
            } else {
                $this->components->error('The list of errors is as follows:');

                foreach (StatusCheckService::errors()->all() as $method => $error) {
                    $this->line(sprintf('  <error>[%s]</error> ', $method).$error);
                }
            }

            return self::FAILURE;
        }

        if (StatusCheckService::isValid()) {
            $this->components->success('Your Fiov setup should be good to go!');

            return self::SUCCESS;
        }

        $this->components->warn('Your Fiov setup has some issues. Fiov might not work properly.');

        return self::FAILURE;
    }

    public function checkMailConfiguration(): void
    {
        $mailConfig = StatusCheckService::checkMailConfiguration();
        if ($mailConfig === -1) {
            $this->report('Mailer configuration', 'Not available', 'warning');

            return;
        }

        if ($mailConfig) {
            $this->report('Mailer configuration');

            return;
        }

        $this->report('Mailer configuration', type: 'error');
    }

    private function checkDirectoryPermissions(): void
    {
        foreach (StatusCheckService::checkDirectoryPermissions() as $name => $item) {
            $this->assert(
                condition: $item['valid'],
                success: sprintf('%s directory <info>%s</info> is readable/writable.', $name, $item['path']),
                error: sprintf('%s directory <info>%s</info> is not readable/writable.', $name, $item['path']),
            );
        }
    }

    private function assert(Closure|bool $condition, Closure|string|null $success = null, Closure|string|null $error = null, Closure|string|null $warning = null): void
    {
        $result = value($condition);

        if ($result) {
            $this->report(value($success));

            return;
        }

        if ($error !== null) {
            $this->report(value($error), type: 'error');

            return;
        }

        if ($warning !== null) {
            $this->report(value($warning), type: 'warning');
        }
    }

    private function report(string $message, ?string $value = null, string $type = 'ok'): void
    {
        $value ??= strtoupper($type);
        $value = trim($value);

        $this->components->twoColumnDetail(trim($message), match ($type) {
            'ok' => sprintf('<info>%s</info>', $value),
            'warning' => sprintf('<comment>%s</comment>', $value),
            'error' => sprintf('<error>%s</error>', $value),
            default => $value
        });
    }

    private function checkPhp(): void
    {
        $php = StatusCheckService::checkPhp();
        $this->report('PHP Version >= '.$php->required, 'v'.$php->version, $php->valid ? 'ok' : 'error');
    }

    private function checkNpm(): void
    {
        $npm = StatusCheckService::checkNpm();
        $this->report(sprintf('NPM Version >= %s <comment>(optional)</comment>', $npm->required), $npm->valid ? 'OK' : 'FAIL', $npm->valid ? 'ok' : 'error');
    }

    private function checkNode(): void
    {
        $node = StatusCheckService::checkNode();
        $this->report(sprintf('Node Version >= %s <comment>(optional)</comment>', $node->required), $node->valid ? 'OK' : 'FAIL', $node->valid ? 'ok' : 'error');
    }
}
