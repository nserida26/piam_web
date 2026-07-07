
<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('sidebar.injection'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheader'); ?>
    <?php echo app('translator')->get('sidebar.injection'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheaderlink'); ?>
    <a href="<?php echo e(route('admin.imports.administrations.index')); ?>"> <?php echo app('translator')->get('sidebar.injection'); ?> </a>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheaderactive'); ?>
    <?php echo app('translator')->get('sidebar.injection'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('css'); ?>
    <!-- Select2 -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/plugins/select2/css/select2.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')); ?>">
<?php $__env->stopPush(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-md-12">
            <?php echo $__env->yieldContent('import'); ?>
        </div>
    </div>
    <!-- /.row -->
<?php $__env->stopSection(); ?>

<?php $__env->startPush('script'); ?>
    <!-- Select2 -->
    <script src="<?php echo e(asset('assets/admin/plugins/select2/js/select2.full.min.js')); ?>"></script>
    <!-- dropzonejs -->
    <!-- Page specific script -->
    <script>
        $(function() {
            //Initialize Select2 Elements
            $('.select2').select2()

        });
    </script>
    <script type="text/javascript"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\lapto\piam_projet\piam\resources\views/admin/imports/index.blade.php ENDPATH**/ ?>