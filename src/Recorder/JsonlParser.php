<?php

declare(strict_types=1);

namespace TranquilTools\PestRecorder\Recorder;

class JsonlParser
{
    public function parse(string $file): array
    {
        $events = [];

        foreach (file($file, FILE_IGNORE_NEW_LINES) as $line) {
            if ($decoded = json_decode($line, true)) {
                $events[] = $decoded;
            }
        }

        return $events;
    }
}
