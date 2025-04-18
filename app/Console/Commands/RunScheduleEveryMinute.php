<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RunScheduleEveryMinute extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This runs the scheduler every minute to ensure that scheduled tasks run on time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //Ideally, we'd use an external process manager like docker or supervisord to trigger this 
        // but this is not an enterprise scale work, so this makeshift solution would have to suffice
        $this->info('Running Laravel schedule every minute...');

        while (true) {
            Artisan::call('schedule:run');
            sleep(60);
        }
    }
}
