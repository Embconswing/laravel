

<?php $__env->startSection('content'); ?>

<?php if(session('error')): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?php echo e(session('error')); ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="container py-3">

    
    <?php echo $__env->make('dashboard.partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php if($appointments->isEmpty()): ?>
        <div class="alert alert-info">
            No applications are currently assigned to you.
        </div>
    <?php else: ?>

        
        <?php echo $__env->make('dashboard.partials.summary-chart', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        <?php echo $__env->make('dashboard.partials.filters', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        <?php echo $__env->make('dashboard.partials.table', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        <?php if($appointments->hasPages()): ?>
        <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap">

            <div class="small text-muted mb-2">
                Showing <?php echo e($appointments->firstItem()); ?>

                to <?php echo e($appointments->lastItem()); ?>

                of <?php echo e($appointments->total()); ?> applications
            </div>

            <div>
                <?php echo e($appointments->links()); ?>

            </div>

        </div>
        <?php endif; ?>

    <?php endif; ?>

</div>


<?php echo $__env->make('dashboard.partials.modals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<?php echo $__env->make('dashboard.partials.scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Berlin-Appointments-31\resources\views/dashboard/my-assignments.blade.php ENDPATH**/ ?>