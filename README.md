# Laravel Pest Browsertest Recorder

This package introduces the `php artisan pest:record` command which provides an interactive way to generate a base for your Pest Browsertests.

Running this command allows you to perform actions in the browser which will be translated into Pest tests.
This process works by using the `npx playwirght codegen` command. You can use the Codegen toolbar to add assertions:

<img src="art/playwright-codegen-toolbar.png" alt="Playwright Codegen toolbar (assertions)" />

By default, a development server (`php artisan serve`) will be started, this can be disabled with the `--server=false` flag.

After closing the browser you'll be prompted with a question if you want to expand an existing test file or create a new one, and to give your new test a name:

<img src="art/pest-recorder-cli-output.png" alt="Laravel Pest Recorder CLI output / questions" />

## Installation

You can install the package via composer:

```bash
composer require tranquil-tools/laravel-pest-recorder --dev
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-pest-recorder-config"
```

The content of the published config can be viewed [here](./config/pest-recorder.php).

## Usage

```cli
php artisan pest:record
```
or:
```cli
php artisan pest:record
    --env=testing
    --url=http://localhost:8001
    --visit=/login
    --acting-as=user
    --server=true
    --migrate-fresh=false
    --seed=false
    --viewport-size=fullscreen
```

## Available flags / options
The environment variable, obliged when using --migrate-fresh=true
```cli
--env=testing
```

Provide a URL which will be opened in the browser as starting point for your tests.
When omitted, your .env APP_URL setting will be used.
```cli
--url=http://localhost:8001
```

Open a specific URI path when the recording browser starts. The path is appended to the base URL.
The initial navigation will automatically generate `$page = visit('/...');` in the test.
```cli
--visit=/login
```

Record and store Playwright browser auth state under a named key (e.g. `user`). The state is saved
to `storage/app/tmp/auth/{name}.json` (configurable via `acting_as_storage_path` in the config).

- **First run (no file yet):** you will be prompted to record a login sequence. A browser opens at
  `acting_as_login_path` (default `/login`); log in and close it — the browser storage state is saved.
- **Subsequent runs (file exists):** the saved state is loaded automatically, so the browser starts
  pre-authenticated, and you can skip straight to recording the feature you want to test.
- **Generated test:** `$this->actingAs(\App\Models\User::factory()->create());` is prepended to the
  `it()` block. Authentication during the actual test run is handled by Laravel's `actingAs()`, not
  by replaying browser login steps.

```php
it('test name', function () {
    $this->actingAs(\App\Models\User::factory()->create());

    $page = visit('/dashboard');
    $page->assertSee('...');
});
```
```cli
--acting-as=user
```

Start a development server (php artisan serve) for the given environment, URL and port.
```cli
--server=true
```

Run `php artisan migrate:fresh` before starting the server? Specifying --env=... is mandatory.
```cli
--migrate-fresh=false
```

Seed the database after migrate:fresh? This option is only available when using `--migrate-fresh=true`.
```cli
--seed=false
```

Set the viewport dimensions, or use `fullscreen` (the default) to detect the primary screen resolution.
Detected via `system_profiler` (macOS), `xdpyinfo`/`xrandr` (Linux), or PowerShell (Windows).
Falls back to `1920,1080` if detection fails. Ignored when `--device` is set.
```cli
--viewport-size=fullscreen  (default)
--viewport-size=1280,720
```

Emulate a specific device, including its viewport, user agent and touch settings. Overrides `--viewport-size`.
Run `npx playwright codegen --help` for the full list of supported device names.
```cli
--device="iPhone 15"
--device="Pixel 7"
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Pull requests are welcome!

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [ComfyCoders BV](https://github.com/comfycodersbv) - https://comfycoders.nl

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
