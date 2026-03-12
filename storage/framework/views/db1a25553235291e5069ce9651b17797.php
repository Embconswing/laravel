

<?php $__env->startSection('content'); ?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">Delete Appointments</h5>
                    </div>

                    <div class="card-body">

                        <?php if($errors->any()): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if(session('success')): ?>
                            <div class="alert alert-success">
                                <?php echo e(session('success')); ?>

                            </div>
                        <?php endif; ?>

                        <form method="POST" action="<?php echo e(route('admin.appointments.delete')); ?>">
                            <?php echo csrf_field(); ?>

                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">Delete Method</label>

                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="delete_type" id="deleteByNo"
                                        value="appointment_no" checked>
                                    <label class="form-check-label" for="deleteByNo">
                                        Delete by Appointment Number
                                    </label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="delete_type" id="deleteByDate"
                                        value="date_range">
                                    <label class="form-check-label" for="deleteByDate">
                                        Delete by Date Range
                                    </label>
                                </div>
                            </div>

                            <hr>

                            
                            <div id="appointmentNoSection" class="mb-4">
                                <label class="form-label">Appointment Number</label>
                                <input type="text" name="appointment_no" class="form-control"
                                    placeholder="Enter Appointment No">
                            </div>

                            
                            <div id="dateRangeSection" class="mb-4" style="display:none;">

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">From Date</label>
                                        <input type="date" name="from_date" class="form-control">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">To Date</label>
                                        <input type="date" name="to_date" class="form-control">
                                    </div>
                                </div>

                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Application Status *</label>
                                        <select name="status" class="form-select">
                                            <option value="">— Select Status —</option>

                                            <?php $__currentLoopData = [\App\Models\Appointment::STATUS_PROCESS_COMPLETED, \App\Models\Appointment::STATUS_ACCEPTED, \App\Models\Appointment::STATUS_CANCELLED]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($status); ?>">
                                                    <?php echo e(ucfirst(str_replace('_', ' ', strtolower($status)))); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>

                                    
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Delivery Status *</label>
                                        <select name="delivery_status" class="form-select">
                                            <option value="">— Select Delivery Status —</option>

                                            <?php $__currentLoopData = [
            \App\Models\Appointment::STATUS_MAIL => 'Ready to Mail',
            \App\Models\Appointment::STATUS_EMAILSENT => 'Email Sent',
            \App\Models\Appointment::STATUS_COLLECTED => 'Collected',
            \App\Models\Appointment::STATUS_DISPATCHED => 'Dispatched',
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($value); ?>">
                                                    <?php echo e($label); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>

                            </div>

                            <div class="text-end d-flex justify-content-end gap-2">

                                
                                <a href="<?php echo e(url('/dashboard')); ?>" class="btn btn-outline-secondary px-4">
                                    Cancel
                                </a>

                                
                                <button type="submit" class="btn btn-danger px-4"
                                    onclick="return confirm('Are you sure you want to delete? This action cannot be undone.')">
                                    Delete Appointments
                                </button>

                            </div>


                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>


<?php $__env->startPush('scripts'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const deleteByNo = document.getElementById('deleteByNo');
            const deleteByDate = document.getElementById('deleteByDate');

            const appointmentSection = document.getElementById('appointmentNoSection');
            const dateSection = document.getElementById('dateRangeSection');

            const statusSelect = document.querySelector('select[name="status"]');
            const deliverySelect = document.querySelector('select[name="delivery_status"]');

            const STATUS_ACCEPTED = "<?php echo e(\App\Models\Appointment::STATUS_ACCEPTED); ?>";
            const STATUS_CANCELLED = "<?php echo e(\App\Models\Appointment::STATUS_CANCELLED); ?>";
            const STATUS_PROCESS_COMPLETED = "<?php echo e(\App\Models\Appointment::STATUS_PROCESS_COMPLETED); ?>";

            function toggleSections() {
                if (deleteByDate.checked) {
                    appointmentSection.style.display = 'none';
                    dateSection.style.display = 'block';
                } else {
                    appointmentSection.style.display = 'block';
                    dateSection.style.display = 'none';
                }
            }

            function toggleDeliveryStatus() {

                const selectedStatus = statusSelect.value;

                if (selectedStatus === STATUS_ACCEPTED || selectedStatus === STATUS_CANCELLED) {
                    deliverySelect.value = '';
                    deliverySelect.disabled = true;
                } else if (selectedStatus === STATUS_PROCESS_COMPLETED) {
                    deliverySelect.disabled = false;
                } else {
                    deliverySelect.value = '';
                    deliverySelect.disabled = true;
                }
            }

            deleteByNo.addEventListener('change', toggleSections);
            deleteByDate.addEventListener('change', toggleSections);
            statusSelect.addEventListener('change', toggleDeliveryStatus);

            toggleSections();
            toggleDeliveryStatus();
        });





        document.addEventListener('DOMContentLoaded', function() {

            const deleteByNo = document.getElementById('deleteByNo');
            const deleteByDate = document.getElementById('deleteByDate');

            const appointmentSection = document.getElementById('appointmentNoSection');
            const dateSection = document.getElementById('dateRangeSection');

            function toggleSections() {
                if (deleteByDate.checked) {
                    appointmentSection.style.display = 'none';
                    dateSection.style.display = 'block';
                } else {
                    appointmentSection.style.display = 'block';
                    dateSection.style.display = 'none';
                }
            }

            deleteByNo.addEventListener('change', toggleSections);
            deleteByDate.addEventListener('change', toggleSections);

            toggleSections();
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Berlin-Appointments-25\resources\views/admin/appointments/delete.blade.php ENDPATH**/ ?>