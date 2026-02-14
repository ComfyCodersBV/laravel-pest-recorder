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


    public function record(Command $command, string $url, string $viewport): array
    {
        $file = $this->prepareTempFile();

        $process = new Process(
            PlaywrightCodegenCommand::build($url, $file, $viewport)
        );

        $process->setTimeout(null);

        $command->line('Recorder running — close the browser to finish...');
        $process->run();

        if (! file_exists($file)) {
            return [];
        }

        return new JsonlParser()->parse($file);
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
