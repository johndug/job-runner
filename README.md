# Job Runner

A Laravel-based application for running and monitoring background jobs with a user-friendly interface.

## Features

- Run PHP class methods as background jobs
- Real-time job execution monitoring
- Detailed logging of job execution
- Web interface for viewing logs and running jobs
- Error handling and stack trace logging
- Job execution history with timestamps

## Prerequisites

- PHP 8.4
- Composer
- Laravel 12

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

## Project Structure

- `app/Helpers/JobRunner.php` - Core job execution functionality
- `app/Http/Controllers/LogController.php` - Handles log viewing and job execution
- `resources/views/logs/index.blade.php` - Web interface for logs
- `storage/logs/job-runner-log.log` - Job execution logs

## Usage

### Running Jobs

1. Access the web interface at `http://your-domain/logs`
2. View job execution logs
3. Click "Run Job" on any previous job to re-execute it
4. Monitor job execution status and results

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
        return true;
    }
}
```

2. The job will be automatically available in the web interface

### Manual Testing

1. Start the development server:
```bash
php artisan serve
```

2. Access the web interface at `http://localhost:8000/logs`

3. Test job execution:
   - Click "Run Job" on any existing log entry
   - Verify the job executes and new log entry appears
   - Check for proper error handling if job fails

4. Test log viewing:
   - Verify logs are displayed correctly
   - Check that long messages are truncated with hover tooltip
   - Verify date formatting and log types (success/error)

## Log Format

Logs are stored in `storage/logs/job-runner-log.log` with the following format:

```
[YYYY-MM-DD HH:MM:SS] [Success/Error] ClassName::methodName [Params] param1,param2 [Command] php run-job.php ClassName methodName param1 param2
```

## Troubleshooting

1. If jobs fail to execute:
   - Check PHP permissions
   - Verify class and method names are correct
   - Check log file permissions

