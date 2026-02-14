<?php

namespace ComfyCoders BV\PestRecorder;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use ComfyCoders BV\PestRecorder\Commands\PestRecorderCommand;

class PestRecorderServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-pest-recorder')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_pest_recorder_table')
            ->hasCommand(PestRecorderCommand::class);
    }
}
