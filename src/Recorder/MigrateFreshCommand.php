<?php

declare(strict_types=1);

namespace TranquilTools\PestRecorder\Recorder;

final class MigrateFreshCommand
{
    public static function build(string $environment, bool $seed = false): array
    {
        $command = [
            'php',
            'artisan',
            'migrate:fresh',
            '--force',
            '--env=' . $environment,
        ];

        if ($seed) {
            $command[] = '--seed';
        }

        return $command;
    }
}
