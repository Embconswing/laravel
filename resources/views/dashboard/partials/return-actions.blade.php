@php
    // Latest tracking (may be null)
    $latestTracking = $appointment->trackings->first();

    // User who forwarded the application (may be null)
    $forwardedByUser = $latestTracking && $latestTracking->assignedBy ? $latestTracking->assignedBy : null;

    // Last remarks (may be null)
    $lastRemark = $latestTracking->remarks ?? null;

    $authUser = auth()->user();
@endphp

{{-- =========================
   NORMAL USER → SHOW FORM
   ========================= --}}
@if ($authUser->isNormal())

    <form method="POST" action="{{ route('appointments.return') }}" 
      data-status="{{ $appointment->status }}"
      class="return-form">

        @csrf
        <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">

        <select name="return_to" class="form-select form-select-sm mb-1" required>
            <option value="">— Return To —</option>
            <option value="pool">Main Pool</option>
            <option value="supervisor">Consular Attaché</option>
        </select>

        @if ($lastRemark)
            <div style="font-size: 0.7rem; color:#6c757d;">
    <strong>message:</strong> {{ $lastRemark }}
</div>
        @endif

        <textarea name="remarks" class="form-control form-control-sm mb-1" rows="2" required
            placeholder="Remarks">{{ old('remarks') }}</textarea>

        <button type="button" class="btn btn-sm btn-outline-warning w-100 return-btn">
    Send to Officer
</button>
    </form>

    {{-- =========================
   SUPERVISOR → RETURN OPTIONS
   ========================= --}}
@elseif($authUser->isSupervisor())
    <form method="POST" action="{{ route('appointments.return') }}" 
      data-status="{{ $appointment->status }}"
      class="return-form">

        @csrf
        <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">

        <select name="user_id" class="form-select form-select-sm mb-1" required>
            <option value="">— Send Back To —</option>

            {{-- 1️⃣ Person who forwarded it --}}
            @if ($forwardedByUser && $forwardedByUser->id !== $authUser->id)
                <option value="{{ $forwardedByUser->id }}">
                    {{ $forwardedByUser->name }} (Forwarded this)
                </option>
            @endif

            {{-- 2️⃣ Any normal user --}}
            @foreach ($users as $user)
                @if (
                    $user->user_type === \App\Models\User::TYPE_NORMAL &&
                        $user->id !== $authUser->id &&
                        $user->id !== optional($forwardedByUser)->id)
                    <option value="{{ $user->id }}">
                        {{ $user->name }}
                    </option>
                @endif
            @endforeach
        </select>

        @if ($lastRemark)
       <div class="mb-2 p-2 rounded bg-light border" style="font-size: 0.75rem;">
    <div class="text-muted fw-semibold">Previous Remark</div>
    <div class="text-secondary fst-italic">{{ $lastRemark }}</div>
</div>
        @endif

        <textarea name="remarks" class="form-control form-control-sm mb-1" rows="2"
            placeholder="Remarks..." required>{{ old('remarks') }}</textarea>

        <button type="button" class="btn btn-sm btn-outline-warning w-100 return-btn">
    Send Back
</button>
    </form>

    {{-- =========================
   OFFICER → INFO ONLY
   ========================= --}}
@else
    <div class="small text-muted">
        <strong>Forwarded by:</strong>
        {{ $forwardedByUser ? $forwardedByUser->name : '—' }}
    </div>

    @if ($lastRemark)
        <div class="small mt-1">
            <strong>Remarks:</strong><br>
            <span class="text-muted">{{ $lastRemark }}</span>
        </div>
    @endif

@endif

<!-- Confirmation Modal -->
<div class="modal fade" id="returnConfirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            
            <div class="modal-header">
                <h5 class="modal-title">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" id="confirmMessage">
                <!-- Dynamic message here -->
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="button" class="btn btn-primary" id="confirmSubmitBtn">
                    Yes, Confirm
                </button>
            </div>

        </div>
    </div>
</div>


<script>
document.addEventListener("DOMContentLoaded", function () {

    let selectedForm = null;
    const modal = new bootstrap.Modal(document.getElementById('returnConfirmModal'));
    const confirmMessage = document.getElementById('confirmMessage');
    const confirmSubmitBtn = document.getElementById('confirmSubmitBtn');

    document.querySelectorAll('.return-btn').forEach(button => {

        button.addEventListener('click', function () {

            const form = this.closest('form');
            const isNormalUser = {{ auth()->user()->isNormal() ? 'true' : 'false' }};

            if (isNormalUser) {

                const select = form.querySelector('select[name="return_to"]');
                const remarks = form.querySelector('textarea[name="remarks"]');

                if (!select.value) {
                    alert("Please select where to return the application.");
                    return;
                }

                if (!remarks.value.trim()) {
                    alert("Remarks are required before forwarding.");
                    remarks.focus();
                    return;
                }

                const target = select.value === 'supervisor'
                    ? 'Consular Attaché'
                    : 'Main Pool';

                confirmMessage.innerHTML =
                    `Are you sure you want to forward this application to <strong>${target}</strong>?`;

            } else {

                confirmMessage.innerHTML =
                    "Are you sure you want to send this application back?";

            }

            selectedForm = form;
            modal.show();
        });

    });

    confirmSubmitBtn.addEventListener('click', function () {
        if (selectedForm) {
            selectedForm.submit();
        }
    });

});
</script>
