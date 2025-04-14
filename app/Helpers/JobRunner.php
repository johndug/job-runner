<?php

use Illuminate\Support\Facades\Log;

if (!function_exists('runBackgroundJob')) {
    /**
     * Run a job in the background
     *
     * @param string $class The fully qualified class name (with namespace)
     * @param string $method The method name to call
     * @param array $params Optional parameters to pass to the method
     * @return bool Returns true if job was successfully queued, false otherwise
     */
    function runBackgroundJob(string $class, string $method, array $params = []): bool
    {
        try {
            $command = sprintf("php run-job.php %s %s %s", $class, $method, implode(',', $params));
            // Get the class name without the namespace
            $classPath = "App\\Libs\\" . class_basename($class);

            // Validate class exists
            if (!class_exists($classPath)) {
                Log::channel('joblog')->error(sprintf("[Error] Class %s not found", $class));
                return false;
            }

            // Validate method exists
            if (!method_exists($classPath, $method)) {
                Log::channel('joblog')->error(sprintf("[Error] Method %s not found in class %s", $method, $class));
                return false;
            }

            // Create class instance
            $instance = app()->make($classPath);

            // Call the method with parameters
            $result = call_user_func_array([$instance, $method], $params);

            if ($result) {
                Log::channel('joblog')->info(sprintf("[Success] %s::%s [Params] %s [Command] %s", $class, $method, implode(',', $params), $command));
            } else {
                Log::channel('joblog')->error(sprintf("[Error] %s::%s [Params] %s [Command] %s", $class, $method, implode(',', $params), $command));
            }

            return true;

        } catch (\Throwable $e) {
            Log::channel('joblog')->error(sprintf(
                "[Error] Error in runBackgroundJob: %s\n[Command] %s\n[Stack trace] %s",
                $e->getMessage(),
                $command,
                $e->getTraceAsString()
            ));
            return false;
        }
    }
}
