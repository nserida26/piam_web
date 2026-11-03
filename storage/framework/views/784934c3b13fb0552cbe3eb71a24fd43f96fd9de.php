<?php $__env->startSection('title'); ?> <?php echo app('translator')->get('coordinateur.space'); ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheader'); ?> <?php echo app('translator')->get('coordinateur.space'); ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheaderlink'); ?>
    <a href="<?php echo e(route('coordinateur.dashboard')); ?>"><?php echo app('translator')->get('coordinateur.space'); ?></a>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheaderactive'); ?> <?php echo app('translator')->get('coordinateur.dashboard'); ?> <?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="alert alert-warning">
    <h5><i class="fas fa-exclamation-triangle"></i> <?php echo app('translator')->get('coordinateur.no_ong_title'); ?></h5>
    <p class="mb-0"><?php echo app('translator')->get('coordinateur.no_ong_text'); ?></p>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\lapto\piam_projet\piam\resources\views/admin/coordinateur/no_ong.blade.php ENDPATH**/ ?>