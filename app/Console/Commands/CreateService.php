<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateService extends Command
{
    protected $signature = 'make:service {name}';
    protected $description = 'Create a new service class';

    public function handle()
    {
        $name = $this->argument('name');
        $path = app_path('Services');

        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        $filePath = $path . '/' . $name . '.php';
        $stub = <<<EOD
        <?php

        namespace App\Services;

        class {$name}
        {
           public function __construct()
           {
                // Code here
           }
        }
        EOD;

        File::put($filePath, $stub);

        $this->info("Service {$name} created successfully at {$filePath}");
    }
}
