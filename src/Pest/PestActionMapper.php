<?php

declare(strict_types=1);

namespace TranquilTools\PestRecorder\Pest;

use Illuminate\Support\Str;
use TranquilTools\PestRecorder\Events\SelectorResolver;

readonly class PestActionMapper
{
    public function __construct(
        private SelectorResolver $resolver
    ) {}

    public function map(array $e, string $baseUrl): ?string
    {
        return match ($e['name'] ?? null) {
            'navigate' => $this->navigate($e, $baseUrl),
            'click' => $this->click($e),
            'fill' => $this->fill($e),
            'selectOption' => $this->select($e),
            'assertVisible' => $this->assertVisible($e),
            default => null,
        };
    }

    private function navigate(array $e, string $baseUrl): string
    {
        $path = Str::after($e['url'], $baseUrl) ?: '/';
        return "visit('{$path}')";
    }

    private function click(array $e): ?string
    {
        if ($s = $this->resolver->resolve($e)) {
            return "->click('{$s}')";
        }

        return null;
    }

    private function fill(array $e): ?string
    {
        if ($s = $this->resolver->resolve($e)) {
            $v = addslashes($e['text'] ?? $e['value'] ?? '');
            return "->fill('{$s}', '{$v}')";
        }

        return null;
    }

    private function assertVisible(array $e): string
    {
        return "->assertSee('".($this->resolver->resolve($e) ?? '')."')";
    }

    private function select(array $e): string
    {
        $field = $e['locator']['options']['name'] ?? $e['selector'];
        $value = $e['options'][0]['label']
            ?? $e['options'][0]['value']
            ?? $e['options'][0] ?? '';

        return "->select('{$field}', '{$value}')";
    }
}
