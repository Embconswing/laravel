@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Completed Appointments (Assign for Processing)</h4>
        <span class="text-muted small">
            {{ $appointments->count() }} case(s)
        </span>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Card --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Appointment No</th>
                            <th>Applicant</th>
                            <th>Service</th>
                            <th>Status</th>
                            <th style="min-width: 220px;">Assign To</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($appointments as $appointment)
                            <tr>
                                <td class="fw-semibold">
                                    {{ $appointment->id }}
                                </td>

                                <td>
                                    {{ $appointment->appointment_no }}
                                </td>

                                <td>
                                    {{ $appointment->applicant_name }}
                                </td>

                                <td>
                                    {{ $appointment->service }}
                                </td>

                                <td>
                                    <span class="badge rounded-pill bg-success">
                                        Completed
                                    </span>
                                </td>

                                <td>
                                    {{-- If already assigned, show assignee --}}
                                    @if($appointment->currentAssignee)
                                        <span class="badge bg-info">
                                            {{ $appointment->currentAssignee->name }}
                                        </span>
                                    @else
                                        {{-- Assignment Form --}}
                                        <form method="POST"
                                              action="{{ route('appointments.assign', $appointment) }}">
                                            @csrf
                                            @method('PATCH')

                                            <select name="user_id"
                                                    class="form-select form-select-sm"
                                                    onchange="this.form.submit()">
                                                <option value="">— Select User —</option>

                                                @foreach($users as $user)
                                                    @if($user->id !== auth()->id())
                                                        <option value="{{ $user->id }}">
                                                            {{ $user->name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No completed appointments found for today.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
    </div>

</div>
@endsection
