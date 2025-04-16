<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeAction extends Command
{
    protected $signature = 'make:action {name}';

    protected $description = 'Create a new Action class';

    public function handle(): void
    {
        $nameInput = $this->argument('name'); // e.g. Auth/RegisterUser
        $className = class_basename($nameInput); // RegisterUser
        $relativePath = str_replace('\\', '/', $nameInput); // Auth/RegisterUser
        $path = app_path("Actions/{$relativePath}.php");

        if (File::exists($path)) {
            $this->error("Action {$className} already exists!");

            return;
        }

        // Get the namespace
        $namespace = 'App\\Actions\\'.str_replace('/', '\\', Str::beforeLast($relativePath, '/'));

        // Fallback in case there's no subdirectory (root level)
        if (! Str::contains($namespace, '\\')) {
            $namespace = 'App\\Actions';
        }

        $stub = File::get(base_path('stubs/action.stub'));

        $content = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$namespace, $className],
            $stub
        );

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);

        $this->info("✅ Action created: {$namespace}\\{$className}");
    }
}
