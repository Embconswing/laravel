@extends('layouts.app')

@section('content')

<div class="container">

<x-page-header title="📬 Collection of Documents">

<x-slot:right>
<a href="{{ url('/') }}" class="btn btn-sm btn-primary">
🏠 Dashboard
</a>
</x-slot>

</x-page-header>
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

@foreach($appointments as $a)

<tr>

<td>{{ $a->appointment_no }}</td>

<td>{{ $a->ReferenceNr }}</td>

<td>{{ $a->applicant_name }}</td>

<td>{{ $a->service }}</td>

<td>

<button class="btn btn-sm btn-success"

onclick="openCollectModal(
{{ $a->id }},
'{{ $a->appointment_no }}',
'{{ $a->ReferenceNr }}',
'{{ $a->applicant_name }}'
)">

Collect

</button>

</td>

</tr>

@endforeach

</tbody>

</table>

{{ $appointments->links() }}

</div>



{{-- MODAL --}}
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

fetch("{{ route('appointments.collect-documents.scan') }}",{

method:'POST',

headers:{
'Content-Type':'application/json',
'X-CSRF-TOKEN':'{{ csrf_token() }}'
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

fetch("{{ route('appointments.collect-documents.process') }}",{

method:'POST',

headers:{
'Content-Type':'application/json',
'X-CSRF-TOKEN':'{{ csrf_token() }}'
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

@endsection