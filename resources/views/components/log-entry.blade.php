@props(['log'])

<div class="p-4 bg-gray-50 rounded-lg">
    <div class="flex items-start">
        <div class="flex-1">
            <div class="text-xs text-gray-500 mb-1">
                [{{ $log['date'] }}]
            </div>
            <p class="text-sm text-gray-600">
                @if($log['type'] == 'error')
                    <span class="text-red-500">Error:</span>
                @elseif($log['type'] == 'success')
                    <span class="text-green-500">Success:</span>
                @endif

                <span class="text-gray-500">({{ $log['message'] }})</span>
                @if(isset($log['maxRetries']))
                    <span class="text-gray-500">({{ $log['maxRetries'] }} retries)</span>
                @endif
                @if(isset($log['retryDelay']))
                    <span class="text-gray-500">({{ $log['retryDelay'] }} seconds delay)</span>
                @endif
                @if(isset($log['attempt']))
                    <span class="text-gray-500">({{ $log['attempt'] }} attempt)</span>
                @endif
                @if(isset($log['command']))
                <button
                    class="bg-blue-500 text-white px-4 py-2 rounded-md float-right log-run-options"
                    data-command="{{ $log['command'] }}"
                >
                    Run Again
                </button>
                @endif
            </p>
        </div>
    </div>
</div>
