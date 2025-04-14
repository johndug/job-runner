<div id="logs-container">
    @foreach($logs as $log)
        <x-log-entry :log="$log" />
    @endforeach
</div>
