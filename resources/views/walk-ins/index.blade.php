@extends('layouts.app')

@section('content')
    <div class="container py-3">

        <div class="d-flex align-items-center justify-content-between mb-4">

            <div class="text-center flex-grow-1">
                <div class="fw-bold fs-5">
                    {{ isset($appointment) ? 'Edit Appointment' : 'Walk in Applications' }}
                </div>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ url('/') }}" class="btn btn-sm btn-outline-secondary">
                    🏠 Dashboard
                </a>

                <a href="{{ url('/p') }}" class="btn btn-sm btn-outline-primary">
                    🔙 Back to Display Page
                </a>
            </div>
        </div>

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

        <div class="card shadow-sm">
            <div class="card-body">

                @if (isset($appointment))
                    <form method="POST" action="{{ route('appointments.updateService', $appointment->id) }}">
                        @method('PATCH')
                    @else
                        <form method="POST" action="{{ route('walkins.store') }}">
                @endif
                @csrf

                {{-- PERSONAL DETAILS --}}
                <div class="row g-3">

                    {{-- Applicant Name --}}
                    <div class="col-md-6">
                        <label class="form-label">Applicant Name *</label>
                        <input type="text" name="applicant_name" class="form-control"
                            value="{{ old('applicant_name', $appointment->applicant_name ?? '') }}" required>
                    </div>

                    {{-- Passport Holder Name (only for New Born passport service) --}}
                    <div class="col-md-6" id="passport-holder-wrapper" style="display:none;">
                        <label class="form-label">Passport Holder Name *</label>
                        <input type="text" name="passport_holder_name" id="passport_holder_name" class="form-control"
                            value="{{ old('passport_holder_name') }}" placeholder="Enter passport holder name">
                    </div>

                    {{-- Gender --}}
                    <div class="col-md-3">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select">
                            <option value="">— Select —</option>
                            <option value="Male"
                                {{ old('gender', $appointment->gender ?? '') == 'Male' ? 'selected' : '' }}>
                                Male
                            </option>
                            <option value="Female"
                                {{ old('gender', $appointment->gender ?? '') == 'Female' ? 'selected' : '' }}>
                                Female
                            </option>
                            <option value="Other"
                                {{ old('gender', $appointment->gender ?? '') == 'Other' ? 'selected' : '' }}>
                                Other
                            </option>
                        </select>
                    </div>

                    {{-- Date of Birth --}}
                    <div class="col-md-3">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control"
                            value="{{ old('date_of_birth', $appointment->date_of_birth ?? '') }}">
                    </div>

                    {{-- Nationality --}}
                    <div class="col-md-4">
                        <label class="form-label">Nationality *</label>
                        <select name="nationality" id="nationality" class="form-select" required>
                            <option value="">— Select Nationality —</option>

                            @foreach ($nationalities as $nation)
                                <option value="{{ $nation->Nationid }}"
                                    {{ old('nationality', $appointment->nationality ?? '') == $nation->Nationid ? 'selected' : '' }}>
                                    {{ $nation->Nationname }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Mobile --}}
                    <div class="col-md-4">
                        <label class="form-label">Mobile Number *</label>
                        <input type="tel" id="mobile_number" name="mobile_number" class="form-control"
                            value="{{ old('mobile_number', $appointment->mobile_number ?? '') }}" required>
                    </div>

                    {{-- Email --}}
                    <div class="col-md-4">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control"
                            value="{{ old('email', $appointment->email ?? '') }}" required>
                    </div>

                </div>

                {{-- APPLICATION DETAILS --}}
                <div class="row g-3 mt-2">

                    <div class="col-md-6">
                        <label class="form-label">Passport No</label>
                        <input type="text" name="passportno" class="form-control"
                            value="{{ old('passportno', $appointment->passportno ?? '') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Service *</label>

                        @php
                            $selectedService = old('service', $appointment->service ?? '');
                        @endphp

                        <select name="service" id="service" class="form-select" required>
                            <option value="">— Select Service —</option>

                            <option value="Visa Services" {{ $selectedService == 'Visa Services' ? 'selected' : '' }}>
                                Visa
                            </option>

                            <optgroup label="OCI Services">
                                <option value="Fresh OCI" {{ $selectedService == 'Fresh OCI' ? 'selected' : '' }}>
                                    Fresh OCI
                                </option>
                                <option value="Miscellaneous OCI"
                                    {{ $selectedService == 'Miscellaneous OCI' ? 'selected' : '' }}>
                                    Miscellaneous OCI
                                </option>
                                <option value="PIO to OCI" {{ $selectedService == 'PIO to OCI' ? 'selected' : '' }}>
                                    PIO to OCI
                                </option>
                            </optgroup>

                            <optgroup label="Passport Services">
                                <option value="Passport - New / Issue / Reissue / Lost"
                                    {{ $selectedService == 'Passport - New / Issue / Reissue / Lost' ? 'selected' : '' }}>
                                    New / Issue / Reissue / Lost
                                </option>
                                <option value="Passport-Birth Registration & Fresh passport to New Born"
                                    {{ $selectedService == 'Passport-Birth Registration & Fresh passport to New Born' ? 'selected' : '' }}>
                                    Passport-Birth Registration & Fresh passport to New Born
                                </option>
                                Passport Services:
                                <option value="Emergency Certificate"
                                    {{ $selectedService == 'Emergency Certificate' ? 'selected' : '' }}>
                                    Emergency Certificate
                                </option>
                                <option value="Passport PCC" {{ $selectedService == 'Passport PCC' ? 'selected' : '' }}>
                                    Police Clearance Certificate
                                </option>
                            </optgroup>

                            {{-- ================= MISCELLANEOUS CONSULAR ================= --}}
                            <optgroup label="Miscellaneous Consular Services">

                                <option value="Misc - Attestation / Legalisation of Documents"
                                    {{ old('service', $selectedService ?? '') == 'Misc - Attestation / Legalisation of Documents' ? 'selected' : '' }}>
                                    Attestation / Legalisation of Documents
                                </option>

                                <option value="Misc - Consular Surrender Certificate"
                                    {{ old('service', $selectedService ?? '') == 'Misc - Consular Surrender Certificate' ? 'selected' : '' }}>
                                    Surrender Certificate
                                </option>

                                <option value="Misc - NRI Certificate"
                                    {{ old('service', $selectedService ?? '') == 'Misc - NRI Certificate' ? 'selected' : '' }}>
                                    NRI Certificate
                                </option>

                                <option value="Misc - Consular Police Clearance Certificate"
                                    {{ old('service', $selectedService ?? '') == 'Misc - Consular Police Clearance Certificate' ? 'selected' : '' }}>
                                    Police Clearance Certificate
                                </option>

                                <option value="Misc - Life Certificate"
                                    {{ old('service', $selectedService ?? '') == 'Misc - Life Certificate' ? 'selected' : '' }}>
                                    Life Certificate
                                </option>

                                <option value="Misc - Birth Certificate as per Indian Passport"
                                    {{ old('service', $selectedService ?? '') == 'Misc - Birth Certificate as per Indian Passport' ? 'selected' : '' }}>
                                    Birth Certificate as per Indian Passport
                                </option>

                                <option value="Misc - Name Change Certificate"
                                    {{ old('service', $selectedService ?? '') == 'Misc - Name Change Certificate' ? 'selected' : '' }}>
                                    Name Change Certificate as per Indian Passport
                                </option>

                                <option value="Misc - NOC for Naming of New Born Child"
                                    {{ old('service', $selectedService ?? '') == 'Misc - NOC for Naming of New Born Child' ? 'selected' : '' }}>
                                    No Objection Certificate for Naming of New Born Child
                                </option>

                            </optgroup>

                        </select>
                    </div>

                    <div class="col-md-6" id="reference-wrapper">
                        <label class="form-label">Reference Number *</label>
                        <input type="text" name="ReferenceNr" id="reference" class="form-control"
                            value="{{ old('ReferenceNr') }}" placeholder="Enter reference number">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="2">{{ old('address', $appointment->address ?? '') }}</textarea>
                    </div>

                </div>

                <div class="mt-4 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-4">
                        {{ isset($appointment) ? 'Update Appointment' : 'Create Walk-in Application' }}
                    </button>
                </div>

                </form>

            </div>
        </div>
    </div>

<!-- New Born Service Warning Modal -->
<div class="modal fade" id="newBornWarningModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-warning">

            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">⚠️ Important Warning</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <p class="fw-bold mb-2">
                    You selected:
                </p>

                <div class="alert alert-warning">
                    Passport - Birth Registration & Fresh Passport to New Born
                </div>

                <p class="mb-2">Please confirm the following before continuing:</p>

                <ul>
                    <li>The applicant is a <strong>new born child</strong></li>
                    <li>Child's passport application is ready</li>
                    <li>The passport holder name (Child's Name) must be entered</li>
                </ul>

                <p class="text-danger fw-semibold">
                    Do you want to continue?
                </p>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" id="cancelNewBorn">
                    Cancel
                </button>

                <button class="btn btn-warning" id="confirmNewBorn">
                    Yes Continue
                </button>
            </div>

        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const serviceSelect = document.getElementById('service');
    const referenceWrapper = document.getElementById('reference-wrapper');

    const passportHolderWrapper = document.getElementById('passport-holder-wrapper');
    const passportHolderInput = document.getElementById('passport_holder_name');

    const NEW_BORN_SERVICE = 'Passport-Birth Registration & Fresh passport to New Born';

    const modal = new bootstrap.Modal(document.getElementById('newBornWarningModal'));

    const confirmBtn = document.getElementById('confirmNewBorn');
    const cancelBtn = document.getElementById('cancelNewBorn');

    let previousValue = serviceSelect.value;

    function toggleFields() {

    const selectedValue = serviceSelect.value || '';

    // Hide reference number for all Misc services
    if (selectedValue.startsWith('Misc -')) {
        referenceWrapper.style.display = 'none';
    } else {
        referenceWrapper.style.display = 'block';
    }

    // Show extra field only for New Born service
    if (selectedValue === NEW_BORN_SERVICE) {

        passportHolderWrapper.style.display = 'block';
        passportHolderInput?.setAttribute('required', 'required');

        // add red border
        passportHolderInput.classList.add('border-danger');

    } else {

        passportHolderWrapper.style.display = 'none';
        passportHolderInput?.removeAttribute('required');

        // remove red border
        passportHolderInput.classList.remove('border-danger');

        if (passportHolderInput) passportHolderInput.value = '';
    }
}

    serviceSelect.addEventListener('change', function () {

        const selectedValue = serviceSelect.value;

        if (selectedValue === NEW_BORN_SERVICE) {

            modal.show();

            confirmBtn.onclick = function () {
                modal.hide();
                previousValue = NEW_BORN_SERVICE;
                toggleFields();
            };

            cancelBtn.onclick = function () {
                modal.hide();
                serviceSelect.value = previousValue;
            };

        } else {

            previousValue = selectedValue;
            toggleFields();
        }

    });

    toggleFields();
});
</script>
@endpush
