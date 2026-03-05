<?php

declare(strict_types=1);

namespace TranquilTools\PestRecorder\Recorder;

use Exception;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class PlaywrightRecorder
{
    public function checkDependencies(): void
    {
        $process = new Process([
            'npx',
            'playwright',
            '--version',
        ]);

        $process->run();

        if (! $process->isSuccessful()) {
            throw new Exception('Playwright not found. Please run: npm install -D @playwright/test');
        }
    }


    public function record(Command $command, string $url, ?string $viewport, ?string $visitPath = null, ?string $loadStorage = null, ?string $device = null): array
    {
        $file = $this->prepareTempFile();

        $process = new Process(
            PlaywrightCodegenCommand::build($url, $file, $viewport, $visitPath, null, $loadStorage, $device)
        );

        $process->setTimeout(null);

        $command->line('Recorder running — close the browser to finish...');
        $process->run();

        if (! file_exists($file)) {
            return [];
        }

        return new JsonlParser()->parse($file);
    }

    public function recordActingAs(Command $command, string $url, string $viewport, string $loginPath, string $storageFile): void
    {
        $dir = dirname($storageFile);

        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $file = $this->prepareTempFile();

        $process = new Process(
            PlaywrightCodegenCommand::build($url, $file, $viewport, $loginPath, $storageFile)
        );

        $process->setTimeout(null);

        $command->line('Login recorder running — log in and close the browser to save...');
        $process->run();
    }

    private function prepareTempFile(): string
    {
        $dir = storage_path('app/tmp');

        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $file = $dir . '/recording.jsonl';
        @unlink($file);

        return $file;
    }
}
