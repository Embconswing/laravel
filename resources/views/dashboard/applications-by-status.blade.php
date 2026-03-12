@extends('layouts.app')

@php
    use App\Models\Appointment;
@endphp

@section('content')
    <div class="container py-3">

        {{-- HEADER --}}
        <div class="d-flex align-items-center mb-4 position-relative">
            <div style="width:160px;"></div>

            <div class="text-center flex-grow-1">

                <div class="fw-bold fs-5">
                    Application by Status
                </div>
            </div>

            <div class="d-flex gap-2" style="width:160px; justify-content:end;">
                <a href="{{ url('/dashboard') }}" class="btn btn-sm btn-primary" title="Assigned Pool">🏠</a>
            </div>
        </div>

        {{-- SEARCH & FILTERS --}}
        <form method="GET" action="{{ route('applications.byStatus') }}" class="row g-2 mb-4">

            <div class="col-md-3">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                    placeholder="Search name, email, ref no">
            </div>

            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">— All Application Statuses —</option>

                    @foreach (Appointment::STATUS_LABELS as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>
                            {{ $label }}
                        </option>
                    @endforeach

                </select>
            </div>

            <div class="col-md-2">
                <select name="delivery_status" class="form-select">
                    <option value="">— Delivery Status —</option>

                    @foreach (Appointment::DELIVERY_LABELS as $value => $label)
                        <option value="{{ $value }}" @selected(request('delivery_status') === $value)>
                            {{ $label }}
                        </option>
                    @endforeach

                </select>
            </div>

            <div class="col-md-2">
                <button class="btn btn-primary w-100">Search</button>
            </div>

            <div class="col-md-2">
                <a href="{{ route('applications.byStatus') }}" class="btn btn-outline-secondary w-100">
                    Reset
                </a>
            </div>

        </form>

        {{-- TABLE --}}
        @if ($appointments->isEmpty())
            <div class="alert alert-info">No applications found.</div>
        @else
            <div class="card shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="table-layout: fixed;">
                        <thead class="table-light">
                            <tr>
                                <th>Appointment No</th>
                                <th style="width:170px;">Reference No</th>
                                <th>Applicant</th>
                                <th>Email</th>
                                <th>Service</th>
                                <th style="width:95px;">Date</th>
                                <th>Status</th>
                                <th style="min-width:320px;">Delivery & Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($appointments as $appointment)
                                @php
                                    $appBadge = Appointment::STATUS_COLORS[$appointment->status] ?? 'secondary';
                                    $delBadge =
                                        Appointment::DELIVERY_COLORS[$appointment->delivery_status] ?? 'secondary';

                                    $statusColors = [
                                        Appointment::STATUS_ASSIGNED => 'primary',
                                        Appointment::STATUS_ACCEPTED => 'success',
                                        Appointment::STATUS_PROCESS_IN_PROGRESS => 'info',
                                        Appointment::STATUS_PROCESS_COMPLETED => 'success',
                                        // Appointment::STATUS_COMPLETED => 'success',
                                        Appointment::STATUS_PENDING => 'secondary',
                                        Appointment::STATUS_RETURNED_TO_SUPERVISOR => 'warning',
                                    ];

                                    $deliveryColors = [
                                        Appointment::STATUS_MAIL => 'secondary',
                                        Appointment::STATUS_EMAILSENT => 'info',
                                        Appointment::STATUS_COLLECTED => 'success',
                                        Appointment::STATUS_DISPATCHED => 'primary',
                                        Appointment::STATUS_RETURNED => 'danger',
                                    ];

                                @endphp

                                <tr>
                                    <td>{{ $appointment->appointment_no }}</td>
                                    <td class="text-truncate" title="{{ $appointment->ReferenceNr }}">
                                        {{ $appointment->ReferenceNr }}
                                    </td>
                                    <td>{{ $appointment->applicant_name }}</td>
                                    <td>{{ $appointment->email }}</td>
                                    <td>{{ $appointment->service }}</td>
                                    <td>{{ optional($appointment->appointment_date)->format('d M Y') ?? '-' }}</td>

                                    <td>
                                        <span class="badge bg-{{ $appBadge }}">
                                            {{ Appointment::STATUS_LABELS[$appointment->status] ?? ucfirst($appointment->status) }}
                                        </span>

                                        @if ($appointment->delivery_status)
                                            <div class="mt-1">
                                                <span class="badge bg-{{ $delBadge }}">
                                                    {{ Appointment::DELIVERY_LABELS[$appointment->delivery_status] ?? ucfirst($appointment->delivery_status) }}
                                                </span>
                                            </div>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="d-flex flex-column gap-2">

                                            {{-- Row 1: Select --}}
                                            <form method="POST"
                                                action="{{ route('appointments.deliveryStatus', $appointment) }}"
                                                class="mb-0">
                                                @csrf
                                                @method('PATCH')

                                                <select name="delivery_status" class="form-select form-select-sm"
                                                    data-appointment-no="{{ $appointment->appointment_no }}"
                                                    data-reference="{{ $appointment->ReferenceNr }}"
                                                    data-applicant="{{ $appointment->applicant_name }}"
                                                    data-email="{{ $appointment->email }}"
                                                    data-passport="{{ $appointment->passportno }}"
                                                    onchange="handleDeliveryChange(this)" @disabled(!$appointment->canHaveDeliveryStatus())
                                                    required>

                                                    <option value="">—Delivery Status—</option>

                                                    @foreach ([
            Appointment::STATUS_MAIL => 'Ready to Mail',
            Appointment::STATUS_EMAILSENT => 'Email Sent',
            Appointment::STATUS_COLLECTED => 'Collected',
            Appointment::STATUS_DISPATCHED => 'Dispatched',
            Appointment::STATUS_RETURNED => 'Post Returned',
        ] as $value => $label)
                                                        @php
                                                            $disableEmailSent =
                                                                $value === Appointment::STATUS_EMAILSENT &&
                                                                !in_array($appointment->delivery_status, [
                                                                    Appointment::STATUS_MAIL,
                                                                    Appointment::STATUS_EMAILSENT,
                                                                ]);
                                                        @endphp

                                                        <option value="{{ $value }}" @selected($appointment->delivery_status === $value)
                                                            @disabled($disableEmailSent)>
                                                            {{ $label }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                {{-- Row 2: Buttons --}}
                                                <div class="d-flex gap-2 mt-2">
                                                    <button type="submit" class="btn btn-sm btn-outline-success flex-fill"
                                                        @disabled(!$appointment->canHaveDeliveryStatus())>
                                                        Update
                                                    </button>

                                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                                        onclick="openGlobalHistory({{ $appointment->id }})">
                                                        View History
                                                    </button>

                                                </div>

                                            </form>

                                        </div>
                                    </td>

                                </tr>

                                {{-- HISTORY MODAL --}}
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-3">
                {{ $appointments->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

    {{-- READY TO MAIL MODAL --}}
    <div id="readyToMailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg w-full max-w-md p-4">
            <h5 class="mb-3 fw-bold">Ready to Mail Details</h5>

            <form id="readyToMailForm" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" name="delivery_status" value="{{ Appointment::STATUS_MAIL }}">

                <div class="mb-2">
                    <label class="form-label">Collection Date</label>
                    <input type="date" name="collection_date" class="form-control" required>
                </div>

                <div class="mb-2">
                    <label class="form-label">Collection Time</label>
                    <input type="time" name="collection_time" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Passport No</label>
                    <input type="text" name="passport_no" class="form-control" required>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary"
                        onclick="closeReadyToMailModal()">Cancel</button>
                    <button type="submit" class="btn btn-success">Confirm</button>
                </div>
            </form>
        </div>
    </div>
    {{-- EMAIL SENT CONFIRMATION MODAL --}}
    <div class="modal fade" id="emailSentConfirmModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Send Email Notification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <p class="text-muted small">
                        Please verify the applicant details before sending the email notification.
                    </p>

                    <div class="border rounded p-3 bg-light">

                        <div class="mb-2">
                            <strong>Appointment No:</strong>
                            <span id="emailAppNo"></span>
                        </div>

                        <div class="mb-2">
                            <strong>Reference No:</strong>
                            <span id="emailReference"></span>
                        </div>

                        <div class="mb-2">
                            <strong>Applicant:</strong>
                            <span id="emailApplicant"></span>
                        </div>

                        <div class="mb-2">
                            <strong>Email Address:</strong>
                            <input type="email" class="form-control mt-1" id="emailAddressInput">

                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="confirmEmailEdit"
                                    style="display:none;">
                                <label class="form-check-label small text-warning" for="confirmEmailEdit"
                                    id="confirmEmailEditLabel" style="display:none;">
                                    I confirm the edited email address is correct
                                </label>
                            </div>
                        </div>

                    </div>

                    <div class="mt-3 text-warning small">
                        The system will send an automatic notification email.
                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="button" class="btn btn-success" id="confirmEmailSentBtn">
                        Send Email & Update Status
                    </button>

                </div>

            </div>
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
    <div class="modal fade" id="dispatchModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Dispatch Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="dispatchForm" method="POST">

                    @csrf
                    @method('PATCH')

                    <input type="hidden" name="delivery_status" value="{{ Appointment::STATUS_DISPATCHED }}">

                    <div class="modal-body">

                        <div class="mb-2">
                            <strong>Appointment No:</strong>
                            <span id="dispatchAppNo"></span>
                        </div>

                        <div class="mb-2">
                            <strong>Reference No:</strong>
                            <span id="dispatchRef"></span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks" class="form-control" placeholder="Dispatch remarks (courier, tracking etc.)" required></textarea>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>

                        <button type="submit" class="btn btn-primary">
                            Confirm Dispatch
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    {{-- SCRIPTS --}}
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


        let pendingEmailForm = null;
        let pendingEmailSelect = null;
        let pendingEmailValue = null;
        let originalEmail = "";


        /* ================================
           DELIVERY STATUS HANDLER
        ================================ */

        function handleDeliveryChange(select) {

            /* READY TO MAIL MODAL */
            if (select.value === '{{ Appointment::STATUS_MAIL }}') {

                const appointmentNo = select.dataset.appointmentNo;
                const passportNo = select.dataset.passport;

                const modalForm = document.getElementById('readyToMailForm');
                const passportInput = modalForm.querySelector('input[name="passport_no"]');

                modalForm.action = `/appointments/${appointmentNo}/delivery-status`;
                passportInput.value = passportNo ?? '';

                openReadyToMailModal();

                select.value = '';
                return;
            }


            /* EMAIL SENT CONFIRMATION MODAL */
            if (select.value === '{{ Appointment::STATUS_EMAILSENT }}') {

                pendingEmailForm = select.closest("form");
                pendingEmailSelect = select;
                pendingEmailValue = select.value;

                document.getElementById('emailAppNo').innerText = select.dataset.appointmentNo;
                document.getElementById('emailReference').innerText = select.dataset.reference ?? '-';
                document.getElementById('emailApplicant').innerText = select.dataset.applicant ?? '-';
                //document.getElementById('emailAddress').innerText = select.dataset.email ?? '-';
                const emailInput = document.getElementById('emailAddressInput');

                emailInput.value = select.dataset.email ?? '';
                originalEmail = select.dataset.email ?? '';

                document.getElementById('confirmEmailEdit').checked = false;
                document.getElementById('confirmEmailEdit').style.display = 'none';
                document.getElementById('confirmEmailEditLabel').style.display = 'none';




                const modal = new bootstrap.Modal(
                    document.getElementById('emailSentConfirmModal')
                );

                modal.show();

                select.value = '';

                return;
            }


            /* DISPATCH MODAL */
            if (select.value === '{{ Appointment::STATUS_DISPATCHED }}') {

                const appointmentNo = select.dataset.appointmentNo;
                const reference = select.dataset.reference;

                document.getElementById('dispatchAppNo').innerText = appointmentNo;
                document.getElementById('dispatchRef').innerText = reference ?? '-';

                const form = document.getElementById('dispatchForm');
                form.action = `/appointments/${appointmentNo}/delivery-status`;

                const modal = new bootstrap.Modal(document.getElementById('dispatchModal'));
                modal.show();

                select.value = '';
                return;
            }
        }



        /* ================================
           READY TO MAIL MODAL
        ================================ */

        function openReadyToMailModal() {

            const modal = document.getElementById('readyToMailModal');

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            const today = new Date().toISOString().split('T')[0];

            const dateInput = document.querySelector(
                '#readyToMailForm input[name="collection_date"]'
            );

            dateInput.setAttribute('min', today);
        }

        function closeReadyToMailModal() {

            const modal = document.getElementById('readyToMailModal');

            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function openEmailConfirmModal(data) {

            currentAppointmentId = data.id;

            document.getElementById('emailAppNo').innerText = data.appointment_no;
            document.getElementById('emailReference').innerText = data.reference_no;
            document.getElementById('emailApplicant').innerText = data.applicant_name;

            const emailInput = document.getElementById('emailAddressInput');

            emailInput.value = data.email;
            originalEmail = data.email;

            document.getElementById('confirmEmailEdit').checked = false;
            document.getElementById('confirmEmailEdit').style.display = 'none';
            document.getElementById('confirmEmailEditLabel').style.display = 'none';

            const modal = new bootstrap.Modal(document.getElementById('emailSentConfirmModal'));
            modal.show();
        }
        document.getElementById('emailAddressInput').addEventListener('input', function() {

            const newEmail = this.value;

            if (newEmail !== originalEmail) {

                document.getElementById('confirmEmailEdit').style.display = 'block';
                document.getElementById('confirmEmailEditLabel').style.display = 'block';

            } else {

                document.getElementById('confirmEmailEdit').style.display = 'none';
                document.getElementById('confirmEmailEditLabel').style.display = 'none';
                document.getElementById('confirmEmailEdit').checked = false;
            }
        });
        /* ================================
           EMAIL CONFIRMATION BUTTON
        ================================ */

        document.getElementById('confirmEmailSentBtn').addEventListener('click', function () {

    const email = document.getElementById('emailAddressInput').value;
    const checkbox = document.getElementById('confirmEmailEdit');

    if (email !== originalEmail && !checkbox.checked) {
        alert("Please confirm the edited email address.");
        return;
    }

    // update hidden email input in form
    let hiddenEmail = pendingEmailForm.querySelector("input[name='email']");

    if (!hiddenEmail) {
        hiddenEmail = document.createElement("input");
        hiddenEmail.type = "hidden";
        hiddenEmail.name = "email";
        pendingEmailForm.appendChild(hiddenEmail);
    }

    hiddenEmail.value = email;

    // set select back to email sent
    pendingEmailSelect.value = pendingEmailValue;

    // close modal
    bootstrap.Modal.getInstance(
        document.getElementById('emailSentConfirmModal')
    ).hide();

    // submit form normally
    pendingEmailForm.submit();
});

        /* ================================
           AJAX DELIVERY STATUS UPDATE
        ================================ */

        function submitDeliveryForm(form) {

            const formData = new FormData(form);

            // Disable submit button to prevent double click
            const submitBtn = form.querySelector("button[type='submit']");
            if (submitBtn) {
                submitBtn.disabled = true;
            }

            fetch(form.action, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json"
                    },
                    body: formData
                })

                .then(async (response) => {

                    let data = null;

                    try {
                        data = await response.json();
                    } catch (e) {
                        throw new Error("Invalid server response");
                    }

                    if (!response.ok || !data.success) {
                        throw new Error(data.message || "Operation failed");
                    }

                    return data;
                })

                .then(data => {

                    const row = form.closest("tr");
                    const statusCell = row.children[6];

                    let wrapper = statusCell.querySelector(".mt-1");

                    if (!wrapper) {
                        wrapper = document.createElement("div");
                        wrapper.className = "mt-1";
                        statusCell.appendChild(wrapper);
                    }

                    let deliveryBadge = wrapper.querySelector(".badge");

                    if (!deliveryBadge) {
                        deliveryBadge = document.createElement("span");
                        wrapper.appendChild(deliveryBadge);
                    }

                    deliveryBadge.innerText = data.delivery_status_label;
                    deliveryBadge.className = "badge bg-" + data.delivery_status_color;

                    const toast = new bootstrap.Toast(
                        document.getElementById('successToast')
                    );
                    toast.show();

                })

                .catch((error) => {

                    console.error(error);

                    const toast = new bootstrap.Toast(
                        document.getElementById('errorToast')
                    );
                    toast.show();

                })

                .finally(() => {

                    // Re-enable button after request finishes
                    if (submitBtn) {
                        submitBtn.disabled = false;
                    }

                });
        }
    </script>



@endsection
