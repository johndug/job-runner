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
            // Get the class name without the namespace
            $classPath = "App\\Libs\\" . class_basename($class);

            // Validate class exists
            if (!class_exists($classPath)) {
                Log::channel('joblog')->error("[Error] Class {$class} not found");
                return false;
            }

            // Validate method exists
            if (!method_exists($classPath, $method)) {
                Log::channel('joblog')->error("[Error] Method {$method} not found in class {$class}");
                return false;
            }

            // Create class instance
            $instance = app()->make($classPath);

            // Call the method with parameters
            $result = call_user_func_array([$instance, $method], $params);

            // Log successful execution
            Log::channel('joblog')->info("[Success] {$class}::{$method} [Params] " . json_encode($params) . " [Result] " . json_encode($result));
            return true;

        } catch (\Throwable $e) {
            Log::channel('joblog')->error("[Error] Error in runBackgroundJob: " . $e->getMessage());
            Log::channel('joblog')->error("[Error] Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }
}
