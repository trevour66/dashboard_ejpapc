<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\CSVFileProcessor\CSVFileProcessor;


class RunCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the cron job';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $processor = new CSVFileProcessor();
        $processor->process_matters_CSV();
        $processor->process_steps_CSV();
    }
}
