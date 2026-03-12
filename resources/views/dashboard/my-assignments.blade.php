@extends('layouts.app')

@section('content')

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="container py-3">

        {{-- ================= HEADER ================= --}}


        <div class="d-flex align-items-center mb-4 position-relative">

            <div style="width:160px;"></div>

            <div class="text-center flex-grow-1">
                <div class="fw-bold fs-5">
                    Assigned Applications
                </div>
            </div>

            <div class="d-flex gap-2" style="width:160px; justify-content:end;">

                @if (auth()->user()->isSupervisor())
                    <a href="{{ route('appointments.supervisorCases') }}" class="btn btn-sm btn-outline-danger"
                        title="Pending & Cancelled Applications">
                        🚨
                    </a>

                    <a href="{{ url('/admin/overview/application-pools') }}" class="btn btn-sm btn-outline-secondary"
                        title="Application Pools Overview">
                        📊
                    </a>
                @endif





                <a href="{{ route('appointments.bulk-ready-mail') }}" class="btn btn-sm btn-outline-danger"
                    title="Ready to Mail">
                    📦
                </a>

                <a href="{{ route('appointments.collect-documents') }}" class="btn btn-sm btn-outline-success"
                    title="Document Collection">
                    📬
                </a>

                <a href="{{ url('/') }}" class="btn btn-sm btn-primary" title="Dashboard">🏠</a>

                <a href="{{ route('applications.byStatus') }}" class="btn btn-sm btn-outline-primary"
                    title="Applications by Status">📋</a>
                <a href="{{ route('appointments.delete') }}" class="btn btn-sm btn-outline-danger"
                    title="Delete Appointments">🗑</a>

            </div>
        </div>

        {{-- ================= TABLE ================= --}}
        @if ($appointments->isEmpty())
            <div class="alert alert-info">
                No applications are currently assigned to you.
            </div>
        @else
            {{-- ================= APPLICATION SUMMARY ================= --}}
            @if ($total > 0)
                <div class="row mb-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">

                            <h6 class="fw-bold mb-3">
                                Application Summary ({{ $total }} Total)
                            </h6>

                            <div style="width:100%; height:160px;">
                                <canvas id="applicationsChart"></canvas>
                            </div>

                        </div>
                    </div>
                </div>
            @endif
            <div class="card shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="table-layout: fixed;">
                        <thead class="table-light">
                            <tr>
                                <th>Appointment No</th>
                                <th style="width:140px;">Reference No</th>
                                <th>Applicant</th>
                                <th>Email</th>
                                <th>Service</th>
                                <th style="width:110px;">Date</th>
                                <th style="min-width:220px;">Update Status</th>
                                <th style="min-width:220px;">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($appointments as $appointment)
                                <tr>
                                    <td>{{ $appointment->appointment_no }}</td>
                                    <td class="text-truncate" style="max-width:140px;"
                                        title="{{ $appointment->ReferenceNr }}">
                                        {{ $appointment->ReferenceNr }}
                                    </td>
                                    <td>{{ $appointment->applicant_name }}</td>
                                    <td>{{ $appointment->email }}</td>
                                    <td>{{ $appointment->service }}</td>
                                    <td>{{ optional($appointment->appointment_date)->format('d M Y') ?? '-' }}</td>

                                    {{-- STATUS UPDATE --}}
                                    <td>
                                        @if (auth()->user()->isNormal())
                                            {{-- PROCESS COMPLETED (OPEN MODAL) --}}
                                            <button type="button"
                                                onclick="openProcessCompletedModal({{ $appointment->id }})"
                                                class="btn btn-sm btn-outline-success w-100 mb-1">
                                                Process Completed
                                            </button>

                                            {{-- KEPT ON HOLD (OPEN MODAL) --}}
                                            <button type="button" onclick="openUnderProcessModal({{ $appointment->id }})"
                                                class="btn btn-sm btn-outline-warning w-100">
                                                Kept on Hold
                                            </button>

                                            {{-- CHANGE REF NO --}}
                                            {{-- CHANGE REF NO --}}
                                            <button type="button"
                                                onclick="openChangeRefModal({{ $appointment->id }}, '{{ $appointment->ReferenceNr }}')"
                                                class="btn btn-sm btn-outline-dark w-100 mt-1">
                                                Change Ref. No
                                            </button>

                                            {{-- CHANGE EMAIL (ONLY BEFORE PROCESS COMPLETED) --}}
                                            @if ($appointment->status !== \App\Models\Appointment::STATUS_PROCESS_COMPLETED)
                                                <button type="button"
                                                    onclick="openChangeEmailModal({{ $appointment->id }}, '{{ $appointment->email }}')"
                                                    class="btn btn-sm btn-outline-primary w-100 mt-1">
                                                    Change Email
                                                </button>
                                            @endif
                                        @endif



                                    </td>

                                    {{-- ACTION --}}

                                    <td>

                                        {{-- NORMAL USER → SLA PROGRESS BAR --}}
                                        @if (auth()->user()->isNormal())
                                            @php
                                                $latestTracking = $appointment->trackings()->latest()->first();

                                                $isReturned = in_array(optional($latestTracking)->action, [
                                                    'returned_by_supervisor',
                                                    'returned_to_supervisor',
                                                    'returned_to_pool',
                                                ]);

                                                $barClass = $isReturned ? 'bg-danger' : $appointment->status_bar_class;
                                            @endphp

                                            <div class="progress mb-2" style="height: 22px;">
                                                <div class="progress-bar {{ $barClass }}" role="progressbar"
                                                    style="width: {{ $appointment->status_percent }}%;">

                                                    {{ \App\Models\Appointment::STATUS_LABELS[$appointment->status] ??
                                                        ucfirst(str_replace('_', ' ', $appointment->status)) }}

                                                    ({{ $appointment->status_days }}d)
                                                </div>
                                            </div>



                                            {{-- SUPERVISOR → BADGE --}}
                                        @else
                                            @php
                                                $color =
                                                    \App\Models\Appointment::STATUS_COLORS[$appointment->status] ??
                                                    'secondary';
                                            @endphp

                                            <span class="badge bg-{{ $color }} d-block mb-1">
                                                {{ \App\Models\Appointment::STATUS_LABELS[$appointment->status] ??
                                                    ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                            </span>
                                        @endif


                                        {{-- RETURN ACTIONS --}}
                                        @includeWhen(view()->exists('dashboard.partials.return-actions'),
                                            'dashboard.partials.return-actions',
                                            ['appointment' => $appointment]
                                        )

                                        {{-- VIEW HISTORY --}}
                                        <div class="d-flex justify-content-center mt-1">
                                            <button type="button" class="btn btn-sm btn-outline-secondary w-100"
                                                onclick="openGlobalHistory({{ $appointment->id }})">
                                                View History
                                            </button>
                                        </div>

                                    </td>







                                </tr>
                                {{-- ================= HISTORY MODAL ================= --}}
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- ================= PAGINATION ================= --}}
            @if ($appointments->hasPages())
                <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap">

                    <div class="small text-muted mb-2">
                        Showing {{ $appointments->firstItem() }}
                        to {{ $appointments->lastItem() }}
                        of {{ $appointments->total() }} applications
                    </div>

                    <div>
                        {{ $appointments->links() }}
                    </div>

                </div>
            @endif
        @endif
    </div>
    {{-- ================= UNDER PROCESS MODAL ================= --}}
    <div id="underProcessModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg w-full max-w-md p-6">

            <h5 class="fw-bold mb-3">
                Reason for Keeping on Hold
            </h5>

            <form method="POST" id="underProcessForm">
                @csrf
                @method('PATCH')

                <input type="hidden" name="status" value="{{ \App\Models\Appointment::STATUS_PROCESS_IN_PROGRESS }}">

                <textarea name="remarks" class="form-control mb-3" rows="4" required
                    placeholder="Enter reason for keeping this application on hold..."></textarea>

                <div class="d-flex justify-content-end gap-2">
                    <button type="button" onclick="closeUnderProcessModal()" class="btn btn-secondary">
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-warning">
                        Save
                    </button>
                </div>
            </form>

        </div>
    </div>

    {{-- ================= PROCESS COMPLETED MODAL ================= --}}
    <div id="processCompletedModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg w-full max-w-md p-6">

            <h5 class="fw-bold mb-3 text-success">
                Confirm Process Completion
            </h5>

            <p class="mb-4">
                Are you sure you want to mark this application as
                <strong>Process Completed</strong>?
            </p>

            <form method="POST" id="processCompletedForm">
                @csrf
                @method('PATCH')

                <input type="hidden" name="status" value="{{ \App\Models\Appointment::STATUS_PROCESS_COMPLETED }}">

                <div class="d-flex justify-content-end gap-2">
                    <button type="button" onclick="closeProcessCompletedModal()" class="btn btn-secondary">
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-success">
                        Confirm
                    </button>
                </div>
            </form>

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

{{-- ================= CHANGE EMAIL MODAL ================= --}}
<div id="changeEmailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-6">

        <h5 class="fw-bold mb-3">
            Change Email Address
        </h5>

        <form method="POST" id="changeEmailForm">
            @csrf
            @method('PATCH')

            <div class="mb-3">
                <label class="form-label">Current Email</label>
                <input type="text" id="currentEmail" class="form-control" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">New Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Remark</label>
                <textarea name="remark"
                    class="form-control"
                    rows="3"
                    required
                    placeholder="Reason for changing email address..."></textarea>
            </div>

            <div class="d-flex justify-content-end gap-2">

                <button type="button"
                    onclick="closeChangeEmailModal()"
                    class="btn btn-secondary">
                    Cancel
                </button>

                <button type="submit"
                    class="btn btn-primary">
                    Update Email
                </button>

            </div>

        </form>

    </div>
</div>


    {{-- ================= CHANGE REF NO MODAL ================= --}}
    <div id="changeRefModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg w-full max-w-md p-6">

            <h5 class="fw-bold mb-3">
                Change Reference Number
            </h5>

            <form method="POST" id="changeRefForm">
                @csrf
                @method('PATCH')

                <div class="mb-3">
                    <label class="form-label">Current Reference No</label>
                    <input type="text" id="currentRefNo" class="form-control" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">New Reference No</label>
                    <input type="text" name="reference_no" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Remark</label>
                    <textarea name="remark" class="form-control" rows="3" required
                        placeholder="Reason for changing reference number..."></textarea>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="button" onclick="closeChangeRefModal()" class="btn btn-secondary">
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-dark">
                        Update
                    </button>
                </div>
            </form>

        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            /* ================= CHART ================= */

            const canvas = document.getElementById('applicationsChart');

            if (canvas) {

                const ctx = canvas.getContext('2d');
                Chart.register(ChartDataLabels);

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Pending', 'Under Process', 'Cancelled'],
                        datasets: [{
                            data: [
                                {{ $pending ?? 0 }},
                                {{ $underProcess ?? 0 }},
                                {{ $cancelled ?? 0 }}
                            ],
                            backgroundColor: [
                                '#198754',
                                '#0d6efd',
                                '#6c757d'
                            ],
                            borderRadius: 8,
                            barThickness: 28
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            datalabels: {
                                anchor: 'end',
                                align: 'right',
                                color: '#000',
                                font: {
                                    weight: 'bold',
                                    size: 14
                                },
                                formatter: function(value) {
                                    return value;
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            },
                            y: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });

            }

            /* ================= REPRINT CONFIRM ================= */

            @if (session('reprint_id'))
                let appointmentId = "{{ session('reprint_id') }}";

                if (confirm("Reference number updated successfully.\n\nDo you want to reprint the label?")) {
                    window.open("{{ route('appointments.printLabel', ':id') }}"
                        .replace(':id', appointmentId), "_blank");
                }
            @endif

        });


        /* ================= OTHER FUNCTIONS ================= */

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

        function openUnderProcessModal(id) {
            const form = document.getElementById('underProcessForm');
            form.action = `/appointments/${id}/status`;
            document.getElementById('underProcessModal').classList.remove('hidden');
            document.getElementById('underProcessModal').classList.add('flex');
        }

        function closeUnderProcessModal() {
            document.getElementById('underProcessModal').classList.add('hidden');
            document.getElementById('underProcessModal').classList.remove('flex');
        }

        function openProcessCompletedModal(id) {
            const form = document.getElementById('processCompletedForm');
            form.action = `/appointments/${id}/status`;
            document.getElementById('processCompletedModal').classList.remove('hidden');
            document.getElementById('processCompletedModal').classList.add('flex');
        }

        function closeProcessCompletedModal() {
            document.getElementById('processCompletedModal').classList.add('hidden');
            document.getElementById('processCompletedModal').classList.remove('flex');
        }

        function openChangeRefModal(id, currentRef) {
            const form = document.getElementById('changeRefForm');
            form.action = `/appointments/${id}/change-ref`;

            document.getElementById('currentRefNo').value = currentRef;

            document.getElementById('changeRefModal').classList.remove('hidden');
            document.getElementById('changeRefModal').classList.add('flex');
        }

        function closeChangeRefModal() {
            document.getElementById('changeRefModal').classList.add('hidden');
            document.getElementById('changeRefModal').classList.remove('flex');
        }


function openChangeEmailModal(id, currentEmail) {

    const form = document.getElementById('changeEmailForm');

    form.action = `/appointments/${id}/change-email`;

    document.getElementById('currentEmail').value = currentEmail;

    document.getElementById('changeEmailModal').classList.remove('hidden');
    document.getElementById('changeEmailModal').classList.add('flex');
}

function closeChangeEmailModal() {

    document.getElementById('changeEmailModal').classList.add('hidden');
    document.getElementById('changeEmailModal').classList.remove('flex');
}
    </script>

@endsection
