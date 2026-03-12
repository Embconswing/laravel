

<?php $__env->startSection('content'); ?>
    <div class="container py-3">

        
        
<div class="position-relative mb-3">

    <!-- CENTERED TITLE -->
    <div class="fw-bold fs-5 text-center">
        Cancelled-Pending Applications - All Users
    </div>

    <!-- RIGHT BUTTON -->
    <div class="position-absolute top-50 end-0 translate-middle-y">
        <a href="<?php echo e(url('/dashboard')); ?>"
           class="btn btn-sm btn-outline-primary">
            ← Back
        </a>
    </div>

</div>


        
        <form method="GET" action="<?php echo e(route('appointments.supervisorCases')); ?>" class="card card-body mb-3">

            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small">Search</label>
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" class="form-control"
                        placeholder="Appointment no, reference, name, email">
                </div>

                <div class="col-md-3">
                    <label class="form-label small">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="pending" <?php if(request('status') === 'pending'): echo 'selected'; endif; ?>>Pending</option>
                        <option value="accepted" <?php if(request('status') === 'accepted'): echo 'selected'; endif; ?>>Accepted</option>
                        <option value="cancelled" <?php if(request('status') === 'cancelled'): echo 'selected'; endif; ?>>Cancelled</option>
                    </select>
                </div>

                <div class="col-md-auto">
                    <button class="btn btn-primary">Search</button>
                    <a href="<?php echo e(route('appointments.supervisorCases')); ?>" class="btn btn-outline-secondary">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        
        <?php if($appointments->total() === 0): ?>
            <div class="alert alert-info">
                No applications found.
            </div>
        <?php else: ?>
            <?php echo $__env->make('appointments.partials.assigned-table', [
                'appointments' => $appointments,
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted small">
                    Showing
                    <?php echo e($appointments->firstItem()); ?>

                    to
                    <?php echo e($appointments->lastItem()); ?>

                    of
                    <?php echo e($appointments->total()); ?>

                    applications
                </div>

                <div>
                    <?php echo e($appointments->links()); ?>

                </div>
            </div>
        <?php endif; ?>

    </div>

    
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

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Berlin-Appointments-25\resources\views/appointments/supervisor-cases.blade.php ENDPATH**/ ?>