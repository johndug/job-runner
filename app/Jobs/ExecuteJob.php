<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExecuteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $maxExceptions = 3;

    public $timeout = 60;

    public $backoff = [5, 10, 15];

    protected $classPath;

    protected $method;

    protected $params;

    /**
     * Create a new job instance.
     */
    public function __construct(string $classPath, string $method, array $params = [])
    {
        $this->classPath = $classPath;
        $this->method = $method;
        $this->params = $params;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $instance = app()->make($this->classPath);
            $result = call_user_func_array([$instance, $this->method], $this->params);

            if ($result) {
                Log::channel('joblog')->info(json_encode([
                    'type' => 'success',
                    'message' => 'Method executed successfully',
                    'class' => $this->classPath,
                    'method' => $this->method,
                    'params' => $this->params,
                ]));
            } else {
                throw new \Exception('Method returned false');
            }
        } catch (\Throwable $e) {
            Log::channel('joblog')->error(json_encode([
                'type' => 'error',
                'message' => 'Error in job execution',
                'class' => $this->classPath,
                'method' => $this->method,
                'params' => $this->params,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]));

            throw $e;
        }
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public function backoff(): array
    {
        return $this->backoff;
    }
}
