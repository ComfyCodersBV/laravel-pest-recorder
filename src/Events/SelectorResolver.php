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

        foreach (['data-test', 'data-testid'] as $key) {
            if (! empty($attributes[$key])) {
                return "[{$key}=\"{$attributes[$key]}\"]";
            }
        }

        if ($kind === 'test-id' && ! empty($locator['body'])) {
            return $locator['body'];
        }

        if ($kind === 'role') {
            $role = $locator['body'] ?? '';
            $name = $options['name'] ?? '';

            if (in_array($role, ['link', 'button']) && $name) {
                return $name;
            }
        }

        if ($kind === 'text' && ! empty($locator['body'])) {
            return $locator['body'];
        }

        if (! empty($options['name']) && $kind === 'role' && $locator['body'] === 'textbox') {
            return 'input[name="'.Str::of($options['name'])->lower()->replace([' ', '-'], '').'"]';
        }

        if ($kind === 'css' && ! empty($locator['body'])) {
            return $locator['body'];
        }

        return null;
    }
}
