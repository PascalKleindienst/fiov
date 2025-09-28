<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\CreateUser;
use App\Enums\UserLevel;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\SQLiteDatabaseDoesNotExistException;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

use function Laravel\Prompts\password;
use function Laravel\Prompts\select;
use function Laravel\Prompts\suggest;
use function Laravel\Prompts\text;
use function sprintf;

final class InitCommand extends Command
{
    protected $signature = 'fiov:init {--no-assets : Do not compile front-end assets}';

    protected $description = 'Initialize Fiov';

    public function handle(): int
    {
        $this->components->alert('FIOV INSTALLATION WIZARD');

        try {
            $this->loadEnv();
            $this->maybeGenerateAppKey();
            $this->maybeSetupDatabase();
            $this->clearCaches();
            $this->migrateDatabase();
            $this->maybeSeedDatabase();
            $this->maybeCompileFrontendAssets();
        } catch (Throwable $throwable) {
            Log::error('Something went wront', ['err' => $throwable]);

            $this->components->error('Oops! Something went wrong.');
            $this->components->error('Fiov installation or upgrade did not complete successfully.');
            $this->components->error('Please check the log file at '.base_path('storage/logs/laravel.log').' for more details.');

            return self::FAILURE;
        }

        $this->newLine();
        $this->components->success('All done!');

        if (app()->isLocal()) {
            $this->components->info('💻️ Fiov can now be run from localhost with `php artisan serve`');
        } else {
            $this->components->info('💻️ Fiov is now available at '.config('app.url'));
        }

        $this->components->info('🛟 Documentation can be found at '.config('fiov.misc.docs_url'));
        // $this->components->info('🤗 Consider supporting Fiov’s development: '.config('fiov.misc.sponsor_github_url'));

        return self::SUCCESS;
    }

    private function loadEnv(): void
    {
        if (! File::exists(base_path('.env'))) {
            $this->components->task('Copying .env file', static function (): void {
                File::copy(base_path('.env.example'), base_path('.env'));
            });
        } else {
            $this->components->task('.env file exists -- skipping');
        }
    }

    private function maybeGenerateAppKey(): void
    {
        $key = config('app.key');

        if (empty($key)) {
            $this->components->task('Generating app key', fn (): int => $this->callSilently('key:generate'));
        }

        $this->components->task('Using app key: '.Str::limit(config('app.key'), 32));
    }

    private function maybeSetupDatabase(): void
    {
        try {
            DB::connection();
            Schema::getTables();
        } catch (Throwable $throwable) {
            if ($throwable instanceof SQLiteDatabaseDoesNotExistException) {
                $this->components->task('Creating database at: <comment>'.config('database.connections.sqlite.database').'</comment>', function (): void {
                    File::append(base_path(config('database.connections.sqlite.database')), PHP_EOL);
                });

                return;
            }

            Log::error('Could not connect to database', ['err' => $throwable]);

            $this->components->warn("Cannot connect to the database. Let's set it up.");
            $this->setUpDatabase();
        }
    }

    /**
     * Prompt user for valid database credentials and set up the database.
     */
    private function setUpDatabase(): void
    {
        $config = [
            'DB_HOST' => '',
            'DB_PORT' => '',
            'DB_USERNAME' => '',
            'DB_PASSWORD' => '',
        ];

        $config['DB_CONNECTION'] = select('Your DB driver of choise', [
            'sqlite' => 'SQLite',
            'mysql' => 'MySQL/MariaDB',
            'pgsql' => 'PostgreSQL',
            'sqlsrv' => 'SQL Server',
        ], 'sqlite');

        if ($config['DB_CONNECTION'] === 'sqlite') {
            $config['DB_DATABASE'] = text('Absolute path to the DB file', default: 'database/database.sqlite', required: true);
        } else {
            $config['DB_HOST'] = suggest('DB host', ['127.0.0.1', 'localhost']);
            $config['DB_PORT'] = text('DB port (leave empty for default)');
            $config['DB_DATABASE'] = suggest('DB name', ['fiov']);
            $config['DB_USERNAME'] = suggest('DB user', ['fiov']);
            $config['DB_PASSWORD'] = password('DB password');
        }

        Env::writeVariables($config, base_path('.env'), true);

        // Set the config so that the next DB attempt uses refreshed credentials
        config([
            'database.default' => $config['DB_CONNECTION'],
            sprintf('database.connections.%s.host', $config['DB_CONNECTION']) => $config['DB_HOST'],
            sprintf('database.connections.%s.port', $config['DB_CONNECTION']) => $config['DB_PORT'],
            sprintf('database.connections.%s.database', $config['DB_CONNECTION']) => $config['DB_DATABASE'],
            sprintf('database.connections.%s.username', $config['DB_CONNECTION']) => $config['DB_USERNAME'],
            sprintf('database.connections.%s.password', $config['DB_CONNECTION']) => $config['DB_PASSWORD'],
        ]);
    }

    private function clearCaches(): void
    {
        $this->components->task('Clearing caches', function (): void {
            $this->callSilently('config:clear');
            $this->callSilently('cache:clear');
        });
    }

    private function migrateDatabase(): void
    {
        $this->components->task('Migrating database', fn (): int => $this->callSilently('migrate', ['--force' => true]));
    }

    private function maybeSeedDatabase(): void
    {
        if (! User::query()->admin()->count()) {
            $this->components->task('Creating admin account', function (): void {
                $this->components->info('Creating admin account...');

                (new CreateUser())->handle([
                    'name' => text('Username', default: 'admin', required: true),
                    'email' => text('Email', default: 'admin@admin.com', required: true),
                    'password' => password('Password', required: true),
                    'level' => UserLevel::Admin->value,
                ]);
            });

            return;
        }

        $this->components->task('Data already seeded -- skipping');
    }

    private function maybeCompileFrontendAssets(): void
    {
        if ($this->option('no-assets')) {
            return;
        }

        $runCommand = function (string $command): void {
            $output = $this->option('verbose')
                ? fn (string $type, string $output) => $this->getOutput()->write($output)
                : null;

            Process::run($command, $output)->throw();
        };

        $this->components->task('Installing npm dependencies', fn () => $runCommand('npm install'));
        $this->components->task('Compiling frontend assets', fn () => $runCommand('npm run build'));
    }
}
