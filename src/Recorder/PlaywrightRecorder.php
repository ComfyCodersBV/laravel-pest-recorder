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
        $dir = storage_path('app/tmp');
        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $file = $dir . '/recording.jsonl';
        @unlink($file);

        $process = new Process([
            'npx',
            'playwright',
            'codegen',
            '--viewport-size=' . $viewport,
            '--target=jsonl',
            '--ignore-https-errors',
            '--test-id-attribute=id',
            '--output=' . $file,
            $url,
        ]);

        $process->setTimeout(null);

        $command->line('Recorder running — close browser when done...');
        $process->run();

        if (! file_exists($file)) {
            return [];
        }

        return new JsonlParser()->parse($file);
    }
}
