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
                                            @if($log['type'] == 'error')
                                                <span class="text-red-500">Error:</span>
                                                <span class="text-gray-500" title="{{ $log['message'] }}">({{ strlen($log['message']) > 200 ? substr($log['message'], 0, 200) . '...' : $log['message'] }})</span>
                                            @elseif($log['type'] == 'success')
                                                <span class="text-green-500">Success:</span>
                                                <span class="text-gray-500">({{ $log['message'] }})</span>
                                                <button
                                                    class="bg-blue-500 text-white px-4 py-2 rounded-md float-right log-run-options"
                                                    data-command="{{ $log['command'] }}"
                                                >
                                                    Run Job
                                                </button>
                                            @endif


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

    <div
        id="modal-run-job"
        class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50 hidden"
    >
        <div class="bg-white p-4 rounded-lg relative">
            <button
                id="modal-run-job-close"
                class="absolute top-2 right-2 text-gray-500 hover:text-gray-700"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <h2 class="text-lg font-medium">Run Job</h2>
            <div id="modal-run-job-content">
                <label for="job-command-input">Command</label>
                <input type="text" id="job-command-input" class="w-full p-2 rounded-md bg-gray-100">
            </div>
            <div class="flex justify-end">
                <button id="modal-run-job-run" class="bg-blue-500 text-white px-4 py-2 rounded-md">Run Job</button>
            </div>
        </div>
    </div>

    <script>
        const options = document.querySelectorAll('.log-run-options');
        options.forEach(option => {
            option.addEventListener('click', () => {
               const modal = document.getElementById('modal-run-job');
               modal.classList.remove('hidden');
               document.getElementById('job-command-input').value = option.dataset.command;
            });
        });

        document.getElementById('modal-run-job-close').addEventListener('click', () => {
            const modal = document.getElementById('modal-run-job');
            modal.classList.add('hidden');
            document.getElementById('job-command-input').value = '';
        });

        document.getElementById('modal-run-job-run').addEventListener('click', async () => {
            const command = document.getElementById('job-command-input').value;
            const [base, url, className, methodName, ...params] = command.split(' ');
            console.log(className, methodName, ...params);
            try {
                const response = await fetch('/api/run-job', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        className,
                        methodName,
                        params: params.join(' ')
                    })
                });

                if (response.ok) {
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    alert('Failed to run job');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to run job');
            }
        });
    </script>
</x-app-layout>


