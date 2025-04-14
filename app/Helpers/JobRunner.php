<?php

use App\Jobs\ExecuteJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

if (! function_exists('runBackgroundJob')) {
    /**
     * Run a job in the background using Laravel's queue system
     *
     * @param  string  $class  The fully qualified class name (with namespace)
     * @param  string  $method  The method name to call
     * @param  array  $params  Optional parameters to pass to the method
     * @param  int  $maxRetries  Maximum number of retry attempts (default: 3)
     * @param  int  $retryDelay  Delay between retries in seconds (default: 5)
     * @return bool Returns true if job was successfully queued, false otherwise
     */
    function runBackgroundJob(string $class, string $method, array $params = [], int $maxRetries = 1, int $retryDelay = 5): bool
    {
        try {
            $command = buildCommand($class, $method, $params);
            $classPath = buildClassPath($class);

            if (! validateClass($class, $classPath, $command)) {
                return false;
            }

            if (! validateMethod($class, $method, $classPath, $command)) {
                return false;
            }

            // Dispatch the job to the queue with retry configuration
            Queue::push(new ExecuteJob($classPath, $method, $params), '', 'default', [
                'maxTries' => $maxRetries,
                'delay' => $retryDelay,
                'timeout' => 60,
            ]);

            logSuccess('Job queued successfully', [
                'class' => $class,
                'method' => $method,
                'params' => $params,
                'command' => $command,
                'maxRetries' => $maxRetries,
                'retryDelay' => $retryDelay,
            ]);

            return true;

        } catch (\Throwable $e) {
            logError('Error in runBackgroundJob', [
                'command' => $command ?? '',
                'stackTrace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }

    /**
     * Build the command string
     */
    function buildCommand(string $class, string $method, array $params): string
    {
        return sprintf('php run-job.php %s %s %s', $class, $method, implode(',', $params));
    }

    /**
     * Build the class path
     */
    function buildClassPath(string $class): string
    {
        return 'App\\Libs\\'.class_basename($class);
    }

    /**
     * Validate if class exists
     */
    function validateClass(string $class, string $classPath, string $command): bool
    {
        if (! class_exists($classPath)) {
            logError('Class not found', [
                'class' => $class,
                'classPath' => $classPath,
                'command' => $command,
            ]);

            return false;
        }

        return true;
    }

    /**
     * Validate if method exists
     */
    function validateMethod(string $class, string $method, string $classPath, string $command): bool
    {
        if (! method_exists($classPath, $method)) {
            logError('Method not found', [
                'method' => $method,
                'class' => $class,
                'command' => $command,
            ]);

            return false;
        }

        return true;
    }

    /**
     * Log success message
     */
    function logSuccess(string $message, array $context = []): void
    {
        Log::channel('joblog')->info(json_encode(array_merge([
            'type' => 'success',
            'message' => $message,
        ], $context)));
    }

    /**
     * Log error message
     */
    function logError(string $message, array $context = []): void
    {
        Log::channel('joblog')->error(json_encode(array_merge([
            'type' => 'error',
            'message' => $message,
        ], $context)));
    }
}
