<div class="card shadow-sm">


    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>Appointment No</th>
                <th>Reference No</th>
                <th>Applicant</th>
                <th>Email</th>
                <th>Service</th>
                <th>Status</th>
                <th style="width:220px;">Action</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($appointments as $appointment)
                <tr>
                    <td>{{ $appointment->appointment_no }}</td>
                    <td>{{ $appointment->ReferenceNr }}</td>
                    <td>{{ $appointment->applicant_name }}</td>
                    <td>{{ $appointment->email }}</td>
                    <td>{{ $appointment->service }}</td>

                    {{-- STATUS BADGE --}}
                    <td>
                        @php
                            $badgeMap = [
                                'accepted' => 'success',
                                'assigned' => 'primary',
                                'pending' => 'warning',
                                'cancelled' => 'danger',
                            ];
                            $badge = $badgeMap[$appointment->status] ?? 'secondary';
                        @endphp

                        <span class="badge bg-{{ $badge }}">
                            {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                        </span>
                    </td>

                    {{-- ACTION COLUMN --}}
                    <td class="space-y-1">

                        {{-- SUPERVISOR STATUS EDIT --}}
                        @if (auth()->user()->isSupervisor())
                            <form method="POST" action="{{ route('appointments.updateStatus', $appointment->id) }}">
                                @csrf
                                @method('PATCH')

                                <select name="status" class="form-select form-select-sm"
                                    data-current="{{ $appointment->status }}" onchange="confirmStatusChange(this)">

                                    <option value="" disabled>
                                        Change status…
                                    </option>

                                    <option value="{{ \App\Models\Appointment::STATUS_ACCEPTED }}"
                                        @selected($appointment->status === \App\Models\Appointment::STATUS_ACCEPTED)>
                                        Accepted
                                    </option>

                                    <option value="{{ \App\Models\Appointment::STATUS_PENDING }}"
                                        @selected($appointment->status === \App\Models\Appointment::STATUS_PENDING)>
                                        Pending
                                    </option>

                                    <option value="{{ \App\Models\Appointment::STATUS_CANCELLED }}"
                                        @selected($appointment->status === \App\Models\Appointment::STATUS_CANCELLED)>
                                        Cancelled
                                    </option>

                                </select>


                            </form>
                        @endif

                        {{-- VIEW HISTORY --}}
                        <button type="button" class="btn btn-sm btn-outline-secondary"
                            onclick="openGlobalHistory({{ $appointment->id }})">
                            View History
                        </button>


                    </td>
                </tr>

                {{-- =========================
                    | HISTORY MODAL
                    |========================= --}}
                <div id="historyModal-{{ $appointment->id }}"
                    class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">

                    <div class="bg-white rounded-lg w-full max-w-2xl p-4 max-h-[80vh] overflow-y-auto">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">
                                Application History
                            </h5>

                            <button type="button" onclick="closeHistoryModal({{ $appointment->id }})"
                                class="btn btn-sm btn-outline-secondary">
                                ✕
                            </button>
                        </div>

                        @php
                            $hasTracking = $appointment->trackings->isNotEmpty();
                            $hasPending = !empty(trim((string) $appointment->reason_pending));
                        @endphp

                        @if (!$hasTracking && !$hasPending)
                            <div class="text-muted text-center py-4">
                                No history available.
                            </div>
                        @else
                            <div class="space-y-3">

                                {{-- ================= PENDING REASON ================= --}}
                                @if ($hasPending)
                                    <div class="border rounded p-3 text-sm bg-warning-subtle">

                                        <div class="d-flex justify-content-between">
                                            <strong class="text-warning">
                                                Pending
                                            </strong>
                                            <span class="text-muted">
                                                {{ optional($appointment->updated_at)->format('d M Y, h:i A') }}
                                            </span>
                                        </div>

                                        <div class="mt-2">
                                            {{ $appointment->reason_pending }}
                                        </div>

                                    </div>
                                @endif

                                {{-- ================= TRACKING HISTORY ================= --}}
                                

                            </div>
                        @endif


                    </div>
                </div>
            @endforeach
        </tbody>

    </table>
</div>
</div>
<div class="modal fade" id="globalHistoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Application History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" id="historyModalBody">
                <div class="text-center text-muted py-4">
                    Loading history...
                </div>
            </div>

        </div>
    </div>
</div>


{{-- =========================
| HISTORY MODAL SCRIPTS
|========================= --}}
<script>

    function openGlobalHistory(id) {

    const modal = new bootstrap.Modal(
        document.getElementById('globalHistoryModal')
    );

    const body = document.getElementById('historyModalBody');

    body.innerHTML = `
        <div class="text-center text-muted py-4">
            Loading history...
        </div>
    `;

    fetch(`/appointments/${id}/history`)
        .then(response => response.text())
        .then(html => {
            body.innerHTML = html;
        });

    modal.show();
}


    function confirmStatusChange(select) {

        const originalValue = select.dataset.current;
        const newValue = select.value;
        const newLabel = select.options[select.selectedIndex].text;

        if (!newValue) return;

        // 🚫 BLOCK: Accepted → Pending
        if (originalValue === '{{ \App\Models\Appointment::STATUS_ACCEPTED }}' &&
            newValue === '{{ \App\Models\Appointment::STATUS_PENDING }}') {

            alert("Status cannot be changed from Accepted to Pending.");

            select.value = originalValue; // revert back
            return;
        }

        const confirmed = confirm(
            "Are you sure you want to change the application status to '" +
            newLabel + "'?"
        );

        if (!confirmed) {
            select.value = originalValue;
            return;
        }

        select.form.submit();
    }
</script>
