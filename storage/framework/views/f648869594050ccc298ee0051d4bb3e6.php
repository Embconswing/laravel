<div class="card shadow-sm">


    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>Appointment No</th>
                <th>Reference No</th>
                <th>Applicant</th>
                <th>Email</th>
                <th>Service</th>
                <th>Status</th>
                <th style="width:220px;">Action</th>
            </tr>
        </thead>

        <tbody>
            <?php $__currentLoopData = $appointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appointment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($appointment->appointment_no); ?></td>
                    <td><?php echo e($appointment->ReferenceNr); ?></td>
                    <td><?php echo e($appointment->applicant_name); ?></td>
                    <td><?php echo e($appointment->email); ?></td>
                    <td><?php echo e($appointment->service); ?></td>

                    
                    <td>
                        <?php
                            $badgeMap = [
                                'accepted' => 'success',
                                'assigned' => 'primary',
                                'pending' => 'warning',
                                'cancelled' => 'danger',
                            ];
                            $badge = $badgeMap[$appointment->status] ?? 'secondary';
                        ?>

                        <span class="badge bg-<?php echo e($badge); ?>">
                            <?php echo e(ucfirst(str_replace('_', ' ', $appointment->status))); ?>

                        </span>
                    </td>

                    
                    <td class="space-y-1">

                        
                        <?php if(auth()->user()->isSupervisor()): ?>
                            <form method="POST" action="<?php echo e(route('appointments.updateStatus', $appointment->id)); ?>">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PATCH'); ?>

                                <select name="status" class="form-select form-select-sm"
                                    data-current="<?php echo e($appointment->status); ?>" onchange="confirmStatusChange(this)">

                                    <option value="" disabled>
                                        Change status…
                                    </option>

                                    <option value="<?php echo e(\App\Models\Appointment::STATUS_ACCEPTED); ?>"
                                        <?php if($appointment->status === \App\Models\Appointment::STATUS_ACCEPTED): echo 'selected'; endif; ?>>
                                        Accepted
                                    </option>

                                    <option value="<?php echo e(\App\Models\Appointment::STATUS_PENDING); ?>"
                                        <?php if($appointment->status === \App\Models\Appointment::STATUS_PENDING): echo 'selected'; endif; ?>>
                                        Pending
                                    </option>

                                    <option value="<?php echo e(\App\Models\Appointment::STATUS_CANCELLED); ?>"
                                        <?php if($appointment->status === \App\Models\Appointment::STATUS_CANCELLED): echo 'selected'; endif; ?>>
                                        Cancelled
                                    </option>

                                </select>


                            </form>
                        <?php endif; ?>

                        
                        <button type="button" class="btn btn-sm btn-outline-secondary"
                            onclick="openGlobalHistory(<?php echo e($appointment->id); ?>)">
                            View History
                        </button>


                    </td>
                </tr>

                
                <div id="historyModal-<?php echo e($appointment->id); ?>"
                    class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">

                    <div class="bg-white rounded-lg w-full max-w-2xl p-4 max-h-[80vh] overflow-y-auto">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">
                                Application History
                            </h5>

                            <button type="button" onclick="closeHistoryModal(<?php echo e($appointment->id); ?>)"
                                class="btn btn-sm btn-outline-secondary">
                                ✕
                            </button>
                        </div>

                        <?php
                            $hasTracking = $appointment->trackings->isNotEmpty();
                            $hasPending = !empty(trim((string) $appointment->reason_pending));
                        ?>

                        <?php if(!$hasTracking && !$hasPending): ?>
                            <div class="text-muted text-center py-4">
                                No history available.
                            </div>
                        <?php else: ?>
                            <div class="space-y-3">

                                
                                <?php if($hasPending): ?>
                                    <div class="border rounded p-3 text-sm bg-warning-subtle">

                                        <div class="d-flex justify-content-between">
                                            <strong class="text-warning">
                                                Pending
                                            </strong>
                                            <span class="text-muted">
                                                <?php echo e(optional($appointment->updated_at)->format('d M Y, h:i A')); ?>

                                            </span>
                                        </div>

                                        <div class="mt-2">
                                            <?php echo e($appointment->reason_pending); ?>

                                        </div>

                                    </div>
                                <?php endif; ?>

                                
                                

                            </div>
                        <?php endif; ?>


                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>

    </table>
</div>
</div>
<div class="modal fade" id="globalHistoryModal" tabindex="-1">
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



<script>

    function openGlobalHistory(id) {

    const modal = new bootstrap.Modal(
        document.getElementById('globalHistoryModal')
    );

    const body = document.getElementById('historyModalBody');

    body.innerHTML = `
        <div class="text-center text-muted py-4">
            Loading history...
        </div>
    `;

    fetch(`/appointments/${id}/history`)
        .then(response => response.text())
        .then(html => {
            body.innerHTML = html;
        });

    modal.show();
}


    function confirmStatusChange(select) {

        const originalValue = select.dataset.current;
        const newValue = select.value;
        const newLabel = select.options[select.selectedIndex].text;

        if (!newValue) return;

        // 🚫 BLOCK: Accepted → Pending
        if (originalValue === '<?php echo e(\App\Models\Appointment::STATUS_ACCEPTED); ?>' &&
            newValue === '<?php echo e(\App\Models\Appointment::STATUS_PENDING); ?>') {

            alert("Status cannot be changed from Accepted to Pending.");

            select.value = originalValue; // revert back
            return;
        }

        const confirmed = confirm(
            "Are you sure you want to change the application status to '" +
            newLabel + "'?"
        );

        if (!confirmed) {
            select.value = originalValue;
            return;
        }

        select.form.submit();
    }
</script>
<?php /**PATH D:\Berlin-Appointments-25\resources\views/appointments/partials/assigned-table.blade.php ENDPATH**/ ?>