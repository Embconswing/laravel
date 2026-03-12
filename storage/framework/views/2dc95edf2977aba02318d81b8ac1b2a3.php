<?php
    // Latest tracking (may be null)
    $latestTracking = $appointment->trackings->first();

    // User who forwarded the application (may be null)
    $forwardedByUser = $latestTracking && $latestTracking->assignedBy ? $latestTracking->assignedBy : null;

    // Last remarks (may be null)
    $lastRemark = $latestTracking->remarks ?? null;

    $authUser = auth()->user();
?>


<?php if($authUser->isNormal()): ?>

    <form method="POST" action="<?php echo e(route('appointments.return')); ?>" 
      data-status="<?php echo e($appointment->status); ?>"
      class="return-form">

        <?php echo csrf_field(); ?>
        <input type="hidden" name="appointment_id" value="<?php echo e($appointment->id); ?>">

        <select name="return_to" class="form-select form-select-sm mb-1" required>
            <option value="">— Return To —</option>
            <option value="pool">Main Pool</option>
            <option value="supervisor">Consular Attaché</option>
        </select>

        <?php if($lastRemark): ?>
            <div style="font-size: 0.7rem; color:#6c757d;">
    <strong>message:</strong> <?php echo e($lastRemark); ?>

</div>
        <?php endif; ?>

        <textarea name="remarks" class="form-control form-control-sm mb-1" rows="2" required
            placeholder="Remarks"><?php echo e(old('remarks')); ?></textarea>

        <button type="button" class="btn btn-sm btn-outline-warning w-100 return-btn">
    Send to Officer
</button>
    </form>

    
<?php elseif($authUser->isSupervisor()): ?>
    <form method="POST" action="<?php echo e(route('appointments.return')); ?>" 
      data-status="<?php echo e($appointment->status); ?>"
      class="return-form">

        <?php echo csrf_field(); ?>
        <input type="hidden" name="appointment_id" value="<?php echo e($appointment->id); ?>">

        <select name="user_id" class="form-select form-select-sm mb-1" required>
            <option value="">— Send Back To —</option>

            
            <?php if($forwardedByUser && $forwardedByUser->id !== $authUser->id): ?>
                <option value="<?php echo e($forwardedByUser->id); ?>">
                    <?php echo e($forwardedByUser->name); ?> (Forwarded this)
                </option>
            <?php endif; ?>

            
            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(
                    $user->user_type === \App\Models\User::TYPE_NORMAL &&
                        $user->id !== $authUser->id &&
                        $user->id !== optional($forwardedByUser)->id): ?>
                    <option value="<?php echo e($user->id); ?>">
                        <?php echo e($user->name); ?>

                    </option>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>

        <?php if($lastRemark): ?>
       <div class="mb-2 p-2 rounded bg-light border" style="font-size: 0.75rem;">
    <div class="text-muted fw-semibold">Previous Remark</div>
    <div class="text-secondary fst-italic"><?php echo e($lastRemark); ?></div>
</div>
        <?php endif; ?>

        <textarea name="remarks" class="form-control form-control-sm mb-1" rows="2"
            placeholder="Remarks..." required><?php echo e(old('remarks')); ?></textarea>

        <button type="button" class="btn btn-sm btn-outline-warning w-100 return-btn">
    Send Back
</button>
    </form>

    
<?php else: ?>
    <div class="small text-muted">
        <strong>Forwarded by:</strong>
        <?php echo e($forwardedByUser ? $forwardedByUser->name : '—'); ?>

    </div>

    <?php if($lastRemark): ?>
        <div class="small mt-1">
            <strong>Remarks:</strong><br>
            <span class="text-muted"><?php echo e($lastRemark); ?></span>
        </div>
    <?php endif; ?>

<?php endif; ?>

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
            const isNormalUser = <?php echo e(auth()->user()->isNormal() ? 'true' : 'false'); ?>;

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
<?php /**PATH D:\Berlin-Appointments-20\resources\views/dashboard/partials/return-actions.blade.php ENDPATH**/ ?>