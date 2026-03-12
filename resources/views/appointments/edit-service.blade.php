@extends('layouts.app')

@section('content')
    <div class="container py-3">

        {{-- Header --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="fw-bold fs-5">
                Edit Appointment Service
            </div>

            <div>
                <a href="{{ route('appointments.index') }}" class="btn btn-sm btn-outline-secondary">
                    🔙 Back to Appointments
                </a>
            </div>
        </div>

        {{-- Success / Error Messages --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Card --}}
        <div class="card shadow-sm">
            <div class="card-body">

                <form method="POST" action="{{ route('appointments.updateService', $appointment->id) }}">
                    @csrf
                    @method('PATCH')

                    {{-- ✅ Hidden fields used by controller --}}
                    <input type="hidden" name="split_service" id="split_service" value="0">
                    <input type="hidden" name="child_name" id="child_name" value="">
                    <input type="hidden" name="child_passportno" id="child_passportno" value="">
                    <div class="row g-3">

                        {{-- Applicant Name --}}
                        <div class="col-md-6">
                            <label class="form-label">Applicant Name</label>
                            <input type="text" name="applicant_name" class="form-control"
                                value="{{ old('applicant_name', $appointment->applicant_name) }}" required>
                        </div>

                        {{-- Reference Number (Read-only) --}}
                        <div class="col-md-6">
                            <div class="col-md-6">
                                <label class="form-label">Reference Number</label>

                                <div class="input-group">

                                    <input type="text" name="ReferenceNr" id="referenceField" class="form-control"
                                        value="{{ old('ReferenceNr', $appointment->ReferenceNr) }}" readonly>

                                    <button type="button" class="btn btn-outline-secondary" id="unlockReferenceBtn">
                                        ✏️ Edit
                                    </button>

                                </div>

                                <small class="text-muted">
                                    Click Edit to modify the reference number.
                                </small>
                            </div>
                        </div>

                        {{-- Passport Number (Editable) --}}
                        <div class="col-md-6">
                            <label class="form-label">Passport Number</label>
                            <input type="text" name="passportno" class="form-control"
                                value="{{ old('passportno', $appointment->passportno) }}">
                        </div>

                        {{-- Service Dropdown --}}
                        <div class="col-md-6">
                            <label class="form-label">Service *</label>

                            <select name="service" id="serviceSelect" class="form-select" required>
                                <option value="">— Select Service —</option>

                                {{-- VISA --}}
                                <option value="Visa Services"
                                    {{ old('service', $appointment->service) == 'Visa Services' ? 'selected' : '' }}>
                                    Visa
                                </option>

                                {{-- OCI --}}
                                <optgroup label="OCI Services">
                                    <option value="Fresh OCI"
                                        {{ old('service', $appointment->service) == 'Fresh OCI' ? 'selected' : '' }}>
                                        Fresh OCI
                                    </option>

                                    <option value="Miscellaneous OCI"
                                        {{ old('service', $appointment->service) == 'Miscellaneous OCI' ? 'selected' : '' }}>
                                        Miscellaneous OCI
                                    </option>
                                </optgroup>

                                {{-- PASSPORT --}}
                                <optgroup label="Passport Services">
                                    <option value="Passport - New / Issue / Reissue / Lost"
                                        {{ old('service', $appointment->service) == 'Passport - New / Issue / Reissue / Lost' ? 'selected' : '' }}>
                                        New / Issue / Reissue / Lost
                                    </option>

                                    <option value="Passport - Birth Registration & Fresh Passport to New Born"
                                        {{ old('service', $appointment->service) == 'Passport - Birth Registration & Fresh Passport to New Born' ? 'selected' : '' }}>
                                        Birth Registration & Fresh Passport to New Born
                                    </option>

                                    <option value="Passport - PCC"
                                        {{ old('service', $appointment->service) == 'Passport - PCC' ? 'selected' : '' }}>
                                        Police Clearance Certificate
                                    </option>

                                    <option value="Emergency Certificate"
                                        {{ old('service', $appointment->service) == 'Emergency Certificate' ? 'selected' : '' }}>
                                        Emergency Certificate
                                    </option>
                                </optgroup>

                                {{-- MISC --}}
                                <optgroup label="Miscellaneous Consular Services">
                                    <option value="Misc - Attestation / Legalisation of Documents"
                                        {{ old('service', $appointment->service) == 'Misc - Attestation / Legalisation of Documents' ? 'selected' : '' }}>
                                        Attestation / Legalisation of Documents
                                    </option>

                                    <option value="Misc - Consular Surrender Certificate"
                                        {{ old('service', $appointment->service) == 'Misc - Consular Surrender Certificate' ? 'selected' : '' }}>
                                        Surrender Certificate
                                    </option>

                                    <option value="Misc - NRI Certificate"
                                        {{ old('service', $appointment->service) == 'Misc - NRI Certificate' ? 'selected' : '' }}>
                                        NRI Certificate
                                    </option>

                                    <option value="Misc - Consular Police Clearance Certificate"
                                        {{ old('service', $appointment->service) == 'Misc - Consular Police Clearance Certificate' ? 'selected' : '' }}>
                                        Police Clearance Certificate
                                    </option>

                                    <option value="Misc - Life Certificate"
                                        {{ old('service', $appointment->service) == 'Misc - Life Certificate' ? 'selected' : '' }}>
                                        Life Certificate
                                    </option>

                                    <option value="Misc - Birth Certificate as per Indian Passport"
                                        {{ old('service', $appointment->service) == 'Misc - Birth Certificate as per Indian Passport' ? 'selected' : '' }}>
                                        Birth Certificate as per Indian Passport
                                    </option>

                                    <option value="Misc - Name Change Certificate"
                                        {{ old('service', $appointment->service) == 'Misc - Name Change Certificate' ? 'selected' : '' }}>
                                        Name Change Certificate as per Indian Passport
                                    </option>

                                    <option value="Misc - NOC for Naming of New Born Child"
                                        {{ old('service', $appointment->service) == 'Misc - NOC for Naming of New Born Child' ? 'selected' : '' }}>
                                        No Objection Certificate for Naming of New Born Child
                                    </option>
                                </optgroup>

                            </select>
                        </div>

                    </div>

                    <div class="mt-4 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4">
                            Update Service
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>
    <div class="modal fade" id="splitServiceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-sm">

                {{-- Soft enterprise header --}}
                <div class="modal-header border-top border-4 border-danger justify-content-center position-relative">
                    <h5 class="modal-title fw-bold text-danger text-center mb-0">
                        Split Combined Service
                    </h5>

                    <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <p class="text-center mb-3">
                        The selected service contains:
                    </p>

                    <div class="border rounded p-3 bg-light mb-4">
                        <ul class="mb-0">
                            <li><strong>Birth Registration</strong></li>
                            <li><strong>Fresh Passport for New Born</strong></li>
                        </ul>
                    </div>

                    {{-- Radio choice --}}
                    <div class="border rounded p-3 mb-3">
                        <div class="fw-semibold mb-2">Choose an option:</div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="split_choice" id="choiceKeepOne"
                                value="0">
                            <label class="form-check-label" for="choiceKeepOne">
                                Keep as one service (no split)
                            </label>
                        </div>

                        <div class="form-check mt-2">
                            <input class="form-check-input" type="radio" name="split_choice" id="choiceSplit"
                                value="1">
                            <label class="form-check-label" for="choiceSplit">
                                Split into two services
                            </label>
                        </div>
                    </div>

                    {{-- Child name section (only when split selected) --}}
                    <div class="border rounded p-3 d-none" id="childNameBox">
                        <label class="form-label fw-semibold">
                            Child / Passport Holder Name <span class="text-danger">*</span>
                        </label>

                        <input type="text" class="form-control" id="childNameField" placeholder="Enter child name"
                            disabled>

                        <div class="invalid-feedback">
                            Child name is required to split.
                        </div>
                    </div>

                    <div class="mt-3 small text-muted text-center">
                        Your selection will apply to this update.
                    </div>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                        id="cancelSplitModalBtn">
                        Cancel
                    </button>

                    <button type="button" class="btn btn-danger" id="confirmSplitBtn" disabled>
                        Confirm
                    </button>
                </div>

            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const newbornService = 'Passport - Birth Registration & Fresh Passport to New Born';

            const serviceSelect = document.getElementById('serviceSelect');
            const splitHidden = document.getElementById('split_service');
            const childNameHidden = document.getElementById('child_name');

            const modalEl = document.getElementById('splitServiceModal');
            const modal = new bootstrap.Modal(modalEl, {
                backdrop: 'static',
                keyboard: false
            });

            const choiceKeepOne = document.getElementById('choiceKeepOne');
            const choiceSplit = document.getElementById('choiceSplit');

            const childNameBox = document.getElementById('childNameBox');
            const childNameField = document.getElementById('childNameField');

            const confirmBtn = document.getElementById('confirmSplitBtn');

            let decisionMade = false;

            function resetHiddenValues() {
                splitHidden.value = 0;
                childNameHidden.value = '';
            }

            function resetModalUI() {
                choiceKeepOne.checked = false;
                choiceSplit.checked = false;
                confirmBtn.disabled = true;

                childNameBox.classList.add('d-none');
                childNameField.value = '';
                childNameField.disabled = true;
                childNameField.classList.remove('is-invalid');
            }

            function handleServiceChange() {
                resetHiddenValues();
                decisionMade = false;

                if (serviceSelect.value === newbornService) {
                    resetModalUI();
                    modal.show();
                }
            }

            // Trigger on service change
            serviceSelect.addEventListener('change', handleServiceChange);

            // Radio change logic
            function updateUIFromChoice() {
                confirmBtn.disabled = false;

                if (choiceSplit.checked) {
                    childNameBox.classList.remove('d-none');
                    childNameField.disabled = false;
                    setTimeout(() => childNameField.focus(), 150);
                } else {
                    childNameBox.classList.add('d-none');
                    childNameField.disabled = true;
                    childNameField.value = '';
                    childNameField.classList.remove('is-invalid');
                }
            }

            choiceKeepOne.addEventListener('change', updateUIFromChoice);
            choiceSplit.addEventListener('change', updateUIFromChoice);

            confirmBtn.addEventListener('click', function() {

                // KEEP AS ONE
                if (choiceKeepOne.checked) {
                    splitHidden.value = 0;
                    childNameHidden.value = '';
                    decisionMade = true;
                    modal.hide();
                    return;
                }

                // SPLIT
                if (choiceSplit.checked) {

                    const name = (childNameField.value || '').trim();

                    if (!name) {
                        childNameField.classList.add('is-invalid');
                        childNameField.focus();
                        return;
                    }

                    childNameField.classList.remove('is-invalid');

                    splitHidden.value = 1;
                    childNameHidden.value = name;

                    decisionMade = true;
                    modal.hide();
                }
            });

            modalEl.addEventListener('hidden.bs.modal', function() {
                if (!decisionMade && serviceSelect.value === newbornService) {
                    serviceSelect.value = '';
                    resetHiddenValues();
                }
            });

        });


    const referenceField = document.getElementById('referenceField');
const unlockBtn = document.getElementById('unlockReferenceBtn');

unlockBtn.addEventListener('click', function () {

    if (referenceField.hasAttribute('readonly')) {

        referenceField.removeAttribute('readonly');
        referenceField.focus();

        unlockBtn.innerHTML = '🔒 Lock';
        unlockBtn.classList.remove('btn-outline-secondary');
        unlockBtn.classList.add('btn-outline-danger');

    } else {

        referenceField.setAttribute('readonly', true);

        unlockBtn.innerHTML = '✏️ Edit';
        unlockBtn.classList.remove('btn-outline-danger');
        unlockBtn.classList.add('btn-outline-secondary');
    }

});
    </script>
@endsection
