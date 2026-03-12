@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">Delete Appointments</h5>
                    </div>

                    <div class="card-body">

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.appointments.delete') }}">
                            @csrf

                            {{-- DELETE METHOD --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold">Delete Method</label>

                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="delete_type" id="deleteByNo"
                                        value="appointment_no" checked>
                                    <label class="form-check-label" for="deleteByNo">
                                        Delete by Appointment Number
                                    </label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="delete_type" id="deleteByDate"
                                        value="date_range">
                                    <label class="form-check-label" for="deleteByDate">
                                        Delete by Date Range
                                    </label>
                                </div>
                            </div>

                            <hr>

                            {{-- APPOINTMENT NUMBER --}}
                            <div id="appointmentNoSection" class="mb-4">
                                <label class="form-label">Appointment Number</label>
                                <input type="text" name="appointment_no" class="form-control"
                                    placeholder="Enter Appointment No">
                            </div>

                            {{-- DATE RANGE --}}
                            <div id="dateRangeSection" class="mb-4" style="display:none;">

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">From Date</label>
                                        <input type="date" name="from_date" class="form-control">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">To Date</label>
                                        <input type="date" name="to_date" class="form-control">
                                    </div>
                                </div>

                                {{-- REQUIRED STATUS --}}
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Application Status *</label>
                                        <select name="status" class="form-select">
                                            <option value="">— Select Status —</option>

                                            @foreach ([\App\Models\Appointment::STATUS_PROCESS_COMPLETED, \App\Models\Appointment::STATUS_ACCEPTED, \App\Models\Appointment::STATUS_CANCELLED] as $status)
                                                <option value="{{ $status }}">
                                                    {{ ucfirst(str_replace('_', ' ', strtolower($status))) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- REQUIRED DELIVERY STATUS --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Delivery Status *</label>
                                        <select name="delivery_status" class="form-select">
                                            <option value="">— Select Delivery Status —</option>

                                            @foreach ([
            \App\Models\Appointment::STATUS_MAIL => 'Ready to Mail',
            \App\Models\Appointment::STATUS_EMAILSENT => 'Email Sent',
            \App\Models\Appointment::STATUS_COLLECTED => 'Collected',
            \App\Models\Appointment::STATUS_DISPATCHED => 'Dispatched',
        ] as $value => $label)
                                                <option value="{{ $value }}">
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div>

                            <div class="text-end d-flex justify-content-end gap-2">

                                {{-- CANCEL BUTTON --}}
                                <a href="{{ url('/dashboard') }}" class="btn btn-outline-secondary px-4">
                                    Cancel
                                </a>

                                {{-- DELETE BUTTON --}}
                                <button type="submit" class="btn btn-danger px-4"
                                    onclick="return confirm('Are you sure you want to delete? This action cannot be undone.')">
                                    Delete Appointments
                                </button>

                            </div>


                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const deleteByNo = document.getElementById('deleteByNo');
            const deleteByDate = document.getElementById('deleteByDate');

            const appointmentSection = document.getElementById('appointmentNoSection');
            const dateSection = document.getElementById('dateRangeSection');

            const statusSelect = document.querySelector('select[name="status"]');
            const deliverySelect = document.querySelector('select[name="delivery_status"]');

            const STATUS_ACCEPTED = "{{ \App\Models\Appointment::STATUS_ACCEPTED }}";
            const STATUS_CANCELLED = "{{ \App\Models\Appointment::STATUS_CANCELLED }}";
            const STATUS_PROCESS_COMPLETED = "{{ \App\Models\Appointment::STATUS_PROCESS_COMPLETED }}";

            function toggleSections() {
                if (deleteByDate.checked) {
                    appointmentSection.style.display = 'none';
                    dateSection.style.display = 'block';
                } else {
                    appointmentSection.style.display = 'block';
                    dateSection.style.display = 'none';
                }
            }

            function toggleDeliveryStatus() {

                const selectedStatus = statusSelect.value;

                if (selectedStatus === STATUS_ACCEPTED || selectedStatus === STATUS_CANCELLED) {
                    deliverySelect.value = '';
                    deliverySelect.disabled = true;
                } else if (selectedStatus === STATUS_PROCESS_COMPLETED) {
                    deliverySelect.disabled = false;
                } else {
                    deliverySelect.value = '';
                    deliverySelect.disabled = true;
                }
            }

            deleteByNo.addEventListener('change', toggleSections);
            deleteByDate.addEventListener('change', toggleSections);
            statusSelect.addEventListener('change', toggleDeliveryStatus);

            toggleSections();
            toggleDeliveryStatus();
        });





        document.addEventListener('DOMContentLoaded', function() {

            const deleteByNo = document.getElementById('deleteByNo');
            const deleteByDate = document.getElementById('deleteByDate');

            const appointmentSection = document.getElementById('appointmentNoSection');
            const dateSection = document.getElementById('dateRangeSection');

            function toggleSections() {
                if (deleteByDate.checked) {
                    appointmentSection.style.display = 'none';
                    dateSection.style.display = 'block';
                } else {
                    appointmentSection.style.display = 'block';
                    dateSection.style.display = 'none';
                }
            }

            deleteByNo.addEventListener('change', toggleSections);
            deleteByDate.addEventListener('change', toggleSections);

            toggleSections();
        });
    </script>
@endpush
