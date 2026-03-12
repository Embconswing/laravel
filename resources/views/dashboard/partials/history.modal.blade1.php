<div class="modal fade"
     id="historyModal-{{ $appointment->id }}"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            {{-- =========================
               MODAL HEADER
               ========================= --}}
            <div class="modal-header">
                <h5 class="modal-title">
                    Application History
                </h5>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>

            {{-- =========================
               MODAL BODY
               ========================= --}}
            <div class="modal-body">

                @if($appointment->trackings->isEmpty())
                    <div class="text-muted text-center py-4">
                        No history available for this application.
                    </div>
                @else
                    <ul class="list-group list-group-flush">

                        @foreach($appointment->trackings as $tracking)
                            <li class="list-group-item">

                                {{-- ACTION + DATE --}}
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>
                                            {{ ucfirst(str_replace('_', ' ', $tracking->action)) }}
                                        </strong>
                                    </div>

                                    <small class="text-muted">
                                        {{ $tracking->created_at->format('d M Y, h:i A') }}
                                    </small>
                                </div>

                                {{-- BY → TO --}}
                                <div class="small text-muted mt-1">
                                    <strong>By:</strong>
                                    {{ optional($tracking->assignedBy)->name ?? 'System' }}

                                    @if($tracking->assigned_to)
                                        <span class="mx-1">→</span>
                                        <strong>To:</strong>
                                        {{ optional($tracking->assignedTo)->name }}
                                    @endif
                                </div>

                                {{-- REMARKS --}}
                                @if($tracking->remarks)
                                    <div class="mt-2 p-2 bg-light rounded small">
                                        <strong>Remarks:</strong><br>
                                        {{ $tracking->remarks }}
                                    </div>
                                @endif

                            </li>
                        @endforeach

                    </ul>
                @endif

            </div>

            {{-- =========================
               MODAL FOOTER
               ========================= --}}
            <div class="modal-footer">
                <button type="button"
                        class="btn btn-secondary btn-sm"
                        data-bs-dismiss="modal">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>
