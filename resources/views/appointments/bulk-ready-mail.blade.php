@extends('layouts.app')

@section('content')

<div class="container">

<x-page-header title="📦 Bulk Ready to Mail">

<x-slot:right>
<a href="{{ url('/') }}" class="btn btn-sm btn-primary">
🏠 Dashboard
</a>
</x-slot>

</x-page-header>


<div class="alert alert-info">
Scan the barcode or manually select applications below.
</div>


<form method="POST" action="{{ route('appointments.bulk-ready-mail.process') }}">
@csrf


{{-- ========================================= --}}
{{-- BARCODE SCANNER --}}
{{-- ========================================= --}}

<div class="row mb-4">

<div class="col-md-4">

<input type="text"
id="barcode"
class="form-control form-control-lg"
placeholder="Scan Application Barcode"
autofocus>

</div>

<div class="col-md-2">

<span class="badge bg-success p-3">
Scanned: <span id="scanCount">0</span>
</span>

</div>

</div>



{{-- ========================================= --}}
{{-- SCANNED APPLICATIONS --}}
{{-- ========================================= --}}

<h5>Scanned Applications</h5>

<table class="table table-bordered table-striped" id="scanTable">

<thead class="table-dark">

<tr>
<th>Reference</th>
<th>Name</th>
<th>Service</th>
<th>Remove</th>
</tr>

</thead>

<tbody></tbody>

</table>

<div id="hiddenIds"></div>



{{-- ========================================= --}}
{{-- MANUAL SELECTION --}}
{{-- ========================================= --}}

<hr class="my-5">

<h5>Manual Selection (Process Completed Applications)</h5>


<div class="row mb-2">

<div class="col-md-4">

<input type="text"
id="searchApp"
class="form-control"
placeholder="Search Reference / Name">

</div>

</div>


<table class="table table-bordered table-striped table-sm" id="manualTable">

<thead class="table-secondary">

<tr>

<th width="40">
<input type="checkbox" id="selectAll">
</th>

<th>Reference</th>
<th>Name</th>
<th>Service</th>
<th>Passport</th>
<th>Appointment No</th>

</tr>

</thead>


<tbody>

@foreach($appointments as $app)

<tr>

<td>
<input type="checkbox"
name="appointment_ids[]"
value="{{ $app->id }}">
</td>

<td>{{ $app->ReferenceNr }}</td>
<td>{{ $app->applicant_name }}</td>
<td>{{ $app->service }}</td>
<td>{{ $app->passportno }}</td>
<td>{{ $app->appointment_no }}</td>

</tr>

@endforeach

</tbody>

</table>

{{ $appointments->links() }}



{{-- ========================================= --}}
{{-- COLLECTION DETAILS --}}
{{-- ========================================= --}}

<div class="row mt-4">

<div class="col-md-3">

<label>Collection Date</label>

<input type="date"
name="collection_date"
class="form-control"
value="{{ now()->format('Y-m-d') }}"
required>

</div>


<div class="col-md-3">

<label>Collection Time</label>

<input type="time"
name="collection_time"
class="form-control"
value="{{ now()->format('H:i') }}"
required>

</div>

</div>


<button class="btn btn-success btn-lg mt-4">
Set Ready to Mail
</button>


</form>

</div>

@endsection


@push('scripts')

<script>

let scannedIds = [];
let scanCount = 0;

const successBeep = new Audio("https://actions.google.com/sounds/v1/cartoon/clang_and_wobble.ogg");
const errorBeep = new Audio("https://actions.google.com/sounds/v1/cartoon/wood_plank_flicks.ogg");


document.getElementById('barcode').addEventListener('keypress', function(e){

if(e.key === 'Enter'){

e.preventDefault();

let barcode = this.value.trim();

if(barcode === '') return;


fetch("{{ route('appointments.bulk-ready-mail.scan') }}",{
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

errorBeep.play();
alert(data.error);
return;

}


if(scannedIds.includes(data.id)){

errorBeep.play();
return;

}


successBeep.play();

scannedIds.push(data.id);

scanCount++;

document.getElementById('scanCount').innerText = scanCount;


let row = `
<tr id="row_${data.id}" class="table-success">

<td>${data.reference}</td>
<td>${data.name}</td>
<td>${data.service}</td>

<td>
<button type="button"
class="btn btn-danger btn-sm"
onclick="removeRow(${data.id})">
Remove
</button>
</td>

</tr>
`;

document.querySelector('#scanTable tbody').insertAdjacentHTML('beforeend', row);


document.getElementById('hiddenIds').insertAdjacentHTML('beforeend',
`<input type="hidden" name="appointment_ids[]" value="${data.id}" id="input_${data.id}">`
);

});


this.value='';

}

});



function removeRow(id){

document.getElementById('row_'+id).remove();
document.getElementById('input_'+id).remove();

scannedIds = scannedIds.filter(item => item !== id);

scanCount--;

document.getElementById('scanCount').innerText = scanCount;

}



document.getElementById('selectAll').addEventListener('click', function(){

document.querySelectorAll('#manualTable input[name="appointment_ids[]"]').forEach(cb => {
cb.checked = this.checked;
});

});



document.getElementById('searchApp').addEventListener('keyup', function(){

let value = this.value.toLowerCase();

document.querySelectorAll("#manualTable tbody tr").forEach(row => {

row.style.display =
row.innerText.toLowerCase().includes(value)
? ''
: 'none';

});

});

</script>

@endpush