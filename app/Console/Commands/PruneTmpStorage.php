<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PruneTmpStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:prune-tmp-storage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune temporary storage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Storage::disk('tmp')->delete(Storage::disk('tmp')->allFiles());
    }
}
