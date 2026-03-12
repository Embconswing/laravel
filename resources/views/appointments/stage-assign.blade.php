@extends('layouts.app')

@section('content')
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
{{ $appointments->count() }} case(s)
</span>

<a href="{{ route('appointments.delete') }}" class="btn btn-sm btn-danger">
<i class="bi bi-trash3"></i>
</a>

</div>
</div>


@if (session('success'))
<div class="alert alert-success alert-dismissible fade show">
{{ session('success') }}
<button class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if (session('error'))
<div class="alert alert-danger alert-dismissible fade show">
{{ session('error') }}
<button class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif


@php
$currentService = request('service');
@endphp

<div class="card shadow-sm mb-3">
<div class="card-body py-2">

<ul class="nav nav-pills nav-fill">

<li class="nav-item">
<a class="nav-link {{ !$currentService ? 'active' : '' }}"
href="{{ route('appointments.assign.index') }}">
All
</a>
</li>

<li class="nav-item">
<a class="nav-link {{ $currentService == 'Passport' ? 'active' : '' }}"
href="{{ route('appointments.assign.index',['service'=>'Passport']) }}">
Passport
</a>
</li>

<li class="nav-item">
<a class="nav-link {{ $currentService == 'Visa' ? 'active' : '' }}"
href="{{ route('appointments.assign.index',['service'=>'Visa']) }}">
Visa
</a>
</li>

<li class="nav-item">
<a class="nav-link {{ $currentService == 'OCI' ? 'active' : '' }}"
href="{{ route('appointments.assign.index',['service'=>'OCI']) }}">
OCI
</a>
</li>

<li class="nav-item">
<a class="nav-link {{ $currentService == 'Miscellaneous' ? 'active' : '' }}"
href="{{ route('appointments.assign.index',['service'=>'Miscellaneous']) }}">
Miscellaneous
</a>
</li>

</ul>

</div>
</div>


<div class="card shadow-sm">

<form method="POST" action="{{ route('appointments.bulkAssign') }}">
@csrf
@method('PATCH')

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

@forelse($appointments as $appointment)

<tr data-appointment-id="{{ $appointment->id }}">

<td>
<input
type="checkbox"
name="appointment_ids[]"
value="{{ $appointment->id }}"
class="appointment-checkbox">
</td>

<td>
<span class="badge bg-dark reference-text">
{{ $appointment->ReferenceNr }}
</span>
</td>

<td>{{ $appointment->appointment_no }}</td>

<td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d-m-Y') }}</td>

<td>{{ $appointment->applicant_name }}</td>

<td>{{ $appointment->service }}</td>

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

@empty

<tr>
<td colspan="8" class="text-center text-muted">
No applications found.
</td>
</tr>

@endforelse

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

@foreach ($users as $user)
@if ($user->id !== auth()->id())
<option value="{{ $user->id }}">
{{ $user->name }}
</option>
@endif
@endforeach

</select>

<button type="submit" class="btn btn-primary">
Assign Selected
</button>

</div>

</form>
</div>





</div>
@endsection



@push('scripts')
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

fetch("{{ route('appointments.findByReference') }}?reference="+reference)

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

fetch("{{ route('appointments.updateReferenceAjax') }}",{

method:"PATCH",

headers:{
"Content-Type":"application/json",
"X-CSRF-TOKEN":"{{ csrf_token() }}"
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
@endpush