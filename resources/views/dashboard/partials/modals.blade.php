{{-- UNDER PROCESS MODAL --}}
<div class="modal fade" id="underProcessModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Reason for Keeping on Hold</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" id="underProcessForm">
                <div class="modal-body">
                    @csrf
                    @method('PATCH')

                    <input type="hidden" name="status" value="{{ \App\Models\Appointment::STATUS_PROCESS_IN_PROGRESS }}">

                    <textarea name="remarks"
                              class="form-control"
                              rows="4"
                              required
                              placeholder="Enter reason for keeping this application on hold..."></textarea>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-warning">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- PROCESS COMPLETED MODAL --}}
<div class="modal fade" id="processCompletedModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-success">Confirm Process Completion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" id="processCompletedForm">
                <div class="modal-body">
                    @csrf
                    @method('PATCH')

                    <input type="hidden" name="status" value="{{ \App\Models\Appointment::STATUS_PROCESS_COMPLETED }}">

                    <p class="mb-0">
                        Are you sure you want to mark this application as
                        <strong>Process Completed</strong>?
                    </p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        Confirm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- GLOBAL HISTORY MODAL --}}
<div class="modal fade" id="globalHistoryModal" tabindex="-1" aria-hidden="true">
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

{{-- CHANGE EMAIL MODAL --}}
<div class="modal fade" id="changeEmailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Change Email Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" id="changeEmailForm">
                <div class="modal-body">
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
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Update Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- CHANGE REF NO MODAL --}}
<div class="modal fade" id="changeRefModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Change Reference Number</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" id="changeRefForm">
                <div class="modal-body">
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
                        <textarea name="remark"
                                  class="form-control"
                                  rows="3"
                                  required
                                  placeholder="Reason for changing reference number..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-dark">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>