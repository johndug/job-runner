<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <h3 class="text-lg font-medium">Recent Job Logs</h3>
                    </div>
                    <div class="mb-4 flex justify-end gap-2">
                        <button
                            id="clear-logs"
                            class="bg-red-500 text-white px-4 py-2 rounded-md"
                            onclick="clearLogs()"
                        >
                            Clear Logs
                        </button>
                        <button
                            id="run-command"
                            class="bg-blue-500 text-white px-4 py-2 rounded-md log-run-options"
                            data-command="php run-job.php Class::Method 'param1,param2,param3'"
                        >
                            Run Command
                        </button>
                    </div>

                    <div class="space-y-4" id="logs-container">

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
                onclick="closeModal()"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <h2 class="text-lg font-medium">Execute Command</h2>
            <div id="modal-run-job-content">
                <label for="job-command-input">Command</label>
                <input type="text" id="job-command-input" class="w-full p-2 rounded-md bg-gray-100">
            </div>
            <div class="flex justify-end mt-4">
                <button id="modal-run-job-run" class="bg-blue-500 text-white px-4 py-2 rounded-md">Execute</button>
            </div>
        </div>
    </div>

    <script>
        function attachEventListeners() {
            // Remove existing event listeners first
            document.querySelectorAll('.log-run-options').forEach(button => {
                const newButton = button.cloneNode(true);
                button.parentNode.replaceChild(newButton, button);
            });

            // Attach new event listeners
            document.querySelectorAll('.log-run-options').forEach(button => {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    const modal = document.querySelector('#modal-run-job');
                    modal.classList.remove('hidden');
                    document.querySelector('#job-command-input').value = button.dataset.command;
                });
            });
        }

        function fetchLogs() {
            const logsContainer = document.querySelector('#logs-container');
            fetch('/logs', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/html',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.text())
            .then(html => {
                logsContainer.innerHTML = html;
                // Reattach event listeners after loading new content
                attachEventListeners();
            })
            .catch(error => {
                console.error('Error fetching logs:', error);
                logsContainer.innerHTML = `
                    <div class="text-center py-4">
                        <p class="text-red-500">Error loading logs</p>
                    </div>
                `;
            });
        }

        const closeModal = () => {
            const modal = document.querySelector('#modal-run-job');
            modal.classList.add('hidden');
            document.querySelector('#job-command-input').value = '';
        };

        document.querySelector('#modal-run-job-run').addEventListener('click', async () => {
            document.querySelector('#modal-run-job-run').disabled = true;
            const command = document.querySelector('#job-command-input').value;

            closeModal();
            try {
                const response = await fetch('/api/run-job', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        command
                    })
                });

                if (response.ok) {
                    setTimeout(() => {
                        fetchLogs();
                    }, 1000);
                } else {
                    alert('Failed to run job');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to run job');
            } finally {
                document.querySelector('#modal-run-job-run').disabled = false;
            }
        });

        const clearLogs = () => {
            if (confirm('Are you sure you want to clear the logs?')) {
                fetch('/logs/clear', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                fetchLogs();
            }
        }

        fetchLogs();

        setInterval(fetchLogs, 5000);
    </script>
</x-app-layout>


