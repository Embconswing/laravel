

<?php $__env->startSection('content'); ?>

<div class="container">

<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => '📬 Collection of Documents']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => '📬 Collection of Documents']); ?>

 <?php $__env->slot('right', null, []); ?> 
<a href="<?php echo e(url('/')); ?>" class="btn btn-sm btn-primary">
🏠 Dashboard
</a>
 <?php $__env->endSlot(); ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e)): ?>
<?php $attributes = $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e; ?>
<?php unset($__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e)): ?>
<?php $component = $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e; ?>
<?php unset($__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e); ?>
<?php endif; ?>
<input type="text"
       id="barcode"
       class="form-control"
       placeholder="Scan Application Barcode"
       autofocus>


<table class="table table-striped table-bordered mt-3">

<thead class="table-light">

<tr>
<th>Appointment No</th>
<th>Reference</th>
<th>Name</th>
<th>Service</th>
<th>Action</th>
</tr>

</thead>

<tbody>

<?php $__currentLoopData = $appointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

<tr>

<td><?php echo e($a->appointment_no); ?></td>

<td><?php echo e($a->ReferenceNr); ?></td>

<td><?php echo e($a->applicant_name); ?></td>

<td><?php echo e($a->service); ?></td>

<td>

<button class="btn btn-sm btn-success"

onclick="openCollectModal(
<?php echo e($a->id); ?>,
'<?php echo e($a->appointment_no); ?>',
'<?php echo e($a->ReferenceNr); ?>',
'<?php echo e($a->applicant_name); ?>'
)">

Collect

</button>

</td>

</tr>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</tbody>

</table>

<?php echo e($appointments->links()); ?>


</div>




<div class="modal fade" id="collectModal">

<div class="modal-dialog">

<div class="modal-content">

<div class="modal-header">

<h5 class="modal-title">Document Collection</h5>

<button type="button" class="btn-close" data-bs-dismiss="modal"></button>

</div>


<div class="modal-body">

<input type="hidden" id="appointment_id">

<div class="mb-2">
<strong>Appointment No:</strong>
<span id="modal_app_no"></span>
</div>

<div class="mb-2">
<strong>Reference:</strong>
<span id="modal_ref"></span>
</div>

<div class="mb-3">
<strong>Applicant:</strong>
<span id="modal_name"></span>
</div>

<label>Remarks</label>

<textarea class="form-control"
          id="remarks"
          placeholder="Optional remarks"></textarea>

</div>


<div class="modal-footer">

<button class="btn btn-secondary"
        data-bs-dismiss="modal">

Cancel

</button>

<button class="btn btn-success"
        onclick="confirmCollection()">

Confirm Collection

</button>

</div>

</div>

</div>

</div>



<script>

function openCollectModal(id, appno, reference, name){

document.getElementById('appointment_id').value = id;

document.getElementById('modal_app_no').innerText = appno;
document.getElementById('modal_ref').innerText = reference;
document.getElementById('modal_name').innerText = name;

document.getElementById('remarks').value = '';

let modal = new bootstrap.Modal(document.getElementById('collectModal'));

modal.show();

}



document.getElementById('barcode').addEventListener('keypress',function(e){

if(e.key === 'Enter'){

e.preventDefault();

let barcode = this.value;

fetch("<?php echo e(route('appointments.collect-documents.scan')); ?>",{

method:'POST',

headers:{
'Content-Type':'application/json',
'X-CSRF-TOKEN':'<?php echo e(csrf_token()); ?>'
},

body:JSON.stringify({barcode:barcode})

})
.then(res => res.json())
.then(data => {

if(data.error){

alert(data.error);

return;

}

openCollectModal(

data.id,
data.appointment_no,
data.reference,
data.name

);

});

this.value='';

}

});



function confirmCollection(){

let id = document.getElementById('appointment_id').value;
let remarks = document.getElementById('remarks').value;

fetch("<?php echo e(route('appointments.collect-documents.process')); ?>",{

method:'POST',

headers:{
'Content-Type':'application/json',
'X-CSRF-TOKEN':'<?php echo e(csrf_token()); ?>'
},

body:JSON.stringify({

appointment_id:id,
remarks:remarks

})

})
.then(res => res.json())
.then(data => {

if(data.success){

location.reload();

}

});

}



document.getElementById('collectModal')
.addEventListener('hidden.bs.modal', function () {

document.getElementById('barcode').focus();

});

</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Berlin-Appointments-20\resources\views/appointments/collect-documents.blade.php ENDPATH**/ ?>