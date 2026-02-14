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
        string $env,
        string $enabled
    ): ?Process
    {
        if ($enabled !== 'true') {
            return null;
        }

        $port = parse_url($url, PHP_URL_PORT) ?: 80;

        $process = new Process([
            'php',
            'artisan',
            'serve',
            '--port=' . $port,
            '--env=' . $env,
        ]);

        $process->setTimeout(null);
        $process->start();

        sleep(2);
        $command->info('Server started at ' . $url);

        return $process;
    }
}
