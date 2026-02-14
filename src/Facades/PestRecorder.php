<?php

declare(strict_types=1);

namespace TranquilTools\PestRecorder\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \TranquilTools\PestRecorder\PestRecorder
 */
class PestRecorder extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \TranquilTools\PestRecorder\PestRecorder::class;
    }
}
