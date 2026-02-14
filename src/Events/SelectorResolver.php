<?php

declare(strict_types=1);

namespace TranquilTools\PestRecorder\Events;

use Illuminate\Support\Str;

class SelectorResolver
{
    public function resolve(array $e): ?string
    {
        $locator = $e['locator'] ?? [];
        $kind = $locator['kind'] ?? '';
        $options = $locator['options'] ?? [];
        $attributes = $options['attributes'] ?? [];

        $priority = config('pest-recorder.selector_priority', [
            'data-test',
            'data-testid',
            'test-id',
            'role',
            'text',
            'css',
        ]);

        foreach ($priority as $strategy) {
            switch ($strategy) {
                case 'data-test':
                case 'data-testid':
                    if (! empty($attributes[$strategy])) {
                        return "[{$strategy}=\"{$attributes[$strategy]}\"]";
                    }
                    break;

                case 'test-id':
                    if ($kind === 'test-id' && ! empty($locator['body'])) {
                        return $locator['body'];
                    }
                    break;

                case 'role':
                    if ($kind === 'role') {
                        $role = $locator['body'] ?? '';
                        $name = $options['name'] ?? '';

                        if (in_array($role, ['link', 'button']) && $name) {
                            return $name;
                        }

                        if ($role === 'textbox' && ! empty($name)) {
                            $normalized = Str::of($name)
                                ->lower()
                                ->replace([' ', '-'], '')
                                ->toString();

                            return "input[name=\"{$normalized}\"]";
                        }
                    }
                    break;

                case 'text':
                    if ($kind === 'text' && ! empty($locator['body'])) {
                        return $locator['body'];
                    }
                    break;

                case 'css':
                    if ($kind === 'css' && ! empty($locator['body'])) {
                        return $locator['body'];
                    }
                    break;
            }
        }

        return null;
    }
}
