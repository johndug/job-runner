<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class LogController extends Controller
{
    public function index()
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $logs = $this->logs();

        return view('logs.index', compact('logs'));
    }

    public function getLogs()
    {
        if (! Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $logs = $this->logs();

        return view('components.logs', compact('logs'));
    }

    public function clearLogs()
    {
        if (! Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $logFile = storage_path('logs/job-runner-log.log');
        File::put($logFile, '');

        return response()->json(['success' => true]);
    }

    public function runJob(Request $request)
    {
        if (! Auth::check()) {
            return abort(401);
        }

        $command = $request->input('command');
        $parts = explode(' ', $command);

        $className = $parts[2] ?? null;
        $methodName = $parts[3] ?? null;
        $params = $parts[4] ?? '';

        if (! $className || ! $methodName) {
            return response()->json(['error' => 'Invalid command format'], 400);
        }

        $params_array = ! empty($params) ? array_map('trim', explode(',', $params)) : [];

        $result = runBackgroundJob($className, $methodName, $params_array);

        return response()->json(['success' => $result]);
    }

    private function logs()
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

                $data = json_decode(trim($parts[$i + 1]), true);

                $logs[] = array_merge($data, ['date' => $date]);
            }

            // Reverse to show newest first
            $logs = array_reverse($logs);
        }

        return $logs;
    }
}
