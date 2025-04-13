<?php

require __DIR__ . '/vendor/autoload.php';

// Include the helper file directly
require_once __DIR__ . '/app/Helpers/JobRunner.php';

// Set error log path to Laravel's log file
ini_set('error_log', __DIR__ . '/storage/logs/job-runner-log.log');

// Initialize Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get command line arguments
$className = $argv[1] ?? null;
$methodName = $argv[2] ?? null;
$params = $argv[3] ?? '';

if (!$className || !$methodName) {
    die("Usage: php run-job.php <ClassName> <MethodName> [params]\n");
}

// Process parameters
$params_array = !empty($params) ? array_map('trim', explode(',', $params)) : [];

// Use runBackgroundJob helper
if (runBackgroundJob($className, $methodName, $params_array)) {
    echo "Job {$className}::{$methodName} has been queued successfully\n";
    exit(0);
} else {
    echo "Failed to queue job {$className}::{$methodName}\n";
    exit(1);
}
