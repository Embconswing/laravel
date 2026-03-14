<div class="d-flex align-items-center mb-4 position-relative">

    <div style="width:160px;"></div>

    <div class="text-center flex-grow-1">
        <div class="fw-bold fs-5">
            Assigned Applications
        </div>
    </div>

    <div class="d-flex gap-2" style="width:160px; justify-content:end;">

        <?php if(auth()->user()->isSupervisor()): ?>

        <a href="<?php echo e(route('appointments.supervisorCases')); ?>"
           class="btn btn-sm btn-outline-danger"
           title="Pending & Cancelled Applications">
           🚨
        </a>

        <a href="<?php echo e(url('/admin/overview/application-pools')); ?>"
           class="btn btn-sm btn-outline-secondary"
           title="Application Pools Overview">
           📊
        </a>

        <?php endif; ?>

        <a href="<?php echo e(route('appointments.bulk-ready-mail')); ?>"
           class="btn btn-sm btn-outline-danger"
           title="Ready to Mail">
           📦
        </a>

        <a href="<?php echo e(route('appointments.collect-documents')); ?>"
           class="btn btn-sm btn-outline-success"
           title="Document Collection">
           📬
        </a>

        <a href="<?php echo e(url('/')); ?>" class="btn btn-sm btn-primary" title="Dashboard">🏠</a>

        <a href="<?php echo e(route('applications.byStatus')); ?>"
           class="btn btn-sm btn-outline-primary"
           title="Applications by Status">
           📋
        </a>

        <a href="<?php echo e(route('appointments.delete')); ?>"
           class="btn btn-sm btn-outline-danger"
           title="Delete Appointments">
           🗑
        </a>

    </div>

</div><?php /**PATH D:\Berlin-Appointments-31\resources\views/dashboard/partials/header.blade.php ENDPATH**/ ?>