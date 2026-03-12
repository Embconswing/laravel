@php
    $hasTracking = $appointment->trackings->isNotEmpty();
    $hasPending = !empty(trim((string) $appointment->reason_pending));
@endphp

@if (!$hasTracking && !$hasPending)
    <div class="text-muted text-center py-4">
        No history available.
    </div>
@else
    {{-- Pending --}}
    @if ($hasPending)
        <div class="alert alert-warning">
            <strong>Pending:</strong>
            {{ $appointment->reason_pending }}
        </div>
    @endif

    {{-- Trackings --}}
    @foreach ($appointment->trackings as $tracking)
        <div class="border rounded p-3 mb-2">

            <div class="d-flex justify-content-between">
                <strong>
                    {{ ucfirst(str_replace('_', ' ', $tracking->action)) }}
                </strong>
                <span class="text-muted">
                    {{ $tracking->created_at->format('d M Y, h:i A') }}
                </span>
            </div>

            <div class="small text-muted mt-1">
                <strong>By:</strong>
                {{ optional($tracking->assignedBy)->name ?? 'System' }}

                @if ($tracking->assigned_to)
                    <span class="mx-1">→</span>
                    <strong>To:</strong>
                    {{ optional($tracking->assignedTo)->name }}
                @endif
            </div>


            @if ($tracking->remarks)
                <div class="mt-2 bg-light p-2 rounded">
                    {{ $tracking->remarks }}
                </div>
            @endif

        </div>
    @endforeach

@endif
