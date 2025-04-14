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

                $type = 'info';

                if (str_contains($message, '[Error]')) {
                    $type = 'error';
                } elseif (str_contains($message, '[Success]')) {
                    $type = 'success';
                    $command = trim(last(explode('[Command]', $message)));
                    // Remove anything after '[' if it exists
                    $command = preg_replace('/\[.*$/', '', $command);
                }

                $logs[] = [
                    'date' => $date,
                    'type' => $type,
                    'message' => $message,
                    'command' => $command ?? '',
                ];
            }

            // Reverse to show newest first
            $logs = array_reverse($logs);
        }

        return view('logs.index', compact('logs'));
    }

    public function runJob(Request $request)
    {
        $className = $request->input('className');
        $methodName = $request->input('methodName');
        $params = $request->input('params');

        $params_array = !empty($params) ? array_map('trim', explode(',', $params)) : [];

        $result = runBackgroundJob($className, $methodName, $params_array);

        return response()->json(['success' => $result]);
    }
}
