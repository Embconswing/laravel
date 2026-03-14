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

                    <td class="text-truncate"
                        style="max-width:140px;"
                        title="{{ $appointment->ReferenceNr }}">
                        {{ $appointment->ReferenceNr }}
                    </td>

                    <td>{{ $appointment->applicant_name }}</td>

                    <td>{{ $appointment->email }}</td>

                    <td>{{ $appointment->service }}</td>

                    <td>
                        {{ optional($appointment->appointment_date)->format('d M Y') ?? '-' }}
                    </td>


                    {{-- ================= STATUS UPDATE ================= --}}
                    <td>

                        @if (auth()->user()->isNormal())

                            {{-- PROCESS COMPLETED --}}
                            <button type="button"
                                    onclick="openProcessCompletedModal({{ $appointment->id }})"
                                    class="btn btn-sm btn-outline-success w-100 mb-1">
                                Process Completed
                            </button>

                            {{-- KEPT ON HOLD --}}
                            <button type="button"
                                    onclick="openUnderProcessModal({{ $appointment->id }})"
                                    class="btn btn-sm btn-outline-warning w-100">
                                Kept on Hold
                            </button>

                            {{-- CHANGE REF NO --}}
                            <button type="button"
                                    onclick="openChangeRefModal({{ $appointment->id }}, '{{ $appointment->ReferenceNr }}')"
                                    class="btn btn-sm btn-outline-dark w-100 mt-1">
                                Change Ref. No
                            </button>

                            {{-- CHANGE EMAIL --}}
                            @if ($appointment->status !== \App\Models\Appointment::STATUS_PROCESS_COMPLETED)

                                <button type="button"
                                        onclick="openChangeEmailModal({{ $appointment->id }}, '{{ $appointment->email }}')"
                                        class="btn btn-sm btn-outline-primary w-100 mt-1">
                                    Change Email
                                </button>

                            @endif

                        @endif

                    </td>


                    {{-- ================= ACTION ================= --}}
                    <td>

                        {{-- NORMAL USER → SLA BAR --}}
                        @if (auth()->user()->isNormal())

                            @php
                                $latestTracking = $appointment->trackings()->latest()->first();

                                $isReturned = in_array(optional($latestTracking)->action, [
                                    'returned_by_supervisor',
                                    'returned_to_supervisor',
                                    'returned_to_pool',
                                ]);

                                $barClass = $isReturned
                                    ? 'bg-danger'
                                    : $appointment->status_bar_class;
                            @endphp


                            <div class="progress mb-2" style="height:22px;">

                                <div class="progress-bar {{ $barClass }}"
                                     role="progressbar"
                                     style="width: {{ $appointment->status_percent }}%;">

                                    {{ \App\Models\Appointment::STATUS_LABELS[$appointment->status]
                                        ?? ucfirst(str_replace('_', ' ', $appointment->status)) }}

                                    ({{ $appointment->status_days }}d)

                                </div>

                            </div>

                        {{-- SUPERVISOR → STATUS BADGE --}}
                        @else

                            @php
                                $color =
                                    \App\Models\Appointment::STATUS_COLORS[$appointment->status]
                                    ?? 'secondary';
                            @endphp

                            <span class="badge bg-{{ $color }} d-block mb-1">

                                {{ \App\Models\Appointment::STATUS_LABELS[$appointment->status]
                                    ?? ucfirst(str_replace('_', ' ', $appointment->status)) }}

                            </span>

                        @endif


                        {{-- RETURN ACTIONS --}}
                        @includeWhen(
                            view()->exists('dashboard.partials.return-actions'),
                            'dashboard.partials.return-actions',
                            ['appointment' => $appointment]
                        )


                        {{-- VIEW HISTORY --}}
                        <div class="d-flex justify-content-center mt-1">

                            <button type="button"
                                    class="btn btn-sm btn-outline-secondary w-100"
                                    onclick="openGlobalHistory({{ $appointment->id }})">

                                View History

                            </button>

                        </div>

                    </td>

                </tr>

            @endforeach

            </tbody>

        </table>

    </div>

</div>