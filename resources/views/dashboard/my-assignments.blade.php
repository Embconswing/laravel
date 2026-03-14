@extends('layouts.app')

@section('content')

@if (session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="container py-3">

    {{-- HEADER --}}
    @include('dashboard.partials.header')

    {{-- EMPTY STATE --}}
    @if ($appointments->isEmpty())
        <div class="alert alert-info">
            No applications are currently assigned to you.
        </div>
    @else

        {{-- SUMMARY CHART --}}
        @include('dashboard.partials.summary-chart')

        {{-- FILTERS --}}
        @include('dashboard.partials.filters')

        {{-- TABLE --}}
        @include('dashboard.partials.table')

        {{-- PAGINATION --}}
        @if ($appointments->hasPages())
        <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap">

            <div class="small text-muted mb-2">
                Showing {{ $appointments->firstItem() }}
                to {{ $appointments->lastItem() }}
                of {{ $appointments->total() }} applications
            </div>

            <div>
                {{ $appointments->links() }}
            </div>

        </div>
        @endif

    @endif

</div>

{{-- MODALS --}}
@include('dashboard.partials.modals')

{{-- SCRIPTS --}}
@include('dashboard.partials.scripts')

@endsection