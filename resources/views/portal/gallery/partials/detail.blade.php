<div>
    <p><strong>Name:</strong> {{ isset($record->name) ? $record->name : 'N/A' }}</p>
    <p><strong>URL:</strong> 
        <span class="text-break">{{ isset($record->path) ? $record->path : 'N/A' }}</span>
    </p>
    <p><strong>Image:</strong><br>
        @if(isset($record->path) && $record->path)
        <img src="{{ $record->path }}" alt="Image" class="img-fluid mt-2" style="max-width: 200px; height: auto;">
        @else
        N/A
        @endif
    </p>
</div>