<?php
    $hasTracking = $appointment->trackings->isNotEmpty();
    $hasPending = !empty(trim((string) $appointment->reason_pending));
?>

<?php if(!$hasTracking && !$hasPending): ?>
    <div class="text-muted text-center py-4">
        No history available.
    </div>
<?php else: ?>
    
    <?php if($hasPending): ?>
        <div class="alert alert-warning">
            <strong>Pending:</strong>
            <?php echo e($appointment->reason_pending); ?>

        </div>
    <?php endif; ?>

    
    <?php $__currentLoopData = $appointment->trackings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tracking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="border rounded p-3 mb-2">

            <div class="d-flex justify-content-between">
                <strong>
                    <?php echo e(ucfirst(str_replace('_', ' ', $tracking->action))); ?>

                </strong>
                <span class="text-muted">
                    <?php echo e($tracking->created_at->format('d M Y, h:i A')); ?>

                </span>
            </div>

            <div class="small text-muted mt-1">
                <strong>By:</strong>
                <?php echo e(optional($tracking->assignedBy)->name ?? 'System'); ?>


                <?php if($tracking->assigned_to): ?>
                    <span class="mx-1">→</span>
                    <strong>To:</strong>
                    <?php echo e(optional($tracking->assignedTo)->name); ?>

                <?php endif; ?>
            </div>


            <?php if($tracking->remarks): ?>
                <div class="mt-2 bg-light p-2 rounded">
                    <?php echo e($tracking->remarks); ?>

                </div>
            <?php endif; ?>

        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php endif; ?>
<?php /**PATH D:\Berlin-Appointments-25\resources\views/appointments/partials/history-content.blade.php ENDPATH**/ ?>