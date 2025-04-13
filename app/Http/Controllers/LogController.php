<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class LogController extends Controller
{
    public function index()
    {
        $logFile = storage_path('logs/job-runner-log.log');
        $logs = [];

        if (File::exists($logFile)) {
            $content = File::get($logFile);

            // Split content by date pattern
            $pattern = '/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/';
            $parts = preg_split($pattern, $content, -1, PREG_SPLIT_DELIM_CAPTURE);

            // Process the split parts
            for ($i = 1; $i < count($parts); $i += 2) {
                $date = $parts[$i];
                $message = trim($parts[$i + 1]);
                if (!empty($message)) {
                    $logs[] = [
                        'date' => $date,
                        'message' => $message
                    ];
                }
            }

            // Reverse to show newest first
            $logs = array_reverse($logs);
        }

        return view('logs.index', compact('logs'));
    }

    public function logs()
    {
        $logFile = storage_path('logs/job-runner-log.log');
        $logs = [];

        if (File::exists($logFile)) {
            $content = File::get($logFile);

            // Split content by date pattern
            $pattern = '/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/';
            $parts = preg_split($pattern, $content, -1, PREG_SPLIT_DELIM_CAPTURE);

            // Process the split parts
            for ($i = 1; $i < count($parts); $i += 2) {
                $date = $parts[$i];
                $message = trim($parts[$i + 1]);

                switch ($message) {
                    case str_contains($message, '[Error]'):
                        $logs[] = [
                            'date' => $date,
                            'type' => 'error',
                            'message' => trim(str_replace('[Error]', '', $message)),
                        ];
                        break;
                    case str_contains($message, '[Success]'):
                        $logs[] = [
                            'date' => $date,
                            'type' => 'success',
                            'message' => trim(str_replace('[Success]', '', $message)),
                        ];
                        break;
                    default:
                        $logs[] = [
                            'date' => $date,
                            'type' => 'info',
                            'message' => $message
                        ];
                        break;
                }
            }

            // Reverse to show newest first
            $logs = array_reverse($logs);
        }

        return $logs;
    }

    public function runJob(Request $request)
    {
        $className = $request->input('className');
        $methodName = $request->input('methodName');
        $params = $request->input('params');

        $params_array = !empty($params) ? array_map('trim', explode(',', $params)) : [];

        runBackgroundJob($className, $methodName, $params_array);

        return redirect()->route('logs.index')->with('success', 'Job queued successfully');
    }
}
