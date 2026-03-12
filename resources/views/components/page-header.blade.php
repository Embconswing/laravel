<div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-2">

    {{-- Back Button --}}
    <a href="javascript:history.back()" class="btn btn-sm btn-outline-secondary">
        ⬅ Back
    </a>

    {{-- Page Title --}}
    <h1 class="text-primary fw-bold text-center flex-grow-1 m-0">
        {{ $title }}
    </h1>

    {{-- Right Side Slot (optional buttons) --}}
    <div>
        {{ $right ?? '' }}
    </div>

</div>