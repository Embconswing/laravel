@if ($total > 0)

<div class="row mb-4">

<div class="card shadow-sm">

<div class="card-body">

<h6 class="fw-bold mb-3">
Application Summary ({{ $total }} Total)
</h6>

<div style="width:100%; height:160px;">
<canvas id="applicationsChart"></canvas>
</div>

</div>
</div>

</div>

@endif