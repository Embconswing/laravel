

<?php $__env->startSection('content'); ?>


<style>
.status-cell {
    min-width: 160px;
    max-width: 220px;
}

.status-cell .progress {
    height: 6px;
}
</style>
    <?php
        use App\Models\Appointment;

        // Synced from the model (single source of truth)
        $statusLabels = Appointment::STATUS_LABELS;
        $statusColors = Appointment::STATUS_COLORS;
        $deliveryLabels = Appointment::DELIVERY_LABELS;
        $deliveryColors = Appointment::DELIVERY_COLORS;
    ?>

    <div class="position-relative mb-2" style="min-height: 20px;">

        <!-- CENTERED TITLE -->
        <div class="fw-bold fs-5 text-center m-0 top-0">
            Application Pools - All Users
        </div>

        <!-- RIGHT BUTTON -->
        <div class="position-absolute top-0 end-0 translate-middle-y">
            <a href="<?php echo e(url('/dashboard')); ?>" class="btn btn-sm btn-outline-primary py-1 px-2">
                ← Dashboard
            </a>
        </div>

        
        <div class="card mb-3">
            <div class="card-body py-2 d-flex flex-wrap gap-2 align-items-center">

                <strong>Filter by Status:</strong>

                
                <select id="statusFilter" class="form-select form-select-sm w-auto">
                    <option value="">All</option>
                    <?php $__currentLoopData = $statusLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($value); ?>"><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>

                
                <select id="deliveryFilter" class="form-select form-select-sm w-auto">
                    <option value="">All Delivery</option>
                    <?php $__currentLoopData = $deliveryLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($value); ?>"><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>

                
                <input type="text" id="searchInput" class="form-control form-control-sm w-auto"
                    placeholder="Search application...">
                    
<label class="small">From:</label>
<input type="date" id="dateFrom" class="form-control form-control-sm w-auto">

<label class="small">To:</label>
<input type="date" id="dateTo" class="form-control form-control-sm w-auto">
            </div>
        </div>
    </div>

    
    <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
            $statusCounts = $user->assignedAppointments->groupBy('status')->map(fn($items) => $items->count());
        ?>

        <div class="card shadow-sm mb-4 user-card">

            
            <div class="card-header bg-light">

                
                <div class="fw-bold mb-2">
                    <?php echo e($user->name); ?>

                </div>

                
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-<?php echo e($statusColors[Appointment::STATUS_ASSIGNED] ?? 'secondary'); ?>">
                        <?php echo e($statusLabels[Appointment::STATUS_ASSIGNED] ?? 'Assigned'); ?>:
                        <?php echo e($statusCounts[Appointment::STATUS_ASSIGNED] ?? 0); ?>

                    </span>

                    <span class="badge bg-<?php echo e($statusColors[Appointment::STATUS_ACCEPTED] ?? 'secondary'); ?>">
                        <?php echo e($statusLabels[Appointment::STATUS_ACCEPTED] ?? 'Accepted'); ?>:
                        <?php echo e($statusCounts[Appointment::STATUS_ACCEPTED] ?? 0); ?>

                    </span>

                    <span class="badge bg-<?php echo e($statusColors[Appointment::STATUS_PENDING] ?? 'secondary'); ?> text-dark">
                        <?php echo e($statusLabels[Appointment::STATUS_PENDING] ?? 'Pending'); ?>:
                        <?php echo e($statusCounts[Appointment::STATUS_PENDING] ?? 0); ?>

                    </span>

                    
                    <span class="badge bg-<?php echo e($statusColors[Appointment::STATUS_PROCESS_IN_PROGRESS] ?? 'secondary'); ?>">
                        <?php echo e($statusLabels[Appointment::STATUS_PROCESS_IN_PROGRESS] ?? 'Under Process'); ?>:
                        <?php echo e($statusCounts[Appointment::STATUS_PROCESS_IN_PROGRESS] ?? 0); ?>

                    </span>

                    <span class="badge bg-<?php echo e($statusColors[Appointment::STATUS_PROCESS_COMPLETED] ?? 'secondary'); ?>">
                        <?php echo e($statusLabels[Appointment::STATUS_PROCESS_COMPLETED] ?? 'Process Completed'); ?>:
                        <?php echo e($statusCounts[Appointment::STATUS_PROCESS_COMPLETED] ?? 0); ?>

                    </span>

                    <span class="badge bg-<?php echo e($statusColors[Appointment::STATUS_RETURNED_TO_SUPERVISOR] ?? 'secondary'); ?>">
                        <?php echo e($statusLabels[Appointment::STATUS_RETURNED_TO_SUPERVISOR] ?? 'Returned'); ?>:
                        <?php echo e($statusCounts[Appointment::STATUS_RETURNED_TO_SUPERVISOR] ?? 0); ?>

                    </span>
                </div>

            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th>Appointment No</th> 
                            <th>Date Assigned</th>
                            <th>Applicant</th>
                            <th>Service</th>
                           
                            <th>Status</th>
                            <th>Delivery</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $__empty_2 = true; $__currentLoopData = $user->assignedAppointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appointment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                            <?php
                                $statusBadge = $statusColors[$appointment->status] ?? 'secondary';
                                $deliveryBadge = $deliveryColors[$appointment->delivery_status] ?? 'secondary';

                                $statusText = $statusLabels[$appointment->status]
                                    ?? ucfirst(str_replace('_', ' ', $appointment->status));

                                $deliveryText = $appointment->delivery_status
                                    ? ($deliveryLabels[$appointment->delivery_status]
                                        ?? ucfirst(str_replace('_', ' ', $appointment->delivery_status)))
                                    : null;
                            ?>

                            <tr class="appointment-row"
    data-status="<?php echo e($appointment->status); ?>"
    data-delivery="<?php echo e($appointment->delivery_status ?? ''); ?>"
    data-date="<?php echo e($appointment->created_at->format('Y-m-d')); ?>">

                                <td><?php echo e($appointment->appointment_no); ?></td>
                                 <td>
    <?php echo e($appointment->created_at->format('d M Y')); ?>

</td>
                                <td><?php echo e($appointment->applicant_name); ?></td>
                                <td><?php echo e($appointment->service); ?></td>
                               


                               
<td class="status-cell">
    <div class="d-flex justify-content-between small">
        <span class="badge bg-<?php echo e($statusBadge); ?>">
            <?php echo e($statusText); ?>

        </span>
       
    </div>

    <div class="progress mt-1" style="height:6px;">
        <div class="progress-bar <?php echo e($appointment->status_bar_class); ?>"
             style="width: <?php echo e($appointment->status_percent); ?>%;">
        </div>
    </div>
</td>

                                
                                <td>
                                    <?php if($appointment->delivery_status): ?>
                                        <span class="badge bg-<?php echo e($deliveryBadge); ?> d-block text-center">
                                            <?php echo e($deliveryText); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>

                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">
                                    No applications assigned
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>

                </table>
            </div>

        </div>

    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="alert alert-info">
            No users with assigned applications found.
        </div>
    <?php endif; ?>

    
    <script>
        document.getElementById('statusFilter').addEventListener('change', filterRows);
document.getElementById('deliveryFilter').addEventListener('change', filterRows);
document.getElementById('searchInput').addEventListener('keyup', filterRows);
document.getElementById('dateFrom').addEventListener('change', filterRows);
document.getElementById('dateTo').addEventListener('change', filterRows);

function filterRows() {

    const status = document.getElementById('statusFilter').value.toLowerCase();
    const delivery = document.getElementById('deliveryFilter').value.toLowerCase();
    const search = document.getElementById('searchInput').value.toLowerCase();

    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;

    document.querySelectorAll('.appointment-row').forEach(row => {

        const rowStatus = (row.dataset.status || '').toLowerCase();
        const rowDelivery = (row.dataset.delivery || '').toLowerCase();
        const rowDate = row.dataset.date;
        const rowText = row.innerText.toLowerCase();

        let show = true;

        if (status && rowStatus !== status) show = false;
        if (delivery && rowDelivery !== delivery) show = false;
        if (search && !rowText.includes(search)) show = false;

        if (dateFrom && rowDate < dateFrom) show = false;
        if (dateTo && rowDate > dateTo) show = false;

        row.style.display = show ? '' : 'none';
    });
}
    </script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Berlin-Appointments-25\resources\views/admin/overview/application-pools.blade.php ENDPATH**/ ?>