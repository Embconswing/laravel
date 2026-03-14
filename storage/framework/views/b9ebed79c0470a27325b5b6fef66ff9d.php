

<?php $__env->startSection('content'); ?>
    <style>
        .highlight-row {
            animation: highlightFade 2s ease;
        }

        @keyframes highlightFade {
            0% {
                background-color: #fff3cd;
            }

            100% {
                background-color: transparent;
            }
        }

        .scan-toolbar {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 10px;
        }

        .section-title {
            font-weight: 600;
            font-size: 14px;
            letter-spacing: .4px;
            color: #495057;
        }

    .service-filter{
    background:#fff5f5;
    border:1px solid #f1c6c6;
}
    </style>


    <div class="container-fluid py-3">
        <div class="text-center mb-3">
            <h1 class="fw-bold mb-0">
                Applications to be Assigned
                (<span class="text-danger">
                    <?php echo e($appointments->count()); ?>


                    <?php echo e(\Illuminate\Support\Str::plural('case', $appointments->count())); ?>

                </span> )</h1>
        </div>


        
        <div class="d-flex justify-content-between align-items-center mb-3">





        </div>



        
        <?php
            $currentService = request('service');
        ?>

       <div class="card shadow-sm mb-3 service-filter">
            <div class="card-body py-2">

                <ul class="nav nav-pills nav-fill">

                    <li class="nav-item">
                        <a class="nav-link <?php echo e(!$currentService ? 'active' : ''); ?>"
                            href="<?php echo e(route('appointments.assign.index')); ?>">
                            All
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?php echo e($currentService == 'Passport' ? 'active' : ''); ?>"
                            href="<?php echo e(route('appointments.assign.index', ['service' => 'Passport'])); ?>">
                            Passport
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?php echo e($currentService == 'Visa' ? 'active' : ''); ?>"
                            href="<?php echo e(route('appointments.assign.index', ['service' => 'Visa'])); ?>">
                            Visa
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?php echo e($currentService == 'OCI' ? 'active' : ''); ?>"
                            href="<?php echo e(route('appointments.assign.index', ['service' => 'OCI'])); ?>">
                            OCI
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?php echo e($currentService == 'Miscellaneous' ? 'active' : ''); ?>"
                            href="<?php echo e(route('appointments.assign.index', ['service' => 'Miscellaneous'])); ?>">
                            Miscellaneous
                        </a>
                    </li>

                </ul>

            </div>
        </div>



        
        <form id="dateFilterForm" method="GET" action="<?php echo e(route('appointments.assign.index')); ?>">
            <input type="hidden" name="service" value="<?php echo e(request('service')); ?>">
        </form>



        <form method="POST" action="<?php echo e(route('appointments.bulkAssign')); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PATCH'); ?>


            
            <div class="scan-toolbar mb-3">

                <div class="row g-2 align-items-center">

                    
                    <div class="col-md-4">
                        <input type="text" id="scanner-input" class="form-control form-control-lg"
                            placeholder="🔍 Scan Reference Number..." autofocus>
                    </div>

                    
                    <div class="col-md-2">
                        <input type="date" name="from_date" value="<?php echo e(request('from_date')); ?>"
                            class="form-control form-control-sm" form="dateFilterForm">
                    </div>

                    
                    <div class="col-md-2">
                        <input type="date" name="to_date" value="<?php echo e(request('to_date')); ?>"
                            class="form-control form-control-sm" form="dateFilterForm">
                    </div>

                    
                    <div class="col-md-1 d-grid">
                        <button class="btn btn-sm btn-outline-primary" form="dateFilterForm">
                            Filter
                        </button>
                    </div>

                    
                    <div class="col-md-1 d-grid">
                        <a href="<?php echo e(route('appointments.assign.index')); ?>" class="btn btn-sm btn-outline-secondary">
                            Reset
                        </a>
                    </div>

                    
                    <div class="col-md-2 text-end small text-muted">


                        Selected:
                        <strong class="selected-count">0</strong>



                    </div>

                </div>

            </div>



            <div class="card shadow-sm">

                <?php if(session('warning')): ?>
                    <div class="alert alert-warning alert-dismissible fade show">
                        <?php echo e(session('warning')); ?>

                        <button class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>



                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>

                                <th width="40">
                                    <input type="checkbox" id="select-all">
                                </th>

                                <th>Reference</th>
                                <th>Appointment No</th>
                                <th>Date</th>
                                <th>Applicant</th>
                                <th>Service</th>
                                <th>Status</th>
                                <th width="120">Actions</th>

                            </tr>

                        </thead>



                        
                        <tbody id="scanned-results">

                            <tr class="table-warning">
                                <td colspan="8" class="section-title">
                                    Scanned Applications
                                    (<span id="scanned-count">0</span>)
                                </td>
                            </tr>

                            <tr id="no-scanned-row">
                                <td colspan="8" class="text-center text-muted py-3">
                                    No scanned applications yet
                                </td>
                            </tr>

                        </tbody>



                        
                        <tbody id="normal-results">

                            <tr class="table-light">
                                <td colspan="8" class="section-title">
                                    Available Applications
                                </td>
                            </tr>


                            <?php $__empty_1 = true; $__currentLoopData = $appointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appointment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr data-appointment-id="<?php echo e($appointment->id); ?>">

                                    <td>
                                        <input type="checkbox" name="appointment_ids[]" value="<?php echo e($appointment->id); ?>"
                                            class="appointment-checkbox">
                                    </td>

                                    <td>
                                        <span class="badge bg-dark reference-text">
                                            <?php echo e($appointment->ReferenceNr); ?>

                                        </span>
                                    </td>

                                    <td><?php echo e($appointment->appointment_no); ?></td>

                                    <td><?php echo e(\Carbon\Carbon::parse($appointment->appointment_date)->format('d-m-Y')); ?></td>

                                    <td><?php echo e($appointment->applicant_name); ?></td>

                                    <td><?php echo e($appointment->service); ?></td>

                                    <td>
                                        <span class="badge bg-success">
                                            Accepted
                                        </span>
                                    </td>

                                    <td>

                                        <button type="button" class="btn btn-sm btn-outline-secondary edit-ref">
                                            ✏️
                                        </button>

                                        <button type="button" class="btn btn-sm btn-outline-danger remove-row">
                                            ❌
                                        </button>

                                    </td>

                                </tr>

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                                <tr>
                                    <td colspan="8" class="text-center text-muted">
                                        No applications found
                                    </td>
                                </tr>
                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>



                <div class="card-footer d-flex justify-content-between align-items-center">

                    

                    <div class="small text-muted">
                        Selected:
                        <strong class="selected-count">0</strong>
                    </div>


                    
                    <div class="d-flex align-items-center gap-2">

                        <select name="current_assignee_id" class="form-select w-auto" required>

                            <option value="">Assign to User</option>

                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($user->id !== auth()->id()): ?>
                                    <option value="<?php echo e($user->id); ?>">
                                        <?php echo e($user->name); ?>

                                    </option>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        </select>

                        <button class="btn btn-primary">
                            Assign Selected
                        </button>

                    </div>

                </div>

            </div>

        </form>

    </div>
<?php $__env->stopSection(); ?>



<?php $__env->startPush('scripts'); ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const scanner = document.getElementById("scanner-input");
            const scannedTable = document.getElementById("scanned-results");
            const selectedCount = document.getElementById("selected-count");
            const selectAll = document.getElementById("select-all");

            const scanSound = new Audio("/sounds/scan.mp3");

            let scanned = [];



            function updateCount() {

                const checked = document.querySelectorAll(
                    'input[name="appointment_ids[]"]:checked'
                );

                document.querySelectorAll(".selected-count").forEach(el => {
                    el.textContent = checked.length;
                });

            }



            selectAll.addEventListener("change", function() {

                document
                    .querySelectorAll('input[name="appointment_ids[]"]')
                    .forEach(cb => cb.checked = selectAll.checked);

                updateCount();

            });



            document.addEventListener("change", function(e) {

                if (e.target.name === "appointment_ids[]") {
                    updateCount();
                }

            });



            scanner.addEventListener("keydown", function(e) {

                if (e.key === "Enter") {

                    e.preventDefault();

                    const reference = scanner.value.trim();

                    if (!reference) return;


                    fetch("<?php echo e(route('appointments.findByReference')); ?>?reference=" + reference)

                        .then(res => res.json())

                        .then(data => {

                            if (!data.success) {

                                alert(data.message);
                                scanner.value = "";
                                scanner.focus();
                                return;

                            }


                            const appointment = data.appointment;


                            if (scanned.includes(appointment.id)) {

                                alert("Already scanned.");
                                scanner.value = "";
                                scanner.focus();
                                return;

                            }


                            const existingRow =
                                document.querySelector(
                                    `tr[data-appointment-id="${appointment.id}"]`
                                );


                            if (existingRow) {

                                const checkbox =
                                    existingRow.querySelector(
                                        'input[name="appointment_ids[]"]'
                                    );

                                checkbox.checked = true;

                                existingRow.classList.add("highlight-row");

                                existingRow.children[6].innerHTML =
                                    `<span class="badge bg-warning">Scanned</span>`;

                                scannedTable.prepend(existingRow);

                            } else {

                                const row = document.createElement("tr");

                                row.dataset.appointmentId = appointment.id;

                                row.classList.add("highlight-row");

                                row.innerHTML = `

<td>
<input type="checkbox"
name="appointment_ids[]"
value="${appointment.id}"
checked>
</td>

<td>
<span class="badge bg-dark reference-text">
${appointment.ReferenceNr}
</span>
</td>

<td>${appointment.appointment_no}</td>

<td>${formatDate(appointment.appointment_date)}</td>

<td>${appointment.applicant_name}</td>

<td>${appointment.service}</td>

<td>
<span class="badge bg-warning">
Scanned
</span>
</td>

<td>
<button type="button" class="btn btn-sm btn-outline-secondary edit-ref">✏️</button>
<button type="button" class="btn btn-sm btn-outline-danger remove-row">❌</button>
</td>

`;

                                scannedTable.prepend(row);

                            }


                            const empty = document.getElementById("no-scanned-row");
                            if (empty) empty.remove();


                            scanned.push(appointment.id);

                            scanSound.play();

                            scanner.value = "";

                            updateCount();
                            updateScannedCount();

                        })

                        .catch(err => {

                            console.error(err);
                            alert("Error scanning application.");

                        });

                }

            });



            function updateScannedCount() {

                const scannedRows =
                    document.querySelectorAll(
                        '#scanned-results tr[data-appointment-id]'
                    );

                document
                    .getElementById("scanned-count")
                    .textContent = scannedRows.length;

            }



            function formatDate(dateString) {

                const date = new Date(dateString);

                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const year = date.getFullYear();

                return `${day}-${month}-${year}`;

            }



            document.addEventListener("click", function(e) {

                if (e.target.classList.contains("remove-row")) {

                    const row = e.target.closest("tr");

                    const id = row.querySelector(
                        'input[name="appointment_ids[]"]'
                    ).value;

                    scanned = scanned.filter(x => x != id);

                    row.remove();

                    updateCount();
                    updateScannedCount();

                }



                if (e.target.classList.contains("edit-ref")) {

                    const row = e.target.closest("tr");

                    const appointmentId = row.dataset.appointmentId;

                    const refElement =
                        row.querySelector(".reference-text");

                    const oldRef = refElement.innerText;

                    const newRef = prompt(
                        "Edit Reference Number",
                        oldRef
                    );

                    if (!newRef || newRef === oldRef) return;


                    fetch("<?php echo e(route('appointments.updateReferenceAjax')); ?>", {

                            method: "PATCH",

                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "<?php echo e(csrf_token()); ?>"
                            },

                            body: JSON.stringify({

                                appointment_id: appointmentId,
                                reference: newRef

                            })

                        })

                        .then(res => res.json())

                        .then(data => {

                            if (!data.success) {

                                alert(data.message);
                                return;

                            }

                            document
                                .querySelectorAll(
                                    `[data-appointment-id="${appointmentId}"] .reference-text`
                                )

                                .forEach(el => {

                                    el.innerText = data.reference;

                                });

                        });

                }

            });

        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Berlin-Appointments-31\resources\views/appointments/stage-assign.blade.php ENDPATH**/ ?>