@extends('layouts.app')

@section('content')


<style>
.status-cell {
    min-width: 160px;
    max-width: 220px;
}

.status-cell .progress {
    height: 6px;
}
</style>
    @php
        use App\Models\Appointment;

        // Synced from the model (single source of truth)
        $statusLabels = Appointment::STATUS_LABELS;
        $statusColors = Appointment::STATUS_COLORS;
        $deliveryLabels = Appointment::DELIVERY_LABELS;
        $deliveryColors = Appointment::DELIVERY_COLORS;
    @endphp

    <div class="position-relative mb-2" style="min-height: 20px;">

        <!-- CENTERED TITLE -->
        <div class="fw-bold fs-5 text-center m-0 top-0">
            Application Pools - All Users
        </div>

        <!-- RIGHT BUTTON -->
        <div class="position-absolute top-0 end-0 translate-middle-y">
            <a href="{{ url('/dashboard') }}" class="btn btn-sm btn-outline-primary py-1 px-2">
                ← Dashboard
            </a>
        </div>

        {{-- =========================
           FILTERS
           ========================= --}}
        <div class="card mb-3">
            <div class="card-body py-2 d-flex flex-wrap gap-2 align-items-center">

                <strong>Filter by Status:</strong>

                {{-- ✅ Status dropdown fully synced to model labels --}}
                <select id="statusFilter" class="form-select form-select-sm w-auto">
                    <option value="">All</option>
                    @foreach ($statusLabels as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>

                {{-- ✅ Delivery dropdown fully synced to model labels --}}
                <select id="deliveryFilter" class="form-select form-select-sm w-auto">
                    <option value="">All Delivery</option>
                    @foreach ($deliveryLabels as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>

                {{-- 🔍 SEARCH BOX --}}
                <input type="text" id="searchInput" class="form-control form-control-sm w-auto"
                    placeholder="Search application...">
                    {{-- DATE FILTER --}}
<label class="small">From:</label>
<input type="date" id="dateFrom" class="form-control form-control-sm w-auto">

<label class="small">To:</label>
<input type="date" id="dateTo" class="form-control form-control-sm w-auto">
            </div>
        </div>
    </div>

    {{-- =========================
       USERS + APPLICATION POOLS
       ========================= --}}
    @forelse ($users as $user)
        @php
            $statusCounts = $user->assignedAppointments->groupBy('status')->map(fn($items) => $items->count());
        @endphp

        <div class="card shadow-sm mb-4 user-card">

            {{-- USER HEADER --}}
            <div class="card-header bg-light">

                {{-- USER NAME --}}
                <div class="fw-bold mb-2">
                    {{ $user->name }}
                </div>

                {{-- STATUS COUNTERS (synced to model) --}}
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-{{ $statusColors[Appointment::STATUS_ASSIGNED] ?? 'secondary' }}">
                        {{ $statusLabels[Appointment::STATUS_ASSIGNED] ?? 'Assigned' }}:
                        {{ $statusCounts[Appointment::STATUS_ASSIGNED] ?? 0 }}
                    </span>

                    <span class="badge bg-{{ $statusColors[Appointment::STATUS_ACCEPTED] ?? 'secondary' }}">
                        {{ $statusLabels[Appointment::STATUS_ACCEPTED] ?? 'Accepted' }}:
                        {{ $statusCounts[Appointment::STATUS_ACCEPTED] ?? 0 }}
                    </span>

                    <span class="badge bg-{{ $statusColors[Appointment::STATUS_PENDING] ?? 'secondary' }} text-dark">
                        {{ $statusLabels[Appointment::STATUS_PENDING] ?? 'Pending' }}:
                        {{ $statusCounts[Appointment::STATUS_PENDING] ?? 0 }}
                    </span>

                    {{-- ✅ Under Process counter --}}
                    <span class="badge bg-{{ $statusColors[Appointment::STATUS_PROCESS_IN_PROGRESS] ?? 'secondary' }}">
                        {{ $statusLabels[Appointment::STATUS_PROCESS_IN_PROGRESS] ?? 'Under Process' }}:
                        {{ $statusCounts[Appointment::STATUS_PROCESS_IN_PROGRESS] ?? 0 }}
                    </span>

                    <span class="badge bg-{{ $statusColors[Appointment::STATUS_PROCESS_COMPLETED] ?? 'secondary' }}">
                        {{ $statusLabels[Appointment::STATUS_PROCESS_COMPLETED] ?? 'Process Completed' }}:
                        {{ $statusCounts[Appointment::STATUS_PROCESS_COMPLETED] ?? 0 }}
                    </span>

                    <span class="badge bg-{{ $statusColors[Appointment::STATUS_RETURNED_TO_SUPERVISOR] ?? 'secondary' }}">
                        {{ $statusLabels[Appointment::STATUS_RETURNED_TO_SUPERVISOR] ?? 'Returned' }}:
                        {{ $statusCounts[Appointment::STATUS_RETURNED_TO_SUPERVISOR] ?? 0 }}
                    </span>
                </div>

            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th>Appointment No</th> 
                            <th>Date Assigned</th>
                            <th>Applicant</th>
                            <th>Service</th>
                           
                            <th>Status</th>
                            <th>Delivery</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($user->assignedAppointments as $appointment)
                            @php
                                $statusBadge = $statusColors[$appointment->status] ?? 'secondary';
                                $deliveryBadge = $deliveryColors[$appointment->delivery_status] ?? 'secondary';

                                $statusText = $statusLabels[$appointment->status]
                                    ?? ucfirst(str_replace('_', ' ', $appointment->status));

                                $deliveryText = $appointment->delivery_status
                                    ? ($deliveryLabels[$appointment->delivery_status]
                                        ?? ucfirst(str_replace('_', ' ', $appointment->delivery_status)))
                                    : null;
                            @endphp

                            <tr class="appointment-row"
    data-status="{{ $appointment->status }}"
    data-delivery="{{ $appointment->delivery_status ?? '' }}"
    data-date="{{ $appointment->created_at->format('Y-m-d') }}">

                                <td>{{ $appointment->appointment_no }}</td>
                                 <td>
    {{ $appointment->created_at->format('d M Y') }}
</td>
                                <td>{{ $appointment->applicant_name }}</td>
                                <td>{{ $appointment->service }}</td>
                               


                               {{-- STATUS (progress bar + days in status) --}}
<td class="status-cell">
    <div class="d-flex justify-content-between small">
        <span class="badge bg-{{ $statusBadge }}">
            {{ $statusText }}
        </span>
       
    </div>

    <div class="progress mt-1" style="height:6px;">
        <div class="progress-bar {{ $appointment->status_bar_class }}"
             style="width: {{ $appointment->status_percent }}%;">
        </div>
    </div>
</td>

                                {{-- DELIVERY (synced label) --}}
                                <td>
                                    @if ($appointment->delivery_status)
                                        <span class="badge bg-{{ $deliveryBadge }} d-block text-center">
                                            {{ $deliveryText }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">
                                    No applications assigned
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>

    @empty
        <div class="alert alert-info">
            No users with assigned applications found.
        </div>
    @endforelse

    {{-- =========================
       FILTER SCRIPT (NO RELOAD)
       ========================= --}}
    <script>
        document.getElementById('statusFilter').addEventListener('change', filterRows);
document.getElementById('deliveryFilter').addEventListener('change', filterRows);
document.getElementById('searchInput').addEventListener('keyup', filterRows);
document.getElementById('dateFrom').addEventListener('change', filterRows);
document.getElementById('dateTo').addEventListener('change', filterRows);

function filterRows() {

    const status = document.getElementById('statusFilter').value.toLowerCase();
    const delivery = document.getElementById('deliveryFilter').value.toLowerCase();
    const search = document.getElementById('searchInput').value.toLowerCase();

    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;

    document.querySelectorAll('.appointment-row').forEach(row => {

        const rowStatus = (row.dataset.status || '').toLowerCase();
        const rowDelivery = (row.dataset.delivery || '').toLowerCase();
        const rowDate = row.dataset.date;
        const rowText = row.innerText.toLowerCase();

        let show = true;

        if (status && rowStatus !== status) show = false;
        if (delivery && rowDelivery !== delivery) show = false;
        if (search && !rowText.includes(search)) show = false;

        if (dateFrom && rowDate < dateFrom) show = false;
        if (dateTo && rowDate > dateTo) show = false;

        row.style.display = show ? '' : 'none';
    });
}
    </script>

@endsection