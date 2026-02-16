<?php

declare(strict_types=1);

namespace TranquilTools\PestRecorder\Pest;

readonly class PestTestBuilder
{
    public function __construct(
        private PestActionMapper $mapper
    ) {}

    public function build(array $events, string $title, string $baseUrl): string
    {
        $blocks = [];
        $chain = [];

        foreach ($events as $event) {
            $mapped = $this->mapper->map($event, $baseUrl);

            if (! $mapped) {
                continue;
            }

            if (str_starts_with($mapped, "visit('")) {
                if ($chain) {
                    $blocks[] = $this->renderChain($chain);
                    $chain = [];
                }
                $blocks[] = "\$page = {$mapped};";
                continue;
            }

            $chain[] = $mapped;
        }

        if ($chain) {
            $blocks[] = $this->renderChain($chain);
        }

        $title = preg_replace("/(?<!\\\\)'/", "\\'", $title);
        $body = implode("\n\n    ", $blocks);

        return <<<PHP
it('{$title}', function () {
    {$body}
});
PHP;
    }

    private function renderChain(array $chain): string
    {
        return '$page'.implode("\n        ", $chain).';';
    }
}
