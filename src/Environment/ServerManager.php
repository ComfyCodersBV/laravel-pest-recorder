<?php

declare(strict_types=1);

namespace TranquilTools\PestRecorder\Environment;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class ServerManager
{
    public function startIfNeeded(
        Command $command,
        string $url,
        string $environment,
        bool $enabled
    ): ?Process
    {
        if (! $enabled) {
            return null;
        }

        $port = parse_url($url, PHP_URL_PORT) ?: 80;

        $process = new Process([
            'php',
            'artisan',
            'serve',
            '--port=' . $port,
            '--env=' . $environment,
        ]);

        $process->setTimeout(null);
        $process->start();

        sleep(2);
        $command->info('Server started at ' . $url);

        return $process;
    }
}
