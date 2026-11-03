
<?php $__env->startSection('title'); ?> <?php echo app('translator')->get('coordinateur.space'); ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheader'); ?> <?php echo app('translator')->get('coordinateur.dashboard'); ?> — <?php echo e($ong->intitule ?? ''); ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheaderlink'); ?>
    <a href="<?php echo e(route('coordinateur.dashboard')); ?>"><?php echo app('translator')->get('coordinateur.space'); ?></a>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheaderactive'); ?> <?php echo app('translator')->get('coordinateur.dashboard'); ?> <?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php if($sansWilaya): ?>
    <div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> <?php echo app('translator')->get('coordinateur.no_wilaya'); ?></div>
<?php endif; ?>
<div class="row">
    <div class="col-md-3 col-sm-6">
        <a href="<?php echo e(route('coordinateur.facilitateurs.index')); ?>" class="text-dark">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><?php echo app('translator')->get('coordinateur.nb_facilitateurs'); ?></span>
                    <span class="info-box-number"><?php echo e($nbFacilitateurs); ?></span>
                </div>
            </div>
        </a>
    </div>
</div>

<?php
    $boxes = [
        'attente' => ['bg-warning', 'fa-hourglass-half'],
        'valide_coord' => ['bg-primary', 'fa-user-check'],
        'rejete' => ['bg-danger', 'fa-times-circle'],
        'valide_admin' => ['bg-success', 'fa-check-double'],
    ];
?>

<?php $__currentLoopData = $stats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $counts): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <h5 class="mt-3"><?php echo app('translator')->get('coordinateur.type_' . $type); ?></h5>
    <div class="row">
        <?php $__currentLoopData = $boxes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $statut => [$bg, $icon]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-md-3 col-sm-6">
                <a href="<?php echo e(route('coordinateur.validation', ['type' => $type, 'statut' => $statut])); ?>" class="text-dark">
                    <div class="info-box">
                        <span class="info-box-icon <?php echo e($bg); ?>"><i class="fas <?php echo e($icon); ?>"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text text-wrap"><?php echo app('translator')->get('coordinateur.statut_' . $statut); ?></span>
                            <span class="info-box-number"><?php echo e($counts[$statut]); ?></span>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<p class="text-muted small mt-2"><i class="fas fa-info-circle"></i> <?php echo app('translator')->get('coordinateur.help_validation'); ?></p>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\lapto\piam_projet\piam\resources\views/admin/coordinateur/dashboard.blade.php ENDPATH**/ ?>