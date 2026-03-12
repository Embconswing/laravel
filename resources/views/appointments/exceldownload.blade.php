<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Appointments Export</title>

    {{-- Bootstrap --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Export Appointments</h4>
                </div>

                <div class="card-body">
                    <form action="{{ route('appointments.export.download') }}" method="GET">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="from" class="form-label">From Date</label>
                                <input
                                    type="date"
                                    name="from"
                                    id="from"
                                    class="form-control"
                                    value="{{ request('from') }}"
                                >
                            </div>

                            <div class="col-md-6">
                                <label for="to" class="form-label">To Date</label>
                                <input
                                    type="date"
                                    name="to"
                                    id="to"
                                    class="form-control"
                                    value="{{ request('to') }}"
                                >
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                ⬇ Download Excel
                            </button>

                            <a href="{{ route('appointments.export.page') }}"
                               class="btn btn-outline-secondary">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
