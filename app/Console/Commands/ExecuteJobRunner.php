<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

// Include the helper file
require_once __DIR__ . '/../../Helpers/JobRunner.php';

class ExecuteJobRunner extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exec:job-runner {className} {methodName} {params?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute job runner';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $className = $this->argument('className');
        $methodName = $this->argument('methodName');
        $param_string = $this->argument('params');

        $params = explode(',', $param_string);
        $params_array = array_map('trim', $params);

        // Run the job in background
        runBackgroundJob($className, $methodName, $params_array);

        $this->info("Job {$className}::{$methodName} has been queued to run in background");
        return Command::SUCCESS;
    }
}
