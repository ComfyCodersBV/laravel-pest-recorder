<?php

declare(strict_types=1);

namespace TranquilTools\PestRecorder\Environment;

use Exception;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class DatabaseManager
{
    public function migrateIfNeeded(Command $command, string $env, string $enabled): void
    {
        if ($enabled !== 'true') {
            return;
        }

        if ($env !== 'testing' && ! $command->confirm("Environment is {$env}, continue migrate:fresh?")) {
            return;
        }

        $process = new Process([
            'php',
            'artisan',
            'migrate:fresh',
            '--seed',
            '--force',
            '--env=' . $env,
        ]);

        $process->setTimeout(null);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new Exception('Database migration failed');
        }
    }
}
