<?php

namespace App\Logging;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

class JobLogger
{
    /**
     * Create a custom Monolog instance.
     *
     * @param  array  $config
     * @return \Monolog\Logger
     */
    public function __invoke(array $config)
    {
        $logger = new Logger('job-runner');

        $handler = new StreamHandler(
            $config['path'] ?? storage_path('logs/job-runner-log.log'),
            $config['level'] ?? 'debug'
        );

        // Custom formatter that only includes timestamp and message
        $formatter = new LineFormatter(
            "[%datetime%] %message%\n",
            'Y-m-d H:i:s',
            true,
            true
        );

        $handler->setFormatter($formatter);
        $logger->pushHandler($handler);

        return $logger;
    }
}
