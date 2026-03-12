

<?php $__env->startSection('content'); ?>
<div style="max-width:900px;margin:20px auto;padding:20px;">

    <h1 style="font-size:24px;font-weight:bold;margin-bottom:20px;">
        Manage Announcements
    </h1>

    
    <?php if(session('success')): ?>
        <div style="background:#d1fae5;color:#065f46;padding:10px;margin-bottom:15px;">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    
    <?php $__empty_1 = true; $__currentLoopData = $announcements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div style="border:1px solid #ccc;padding:15px;margin-bottom:15px;">

            
            <form method="POST" action="<?php echo e(route('announcements.update', $a->id)); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <label>Title</label><br>
                <input type="text" name="title" value="<?php echo e($a->title); ?>" required
                       style="width:100%;padding:8px;margin-bottom:8px;"><br>

                <label>Message</label><br>
                <textarea name="message" required
                          style="width:100%;padding:8px;margin-bottom:8px;"
                          rows="3"><?php echo e($a->message); ?></textarea><br>

                <label>Start Date</label><br>
                <input type="date" name="start_date" value="<?php echo e($a->start_date); ?>" required
                       style="padding:6px;margin-bottom:8px;"><br>

                <label>End Date</label><br>
                <input type="date" name="end_date" value="<?php echo e($a->end_date); ?>"
                       style="padding:6px;margin-bottom:8px;"><br><br>

                <label>
                    <input type="checkbox" name="is_active" value="1"
                           <?php echo e($a->is_active ? 'checked' : ''); ?>>
                    Active
                </label>
                <br><br>

                <button type="submit"
                        style="background:#2563eb;color:#fff;padding:8px 16px;border:none;">
                    Update
                </button>
            </form>

            
            <form method="POST"
                  action="<?php echo e(route('announcements.destroy', $a->id)); ?>"
                  style="margin-top:10px;">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>

                <button type="submit"
                        style="background:#dc2626;color:#fff;padding:6px 14px;border:none;">
                    Delete
                </button>
            </form>

        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p>No announcements yet.</p>
    <?php endif; ?>

    <hr style="margin:30px 0;">

    
    <h2 style="font-size:20px;font-weight:bold;margin-bottom:10px;">
        Add New Announcement
    </h2>

    <form method="POST" action="<?php echo e(route('announcements.store')); ?>">
        <?php echo csrf_field(); ?>

        <label>Title</label><br>
        <input type="text" name="title" required
               style="width:100%;padding:8px;margin-bottom:8px;"><br>

        <label>Message</label><br>
        <textarea name="message" required
                  style="width:100%;padding:8px;margin-bottom:8px;"
                  rows="3"></textarea><br>

        <label>Start Date</label><br>
        <input type="date" name="start_date" required
               style="padding:6px;margin-bottom:8px;"><br>

        <label>End Date</label><br>
        <input type="date" name="end_date"
               style="padding:6px;margin-bottom:8px;"><br><br>

        <label>
            <input type="checkbox" name="is_active" value="1" checked>
            Active
        </label>
        <br><br>

        <button type="submit"
                style="background:#16a34a;color:#fff;padding:10px 20px;border:none;">
            Add Announcement
        </button>
    </form>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Berlin-Appointments-25\resources\views/admin/announcements/index.blade.php ENDPATH**/ ?>