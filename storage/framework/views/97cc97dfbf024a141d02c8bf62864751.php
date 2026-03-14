<div class="card shadow-sm mb-3">

    <div class="card-body py-2">

        <form method="GET" action="<?php echo e(route('my.assignments')); ?>">

            <div class="row g-2 align-items-center">

                <div class="col-md-5">
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Search name, email or reference number..."
                           value="<?php echo e(request('search')); ?>">
                </div>

                <div class="col-md-2">
                    <input type="date"
                           name="from_date"
                           class="form-control"
                           value="<?php echo e(request('from_date')); ?>">
                </div>

                <div class="col-md-2">
                    <input type="date"
                           name="to_date"
                           class="form-control"
                           value="<?php echo e(request('to_date')); ?>">
                </div>

                <div class="col-md-1">
                    <button class="btn btn-primary w-100">
                        Search
                    </button>
                </div>

                <div class="col-md-2">
                    <a href="<?php echo e(route('my.assignments')); ?>"
                       class="btn btn-outline-secondary w-100">
                       Reset
                    </a>
                </div>

            </div>

        </form>

    </div>

</div><?php /**PATH D:\Berlin-Appointments-31\resources\views/dashboard/partials/filters.blade.php ENDPATH**/ ?>