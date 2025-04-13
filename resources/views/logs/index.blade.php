<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Job Logs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <h3 class="text-lg font-medium">Recent Job Logs</h3>
                    </div>

                    <div class="space-y-4">
                        @forelse($logs as $log)
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-start">
                                    <div class="flex-1">
                                        <div class="text-xs text-gray-500 mb-1">
                                            [{{ $log['date'] }}]
                                        </div>
                                        <p class="text-sm text-gray-600">
                                            @if(str_contains($log['message'], '[Error]'))
                                                <span class="text-red-500">Error:</span>
                                            @elseif(str_contains($log['message'], '[Success]'))
                                                <span class="text-green-500">Success:</span>
                                            @endif
                                            <code>{{ $log['message'] }}</code>
                                            <button class="bg-blue-500 text-white px-4 py-2 rounded-md float-right"  onclick="runJob('{{{ $log['message'] }}}')">
                                                Run Job
                                            </button>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <p class="text-gray-500">No logs available</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function runJob(log) {
            console.log(log);
        }
    </script>
</x-app-layout>


