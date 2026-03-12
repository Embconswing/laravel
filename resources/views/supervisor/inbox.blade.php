@extends('layouts.app')

@section('content')
<div class="container py-3">

    <h4 class="mb-3">Returned Applications (Supervisor Inbox)</h4>

    @if($appointments->isEmpty())
        <div class="alert alert-info">
            No returned applications.
        </div>
    @else
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Appointment No</th>
                            <th>Reference</th>
                            <th>Applicant</th>
                            <th>Returned By</th>
                            <th>Remarks</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($appointments as $appointment)
                            @php
                                $tracking = $appointment->trackings->first();
                            @endphp
                            <tr>
                                <td>{{ $appointment->appointment_no }}</td>
                                <td>{{ $appointment->ReferenceNr }}</td>
                                <td>{{ $appointment->applicant_name }}</td>
                                <td>{{ optional($tracking->assignedBy)->name }}</td>
                                <td>{{ $tracking->remarks }}</td>
                                <td>{{ $tracking->created_at->format('d M Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
@endsection
