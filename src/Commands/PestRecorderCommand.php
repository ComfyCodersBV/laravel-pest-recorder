<?php

declare(strict_types=1);

namespace TranquilTools\PestRecorder\Commands;

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
