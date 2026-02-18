<?php

declare(strict_types=1);

namespace TranquilTools\PestRecorder\Environment;

use Exception;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use TranquilTools\PestRecorder\Recorder\MigrateFreshCommand;

class DatabaseManager
{
    public function migrateIfNeeded(Command $command, string $environment, bool $enabled): void
    {
        if (! $enabled) {
            return;
        }

        if (empty($command->option('env'))) {
            throw new Exception('Please specify an environment like --env=testing');
        }

        if ($environment !== 'testing' && ! $command->confirm('Environment is ' . $environment . ', continue migrate:fresh?')) {
            return;
        }

        $seed = $command->option('seed') === 'true';

        $command->info("Executing 'migrate:fresh" . ($seed ? " --seed" : "") . "' for environment: " . $environment . "...");

        $process = new Process(
            MigrateFreshCommand::build($environment, $seed)
        );

        $process->setTimeout(null);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new Exception('Database migration failed');
        }
    }
}
