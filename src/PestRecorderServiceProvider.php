<?php

declare(strict_types=1);

namespace TranquilTools\PestRecorder;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use TranquilTools\PestRecorder\Commands\PestRecorderCommand;

class PestRecorderServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-pest-recorder')
            ->hasConfigFile()
            ->hasCommand(PestRecorderCommand::class);
    }
}
