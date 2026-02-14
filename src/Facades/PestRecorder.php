<?php

namespace ComfyCoders BV\PestRecorder\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \ComfyCoders BV\PestRecorder\PestRecorder
 */
class PestRecorder extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \ComfyCoders BV\PestRecorder\PestRecorder::class;
    }
}
