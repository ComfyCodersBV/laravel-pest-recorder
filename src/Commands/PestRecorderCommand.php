<?php

namespace ComfyCoders BV\PestRecorder\Commands;

use Illuminate\Console\Command;

class PestRecorderCommand extends Command
{
    public $signature = 'laravel-pest-recorder';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
