<?php if(Session::has('errors')): ?>
    <div class="alert alert-danger" role="alert">
        <?php echo e(Session::get('errors')); ?>

    </div>
<?php endif; ?>
<?php /**PATH C:\Users\lapto\piam_projet\piam\resources\views/admin/includes/alerts/error.blade.php ENDPATH**/ ?>