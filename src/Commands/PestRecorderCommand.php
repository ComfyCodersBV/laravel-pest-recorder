<?php

declare(strict_types=1);

namespace TranquilTools\PestRecorder\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use TranquilTools\PestRecorder\Environment\DatabaseManager;
use TranquilTools\PestRecorder\Environment\ServerManager;
use TranquilTools\PestRecorder\Events\EventCleaner;
use TranquilTools\PestRecorder\Filesystem\PestTestWriter;
use TranquilTools\PestRecorder\Pest\PestTestBuilder;
use TranquilTools\PestRecorder\Recorder\PlaywrightRecorder;

class PestRecorderCommand extends Command
{
    protected $signature = 'pest:record
        { --url=http://localhost:8001 }
        { --server=true }
        { --migrate=false }
        { --viewport-size=1920,1080 }';

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
        if ($this->option('migrate') === 'true' && empty($this->option('env'))) {
            $this->error('Please specify an environment like --env=testing');

            return self::FAILURE;
        }

        $recorder->checkDependencies();

        $url = $this->option('url');
        $environment = $this->option('env') ?: 'testing';

        try {
            $database->migrateIfNeeded($this, $environment, $this->option('migrate'));
        } catch (Exception $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $process = $server->startIfNeeded($this, $url, $environment, $this->option('server'));

        try {
            $events = $recorder->record($this, $url, $this->option('viewport-size'));

            if (empty($events)) {
                $this->error('No recording captured');

                return self::FAILURE;
            }

            $file = $writer->chooseTestFile($this);
            $title = (string) $this->ask('Test name (it(...))');

            $events = $cleaner->clean($events);
            $code = $builder->build($events, $title, $url);

            $path = $this->getPath($file);
            $writer->write($path, $code);

            $this->info('Test generated: ' . $path);
        } finally {
            $process?->stop(3, SIGINT);
        }

        return self::SUCCESS;
    }

    private function getPath(string $file): string
    {
        $fileName = Str::chopEnd($file, 'Test.php') . 'Test.php';
        $path = Str::chopEnd(config('pest-recorder.default_test_path'), '/');

        return base_path($path . '/' . $fileName);
    }
}
