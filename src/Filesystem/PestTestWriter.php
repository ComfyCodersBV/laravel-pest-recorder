<?php

declare(strict_types=1);

namespace TranquilTools\PestRecorder\Filesystem;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PestTestWriter
{
    public function chooseTestFile(Command $command): string
    {
        $existing = $this->existingTests();

        $choice = $command->choice(
            'Select test file or New...',
            array_merge(['New...'], $existing),
            0
        );

        if ($choice === 'New...') {
            return Str::studly($command->ask('Test class name'));
        }

        return $choice;
    }

    public function write(string $base, string $code): void
    {
        $path = base_path("tests/Browser/{$base}Test.php");

        if (! file_exists($path)) {
            file_put_contents($path, "<?php\n\ndeclare(strict_types=1);\n\n{$code}\n");
            return;
        }

        file_put_contents($path, rtrim(file_get_contents($path))."\n\n{$code}\n");
    }

    private function existingTests(): array
    {
        $dir = base_path('tests/Browser');
        if (! is_dir($dir)) return [];

        return collect(File::allFiles($dir))
            ->filter(fn ($f) => str_ends_with($f->getFilename(), 'Test.php'))
            ->map(fn ($f) => substr($f->getFilename(), 0, -8))
            ->sort()
            ->values()
            ->all();
    }
}
