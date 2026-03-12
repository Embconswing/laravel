@extends('layouts.app')

@section('content')
    <div class="container py-3">

        {{-- HEADER --}}
        {{-- HEADER --}}
<div class="position-relative mb-3">

    <!-- CENTERED TITLE -->
    <div class="fw-bold fs-5 text-center">
        Cancelled-Pending Applications - All Users
    </div>

    <!-- RIGHT BUTTON -->
    <div class="position-absolute top-50 end-0 translate-middle-y">
        <a href="{{ url('/dashboard') }}"
           class="btn btn-sm btn-outline-primary">
            ← Back
        </a>
    </div>

</div>


        {{-- 🔍 SEARCH & FILTER BAR --}}
        <form method="GET" action="{{ route('appointments.supervisorCases') }}" class="card card-body mb-3">

            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                        placeholder="Appointment no, reference, name, email">
                </div>

                <div class="col-md-3">
                    <label class="form-label small">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                        <option value="accepted" @selected(request('status') === 'accepted')>Accepted</option>
                        <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelled</option>
                    </select>
                </div>

                <div class="col-md-auto">
                    <button class="btn btn-primary">Search</button>
                    <a href="{{ route('appointments.supervisorCases') }}" class="btn btn-outline-secondary">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        {{-- 📋 TABLE --}}
        @if ($appointments->total() === 0)
            <div class="alert alert-info">
                No applications found.
            </div>
        @else
            @include('appointments.partials.assigned-table', [
                'appointments' => $appointments,
            ])

            {{-- 📄 PAGINATION + TOTAL COUNT --}}
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted small">
                    Showing
                    {{ $appointments->firstItem() }}
                    to
                    {{ $appointments->lastItem() }}
                    of
                    {{ $appointments->total() }}
                    applications
                </div>

                <div>
                    {{ $appointments->links() }}
                </div>
            </div>
        @endif

    </div>

    {{-- STATUS CHANGE CONFIRMATION MODAL --}}
<div class="modal fade" id="statusConfirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Confirm Status Change</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                Are you sure you want to change the application status?
            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="button"
                        class="btn btn-primary"
                        id="confirmStatusChangeBtn">
                    Yes, Change
                </button>
            </div>

        </div>
    </div>
</div>

@endsection
