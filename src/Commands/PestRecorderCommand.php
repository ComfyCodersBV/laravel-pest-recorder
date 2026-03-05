<?php

declare(strict_types=1);

namespace TranquilTools\PestRecorder\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use TranquilTools\PestRecorder\Environment\DatabaseManager;
use TranquilTools\PestRecorder\Environment\ScreenResolutionDetector;
use TranquilTools\PestRecorder\Environment\ServerManager;
use TranquilTools\PestRecorder\Events\EventCleaner;
use TranquilTools\PestRecorder\Filesystem\PestTestWriter;
use TranquilTools\PestRecorder\Pest\PestTestBuilder;
use TranquilTools\PestRecorder\Recorder\PlaywrightRecorder;

class PestRecorderCommand extends Command
{
    protected $signature = 'pest:record
        { --url=default : By default the APP_URL from your .env will be used. You may provide a URL and port: http://localhost:8001 }
        { --visit=/ : URI path to open when the recording browser starts (e.g. /dashboard or /login) }
        { --acting-as= : Named auth state (e.g. --acting-as=user). Saves/loads browser storage from storage/app/tmp/auth/{name}.json and inserts $this->actingAs() in the generated test }
        { --server=true : Starts a development server process (php artisan serve) }
        { --migrate-fresh=false : Run `php artisan migrate:fresh`? Please specify --env=... as well }
        { --seed=false : Run `php artisan db:seed`? }
        { --viewport-size=fullscreen : Viewport dimensions (e.g. 1280,720) or "fullscreen" to detect the primary screen resolution }
        { --device= : Emulate a device (e.g. "iPhone 17"). Overrides --viewport-size. Run `npx playwright codegen --help` for the full list }';

    protected $description = 'Record browser actions with Playwright and generate Pest test';

    public function handle(
        PlaywrightRecorder $recorder,
        EventCleaner $cleaner,
        PestTestBuilder $builder,
        PestTestWriter $writer,
        ServerManager $server,
        DatabaseManager $database,
    ): int
    {
        $recorder->checkDependencies();

        $url = $this->option('url') !== 'default' ? $this->option('url') : config('app.url', 'http://localhost:8001');
        $environment = $this->option('env') ?: 'testing';

        try {
            $database->migrateIfNeeded($this, $environment, $this->option('migrate-fresh') === 'true');
        } catch (Exception $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $process = $server->startIfNeeded($this, $url, $environment, $this->option('server') === 'true');

        try {
            $loadStorage = $this->resolveActingAs($recorder, $url);
            $viewport = $this->option('device') ? null : $this->resolveViewport();

            $events = $recorder->record($this, $url, $viewport, $this->option('visit'), $loadStorage, $this->option('device'));

            if (empty($events)) {
                $this->error('No recording captured');

                return self::FAILURE;
            }

            $file = $writer->chooseTestFile($this);
            $title = (string) $this->ask('Test name (it(...))');

            $events = $cleaner->clean($events);
            $code = $builder->build($events, $title, $url, $this->option('acting-as') !== null);

            $path = $this->getPath($file);
            $writer->write($path, $code);

            $this->info('Test generated: ' . $path);
        } finally {
            $process?->stop(3, SIGINT);
        }

        return self::SUCCESS;
    }

    private function resolveActingAs(PlaywrightRecorder $recorder, string $url): ?string
    {
        $name = $this->option('acting-as');

        if (! $name) {
            return null;
        }

        $storagePath = storage_path(
            rtrim(config('pest-recorder.acting_as_storage_path', 'app/tmp/auth'), '/') . '/' . $name . '.json'
        );

        if (! file_exists($storagePath)) {
            if (! $this->confirm("No auth state found for '{$name}'. Record a login sequence now?", true)) {
                return null;
            }

            $loginPath = config('pest-recorder.acting_as_login_path', '/login');
            $recorder->recordActingAs($this, $url, $this->resolveViewport(), $loginPath, $storagePath);
        }

        return file_exists($storagePath) ? $storagePath : null;
    }

    private function resolveViewport(): string
    {
        if ($this->option('viewport-size') !== 'fullscreen') {
            return $this->option('viewport-size');
        }

        $detected = (new ScreenResolutionDetector)->detect();

        if ($detected) {
            $this->info('Using detected screen resolution of ' . $detected . ' (minus margings)');

            [$w, $h] = explode(',', $detected);
            $detected = ((int) $w - (int) config('pest-recorder.browser_chrome_width_margin', 0))
                . ','
                . ((int) $h - (int) config('pest-recorder.browser_chrome_height_margin', 0));

            return $detected;
        }

        $this->warn('Could not detect screen resolution, falling back to 1920,1080.');

        return '1920,1080';
    }

    private function getPath(string $file): string
    {
        $fileName = Str::chopEnd($file, 'Test.php') . 'Test.php';
        $path = Str::chopEnd(config('pest-recorder.default_test_path'), '/');

        return base_path($path . '/' . $fileName);
    }
}
