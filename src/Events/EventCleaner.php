<?php

declare(strict_types=1);

namespace TranquilTools\PestRecorder\Events;

readonly class EventCleaner
{
    public function __construct(
        private SelectorResolver $resolver
    ) {}

    public function clean(array $events): array
    {
        $clean = [];
        $lastFill = [];

        foreach ($events as $i => $event) {
            $name = $event['name'] ?? null;
            if (! $name) continue;

            $selector = $this->resolver->resolve($event);
            if (! $selector && in_array($name, ['click', 'fill', 'press'])) {
                continue;
            }

            if ($name === 'click') {
                $next = $events[$i + 1] ?? null;
                if ($next && $next['name'] === 'fill' && $selector === $this->resolver->resolve($next)) {
                    continue;
                }
            }

            if ($name === 'fill') {
                if (isset($lastFill[$selector])) {
                    unset($clean[$lastFill[$selector]]);
                }
                $lastFill[$selector] = count($clean);
            }

            $clean[] = $event;
        }

        return array_values($clean);
    }
}
