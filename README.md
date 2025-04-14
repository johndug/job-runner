# Job Runner

A Laravel-based application for running and monitoring background jobs with a user-friendly interface.

## Features

- Run PHP class methods as background jobs through terminal
- Real-time job execution monitoring with auto-refresh
- Detailed logging of job execution
- Web interface for viewing logs and running jobs
- Run Commands, Run Again and Clear Logs functionality
- Automatic retry mechanism for failed jobs
- Queue-based job execution
- Real-time log updates (5-second refresh interval)

## Prerequisites

- PHP 8.2 or higher
- Composer
- Laravel 12
- Node.js and NPM (for asset compilation)

## Installation

1. Clone the repository:
```bash
git clone [repository-url]
cd job-runner
```

2. Install dependencies:
```bash
composer install
```

3. Copy the environment file:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Configure your database in `.env`:
```bash
php artisan migrate --seed
```

6. Configure queue settings in `.env`:
```bash
QUEUE_CONNECTION=database
```

7. Create the jobs table:
```bash
php artisan queue:table
php artisan migrate
```

8. Start the queue worker:
```bash
php artisan queue:work
```

Default login credentials:
```bash
Username: test@example.com
Password: password
```

## Project Structure

- `app/Helpers/JobRunner.php` - Core job execution functionality with retry mechanism
- `app/Http/Controllers/LogController.php` - Handles log viewing, job execution, and log clearing
- `app/Jobs/ExecuteJob.php` - Queue job handler
- `resources/views/logs/index.blade.php` - Web interface for logs
- `resources/views/components/log-entry.blade.php` - Reusable log entry component
- `storage/logs/job-runner-log.log` - Job execution logs

## Usage

### Running Jobs

1. Access the web interface at `http://localhost:8000`
2. Login
3. View job execution logs
4. Click "Run Again" on any previous job to re-execute it
5. Use the "Run Command" button to execute new jobs
6. Monitor job execution status and results in real-time
7. Clear logs using the "Clear Logs" button

### Creating New Jobs

1. Create your job class in `app/Libs/`:
```php
<?php

namespace App\Libs;

class YourJob
{
    public function yourMethod($param1, $param2)
    {
        // Your job logic here
        return true; // Return true for success, false for failure
    }
}
```

2. The job will be automatically available in the web interface

### Job Retry Configuration

Jobs can be configured with retry settings:
```php
runBackgroundJob('YourJob', 'yourMethod', ['param1', 'param2'], $maxRetries = 3, $retryDelay = 5);
```

- `$maxRetries`: Maximum number of retry attempts (default: 3)
- `$retryDelay`: Delay between retries in seconds (default: 5)

### Manual CLI Testing
```bash
php run-job.php NumberTest isEven {even number}
php run-job.php NumberTest isOdd {odd number}
php run-job.php NumberTest isPrime {prime number}
php run-job.php StringTest testString {string} // String: %s
php run-job.php StringTest testString {string,string,string} // String 1: %s String 2: %s String 3: %s
```

## Log Format

Logs are stored in `storage/logs/background_jobs.log.` with the following format:

```
[Y-m-d H:i:s] {"type":"success/error","message":"foo","class":"App\\Libs\\ClassName","method":"methodName","params":["param"]}
```

