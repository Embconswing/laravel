<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Appointments</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        let allowRefresh = true;

        setInterval(() => {
            const modal = document.getElementById('uploadModal');
            if (allowRefresh && (!modal || modal.classList.contains('hidden'))) {
                window.location.reload();
            }
        }, 5000);
    </script>
</head>

<body class="bg-gray-100">

    <div class="container mx-auto p-4">

        
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-xl font-bold">Appointments</h1>

            <div class="flex gap-2">
                <a href="<?php echo e(url('/dashboard')); ?>"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded font-semibold">
                    Dashboard
                </a>

                <button onclick="openUploadModal()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded font-semibold">
                    Upload CSV
                </button>

                <a href="/walk-ins" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded font-semibold">
                    Walk-ins
                </a>

                <button onclick="window.open('https://appointment.indianembassyberlin.gov.in/admin/login', '_blank')"
                    class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded font-semibold">
                    Admin Login
                </button>
            </div>

        </div>

        
        <div class="overflow-x-auto border bg-white rounded">
            <table class="w-full border-collapse table-auto">

                <thead class="bg-gray-200">
                    <tr>
                        <th class="border px-2 py-1">Reference No</th>
                        <th class="border px-2 py-1">Applicant Name</th>
                        <th class="border px-2 py-1">Service</th>
                        <th class="border px-2 py-1">Start Time</th>
                        <th class="border px-2 py-1">Window</th>
                        <th class="border px-2 py-1">Assign Window</th>
                        <th class="border px-2 py-1 w-56">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $__currentLoopData = $appointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appointment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $status = strtolower($appointment->status);
                            $usedByOthers = array_diff($usedWindows, [$appointment->window_no]);
                        ?>

                        <tr
                            class="text-left
                    <?php echo e($status === 'waiting' ? 'bg-blue-50' : ''); ?>

                    <?php echo e($status === 'accepted' ? 'bg-green-50' : ''); ?>">

                            <td class="border px-2 py-1"><?php echo e($appointment->ReferenceNr); ?></td>
                            <td class="border px-2 py-1"><?php echo e($appointment->applicant_name); ?></td>

                            
                            <td class="border px-2 py-1 font-medium">
                                <?php
                                    $service = trim($appointment->service);

                                    // Normalize special dashes
                                    $service = str_replace(['–', '—'], '-', $service);

                                    $label = '';

                                    if (str_contains($service, 'Miscellaneous Consular Services')) {
                                        $label = 'Miscellaneous';
                                    } elseif (str_contains($service, 'Passport Services')) {
                                        $label = 'Passport';
                                    } elseif (str_contains($service, 'Visa Services')) {
                                        $label = 'Visa';
                                    } elseif (str_contains($service, 'OCI Services')) {
                                        $label = 'OCI';
                                    }

                                    // Extract subtype after colon
                                    if (str_contains($service, ':')) {
                                        [, $details] = explode(':', $service, 2);
                                        $details = trim($details);

                                        // Remove duplicated words like "Passport -"
                                        $details = preg_replace('/^(Passport|OCI|Visa)\s*-\s*/i', '', $details);

                                        echo $label . ' - ' . $details;
                                    } else {
                                        echo $label ?: $service;
                                    }
                                ?>

                            </td>

                            <td class="border px-2 py-1"><?php echo e($appointment->appointment_start_time ?? '-'); ?></td>
                            <td class="border px-2 py-1 font-semibold"><?php echo e($appointment->window_no ?? '-'); ?></td>

                            
                            <td class="border px-2 py-1">
                                <?php if($status !== 'accepted'): ?>
                                    <form method="POST" action="<?php echo e(route('appointments.assignWindow', $appointment)); ?>"
                                        class="flex justify-center gap-2 whitespace-nowrap">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>

                                        <?php for($i = 1; $i <= 4; $i++): ?>
                                            <?php
                                                $lockedToOtherWindow =
                                                    $appointment->returned_from_window &&
                                                    $appointment->returned_from_window != $i;

                                                if ($appointment->window_no == $i) {
                                                    $bg = '#38761d';
                                                    $disabled = false;
                                                } elseif ($lockedToOtherWindow) {
                                                    $bg = '#6b7280';
                                                    $disabled = true;
                                                } elseif (in_array($i, $usedByOthers)) {
                                                    $bg = '#dc2626';
                                                    $disabled = true;
                                                } else {
                                                    $bg = '#3b82f6';
                                                    $disabled = false;
                                                }
                                            ?>

                                            <button type="button" value="<?php echo e($i); ?>"
                                                <?php echo e($disabled ? 'disabled' : ''); ?> onclick="assignWindow(this)"
                                                style="background-color: <?php echo e($bg); ?>;
                   color: white;
                   padding: 6px 12px;
                   border-radius: 4px;
                   font-weight: 600;
                   opacity: <?php echo e($disabled ? '0.6' : '1'); ?>">
                                                <?php echo e($i); ?>

                                            </button>
                                        <?php endfor; ?>
                                    </form>


                                    <?php if($appointment->returned_from_window): ?>
                                        <div class="text-xs text-gray-600 mt-1">
                                            Can only be recalled by window
                                            <span class="font-semibold">
                                                <?php echo e($appointment->returned_from_window); ?>

                                            </span>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>

                            
                            
                            <td class="border px-2 py-1">
                                <div class="flex justify-center gap-2 flex-wrap">

                                    <?php if($status === 'accepted'): ?>
                                        <button type="button" onclick="openAssignModal(<?php echo e($appointment->id); ?>)"
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1 rounded font-semibold">
                                            Assign Application
                                        </button>
                                        <a href="<?php echo e(route('appointments.printLabel', $appointment->id)); ?>"
                                            onclick="this.innerText='Printing...'; this.style.pointerEvents='none';"
                                            class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-1 rounded font-semibold"
                                            title="Print label">
                                            Print Label
                                        </a>
                                    <?php else: ?>
                                        
                                        <form method="POST"
                                            action="<?php echo e(route('appointments.complete', $appointment)); ?>">
                                            <?php echo csrf_field(); ?>
                                            <button class="bg-green-600 text-white px-3 py-1 rounded">
                                                Complete
                                            </button>
                                        </form>

                                        
                                        <button type="button" onclick="openPendingModal(<?php echo e($appointment->id); ?>)"
                                            class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1 rounded">
                                            Pending
                                        </button>
                                        <a href="<?php echo e(route('appointments.editService', $appointment->id)); ?>"
                                            class="btn btn-sm btn-outline-primary">
                                            ✏️ Edit
                                        </a>

                                        
                                        <?php if(!is_null($appointment->window_no)): ?>
                                            <form method="POST"
                                                action="<?php echo e(route('appointments.returnToQueue', $appointment)); ?>">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PATCH'); ?>
                                                <button class="bg-red-600 text-white px-3 py-1 rounded">
                                                    Return to Queue
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                </div>
                            </td>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>

    


    
    <div id="uploadModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">

        <div class="bg-white rounded-lg w-full max-w-2xl p-6">

            <h2 class="text-lg font-bold mb-3">CSV Preview & Upload</h2>

            <form id="csvForm" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <input type="file" name="csv_file" id="csvInput" class="border p-2 w-full">

                <div id="preview" class="mt-4 hidden">
                    <table class="w-full text-sm border">
                        <tbody id="previewBody"></tbody>
                    </table>
                </div>

                <div class="mt-4 hidden" id="progressBox">
                    <div class="w-full bg-gray-200 rounded">
                        <div id="progressBar" class="bg-blue-600 text-white text-xs h-4 text-center" style="width:0%">0%
                        </div>
                    </div>
                </div>

                <button type="button" onclick="startPreview()"
                    class="mt-4 bg-gray-600 text-white px-4 py-2 rounded w-full">
                    Preview CSV
                </button>

                <button type="button" onclick="startUpload()"
                    class="mt-2 bg-blue-600 text-white px-4 py-2 rounded w-full">
                    Upload CSV
                </button>

                <button type="button" onclick="closeUploadModal()"
                    class="mt-2 bg-red-600 text-white px-4 py-2 rounded w-full">
                    Close
                </button>
            </form>
        </div>
    </div>

    
    <div id="pendingModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">

        <div class="bg-white rounded-lg w-full max-w-md p-6">
            <h2 class="text-lg font-bold mb-3">Mark Appointment as Pending</h2>

            <form method="POST" id="pendingForm">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PATCH'); ?>

                <textarea name="reason" required class="w-full border rounded p-2 mb-4" rows="4"
                    placeholder="Enter reason for pending..."></textarea>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closePendingModal()"
                        class="bg-gray-600 text-white px-4 py-2 rounded">
                        Cancel
                    </button>

                    <button type="submit" class="bg-yellow-600 text-white px-4 py-2 rounded">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    
    <div id="assignModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">

        <div class="bg-white rounded-lg w-full max-w-md p-6">

            <h2 class="text-lg font-bold mb-4">
                Assign Application
            </h2>

            <form method="POST" id="assignForm">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PATCH'); ?>

                <label class="block mb-2 font-semibold">
                    Assign to User
                </label>

                <select name="current_assignee_id" class="w-full border rounded px-3 py-2 mb-4" required>
                    <option value="">— Select User —</option>

                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($user->user_type === \App\Models\User::TYPE_NORMAL): ?>
                            <option value="<?php echo e($user->id); ?>">
                                <?php echo e($user->name); ?>

                            </option>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeAssignModal()"
                        class="bg-gray-600 text-white px-4 py-2 rounded">
                        Cancel
                    </button>

                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                        Assign
                    </button>
                </div>
            </form>

        </div>
    </div>
    
    <?php if(session('success') || session('error')): ?>
        <div id="printStatusModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

            <div class="bg-white rounded-lg w-full max-w-md p-6">

                <h2
                    class="text-lg font-bold mb-4 
            <?php echo e(session('success') ? 'text-green-600' : 'text-red-600'); ?>">

                    <?php echo e(session('success') ? 'Action Successful' : 'Action Failed'); ?>

                </h2>

                <p class="mb-6 text-gray-700">
                    <?php echo e(session('success') ?? session('error')); ?>

                </p>

                <div class="flex justify-end">
                    <button onclick="closePrintModal()"
                        class="<?php echo e(session('success') ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700'); ?> 
                       text-white px-4 py-2 rounded">
                        OK
                    </button>
                </div>

            </div>
        </div>
    <?php endif; ?>

    
    <script>
        function openAssignModal(appointmentId) {
            allowRefresh = false;

            const form = document.getElementById('assignForm');
            form.action = `/appointments/${appointmentId}/assign-user`;

            document.getElementById('assignModal').classList.remove('hidden');
            document.getElementById('assignModal').classList.add('flex');
        }


        function closeAssignModal() {
            allowRefresh = true;

            document.getElementById('assignModal').classList.add('hidden');
            document.getElementById('assignModal').classList.remove('flex');
        }


        function assignWindow(button) {
            allowRefresh = false;
            const form = button.closest('form');

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'window_no';
            input.value = button.value;

            form.appendChild(input);
            form.submit();
        }


        function openUploadModal() {
            allowRefresh = false;
            document.getElementById('uploadModal').classList.remove('hidden');
            document.getElementById('uploadModal').classList.add('flex');
        }

        function closeUploadModal() {
            allowRefresh = true;
            document.getElementById('uploadModal').classList.add('hidden');
            document.getElementById('uploadModal').classList.remove('flex');
        }

        function openPendingModal(appointmentId) {
            allowRefresh = false;

            const form = document.getElementById('pendingForm');
            form.action = `/appointments/${appointmentId}/pending`;

            document.getElementById('pendingModal').classList.remove('hidden');
            document.getElementById('pendingModal').classList.add('flex');
        }

        function closePendingModal() {
            allowRefresh = true;

            document.getElementById('pendingModal').classList.add('hidden');
            document.getElementById('pendingModal').classList.remove('flex');
        }

        function startPreview() {
            const fd = new FormData(document.getElementById('csvForm'));

            fetch("<?php echo e(route('appointments.preview')); ?>", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    },
                    body: fd
                })
                .then(r => r.json())
                .then(rows => {
                    const body = document.getElementById('previewBody');
                    body.innerHTML = '';
                    rows.forEach(r => {
                        const tr = document.createElement('tr');
                        tr.className = r.errors.length ? 'bg-red-100' : '';
                        tr.innerHTML =
                            `<td class="border p-1">${r.line}</td>
                 <td class="border p-1">${r.data.join(' | ')}</td>
                 <td class="border p-1 text-red-600">${r.errors.join(', ')}</td>`;
                        body.appendChild(tr);
                    });
                    document.getElementById('preview').classList.remove('hidden');
                });
        }

        function startUpload() {
            const fd = new FormData(document.getElementById('csvForm'));

            fetch("<?php echo e(route('appointments.upload.progress')); ?>", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                },
                body: fd
            });

            document.getElementById('progressBox').classList.remove('hidden');

            const timer = setInterval(() => {
                fetch("<?php echo e(route('appointments.progress')); ?>")
                    .then(r => r.json())
                    .then(p => {
                        if (p.total > 0) {
                            const percent = Math.round((p.current / p.total) * 100);
                            const bar = document.getElementById('progressBar');
                            bar.style.width = percent + '%';
                            bar.innerText = percent + '%';
                        }
                        if (p.done) {
                            clearInterval(timer);
                            setTimeout(() => location.reload(), 1000);
                        }
                    });
            }, 300);
        }

        function closePrintModal() {
            allowRefresh = true;
            document.getElementById('printStatusModal')?.remove();
        }

        <?php if(session('success') || session('error')): ?>
            allowRefresh = false;
        <?php endif; ?>
    </script>

</body>

</html>
<?php /**PATH D:\Berlin-Appointments-31\resources\views/appointments/index.blade.php ENDPATH**/ ?>