<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Counter Appointments</title>

    <!-- Bootstrap CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet"/>

    <!-- Favicon -->
    <link rel="icon" href="<?php echo e(asset('favicon.ico')); ?>" type="image/x-icon">
</head>

<body class="bg-light">

<!-- ================= HEADER ================= -->
<div class="container my-4">
    <div class="row">
        <div class="col-12">
            <div class="p-4 rounded shadow text-center bg-primary bg-opacity-10">
                <h1 class="fw-bold display-2 text-primary mb-2">
                    Counter Appointments
                </h1>

                <h2 class="fs-4 text-primary-emphasis mb-0">
                    <?php echo e($registname); ?>

                </h2>
            </div>
        </div>
    </div>
</div>
<!-- ================= END HEADER ================= -->


<!-- ================= NAVIGATION ================= -->
<div class="container mb-4">
    <div class="row">
        <div class="col-12 d-flex justify-content-end gap-3">

            <?php if(Route::has('login')): ?>
                <?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(url('/dashboard')); ?>" class="btn btn-outline-primary">
                        Dashboard
                    </a>
                <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>" class="btn btn-outline-primary">
                        Log in
                    </a>

                    <?php if(Route::has('register')): ?>
                        <a href="<?php echo e(route('register')); ?>" class="btn btn-primary">
                            Register
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>

        </div>
    </div>
</div>
<!-- ================= END NAV ================= -->


<!-- ================= MAIN CONTENT ================= -->
<div class="container">
    <div class="row align-items-start">

        <!-- LEFT: MENU -->
        <div class="col-lg-6 bg-white p-4 rounded shadow-sm">

            <div class="d-grid gap-3">

                <!-- ✅ START QUEUE SYSTEM (TV / HDMI PC ONLY) -->
                <a href="<?php echo e(route('queue.display')); ?>"
                   target="_blank"
                   class="text-decoration-none">
                    <div class="card border-success shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-success mb-1">
                                Start Queue System (TV)
                            </h5>
                            <p class="card-text text-danger opacity-75 mb-0">
    Opens the live queue display (TO BE OPENED ONLY ON SERVER)
</p>

                        </div>
                    </div>
                </a>

                <!-- Counter Appointments -->
                <a href="./p"
                   class="text-decoration-none">
                    <div class="card border-primary shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-primary mb-1">
                                Counter Appointments
                            </h5>
                            <p class="card-text text-muted mb-0">
                                Manage counter appointments
                            </p>
                        </div>
                    </div>
                </a>

                <!-- Token Display -->
                <a href="./tv"
                   class="text-decoration-none">
                    <div class="card border-primary shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-primary mb-1">
                                Token Display (TV)
                            </h5>
                            <p class="card-text text-muted mb-0">
                                Live token and counter display
                            </p>
                        </div>
                    </div>
                </a>

                <!-- Announcements -->
                <a href="./admin/announcements"
                   class="text-decoration-none">
                    <div class="card border-primary shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-primary mb-1">
                                Change Counter Announcements
                            </h5>
                            <p class="card-text text-muted mb-0">
                                Update and manage counter announcements
                            </p>
                        </div>
                    </div>
                </a>

            </div>
        </div>

        <!-- RIGHT: IMAGE -->
        <div class="col-lg-6 p-0 ps-lg-3">
            <div class="rounded overflow-hidden shadow-sm">
                <img
                    src="<?php echo e(asset('images/embassy.jpg')); ?>"
                    alt="Consulate building"
                    class="img-fluid w-100">
            </div>
        </div>

    </div>
</div>
<!-- ================= END MAIN ================= -->

</body>
</html>
<?php /**PATH D:\Berlin-Appointments-25\resources\views/welcome.blade.php ENDPATH**/ ?>