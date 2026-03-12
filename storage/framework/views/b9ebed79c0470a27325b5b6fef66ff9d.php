

<?php $__env->startSection('content'); ?>
<style>
.highlight-row {
    animation: highlightFade 2s ease;
}

@keyframes highlightFade {
    0% { background-color: #fff3cd; }
    100% { background-color: transparent; }
}
</style>

<div class="container-fluid py-3">

<div class="text-center flex-grow-1">
<div class="fw-bold fs-5">
Applications to be Assigned
</div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">

<h4 class="mb-0">
Accepted Applications (Assign for Processing)
</h4>

<div class="d-flex align-items-center gap-2">

<span class="text-muted small">
<?php echo e($appointments->count()); ?> case(s)
</span>

<a href="<?php echo e(route('appointments.delete')); ?>" class="btn btn-sm btn-danger">
<i class="bi bi-trash3"></i>
</a>

</div>
</div>


<?php if(session('success')): ?>
<div class="alert alert-success alert-dismissible fade show">
<?php echo e(session('success')); ?>

<button class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if(session('error')): ?>
<div class="alert alert-danger alert-dismissible fade show">
<?php echo e(session('error')); ?>

<button class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>


<?php
$currentService = request('service');
?>

<div class="card shadow-sm mb-3">
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
href="<?php echo e(route('appointments.assign.index',['service'=>'Passport'])); ?>">
Passport
</a>
</li>

<li class="nav-item">
<a class="nav-link <?php echo e($currentService == 'Visa' ? 'active' : ''); ?>"
href="<?php echo e(route('appointments.assign.index',['service'=>'Visa'])); ?>">
Visa
</a>
</li>

<li class="nav-item">
<a class="nav-link <?php echo e($currentService == 'OCI' ? 'active' : ''); ?>"
href="<?php echo e(route('appointments.assign.index',['service'=>'OCI'])); ?>">
OCI
</a>
</li>

<li class="nav-item">
<a class="nav-link <?php echo e($currentService == 'Miscellaneous' ? 'active' : ''); ?>"
href="<?php echo e(route('appointments.assign.index',['service'=>'Miscellaneous'])); ?>">
Miscellaneous
</a>
</li>

</ul>

</div>
</div>


<div class="card shadow-sm">

<form method="POST" action="<?php echo e(route('appointments.bulkAssign')); ?>">
<?php echo csrf_field(); ?>
<?php echo method_field('PATCH'); ?>

<div class="card shadow-sm mb-3">
<div class="card-body">

<input
type="text"
id="scanner-input"
class="form-control"
placeholder="Scan Reference Number..."
autofocus
>

<small class="text-muted">
Scan applications to add them to assignment list
</small>

</div>
</div>


<div class="card-body p-0">

<div class="table-responsive">

<table class="table table-hover table-striped align-middle mb-0">

<thead class="table-light">
<tr>

<th>
<input type="checkbox" id="select-all">
</th>

<th>Reference</th>
<th>Appointment No</th>
<th>Appointment Date</th>
<th>Applicant</th>
<th>Service</th>
<th>Status</th>
<th>Actions</th>

</tr>
</thead>


<tbody id="scan-results">

<?php $__empty_1 = true; $__currentLoopData = $appointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appointment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

<tr data-appointment-id="<?php echo e($appointment->id); ?>">

<td>
<input
type="checkbox"
name="appointment_ids[]"
value="<?php echo e($appointment->id); ?>"
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
Application accepted
</span>
</td>

<td>
<button type="button" class="btn btn-sm btn-outline-secondary edit-ref">✏️</button>
<button type="button" class="btn btn-sm btn-outline-danger remove-row">❌</button>
</td>

</tr>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

<tr>
<td colspan="8" class="text-center text-muted">
No applications found.
</td>
</tr>

<?php endif; ?>

</tbody>

</table>

</div>
</div>


<div class="card-footer bg-light d-flex justify-content-end align-items-center gap-2">

<div class="me-auto">
<small class="text-muted">
Selected: <span id="selected-count">0</span> application(s)
</small>
</div>

<select name="current_assignee_id" class="form-select w-auto" required>

<option value="">— Assign to User —</option>

<?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php if($user->id !== auth()->id()): ?>
<option value="<?php echo e($user->id); ?>">
<?php echo e($user->name); ?>

</option>
<?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</select>

<button type="submit" class="btn btn-primary">
Assign Selected
</button>

</div>

</form>
</div>





</div>
<?php $__env->stopSection(); ?>



<?php $__env->startPush('scripts'); ?>
<script>

document.addEventListener("DOMContentLoaded", function(){

const scanner=document.getElementById("scanner-input");
const table=document.getElementById("scan-results");
const selectedCount=document.getElementById("selected-count");

const scanSound=new Audio("/sounds/scan.mp3");

let scanned=[];

function updateCount(){
selectedCount.textContent=scanned.length;
}

scanner.addEventListener("keypress",function(e){

if(e.key==="Enter"){

e.preventDefault();

const reference=scanner.value.trim();
if(!reference) return;

fetch("<?php echo e(route('appointments.findByReference')); ?>?reference="+reference)

.then(res=>res.json())

.then(data=>{

if(!data.success){
alert(data.message);
scanner.value="";
return;
}

if(scanned.includes(data.appointment.id)){
alert("Already scanned.");
scanner.value="";
return;
}

const existingRow=document.querySelector(
`[data-appointment-id="${data.appointment.id}"]`
);

if(existingRow){

const checkbox=existingRow.querySelector(
'input[name="appointment_ids[]"]'
);

checkbox.checked=true;
existingRow.classList.add("highlight-row");

}else{

const row=document.createElement("tr");
row.dataset.appointmentId=data.appointment.id;
row.classList.add("highlight-row");

row.innerHTML=`

<td>
<input type="checkbox"
name="appointment_ids[]"
value="${data.appointment.id}"
checked>
</td>

<td>
<span class="badge bg-dark reference-text">
${data.appointment.reference}
</span>
</td>

<td>${data.appointment.appointment_no}</td>

<td>${formatDate(data.appointment.appointment_date)}</td>

<td>${data.appointment.applicant_name}</td>

<td>${data.appointment.service}</td>

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

table.prepend(row);

}

scanned.push(data.appointment.id);

scanSound.play();
scanner.value="";
updateCount();

});

}

});


function formatDate(dateString){

const date=new Date(dateString);

const day=String(date.getDate()).padStart(2,'0');
const month=String(date.getMonth()+1).padStart(2,'0');
const year=date.getFullYear();

return `${day}-${month}-${year}`;

}


table.addEventListener("click",function(e){

if(e.target.classList.contains("remove-row")){

const row=e.target.closest("tr");
const id=row.querySelector('input[name="appointment_ids[]"]').value;

scanned=scanned.filter(x=>x!=id);

row.remove();
updateCount();

}


if(e.target.classList.contains("edit-ref")){

const row=e.target.closest("tr");

const appointmentId=row.dataset.appointmentId;

const refElement=row.querySelector(".reference-text");

const oldRef=refElement.innerText;

const newRef=prompt("Edit Reference Number",oldRef);

if(!newRef||newRef===oldRef){
return;
}

fetch("<?php echo e(route('appointments.updateReferenceAjax')); ?>",{

method:"PATCH",

headers:{
"Content-Type":"application/json",
"X-CSRF-TOKEN":"<?php echo e(csrf_token()); ?>"
},

body:JSON.stringify({
appointment_id:appointmentId,
reference:newRef
})

})
.then(res=>res.json())
.then(data=>{

if(!data.success){
alert(data.message);
return;
}

document
.querySelectorAll(`[data-appointment-id="${appointmentId}"] .reference-text`)
.forEach(el=>{
el.innerText=data.reference;
});

});

}

});

});

</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Berlin-Appointments-31\resources\views/appointments/stage-assign.blade.php ENDPATH**/ ?>