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

            <?php $__currentLoopData = $appointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appointment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                <tr>

                    <td><?php echo e($appointment->appointment_no); ?></td>

                    <td class="text-truncate"
                        style="max-width:140px;"
                        title="<?php echo e($appointment->ReferenceNr); ?>">
                        <?php echo e($appointment->ReferenceNr); ?>

                    </td>

                    <td><?php echo e($appointment->applicant_name); ?></td>

                    <td><?php echo e($appointment->email); ?></td>

                    <td><?php echo e($appointment->service); ?></td>

                    <td>
                        <?php echo e(optional($appointment->appointment_date)->format('d M Y') ?? '-'); ?>

                    </td>


                    
                    <td>

                        <?php if(auth()->user()->isNormal()): ?>

                            
                            <button type="button"
                                    onclick="openProcessCompletedModal(<?php echo e($appointment->id); ?>)"
                                    class="btn btn-sm btn-outline-success w-100 mb-1">
                                Process Completed
                            </button>

                            
                            <button type="button"
                                    onclick="openUnderProcessModal(<?php echo e($appointment->id); ?>)"
                                    class="btn btn-sm btn-outline-warning w-100">
                                Kept on Hold
                            </button>

                            
                            <button type="button"
                                    onclick="openChangeRefModal(<?php echo e($appointment->id); ?>, '<?php echo e($appointment->ReferenceNr); ?>')"
                                    class="btn btn-sm btn-outline-dark w-100 mt-1">
                                Change Ref. No
                            </button>

                            
                            <?php if($appointment->status !== \App\Models\Appointment::STATUS_PROCESS_COMPLETED): ?>

                                <button type="button"
                                        onclick="openChangeEmailModal(<?php echo e($appointment->id); ?>, '<?php echo e($appointment->email); ?>')"
                                        class="btn btn-sm btn-outline-primary w-100 mt-1">
                                    Change Email
                                </button>

                            <?php endif; ?>

                        <?php endif; ?>

                    </td>


                    
                    <td>

                        
                        <?php if(auth()->user()->isNormal()): ?>

                            <?php
                                $latestTracking = $appointment->trackings()->latest()->first();

                                $isReturned = in_array(optional($latestTracking)->action, [
                                    'returned_by_supervisor',
                                    'returned_to_supervisor',
                                    'returned_to_pool',
                                ]);

                                $barClass = $isReturned
                                    ? 'bg-danger'
                                    : $appointment->status_bar_class;
                            ?>


                            <div class="progress mb-2" style="height:22px;">

                                <div class="progress-bar <?php echo e($barClass); ?>"
                                     role="progressbar"
                                     style="width: <?php echo e($appointment->status_percent); ?>%;">

                                    <?php echo e(\App\Models\Appointment::STATUS_LABELS[$appointment->status]
                                        ?? ucfirst(str_replace('_', ' ', $appointment->status))); ?>


                                    (<?php echo e($appointment->status_days); ?>d)

                                </div>

                            </div>

                        
                        <?php else: ?>

                            <?php
                                $color =
                                    \App\Models\Appointment::STATUS_COLORS[$appointment->status]
                                    ?? 'secondary';
                            ?>

                            <span class="badge bg-<?php echo e($color); ?> d-block mb-1">

                                <?php echo e(\App\Models\Appointment::STATUS_LABELS[$appointment->status]
                                    ?? ucfirst(str_replace('_', ' ', $appointment->status))); ?>


                            </span>

                        <?php endif; ?>


                        
                        <?php echo $__env->renderWhen(
                            view()->exists('dashboard.partials.return-actions'),
                            'dashboard.partials.return-actions',
                            ['appointment' => $appointment]
                        , array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1])); ?>


                        
                        <div class="d-flex justify-content-center mt-1">

                            <button type="button"
                                    class="btn btn-sm btn-outline-secondary w-100"
                                    onclick="openGlobalHistory(<?php echo e($appointment->id); ?>)">

                                View History

                            </button>

                        </div>

                    </td>

                </tr>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            </tbody>

        </table>

    </div>

</div><?php /**PATH D:\Berlin-Appointments-31\resources\views/dashboard/partials/table.blade.php ENDPATH**/ ?>