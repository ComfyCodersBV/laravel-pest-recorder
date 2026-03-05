<?php

declare(strict_types=1);

namespace TranquilTools\PestRecorder\Recorder;

final class PlaywrightCodegenCommand
{
    public static function build(string $url, string $file, string $viewport, ?string $visitPath = null): array
    {
        $command = [
            'npx',
            'playwright',
            'codegen',
            '--viewport-size=' . $viewport,
            '--target=jsonl',
            '--test-id-attribute=' . config('pest-recorder.drivers.playwright.test_id_attribute'),
            '--output=' . $file,
        ];

        if (config('pest-recorder.drivers.playwright.ignore_https_errors')) {
            $command[] = '--ignore-https-errors';
        }

        $startUrl = $visitPath
            ? rtrim($url, '/') . '/' . ltrim($visitPath, '/')
            : $url;

        $command[] = $startUrl;

        return $command;
    }
}
