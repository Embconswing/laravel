@extends('layouts.app')

@section('content')
<div class="container py-3">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold">Create Walk-in Application</h4>

        <a href="{{ route('walkins.index') }}"
           class="btn btn-sm btn-outline-secondary">
            ← Back to Walk-ins
        </a>
    </div>

    {{-- Reuse index form --}}
    @include('walk-ins.index')

</div>
@endsection
